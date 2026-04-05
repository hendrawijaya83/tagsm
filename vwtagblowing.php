<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>MySQL Data Explorer | Date Range Filter + Pagination</title>
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
            border-radius: 14px;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        .toolbar {
            background: white;
            padding: 20px 28px;
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
            flex-wrap: wrap;
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
            background: #ffffffd9;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 6px 12px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .date-range-group input {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 6px 10px;
            font-size: 0.8rem;
            min-width: 130px;
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
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 30px;
            cursor: pointer;
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
        }
        .sort-btn.active {
            background: #e0f2fe;
            border-color: #0284c7;
            color: #0369a1;
        }

        .table-wrapper {
            overflow-x: auto;
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
            cursor: pointer;
            user-select: none;
        }
        th:hover {
            background: #e6edf5;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f2f5;
        }
        tr:hover td {
            background-color: #fef9e3;
        }
        .empty-row td {
            text-align: center;
            padding: 48px;
            color: #64748b;
        }

        .pagination-container {
            padding: 8px 14px;
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
            min-width: 36px;
        }
        .page-btn:hover:not(:disabled) {
            background: #e2e8f0;
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
        }
        .date-badge {
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
            .date-range-group { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
<div class="app-container">
<!--     <div class="toolbar">
        <div class="title-area">
            <h1>📅 MySQL Data Explorer</h1>
            <p>Date range filter + Pagination + Sort + Global Search</p>
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
        <button id="applyFilterBtn" class="btn-primary">Apply</button>
        
        <!-- DATE RANGE FILTER SECTION -->
        <div class="filter-group">
            <label>📅 Date Range Filter</label>
            <div class="date-range-group">
                <input type="date" id="dateFrom" placeholder="From date">
                <span>→</span>
                <input type="date" id="dateTo" placeholder="To date">
                <select id="dateColumnSelect" style="min-width: 110px;">
                    <option value="">Select date column</option>
                </select>
            </div>
        </div>
        <button id="clearFiltersBtn" class="badge-clear">Clear All Filters</button>
        <div class="controls">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" id="globalSearch" placeholder="Find in any column...">
            </div>
            <button id="refreshBtn" class="btn-primary">⟳ Refresh</button>
        </div>
   </div>

    <div class="stats-bar">
        <div>📋 <span id="rowCount">0</span> shown (filtered: <span id="filteredTotal">0</span> | total: <span id="totalCount">0</span>)</div>
        <div class="sort-indicator">
            <span>Sort:</span>
            <button data-sort="none" class="sort-btn active">None</button>
            <button data-sort="notag" class="sort-btn">NOTAG</button>
            <button data-sort="nama" class="sort-btn">NAMA</button>
            <button data-sort="noref" class="sort-btn">NOREF</button>
            <button data-sort="itemid" class="sort-btn">ITEMID</button>
            <button data-sort="addby" class="sort-btn">ADDBY</button>
            <button data-sort="tgltag" class="sort-btn">TGL</button>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="dataTable">
            <thead id="tableHeader"><tr><th>Loading...</th></tr></thead>
            <tbody id="tableBody"><tr class="empty-row"><td colspan="10">Fetching data...</td></tr></tbody>
        </table>
    </div>

    <div class="pagination-container">
        <div class="page-info">Showing <span id="pageStart">0</span> - <span id="pageEnd">0</span> of <span id="filteredTotalPages">0</span></div>
        <div class="pagination-controls">
            <button id="firstPageBtn" class="page-btn" disabled>⏮ First</button>
            <button id="prevPageBtn" class="page-btn" disabled>◀ Prev</button>
            <span id="pageNumbersContainer"></span>
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
</div>

<script>
    // -------------------- CONFIG --------------------
    const API_URL = 'query/dtvwtagblowing.php';   // Replace with your backend
    const USE_MOCK_FALLBACK = false;

    // Global state
    let originalDataset = { columns: [], rows: [] };
    let filteredRows = [];
    let currentSort = { column: null, direction: 'asc' };
    let currentColumnFilter = { colName: '', value: '', mode: 'contains' };
    let globalSearchTerm = '';
    let dateRangeFilter = { column: '', fromDate: '', toDate: '' };   // NEW date range state
    let currentPage = 1;
    let pageSize = 10;

    // DOM elements
    const globalSearchInput = document.getElementById('globalSearch');
    const refreshBtn = document.getElementById('refreshBtn');
    const filterColumnSelect = document.getElementById('filterColumnSelect');
    const filterValueInput = document.getElementById('filterValueInput');
    const filterModeSelect = document.getElementById('filterModeSelect');
    const applyFilterBtn = document.getElementById('applyFilterBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const dateColumnSelect = document.getElementById('dateColumnSelect');
    const tableHeader = document.getElementById('tableHeader');
    const tableBody = document.getElementById('tableBody');
    const rowCountSpan = document.getElementById('rowCount');
    const filteredTotalSpan = document.getElementById('filteredTotal');
    const totalCountSpan = document.getElementById('totalCount');
    const sortButtons = document.querySelectorAll('[data-sort]');
    const firstPageBtn = document.getElementById('firstPageBtn');
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    const lastPageBtn = document.getElementById('lastPageBtn');
    const pageSizeSelect = document.getElementById('pageSizeSelect');
    const pageNumbersContainer = document.getElementById('pageNumbersContainer');
    const pageStartSpan = document.getElementById('pageStart');
    const pageEndSpan = document.getElementById('pageEnd');
    const filteredTotalPagesSpan = document.getElementById('filteredTotalPages');

    // Helper: loading state
    function setLoading(isLoading, errorMsg = null) {
        if (isLoading) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="10">⏳ Loading from MySQL...</td></tr>`;
        } else if (errorMsg) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="10">⚠️ ${errorMsg}</td></tr>`;
        }
    }

    // Mock dataset with DATE column (hire_date)
    function getMockDataset() {
        const departments = ['Engineering', 'Sales', 'Marketing', 'Support', 'Finance'];
        const cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
        const firstNames = ['Alice', 'Marcus', 'Sophia', 'James', 'Emma', 'Liam', 'Olivia', 'Noah', 'Ava', 'William'];
        const lastNames = ['Chen', 'Johnson', 'Rodriguez', 'Kim', 'Watson', 'Smith', 'Garcia', 'Martinez', 'Brown', 'Lee'];
        
        const rows = [];
        const startDate = new Date(2018, 0, 1);
        const endDate = new Date(2025, 11, 31);
        
        for (let i = 1; i <= 52; i++) {
            const firstName = firstNames[(i-1) % firstNames.length];
            const lastName = lastNames[(i*2) % lastNames.length];
            const name = `${firstName} ${lastName}`;
            const email = `${firstName.toLowerCase()}.${lastName.toLowerCase()}${i}@example.com`;
            const age = 22 + (i % 40);
            const city = cities[i % cities.length];
            const dept = departments[i % departments.length];
            // random date between 2018 and 2025
            const randomDate = new Date(startDate.getTime() + Math.random() * (endDate.getTime() - startDate.getTime()));
            const hireDate = randomDate.toISOString().split('T')[0];
            rows.push([i, name, email, age, city, dept, hireDate]);
        }
        return {
            columns: ['id', 'name', 'email', 'age', 'city', 'department', 'hire_date'],
            rows: rows
        };
    }

    async function fetchDatasetFromMySQL() {
        try {
            setLoading(true);
            const response = await fetch(API_URL, { method: 'GET', headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            if (!data.columns || !Array.isArray(data.rows)) throw new Error('Invalid API response');
            return { columns: data.columns, rows: data.rows };
        } catch (err) {
            console.warn(err);
            if (USE_MOCK_FALLBACK) return getMockDataset();
            throw new Error(`Connection error: ${err.message}`);
        } finally {
            setLoading(false);
        }
    }

    async function loadData() {
        try {
            const data = await fetchDatasetFromMySQL();
            originalDataset = { columns: data.columns, rows: [...data.rows] };
            resetAllFilters();
            rebuildSelectors();
            applyAllFiltersAndRender();
        } catch (err) {
            setLoading(false, err.message);
        }
    }

    function resetAllFilters() {
        currentColumnFilter = { colName: '', value: '', mode: 'contains' };
        globalSearchTerm = '';
        dateRangeFilter = { column: '', fromDate: '', toDate: '' };
        currentSort = { column: null, direction: 'asc' };
        currentPage = 1;
        globalSearchInput.value = '';
        filterValueInput.value = '';
        filterModeSelect.value = 'contains';
        filterColumnSelect.value = '';
        dateFromInput.value = '';
        dateToInput.value = '';
        if (dateColumnSelect) dateColumnSelect.value = '';
        sortButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelector('[data-sort="none"]')?.classList.add('active');
    }

    function rebuildSelectors() {
        filterColumnSelect.innerHTML = '<option value="">-- Column --</option>';
        dateColumnSelect.innerHTML = '<option value="">-- Date column --</option>';
        originalDataset.columns.forEach(col => {
            filterColumnSelect.appendChild(new Option(col, col));
            // Auto-detect date columns (by name or heuristic)
            const lowerCol = col.toLowerCase();
            if (lowerCol.includes('date') || lowerCol.includes('hired') || lowerCol === 'created_at' || lowerCol === 'updated_at') {
                dateColumnSelect.appendChild(new Option(col, col));
            }
        });
        // if no date column found, add a manual option but we'll still allow any column
        if (dateColumnSelect.options.length === 1 && originalDataset.columns.length) {
            dateColumnSelect.appendChild(new Option(originalDataset.columns[0], originalDataset.columns[0]));
        }
    }

    // Parse date safely
    function parseDateValue(value) {
        if (!value) return null;
        const d = new Date(value);
        return isNaN(d.getTime()) ? null : d;
    }

    // Apply all filters: text column filter, global search, DATE RANGE
    function applyFiltersAndSort() {
        let result = [...originalDataset.rows];
        const columns = originalDataset.columns;
        
        // 1) Column text filter
        if (currentColumnFilter.colName && currentColumnFilter.value.trim() !== '') {
            const colIndex = columns.indexOf(currentColumnFilter.colName);
            if (colIndex !== -1) {
                const filterVal = currentColumnFilter.value.trim().toLowerCase();
                const mode = currentColumnFilter.mode;
                result = result.filter(row => {
                    const cell = row[colIndex] != null ? String(row[colIndex]).toLowerCase() : '';
                    if (mode === 'contains') return cell.includes(filterVal);
                    if (mode === 'exact') return cell === filterVal;
                    if (mode === 'starts') return cell.startsWith(filterVal);
                    return true;
                });
            }
        }
        
        // 2) Global search
        if (globalSearchTerm.trim() !== '') {
            const term = globalSearchTerm.trim().toLowerCase();
            result = result.filter(row => row.some(cell => cell != null && String(cell).toLowerCase().includes(term)));
        }
        
        // 3) DATE RANGE FILTER (critical addition)
        if (dateRangeFilter.column && (dateRangeFilter.fromDate || dateRangeFilter.toDate)) {
            const dateColIndex = columns.indexOf(dateRangeFilter.column);
            if (dateColIndex !== -1) {
                const fromDateObj = dateRangeFilter.fromDate ? parseDateValue(dateRangeFilter.fromDate) : null;
                const toDateObj = dateRangeFilter.toDate ? parseDateValue(dateRangeFilter.toDate) : null;
                result = result.filter(row => {
                    const cellValue = row[dateColIndex];
                    if (cellValue === null || cellValue === undefined) return false;
                    let cellDate = parseDateValue(cellValue);
                    if (!cellDate) return false;
                    if (fromDateObj && cellDate < fromDateObj) return false;
                    if (toDateObj) {
                        // set to end of day for inclusive range
                        const endOfDay = new Date(toDateObj);
                        endOfDay.setHours(23, 59, 59, 999);
                        if (cellDate > endOfDay) return false;
                    }
                    return true;
                });
            }
        }
        
        // 4) Sorting
        if (currentSort.column !== null) {
            const colIdx = currentSort.column;
            const direction = currentSort.direction;
            result.sort((a, b) => {
                let valA = a[colIdx] !== undefined ? a[colIdx] : '';
                let valB = b[colIdx] !== undefined ? b[colIdx] : '';
                // try numeric or date comparison
                const isNumA = !isNaN(parseFloat(valA)) && isFinite(valA);
                const isNumB = !isNaN(parseFloat(valB)) && isFinite(valB);
                if (isNumA && isNumB) {
                    valA = parseFloat(valA);
                    valB = parseFloat(valB);
                } else {
                    // date aware: if both are parseable dates
                    const dateA = parseDateValue(valA);
                    const dateB = parseDateValue(valB);
                    if (dateA && dateB) {
                        valA = dateA.getTime();
                        valB = dateB.getTime();
                    } else {
                        valA = String(valA).toLowerCase();
                        valB = String(valB).toLowerCase();
                    }
                }
                if (valA < valB) return direction === 'asc' ? -1 : 1;
                if (valA > valB) return direction === 'asc' ? 1 : -1;
                return 0;
            });
        }
        filteredRows = result;
        return filteredRows;
    }

    // Pagination
    function getPaginatedRows() {
        const start = (currentPage - 1) * pageSize;
        return filteredRows.slice(start, start + pageSize);
    }

    function renderCurrentPage() {
        if (!originalDataset.columns.length) return;
        const paginated = getPaginatedRows();
        const cols = originalDataset.columns;
        
        rowCountSpan.innerText = paginated.length;
        filteredTotalSpan.innerText = filteredRows.length;
        totalCountSpan.innerText = originalDataset.rows.length;
        
        // Header with sort icons
        const theadHtml = `<tr>${cols.map((col, idx) => `<th data-col-index="${idx}" data-col-name="${col}">${col} ${currentSort.column === idx ? (currentSort.direction === 'asc' ? '▲' : '▼') : ''}</th>`).join('')}</tr>`;
        tableHeader.innerHTML = theadHtml;
        
        if (paginated.length === 0) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="${cols.length}">✨ No records match filters</td></tr>`;
        } else {
            tableBody.innerHTML = paginated.map(row => `<tr>${row.map(cell => `<td>${cell !== undefined && cell !== null ? cell : ''}</td>`).join('')}</tr>`).join('');
        }
        
        // attach sort listeners
        document.querySelectorAll('#tableHeader th').forEach(th => {
            th.addEventListener('click', () => {
                const colName = th.getAttribute('data-col-name');
                const colIndex = parseInt(th.getAttribute('data-col-index'));
                if (isNaN(colIndex)) return;
                if (currentSort.column === colIndex) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = colIndex;
                    currentSort.direction = 'asc';
                }
                updateSortButtonUI(colName);
                applyAllFiltersAndRender();
            });
        });
        updatePaginationUI();
    }
    
    function updateSortButtonUI(activeCol) {
        sortButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-sort') === activeCol) btn.classList.add('active');
        });
        if (currentSort.column === null) document.querySelector('[data-sort="none"]')?.classList.add('active');
    }
    
    function updatePaginationUI() {
        const total = filteredRows.length;
        const totalPages = Math.ceil(total / pageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;
        const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
        const end = Math.min(currentPage * pageSize, total);
        pageStartSpan.innerText = start;
        pageEndSpan.innerText = end;
        filteredTotalPagesSpan.innerText = total;
        
        firstPageBtn.disabled = currentPage === 1 || total === 0;
        prevPageBtn.disabled = currentPage === 1 || total === 0;
        nextPageBtn.disabled = currentPage === totalPages || total === 0;
        lastPageBtn.disabled = currentPage === totalPages || total === 0;
        
        renderPageNumbers(totalPages);
    }
    
    function renderPageNumbers(totalPages) {
        pageNumbersContainer.innerHTML = '';
        let start = Math.max(1, currentPage - 2);
        let end = Math.min(totalPages, currentPage + 2);
        for (let i = start; i <= end; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            btn.addEventListener('click', () => { currentPage = i; renderCurrentPage(); });
            pageNumbersContainer.appendChild(btn);
        }
    }
    
    function applyAllFiltersAndRender() {
        applyFiltersAndSort();
        currentPage = 1;
        renderCurrentPage();
    }
    
    // Event Handlers
    function onGlobalSearch() {
        globalSearchTerm = globalSearchInput.value;
        applyAllFiltersAndRender();
    }
    
    function onApplyColumnFilter() {
        const col = filterColumnSelect.value;
        const val = filterValueInput.value;
        const mode = filterModeSelect.value;
        if (!col) { alert('Select a column'); return; }
        currentColumnFilter = { colName: col, value: val, mode: mode };
        applyAllFiltersAndRender();
    }
    
    function onApplyDateRange() {
        const col = dateColumnSelect.value;
        const fromDate = dateFromInput.value;
        const toDate = dateToInput.value;
        if (!col) {
            alert('Please select a date column for range filtering');
            return;
        }
        dateRangeFilter = { column: col, fromDate: fromDate, toDate: toDate };
        applyAllFiltersAndRender();
    }
    
    function onClearFilters() {
        resetAllFilters();
        applyAllFiltersAndRender();
    }
    
    function onSortButtonClick(e) {
        const key = e.currentTarget.getAttribute('data-sort');
        if (key === 'none') {
            currentSort = { column: null, direction: 'asc' };
            sortButtons.forEach(btn => btn.classList.remove('active'));
            e.currentTarget.classList.add('active');
            applyAllFiltersAndRender();
            return;
        }
        const colIndex = originalDataset.columns.indexOf(key);
        if (colIndex === -1) return;
        if (currentSort.column === colIndex) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = colIndex;
            currentSort.direction = 'asc';
        }
        sortButtons.forEach(btn => btn.classList.remove('active'));
        e.currentTarget.classList.add('active');
        applyAllFiltersAndRender();
    }
    
    function bindEvents() {
        globalSearchInput.addEventListener('input', onGlobalSearch);
        refreshBtn.addEventListener('click', loadData);
        applyFilterBtn.addEventListener('click', onApplyColumnFilter);
        clearFiltersBtn.addEventListener('click', onClearFilters);
        // date range apply listeners
        dateFromInput.addEventListener('change', onApplyDateRange);
        dateToInput.addEventListener('change', onApplyDateRange);
        dateColumnSelect.addEventListener('change', onApplyDateRange);
        
        sortButtons.forEach(btn => btn.addEventListener('click', onSortButtonClick));
        
        firstPageBtn.addEventListener('click', () => { currentPage = 1; renderCurrentPage(); });
        prevPageBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderCurrentPage(); } });
        nextPageBtn.addEventListener('click', () => { const max = Math.ceil(filteredRows.length / pageSize); if (currentPage < max) { currentPage++; renderCurrentPage(); } });
        lastPageBtn.addEventListener('click', () => { const max = Math.ceil(filteredRows.length / pageSize); if (max) currentPage = max; renderCurrentPage(); });
        pageSizeSelect.addEventListener('change', (e) => { pageSize = parseInt(e.target.value); currentPage = 1; renderCurrentPage(); });
    }
    
    async function init() {
        bindEvents();
        await loadData();
    }
    init();
</script>
</body>
</html>