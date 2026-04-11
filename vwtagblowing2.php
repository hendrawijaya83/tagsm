<?php
if (!defined('SECURE_ACCESS')) {
  die('Direct access not permitted');
}
//include 'exportexcel.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>MySQL Data Explorer | Date Range + Filter + Sort + Pagination</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, 'Segoe UI', 'Inter', 'Helvetica Neue', sans-serif;
        }

        body {
            background: #f0f4f8;
            margin: 0;
            padding: 12px 10px;
            color: #0f172a;
        }

        .app-container {
            max-width: 1700px;
            margin: 0 auto;
            background: white;
            border-radius: 18px;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        .toolbar {
            background: white;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            align-items: center;
        }

        .title-area h1 {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
            background: linear-gradient(135deg, #1e293b, #2d4a6e);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .title-area p {
            margin: 4px 0 0;
            font-size: 0.85rem;
            color: #475569;
        }

        .controls {
            display: flex;
            flex-wrap: nowrap;
            gap: 12px;
            align-items: center;
        }

        .search-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 60px;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .search-box:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        }
        .search-box input {
            border: none;
            background: transparent;
            font-size: 0.9rem;
            width: 220px;
            outline: none;
        }
        .search-box span {
            color: #64748b;
        }

        .filter-panel {
            background: #fefce8;
            padding: 8px 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
            border-bottom: 3px solid #e2e8f0;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
        }
        .filter-group select, .filter-group input {
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 8px 12px;
            font-size: 0.85rem;
            min-width: 150px;
        }
        .date-range-group {
            background: #eff6ff;
            border-radius: 16px;
            padding: 8px 16px;
            border-left: 3px solid #3b82f6;
        }
        .date-range-group label {
            font-weight: 600;
            color: #1e40af;
        }
        .badge-clear {
            background: #e2e8f0;
            border: none;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }
        .badge-clear:hover {
            background: #cbd5e1;
        }
        .btn-primary {
            background: #3b82f6;
            border: none;
            color: white;
            padding: 5px 5px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-danger {
            background: #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
        }

        .stats-bar {
            padding: 12px 28px;
            background: #f9fafb;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        /* Total weight card styling */
        .total-weight-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 8px 10px;
            border-radius: 40px;
            color: white;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .total-weight-card span:first-child {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .total-weight-card .weight-value {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 1px;
        }
        .sort-indicator {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .sort-btn {
            background: transparent;
            border: 1px solid #cbd5e1;
            border-radius: 30px;
            padding: 4px 12px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: 0.1s;
        }
        .sort-btn.active {
            background: #e0f2fe;
            border-color: #0284c7;
            color: #0369a1;
        }

        .table-wrapper {
            overflow-x: auto;
            padding: 0;
            max-height: 550px;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        th {
            background: #f1f5f9;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            user-select: none;
            position: sticky;
            top: 0;
            background: #f1f5f9;
            z-index: 10;
        }
        th:hover {
            background: #e6edf5;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f2f5;
            color: #0f172a;
        }
        tr:hover td {
            background-color: #fef9e3;
        }
        .empty-row td {
            text-align: center;
            padding: 48px;
            color: #64748b;
            font-style: italic;
        }
        .date-highlight {
            font-family: monospace;
            font-size: 0.8rem;
        }

        .pagination-container {
            padding: 16px 28px;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .pagination-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .page-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
            min-width: 36px;
            text-align: center;
        }
        .page-btn:hover:not(:disabled) {
            background: #e2e8f0;
            border-color: #94a3b8;
        }
        .page-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .page-size-select {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: white;
            font-size: 0.8rem;
        }
        .page-info {
            font-size: 0.8rem;
            color: #475569;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .filter-badge {
            background: #dbeafe;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        @media (max-width: 780px) {
            .toolbar, .filter-panel { flex-direction: column; align-items: stretch; }
            .search-box input { width: 100%; }
            .filter-group select, .filter-group input { min-width: auto; }
            .pagination-container { flex-direction: column; align-items: stretch; }
            .stats-bar { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="app-container">
<!--     <div class="toolbar">
        <div class="title-area">
            <h1>📅 MySQL Data Explorer</h1>
            <p>Date range filtering + Column filter + Global search + Sort + Pagination</p>
        </div>
        <div class="controls">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" id="globalSearch" placeholder="Find in any column...">
            </div>
            <button id="refreshBtn" class="btn-primary">⟳ Refresh</button>
        </div>
    </div>
 -->
    <div class="filter-panel">
        <div class="filter-group">
            <label>🎯 Column filter</label>
            <select id="filterColumnSelect">
                <option value="">-- Select column --</option>
            </select>
        </div>
        <div class="filter-group">
            <label>🔍 Filter value</label>
            <input type="text" id="filterValueInput" placeholder="text filter...">
        </div>
        <div class="filter-group">
            <label>⚙️ Mode</label>
            <select id="filterModeSelect">
                <option value="contains">Contains</option>
                <option value="exact">Exact match</option>
                <option value="starts">Starts with</option>
            </select>
        </div>
        
        <!-- DATE RANGE FILTER SECTION -->
        <div class="date-range-group filter-group">
            <label>📅 Date Range Filter</label>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <input type="date" id="dateFrom" placeholder="From date" value="<?php echo date('Y-m-d', strtotime('-1 month')); ?>">
                <span>→</span>
                <input type="date" id="dateTo" placeholder="To date" value="<?php echo date('Y-m-d'); ?>">
                <button id="clearDateBtn" class="badge-clear" style="background:#fee2e2;">Clear dates</button>
            </div>
        </div>
        <!-- <button id="clearFiltersBtn" class="badge-clear">Reset All</button> -->
        <div class="controls">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" id="globalSearch" placeholder="Find in any column...">
            </div>
            <div id="activeFiltersContainer" style="display: flex; gap: 6px; 
            flex-wrap: wrap; flex-direction: column;">
                <button id="applyFilterBtn" class="btn-primary">Apply Filters</button>
                <button id="refreshBtn" class="btn-primary">⟳ Refresh</button>
            </div>
        </div>
    </div>

    <div class="stats-bar">
        <div>📊 Showing <span id="rowCount">0</span> records | Total filtered: <span id="filteredTotal">0</span> | Source: <span id="totalCount">0</span>
            <div class="total-weight-card">
            <span>⚖️ TOTAL WEIGHT (filtered):</span>
            <span class="weight-value" id="totalWeightDisplay">0.00</span>
            <span>kg</span>
            </div>
        </div>
        <div class="sort-indicator">
            <span>Sort by:</span>
            <button data-sort="none" class="sort-btn active">None</button>
            <button data-sort="b.notag" class="sort-btn">NOTAG</button>
            <button data-sort="i.nama" class="sort-btn">NAMA</button>
            <button data-sort="b.notrans" class="sort-btn">NOREF</button>
            <!-- <button data-sort="b.itemid" class="sort-btn">ITEMID</button> -->
            <button data-sort="b.addby" class="sort-btn">ADDBY</button>
            <button data-sort="b.adddate2" class="sort-btn">TGL</button>
            <button data-sort="b.shiftid" class="sort-btn">SHIFT</button>
            <button data-sort="b.kodemesin" class="sort-btn">MESIN</button>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="dataTable">
            <thead id="tableHeader">
                <tr><th>Loading ...</th></tr>
            </thead>
            <tbody id="tableBody">
                <tr class="empty-row"><td colspan="10"><div class="loading-spinner"></div> Fetching data...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        <div class="page-info">
            Page <span id="currentPageNum">1</span> of <span id="totalPages">1</span> | <span id="pageStart">0</span>-<span id="pageEnd">0</span>
        </div>
        <div class="pagination-controls">
            <button id="firstPageBtn" class="page-btn" disabled>⏮ First</button>
            <button id="prevPageBtn" class="page-btn" disabled>◀ Prev</button>
            <span id="pageNumbersContainer" style="display: flex; gap: 6px;"></span>
            <button id="nextPageBtn" class="page-btn" disabled>Next ▶</button>
            <button id="lastPageBtn" class="page-btn" disabled>Last ⏭</button>
            <select id="pageSizeSelect" class="page-size-select">
                <option value="5">5 per page</option>
                <option value="10" selected>10 per page</option>
                <option value="20">20 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>
<!--     <div style="padding: 12px 28px 20px; font-size: 0.7rem; color: #62748c; border-top: 1px solid #eef2f6; background:#fafcff">
        ⚡ API accepts: ?page=1&limit=10&sort=created_at&order=desc&date_from=2024-01-01&date_to=2024-12-31&filter_col=city&filter_val=NY&search=john
    </div>
 --></div>

<script type="text/javascript">
    $(document).ready(function() {
        // ============================================================
    // ACCOUNTING NUMBER FORMATTER
    // Formats numbers with thousand separators, 2 decimal places,
    // and parentheses for negative values (accounting style)
    // ============================================================
    function formatAccounting(value) {
        if (value === null || value === undefined || value === '') return '—';
        let num = parseFloat(value);
        if (isNaN(num)) return value;
        
        const options = {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: true
        };
        let formatted = new Intl.NumberFormat('en-US', options).format(Math.abs(num));
        if (num < 0) {
            return `(${formatted})`;
        }
        return formatted;
    }

    window.addEventListener('beforeunload', function() {
        //safeStorage.setItem('currentPage', currentPage);
        //console.log("Saving lastitem2:", strlastitem2);
        safeStorage.setItem('lastitem2', strlastitem2);
        safeStorage.setItem('pageSizelast', pageSize);
        //safeStorage.setItem('lastprinting', strlastprinting);
        });
  
    const safeStorage = {
    setItem: (key, value) => {
      try {
        localStorage.setItem(key, value);
      } catch (e) {
        // Fallback to sessionStorage or do nothing
        console.warn("LocalStorage not available, preferences won't persist");
      }
    },
    getItem: (key) => {
      try {
        return localStorage.getItem(key);
      } catch (e) {
        return null;
      }
    }
  };
    let strlastitem2 = safeStorage.getItem('lastitem2') || "";
    let pageSize = safeStorage.getItem('pageSizelast') || 10;

    // ============================================================
    // CONFIGURATION - Replace with your actual backend endpoint
    // The API must accept query parameters and return JSON with date range support
    // Expected response: { columns, rows, total, page, limit, totalPages }
    // ============================================================
    const API_URL = 'query/dtvwtagblowing.php';   // <-- CHANGE TO YOUR API ENDPOINT
    const USE_MOCK_BACKEND = false;  // Set false when connecting to real backend
    
    // State
    let currentPage = 1;
    //let pageSize = 10;
    let currentSortColumn = 'none';
    let currentSortOrder = 'asc';
    let currentFilterCol = '';
    let currentFilterVal = '';
    let currentFilterMode = 'contains';
    let currentGlobalSearch = strlastitem2;
    let currentDateFrom = '';
    let currentDateTo = '';
    
    let totalRecords = 0;
    let totalPages = 1;
    let columnsList = [];
    // DOM elements
    const globalSearchInput = document.getElementById('globalSearch');
    const refreshBtn = document.getElementById('refreshBtn');
    const filterColumnSelect = document.getElementById('filterColumnSelect');
    const filterValueInput = document.getElementById('filterValueInput');
    const filterModeSelect = document.getElementById('filterModeSelect');
    const applyFilterBtn = document.getElementById('applyFilterBtn');
    //const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const clearDateBtn = document.getElementById('clearDateBtn');
    const tableHeader = document.getElementById('tableHeader');
    const tableBody = document.getElementById('tableBody');
    const rowCountSpan = document.getElementById('rowCount');
    const filteredTotalSpan = document.getElementById('filteredTotal');
    const totalCountSpan = document.getElementById('totalCount');
    const currentPageNumSpan = document.getElementById('currentPageNum');
    const totalPagesSpan = document.getElementById('totalPages');
    const pageStartSpan = document.getElementById('pageStart');
    const pageEndSpan = document.getElementById('pageEnd');
    const sortButtons = document.querySelectorAll('[data-sort]');
    const firstPageBtn = document.getElementById('firstPageBtn');
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    const lastPageBtn = document.getElementById('lastPageBtn');
    const pageSizeSelect = document.getElementById('pageSizeSelect');
    const pageNumbersContainer = document.getElementById('pageNumbersContainer');
    const totalWeightDisplaySpan = document.getElementById('totalWeightDisplay');

    function setTableLoading(isLoading) {
        if (isLoading) {
            tableBody.innerHTML = `<tr class="empty-row">
            <td colspan="10"><div class="loading-spinner">
            </div> Loading from server...</td></tr>`;
        }
    }
    // Calculate total weight from filtered dataset (server-side simulation)
    function calculateTotalWeight(rows) {
        // weight is at index 2 (3th column)
        let sum = 0;
        for (const row of rows) {
            const weight = parseFloat(row[2]);
            if (!isNaN(weight)) {
                sum += weight;
            }
        }
        return sum;
    }
    // Build API URL with all parameters including date range
    function buildApiUrl() {
        const params = new URLSearchParams();
        params.append('page', currentPage);
        params.append('limit', pageSize);
        
        if (currentSortColumn !== 'none') {
            params.append('sort', currentSortColumn);
            params.append('order', currentSortOrder);
        }
        
        if (currentFilterCol && currentFilterVal.trim() !== '') {
            params.append('filter_col', currentFilterCol);
            params.append('filter_val', currentFilterVal.trim());
            params.append('filter_mode', currentFilterMode);
        }
        
        if (currentGlobalSearch.trim() !== '') {
            params.append('search', currentGlobalSearch.trim());
        }
        
        if (currentDateFrom) {
            params.append('date_from', currentDateFrom);
        }
        if (currentDateTo) {
            params.append('date_to', currentDateTo);
        }
        
        return `${API_URL}?${params.toString()}`;
    }
    
    
    async function fetchDataFromApi() {
        setTableLoading(true);
        try {
                
            let responseData;
                const url = buildApiUrl();
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                responseData = await response.json();
                if (!responseData.columns || !responseData.rows || typeof responseData.total === 'undefined') {
                    throw new Error('Invalid API response');
                }
            
            columnsList = responseData.columns;
            totalRecords = responseData.total;
            totalWeight = responseData.totalWeight || 0;
            totalPages = responseData.totalPages || Math.ceil(responseData.total / pageSize);
            if (responseData.page) currentPage = responseData.page;
            
            renderTableHeader();
            renderTableBody(responseData.rows);
            updateStatsAndPagination();
            updateSortButtonsUI();
            populateColumnFilterSelect();
            
            // update filtered total display
            filteredTotalSpan.innerText = totalRecords;
            
            totalCountSpan.innerText = responseData.rows ? 'source: '
             + (USE_MOCK_BACKEND ? 0 : 'N/A') : 'N/A';
            if (!USE_MOCK_BACKEND) totalCountSpan.innerText = formatAccounting(totalWeight) + ' kg';
            else totalCountSpan.innerText = 0;

            const totalWeightfilter = calculateTotalWeight(responseData.rows);
            // Update total weight display
            totalWeightDisplaySpan.innerText = formatAccounting(totalWeightfilter); //.toFixed(2);
        } catch (err) {
            console.error(err);
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="10">
            ⚠️ Error: ${err.message}</td></tr>`;
            totalWeightDisplaySpan.innerText = '0.00';
        } finally {
            setTableLoading(false);
            let strlastitem2 = safeStorage.getItem('lastitem2') || "";
        }
    }
    
    function renderTableHeader() {
        if (!columnsList.length) return;
        
        const headerHtml = `<tr>${columnsList.map(col => `<th data-col="${col}">${col} ${currentSortColumn === col ? 
        (currentSortOrder === 'asc' ? '▲' : '▼') : ''}</th>`).join('')}</tr>`;
        tableHeader.innerHTML = headerHtml;
        document.querySelectorAll('#tableHeader th').forEach(th => {
            th.addEventListener('click', () => {
                const colName = th.getAttribute('data-col');
                
                if (colName) {
                    if (currentSortColumn === colName) {
                        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSortColumn = colName;
                        currentSortOrder = 'asc';
                    }
                    currentPage = 1;
                    fetchDataFromApi();
                }
            });
        });
    }
    
    function renderTableBody(rows) {
        if (!rows.length) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="${columnsList.length || 1}">✨ No records match your filters. Try adjusting date range or search.</td></tr>`;
            rowCountSpan.innerText = '0';
            return;
        }
        const rowsHtml = rows.map(row => `<tr>${row.map(cell => {
            let display = (cell !== undefined && cell !== null) ? cell : '';
            if (typeof display === 'string' && display.match(/^\d{4}-\d{2}-\d{2}/) && columnsList[row.indexOf(cell)] === 'tgltag') {
                return `<td class="date-highlight">📅 ${display}</td>`;
            }
            return `<td>${display}</td>`;
        }).join('')}</tr>`).join('');
        tableBody.innerHTML = rowsHtml;
        rowCountSpan.innerText = rows.length;
    }
    
    function updateStatsAndPagination() {
        currentPageNumSpan.innerText = currentPage;
        totalPagesSpan.innerText = totalPages;
        const start = totalRecords === 0 ? 0 : (currentPage - 1) * pageSize + 1;
        const end = Math.min(currentPage * pageSize, totalRecords);
        pageStartSpan.innerText = start;
        pageEndSpan.innerText = end;
        
        firstPageBtn.disabled = currentPage === 1 || totalRecords === 0;
        prevPageBtn.disabled = currentPage === 1 || totalRecords === 0;
        nextPageBtn.disabled = currentPage === totalPages || totalRecords === 0;
        lastPageBtn.disabled = currentPage === totalPages || totalRecords === 0;
        
        renderPageNumbers();
    }
    
    function renderPageNumbers() {
        pageNumbersContainer.innerHTML = '';
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        if (endPage - startPage < 4) {
            if (startPage === 1) endPage = Math.min(totalPages, startPage + 4);
            if (endPage === totalPages) startPage = Math.max(1, endPage - 4);
        }
        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            btn.addEventListener('click', () => { currentPage = i; fetchDataFromApi(); });
            pageNumbersContainer.appendChild(btn);
        }
    }
    
    function updateSortButtonsUI() {
        sortButtons.forEach(btn => {
            btn.classList.remove('active');
            const sortVal = btn.getAttribute('data-sort');
            if (sortVal === currentSortColumn) btn.classList.add('active');
            else if (sortVal === 'none' && currentSortColumn === 'none') btn.classList.add('active');
        });
    }
    
    function populateColumnFilterSelect() {
        const currentVal = filterColumnSelect.value;
        filterColumnSelect.innerHTML = '<option value="">-- Select column --</option>';
        columnsList.forEach(col => {
            if (col !== 'tgltag') { // optional: exclude date column from text filter if needed, but include anyway
                const option = document.createElement('option');
                option.value = col;
                option.textContent = col;
                filterColumnSelect.appendChild(option);
            } else {
                const option = document.createElement('option');
                option.value = col;
                option.textContent = `${col} (date)`;
                filterColumnSelect.appendChild(option);
            }
        });
        if (currentVal && columnsList.includes(currentVal)) filterColumnSelect.value = currentVal;
    }
    
    // Event handlers
    function onGlobalSearch() {
        currentGlobalSearch = globalSearchInput.value;
        currentPage = 1;
        strlastitem2=currentGlobalSearch;
        fetchDataFromApi();
    }
    
    function onApplyFilter() {
        currentFilterCol = filterColumnSelect.value;
        currentFilterVal = filterValueInput.value;
        currentFilterMode = filterModeSelect.value;
        currentDateFrom = dateFromInput.value;
        currentDateTo = dateToInput.value;
        currentPage = 1;
        fetchDataFromApi();
    }
    
    function onClearFilters() {
        currentFilterCol = '';
        currentFilterVal = '';
        currentFilterMode = 'contains';
        currentGlobalSearch = '';
        currentSortColumn = 'none';
        currentSortOrder = 'asc';
        currentDateFrom = '';
        currentDateTo = '';
        currentPage = 1;
        globalSearchInput.value = '';
        filterValueInput.value = '';
        filterColumnSelect.value = '';
        filterModeSelect.value = 'contains';
        dateFromInput.value = '';
        dateToInput.value = '';
        fetchDataFromApi();
    }
    
    function onClearDateRange() {
        //var today = new Date();
        dateFromInput.value =  '';
        dateToInput.value =  '';
        currentDateFrom = '';
        currentDateTo = '';
        currentPage = 1;
        fetchDataFromApi();
    }
    
    function onSortButtonClick(e) {
        const sortKey = e.currentTarget.getAttribute('data-sort');
        if (sortKey === 'none') {
            currentSortColumn = 'none';
            currentSortOrder = 'asc';
        } else {
            if (currentSortColumn === sortKey) {
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = sortKey;
                currentSortOrder = 'asc';
            }
        }
        currentPage = 1;
        fetchDataFromApi();
    }
    
    function bindEvents() {
        globalSearchInput.value = strlastitem2;
        currentDateFrom = dateFromInput.value;
        currentDateTo = dateToInput.value;
        pageSizeSelect.value=pageSize;
        globalSearchInput.addEventListener('input', onGlobalSearch);        
        refreshBtn.addEventListener('click', () => fetchDataFromApi());        
        applyFilterBtn.addEventListener('click', onApplyFilter);
        //clearFiltersBtn.addEventListener('click', onClearFilters);
        clearDateBtn.addEventListener('click', onClearDateRange);
        sortButtons.forEach(btn => btn.addEventListener('click', onSortButtonClick));
        //console.log("a");
        firstPageBtn.addEventListener('click', () => { currentPage = 1; fetchDataFromApi(); });
        prevPageBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; fetchDataFromApi(); } });
        nextPageBtn.addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; fetchDataFromApi(); } });
        lastPageBtn.addEventListener('click', () => { if (totalPages > 0) { currentPage = totalPages; fetchDataFromApi(); } });
        pageSizeSelect.addEventListener('change', (e) => { pageSize = parseInt(e.target.value); currentPage = 1; fetchDataFromApi(); });
    }
    
    async function init() {
        bindEvents();        
        await fetchDataFromApi();
    }
    
    init();
    });
</script>
</body>
</html>