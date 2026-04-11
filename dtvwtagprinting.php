<?php
// pengecekan ajax request untuk mencegah direct access file, agar file tidak bisa diakses secara langsung dari browser
// jika ada ajax request
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && (isset($_POST['startDate']) && isset($_POST['endDate']))) {
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../config/configpdo2.php";
    //$table = 'vwtblpoprod2';
try {
// Get parameters from request
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'b.adddate';
    $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
    $filterCol = isset($_GET['filter_col']) ? $_GET['filter_col'] : '';
    $filterVal = isset($_GET['filter_val']) ? $_GET['filter_val'] : '';
    $filterMode = isset($_GET['filter_mode']) ? $_GET['filter_mode'] : 'contains';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    
    //$strquery="SELECT a.notag, i.nama , a.berat, a.adddate,
      //  a.addby, i.itemid, a.adddate2 as tgltag, a.notrans as noref 
      //  from tbltag a join tblitem i on a.itemid=i.itemid
      //  order by a.adddate desc";

/*         $stmt = $pdo->prepare($strquery);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    
        // Get column names
        $columnCount = $stmt->columnCount();
        $columns = [];
        for ($i = 0; $i < $columnCount; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $columns[] = $meta['name'];
        }
        // Return JSON response
        echo json_encode([
            'success' => true,
            'columns' => $columns,
            'rows' => $rows,
            'total' => count($rows)
        ]);
 */
// Define allowed columns to prevent SQL injection
    $allowedColumns = ['b.notag', 'i.nama', 'b.berat', 'b.adddate',
     'b.addby', 'b.adddate2','b.notrans', 'b.notagblowing', 'b.shiftid', 'b.kodemesin'];
    if (!in_array($sort, $allowedColumns)) {
        $sort = 'b.adddate';
    }
    
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];
    if (!empty($filterCol)) {
        if ($filterCol === 'nama') {
            $filterCol = 'i.nama';
        } elseif ($filterCol === 'notag') {
            $filterCol = 'b.notag';
        } elseif ($filterCol === 'noref') {
            $filterCol = 'b.notrans';
        } elseif ($filterCol === 'berat') {
            $filterCol = 'b.berat';
        } elseif ($filterCol === 'adddate') {
            $filterCol = 'b.adddate';
        } elseif ($filterCol === 'addby') {
            $filterCol = 'b.addby';
        } elseif ($filterCol === 'tgltag') {
            $filterCol = 'b.adddate2';
        } elseif ($filterCol === 'notagblowing') {
            $filterCol = 'b.notagblowing';
        } elseif ($filterCol === 'shiftid') {
            $filterCol = 'b.shiftid';
        } elseif ($filterCol === 'kodemesin') {
            $filterCol = 'b.kodemesin';
        }
    }

    // Column filter
    //if (!empty($filterCol) && !empty($filterVal) && in_array($filterCol, $allowedColumns)) {
    if (!empty($filterCol) && !empty($filterVal) ) {
        if ($filterMode === 'contains') {
            $whereConditions[] = "$filterCol LIKE :filter_val";
            $params[':filter_val'] = "%$filterVal%";
        } elseif ($filterMode === 'exact') {
            $whereConditions[] = "$filterCol = :filter_val";
            $params[':filter_val'] = $filterVal;
        } elseif ($filterMode === 'starts') {
            $whereConditions[] = "$filterCol LIKE :filter_val";
            $params[':filter_val'] = "$filterVal%";
        }
    }
    
    // Global search (search in multiple columns)
    if (!empty($search)) {
        $searchConditions = [];
        $searchColumns = ['b.notag', 'i.nama', 'b.notrans','b.notagblowing', 'b.shiftid', 'b.kodemesin'];
        foreach ($searchColumns as $col) {
            $searchConditions[] = "$col LIKE :search";
        }
        $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
        $params[':search'] = "%$search%";
    }
    
    // Date range filter
    if (!empty($dateFrom)) {
        $whereConditions[] = "b.adddate2 >= :date_from";
        $params[':date_from'] = $dateFrom;
    }
    if (!empty($dateTo)) {
        $whereConditions[] = "b.adddate2 <= :date_to";
        $params[':date_to'] = $dateTo . " 23:59:59";
    }
    
    $whereSQL = !empty($whereConditions) ? "WHERE " . 
    implode(" AND ", $whereConditions) : "";
    
    // Get total count
    $countSQL = "SELECT COUNT(*) as total FROM tbltagp b join tblitem i 
    on b.itemid=i.itemid $whereSQL";
    $countStmt = $pdo->prepare($countSQL);
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $countSQL = "SELECT sum(b.berat) as total_weight FROM tbltagp b join tblitem i 
    on b.itemid=i.itemid $whereSQL";
    $countStmt = $pdo->prepare($countSQL);
    $countStmt->execute($params);
    $totalWeight = $countStmt->fetch(PDO::FETCH_ASSOC)['total_weight'];
    
    // Calculate pagination
    $offset = ($page - 1) * $limit;
    $totalPages = ceil($total / $limit);
    
    // Get paginated data
    $dataSQL = "SELECT b.notag, i.nama, b.berat, b.adddate, b.addby
    , DATE(b.adddate2) as tgltag , b.notrans as noref, b.notagblowing,b.shiftid,b.kodemesin
                FROM tbltagp b join tblitem i on b.itemid=i.itemid
                $whereSQL 
                ORDER BY $sort $order 
                LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($dataSQL);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    
    // Return JSON response
    echo json_encode([
        'columns' => ['notag', 'nama', 'berat', 'adddate', 'addby', 'tgltag', 'noref', 'notagblowing', 'shiftid', 'kodemesin'],
        'rows' => $rows,
        'total' => (int)$total,
        'page' => $page,
        'limit' => $limit,
        'totalPages' => $totalPages,
        'totalWeight' => (float)$totalWeight
    ]);
    
        } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([        
        'error' => $e->getMessage()
    ]);   }
//}
