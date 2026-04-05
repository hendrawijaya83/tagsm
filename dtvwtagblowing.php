<?php
// pengecekan ajax request untuk mencegah direct access file, agar file tidak bisa diakses secara langsung dari browser
// jika ada ajax request
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && (isset($_POST['startDate']) && isset($_POST['endDate']))) {
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../config/configpdo2.php";
    //$table = 'vwtblpoprod2';
try {
         //$fk_prodid=$_GET['fk_prodid'];
         //$printing=$_GET['printing'];

        //if ($printing=="PRINTING")
/*             {$strquery="SELECT a.notag, i.nama, a.berat, a.adddate,
        a.addby, i.itemid, a.adddate2 as tgltag, a.notrans as noref, '' as status ,'1' as printing
         from tbltagp a left join (select itemid,nama from vwtblitemnama) i on a.itemid=i.itemid
         order by a.adddate desc";}
 */         //ELSE            
                $strquery="SELECT a.notag, i.nama , a.berat, a.adddate,
        a.addby, i.itemid, a.adddate2 as tgltag, a.notrans as noref 
        from tbltag a join tblitem i on a.itemid=i.itemid
        order by a.adddate desc";

        $stmt = $pdo->prepare($strquery);
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
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);   }
//}
