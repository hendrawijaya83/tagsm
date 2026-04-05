<?php
if (!defined('SECURE_ACCESS')) {
  die('Direct access not permitted');
}
//include 'exportexcel.php';
?>

<head>
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      margin: 10px;
    }

    .search-container {
      margin-bottom: 5px;
      background: #f5f5f5;
      padding: 2px;
      border-radius: 5px;
      position: sticky;
    }

    .search-input {
      margin: 0 5px 5px 0;
      padding: 3px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 4px;
      /* text-align: left; */
    }

    th {
      background-color: #f2f2f2;
      position: sticky;
      top: 0;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .no-results {
      padding: 10px;
      text-align: center;
      color: #666;
      font-style: italic;
    }

    .highlight {
      background-color: yellow;
      font-weight: bold;
    }
    .highlight-delete {
      background-color: #ffcccc !important; 
      color: black;
    }

    .date-search-header {
      display: flex;
      flex-direction: column;
    }

    .date-search-label {
      font-size: 0.8em;
      color: #666;
      margin-bottom: 3px;
    }
    #loading {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      color: white;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .spinner {
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .pagination {
      display: flex;
      justify-content: center;
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    .pagination li {
      margin: 0 5px;
    }
    .pagination a {
      display: inline-block;
      padding: 8px 16px;
      text-decoration: none;
      color: #333;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .pagination a.active {
      background-color: #4CAF50;
      color: white;
      border: 1px solid #4CAF50;
    }
    .pagination a:hover:not(.active) {
      background-color: #ddd;
    }
    .page-info {
      text-align: center;
      margin: 10px 0;
      color: #666;
    }
    .page-size-selector {
      text-align: left;
      margin: 10px 0;
    }
  #goToTopBtn {
      display: none; /* Hidden by default */
      position: fixed; /* Fixed/sticky position */
      bottom: 20px; /* Place the button at the bottom of the page */
      right: 20px; /* Place the button 20px from the right */
      z-index: 99; /* Make sure it appears on top */
      border: none; /* Remove borders */
      outline: none; /* Remove outline */
      cursor: pointer; /* Add a mouse pointer on hover */
      padding: 12px; /* Some padding */
      border-radius: 50%; /* Rounded corners */
      width: 50px;
      height: 50px;
    }
    .upload-container {
            border: 2px dashed #3498db;
            border-radius: 10px;
            padding: 40px 20px;
            margin-bottom: 30px;
            transition: all 0.3s;
            background: #f8f9fa;
    }
    .upload-container:hover {
            background: #e8f4ff;
            border-color: #2980b9;
        }
      .upload-icon {
            font-size: 60px;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .upload-text {
            margin-bottom: 20px;
            color: #34495e;
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .file-label:hover {
            background: #2980b9;
        }
        
        .selected-file {
            margin-top: 15px;
            font-weight: 500;
            color: #2c3e50;
        }
        .import-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 14px 28px;
            font-size: 1.1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 10px;
        }
        
        .import-btn:hover {
            background: #219653;
        }
        
        .import-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .progress-container {
            margin: 30px 0;
            display: none;
        }
        
        .progress-bar {
            height: 20px;
            background: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(90deg, #2ecc71, #1abc9c);
            width: 0%;
            transition: width 0.5s;
        }
        
        .progress-text {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .result-container {
            display: none;
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .success {
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        
        .error {
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
  </style>
</head>

<table id="data-table">
  <thead>
    <div class="search-container">
      <!-- <h6>Search Filters:</h6> -->
      <div id="search-fields">
        <!-- Search inputs will be added here dynamically -->
        <button id="reset-search">Reset All Filters</button>
          <!-- <button class="btn" onclick="loadExport(1)">PDF</button>
          <button class="btn" onclick="loadExport(2)">Excel</button> -->
          <button id="btn-detail">Import Excel</button>
          <!-- <button id="btn-hapusall" style="background-color: red;">Hapus Import</button> -->
      </div>

    </div>    
    <tr>
      <th class="text-center">No</th>
      <th class="text-center">No. Tag</th>
      <th class="text-center">Item</th>
      <th class="text-center">Item ID</th>
      <th class="text-center">Berat</th>
      <th class="text-center">Tgl. Tag</th>
      <th class="text-center">Add Date</th>
      <th class="text-center">AddBy</th>
      <th class="text-center" >noref</th>
      <th class="text-center" style="display:none;">noref</th>
      <th class="text-center">Aksi</th>
      <th class="text-center">Data</th>
      <!-- <th class="text-center">Keterangan</th> -->
      <!--
      <th class="text-center">AddDate</th>
      <th class="text-center">ItemID</th>
      <th class="text-center">ID</th> -->
    </tr>
  </thead>
  <tbody id="userTableBody">
    <!-- Table data will be populated dynamically -->
  </tbody>
</table>

  <div class="page-size-selector">
    <label for="page-size">Items per page:</label>
    <select id="page-size" onchange="changePageSize()">
      <option value="5">5</option>
      <option value="10" selected>10</option>
      <option value="20">20</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
  </div>
<div class="page-info" id="page-info"></div>
<ul class="pagination" id="pagination"></ul>
<!-- <button onclick="topFunction()" id="goToTopBtn" class="btn btn-primary rounded-circle position-fixed d-none" aria-label="Go to top" style="bottom: 20px; right: 20px; z-index: 99;">
  <i class="fas fa-arrow-up"></i>
</button> -->

<div id="loading">
    <div class="spinner"></div>
    <div> Generating export file...</div>
</div>
<div id="no-results" class="no-results" style="display: none;">
  No matching records found.
</div>
<?php
  include "extensions/importexcel2.php";
?>
  <button onclick="topFunction()" id="goToTopBtn" class="btn btn-primary shadow" title="Go to top">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
    </svg>
  </button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js" integrity="sha512-oVbWSv2O4y1UzvExJMHaHcaib4wsBMS5tEP3/YkMP6GmkwRJAa79Jwsv+Y/w7w2Vb/98/Xhvck10LyJweB8Jsw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
<script>
    // Get the button
    const goToTopBtn = document.getElementById("goToTopBtn");

    // When the user scrolls down 200px from the top, show the button
    window.onscroll = function() {
      if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        goToTopBtn.style.display = "block";
      } else {
        goToTopBtn.style.display = "none";
      }
    };

    // When the user clicks on the button, scroll to the top
    function topFunction() {
      window.scrollTo({
        top: 0,
        behavior: "smooth" // Smooth scrolling
      });
    }
  //async function loadExport(ispdf=1) {
    function loadExport(ispdf=1) {
        //loadHistory(1,1); // Load data first to ensure the table is populated
        // Wait for the table to be fully rendered before exporting
        // Use a timeout to ensure the table is rendered
        //setTimeout(() => {
        if (ispdf === 1) {
            exportToPDF2();
        } else {
          const wsData = [];
          // const headers = [
          //   'No', 'Item', 'Exp Date', 'Saldo', 'Harga @', '% Modal', 'Saldo Rp.', 'Harga Jual', 'Batch ID'
          // ];
          // wsData.push(headers);

          filterData.forEach((item, index) => {
            wsData.push([
              index + 1,
              item.notag,
              item.nama,
              formatter.format(item.berat),
              formatDate(item.tgltag),
              '',
              item.addby,
              formatDate(item.adddate),
              0,
              0,
            ]);
          });
          var today = new Date();
          var dateExcel = today.getFullYear() + '-' + 
                                    (today.getMonth()+1) + '-' + 
                                    today.getDate() + '_' + 
                                    today.getHours() + '-' + 
                                    today.getMinutes() + '-' + 
                                    today.getSeconds();  

          exportToExcel(wsData, 'dataplan ' + dateExcel + '.xls');

                  //exportToExcel2();
                  //await exportWithTemplate(filterData);
        }
          //}, 1000);
    }

    function showLoading() {
      document.getElementById('loading').style.display = 'flex';
    }

    // Hide loading indicator
    function hideLoading() {
      document.getElementById('loading').style.display = 'none';
    }
     // Export to PDF using jsPDF and autoTable
    // Export to Excel using SheetJS
function getExcelAlignment(htmlClass) {
      if (htmlClass.includes('text-center')) return 'center';
      if (htmlClass.includes('text-end')) return 'right';
      return 'left'; // default
    }

// Pagination variables
    let currentPage = 1;
    let recordsPerPage = 10;
    const table = document.getElementById('data-table');
    const tbody = table.querySelector('tbody');
    const paginationEl = document.getElementById('pagination');
    const pageInfoEl = document.getElementById('page-info');
    //var strprinting="BLOWING";
    // Render pagination controls
    function renderPagination() {
      paginationEl.innerHTML = '';
      
      const pageCount = Math.ceil(filterData.length / recordsPerPage);
      //console.log("Total pages: " + pageCount);
      // Previous button
      // console.log("Current page: " + currentPage);
      // console.log("Records per page: " + recordsPerPage);
      // console.log("Total records: " + filterData.length);
      // console.log("Page count: " + pageCount);

      const prevLi = document.createElement('li');
      prevLi.innerHTML = `<a href="#" ${currentPage === 1 ? 'class="disabled"' : ''} onclick="changePage(${currentPage - 1})">&laquo;</a>`;
      paginationEl.appendChild(prevLi);
      
      // Page numbers
      const maxVisiblePages = 5;
      let startPage, endPage;
      
      if (pageCount <= maxVisiblePages) {
        startPage = 1;
        endPage = pageCount;
        //currentPage=1;
      } else {
        const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
        const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;
        
        if (currentPage <= maxPagesBeforeCurrent) {
          startPage = 1;
          endPage = maxVisiblePages;
          
        } else if (currentPage + maxPagesAfterCurrent >= pageCount) {
          startPage = pageCount - maxVisiblePages + 1;
          endPage = pageCount;
        } else {
          startPage = currentPage - maxPagesBeforeCurrent;
          endPage = currentPage + maxPagesAfterCurrent;
        }
      }
      
      // First page and ellipsis
      if (startPage > 1) {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="changePage(1)">1</a>`;
        paginationEl.appendChild(li);
        
        if (startPage > 2) {
          const ellipsis = document.createElement('li');
          ellipsis.innerHTML = '<span>...</span>';
          paginationEl.appendChild(ellipsis);
        }
      }
      
      // Page range
      for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" ${i === currentPage ? 'class="active"' : ''} onclick="changePage(${i})">${i}</a>`;
        paginationEl.appendChild(li);
      }
      
      // Last page and ellipsis
      if (endPage < pageCount) {
        if (endPage < pageCount - 1) {
          const ellipsis = document.createElement('li');
          ellipsis.innerHTML = '<span>...</span>';
          paginationEl.appendChild(ellipsis);
        }
        
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="changePage(${pageCount})">${pageCount}</a>`;
        paginationEl.appendChild(li);
      }
      
      // Next button
      const nextLi = document.createElement('li');
      nextLi.innerHTML = `<a href="#" ${currentPage === pageCount ? 'class="disabled"' : ''} onclick="changePage(${currentPage + 1})">&raquo;</a>`;
      paginationEl.appendChild(nextLi);
    }

    // Render page information
    function renderPageInfo() {
      //console.log(filterData.length + " total records");
      if ((currentPage - 1) * recordsPerPage + 1>filterData.length) {
        currentPage=currentPage-1;
        if (currentPage<=0) currentPage=1; 
      }
      const startIndex = (currentPage - 1) * recordsPerPage + 1;
      const endIndex = Math.min(currentPage * recordsPerPage, filterData.length);

      if (filterData.length === 0) {
        pageInfoEl.textContent = 'Showing 0 to 0 of 0 entries';
        return;
      }
      pageInfoEl.textContent = `Showing ${startIndex} to ${endIndex} of ${filterData.length} entries`;
    }

    // Change page
    function changePage(page) {
      //if (page < 1 || page > Math.ceil(filterData.length / recordsPerPage)) return;
      
      if (page < 1 ) return;
      if (page > Math.ceil(filterData.length / recordsPerPage)) page = Math.ceil(filterData.length / recordsPerPage);
      currentPage = page;
      if (currentPage<1) currentPage=1;
      populateTable(filterData);
      //console.log("Changed to page: " + currentPage);
      // Scroll to top of table
      table.scrollIntoView({ behavior: 'smooth' });
    }

    // Change page size
    function changePageSize() {
      recordsPerPage = parseInt(document.getElementById('page-size').value);
      currentPage = 1;
      populateTable(filterData);
    }
    
    function loadImport() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.xlsx, .xls';
        input.onchange = e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const data = new Uint8Array(event.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    // Process the imported data (jsonData)
                    //console.log(jsonData);
                    // You can send this data to the server or process it as needed
                    
                };
                reader.readAsArrayBuffer(file);
            }
        };
        input.click();
    }
function hapusImport(pnotag,pall,strblowprint) {
            //console.log(strblowprint);  
    //strprint = strprint === undefined ? "a" : "b";   
            //console.log(strprint);
            // tampilkan notifikasi saat akan menghapus data
    if (pall==0) document.querySelectorAll('tr').forEach(function(r) {
        r.classList.remove('highlight-delete');
      });

            bootbox.dialog({
                title: '<i class="fa-regular fa-trash-can me-2"></i> Hapus '+(pall==0 ? "":"Semua")+' Data Item '+strblowprint,
                //title: '<i class="fa-regular fa-trash-can me-2"></i> Hapus Data Item '+strblowprint,
                message: pall==0?'<p class="mb-2">Anda yakin ingin menghapus satu data item ?</p><p class="fw-bold mb-2">' + 'No. Tag : ' + pnotag + '</p>'
                :'<p class="mb-2">Anda yakin ingin menghapus semua data import ?</p><p class="fw-bold mb-2">' + 'No. Ref : '+ pnotag  + '</p>',
                closeButton: false,
                buttons: {
                    cancel: {
                        label: "Batal",
                        className: 'btn-secondary rounded-pill px-3',
                    },
                    ok: {
                        label: "Ya, Hapus",
                        className: 'btn-danger rounded-pill px-3',
                        callback: function () {
                            // membuat variabel untuk menampung data "id_item"
                            //var notag = data[1];
                            
                            // ajax request untuk delete data item
                            $.ajax({
                                type: "POST",                   // mengirim data dengan method POST 
                                url: "query/deleteimport.php",              // file proses delete data
                                data: { notag : pnotag, alltag: pall,blowprint: strblowprint },   // data yang dikirim
                                // fungsi yang dijalankan sebelum ajax request dikirim
                                beforeSend: function() {
                                    // tampilkan preloader
                                    //$('.preloader').fadeIn('slow');
                                },
                                // fungsi yang dijalankan ketika ajax request berhasil
                                success: function(result) {
                                    // jika delete data berhasil
                                    if (result === "sukses") {
                                        // memberikan interval waktu sebelum fungsi dijalankan
                                        setTimeout(function() {
                                            // tutup preloader
                                            //$('.preloader').fadeOut('fast');
                                            // tampilkan pesan sukses hapus data
                                            alert( pall==0? '('+strblowprint+')' +' No. Tag '+ pnotag + ' berhasil dihapus.': 
                                            '('+strblowprint+') No. Ref. '+ pnotag + ' berhasil dihapus.'
                                            );
                                            // reload data pada tabel
                                            //var table = $('#tabel-item').DataTable();
                                            //table.ajax.reload(null, false);
                                            if ((currentPage - 1) * recordsPerPage + 1>filterData.length-1) {
                                              currentPage=currentPage-1;
                                            }
                                            //console.log("Current page after delete: " + currentPage);
                                            refreshPaginate();
                                            window.location.reload();
                                        }, 500);
                                    }
                                    // jika delete data gagal
                                    else {
                                        // memberikan interval waktu sebelum fungsi dijalankan
                                        setTimeout(function() {
                                            // tutup preloader
                                            //$('.preloader').fadeOut('fast');
                                            // tampilkan pesan gagal dan error result
                                            alert(
                                                pall==0?'('+strblowprint+') No. Tag '+ pnotag + ' gagal dihapus. \n':'('+strblowprint+') No. Ref. '+ pnotag + ' gagal dihapus. \n'
                                                );
                                                window.location.reload();
                                        }, 500);
                                    }
                                }
                            });
                        }
                    }
                }
            });
    };

</script>

<script type="text/javascript">

$(document).ready(function() {

    // $('#btn-detail').click(function() {
    //       $.ajax({
    //           url: "query/intblowprint.php",
    //           type: "POST",
    //           data: {
    //               "param1": (strlastprinting=="BLOWING") ? 0 : 1,
    //           },
    //       });
    //     console.log((strlastprinting=="BLOWING") ? 0 : 1);
    //    $('#mdl-form-detail').modal('show');
    //   }
    // );
    $('#btn-detail').click(function() {
          //var strPrintingOuter = (strlastprinting=="BLOWING") ? 0 : 1;//document.getElementById('selectprinting');
          //$_SESSION['intblowprint'] = strPrintingOuter.value;
          document.getElementById('printing2').value = ((strlastprinting??'BLOWING') =="BLOWING") ? 0 : 1;
       $('#mdl-form-detail').modal('show');
      }
    );
    $(document).keydown(function(e) {
    // Check if the pressed key is the Escape key (keyCode 27)
    if (e.keyCode === 27) {
        // Hide your modal form
        // Replace '#myModal' with the actual ID or class of your modal
        $('#mdl-form-detail').modal('hide'); 
        //console.log("Escape key pressed - modal closed");
        //location.reload(true);
        //history.go(0);
        window.location.reload();
        // Or, if using a framework like Bootstrap:
        // $('#myModal').modal('hide'); 
    }
    });
    const modal = document.getElementById('mdl-form-detail'); // The entire modal container
    const modalContent = document.querySelector('.modal-content'); // The content inside the modal
    document.addEventListener('click', function(event) {
        if (modal.style.display === 'block' && !modalContent.contains(event.target)) {
            // The modal is open and the click was outside its content
            $('#mdl-form-detail').modal('hide'); 
          //history.go(0);
          window.location.reload();
        }
    });        

  });

  var tableData = [];
  var filterData =[];
  
  window.addEventListener('beforeunload', function() {
    //safeStorage.setItem('currentPage', currentPage);
    safeStorage.setItem('lastitem', strlastitem);
    safeStorage.setItem('lastprinting', strlastprinting);
    saveState();
  });

  // Save state to localStorage
function saveState() {
  safeStorage.setItem('tableState', JSON.stringify({
    currentPage,
    recordsPerPage
    //sortColumn,
    //sortDirection
  }));
}

// Load state from localStorage
function loadState() {
  const state = JSON.parse(localStorage.getItem('tableState'));
  if (state) {
    currentPage = state.currentPage || 1;
    recordsPerPage = state.recordsPerPage || 10;
    //sortColumn = state.sortColumn || 'id';
    //sortDirection = state.sortDirection || 'asc';
    
    document.getElementById('page-size').value = recordsPerPage;
  }
}

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
  // Initialize the table
  let strlastitem = safeStorage.getItem('lastitem') || "";
  let strlastprinting = safeStorage.getItem('lastprinting') || "BLOWING";
  loadState();
  //console.log("Last item from storage: " + strlastitem);
  const tableBody = document.querySelector('#data-table tbody');
  const searchFieldsContainer = document.getElementById('search-fields');
  const noResultsMessage = document.getElementById('no-results');
  const resetButton = document.getElementById('reset-search');

$.ajax({
    url: 'query/dtimporttagjb.php',
    type: 'GET',
    
    data: {
      //startDate: date1,
      //endDate : date2,
       fk_prodid: 0,
       page: 1,
       per_page: 1,
       printing: strlastprinting  //"BLOWING"
    //   alldate: 1,
    },
    dataType: 'json',
    beforeSend: function() {
      // $('#loadingSpinner').show();
      // $('#tabel-item').hide();
      // $('#paginationControls').hide();
    },
    success: function(response) {
      if (response.status === 'success') {
        tableData = response.data.users;

        //console.log(tableData);
        //renderPagination(response.data.total_records);
        //updatePageInfo(response.data);
      } else {
        alert('Error: ' + response.message);
      }
    },
    error: function(xhr, status, error) {
      //$('#loadingSpinner').hide();
      alert('AJAX Error: ' + error);
    },
    complete: function() {
      let strlastitem = safeStorage.getItem('lastitem') || "";
      let strlastprinting = safeStorage.getItem('lastprinting')||"BLOWING";
      //console.log("Last printing from storage: " + strlastprinting);
      createSearchInputs();
      createInputSelect();
      //createInputButton();
      filterTable();
    }
  });

  tableBody.addEventListener('click', function(event) {
    //if (event.target.classList.contains('delete-btn')) {
    if (event.ctrlKey) {
      document.querySelectorAll('tr').forEach(function(r) {
        r.classList.remove('highlight-delete');
      });
      const row = event.target.closest('tr');
      if (row && tableBody.contains(row)) {
        row.classList.add('highlight-delete');
        const noref = row.cells[8].textContent;
        hapusImport(noref,1,strlastprinting);
        //row.classList.remove('highlight-delete');
      }
    }
  });

  // Create search inputs for each column
  function createSearchInputs() {
    const headers = document.querySelectorAll('#data-table th');
    headers.forEach((header, index) => {
      if ((index == 1) || (index == 2) || (index == 4) ) { // Skip the first column (No)
        if (index === 4) {
          createDateRangeInput(index);
        } else {
          const columnName = header.textContent.trim();
          const input = document.createElement('input');
          input.id=`search-input-${index}`;
          input.type = 'text';
          input.className = 'search-input';
          input.placeholder = `Search ${columnName}`;
          input.dataset.columnIndex = index;
          input.style.width = '12%';
          if (index == 2) input.value = strlastitem; // Set the last search term for the second column

          // Add event listeners
          input.addEventListener('input', filterTable);
          input.addEventListener('keyup', filterTable);

          searchFieldsContainer.appendChild(input);
        }
      }
    });
  }
  function createInputButton() {
            const newbutton = document.createElement('button');
            newbutton.textContent = 'Hapus Berdasarkan Nomor Ref.';
            newbutton.id = 'btndeleteall';
            newbutton.name = 'Hapus Semua';
            newbutton.style.marginLeft = '5px';
            newbutton.classList.add('btn','btn-danger','btn-sm');
        newbutton.style.padding = '1px';
        newbutton.style.fontWeight = 'bold';
        newbutton.style.fontSize = '0.8rem';
            // Add event listener
        newbutton.addEventListener('click',function() {
                //console.log('Selected printing:', strlastprinting);
                deleteRow(this);
                //hapusImport( strlastprinting,1,strlastprinting);
            });
        searchFieldsContainer.appendChild(newbutton);
  }
  function deleteRow(button) {
      const rowTODelete = button.closest('tr');
      console.log(rowTODelete);
      if (rowTODelete) {
        const noref = rowTODelete.cells[8].textContent;
        console.log('No. Ref to delete:', noref); 
        rowTODelete.parentNode.removeChild(rowTODelete);

        hapusImport( noref,1,strlastprinting);
      }
  }

  function createInputSelect() {
            const options = ["PRINTING"];
            const select = document.createElement('select');
            select.className = 'combo-box';
            select.id = 'selectprinting';
            select.name = 'printing';
            select.placeholder = `Tipe Tag`;
            select.style.marginLeft = '5px';
            select.style.padding = '1px';
            select.style.fontWeight = 'bold';
            select.style.fontSize = '1.1rem';
            select.style.width = '10%';
                //input.dataset.columnIndex = index;
// Create default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '0';
        defaultOption.id = 'defaultOption';
        defaultOption.textContent = 'BLOWING';
        defaultOption.style.fontWeight = 'bold';
        //defaultOption.disabled = true;
        //defaultOption.selected = true;
        select.appendChild(defaultOption);

        // Add options        
        options.forEach((fruit, index) => {
            const option = document.createElement('option');
            option.value = index+1;
            option.id = 'option' + (index+1);
            option.textContent = fruit;
            option.style.fontWeight = 'bold';
            select.appendChild(option);
        });
        select.value = (strlastprinting=="BLOWING") ? 0 : 1;
        select.style.cursor = 'pointer';
        select.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';
        select.style.transition = 'border-color 0.3s, box-shadow 0.3s';
        select.addEventListener('focus', function() {
            this.style.borderColor = '#3498db';
            this.style.boxShadow = '0 4px 10px rgba(52, 152, 219, 0.3)';
        });
        select.addEventListener('blur', function() {
            this.style.borderColor = '#ddd';
            this.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';
        });
        //console.log('Initial printing:', select.value);
        // Add event listener
        select.addEventListener('change',function() {
            const selectedValue = this.value;
            strlastprinting = selectedValue==0 ? "BLOWING" : "PRINTING";
            
            //console.log('Selected printing:', strlastprinting);
        // 1. Construct the URL with the GET parameter
        // const endpoint = '/query/dtimporttagjb.php'; // Replace with your actual API endpoint
        // const url = new URL(endpoint);
        // url.searchParams.append( 'fk_prodid', 0); 
        // url.searchParams.append('page', 1); 
        // url.searchParams.append('per_page', 1); 
        // url.searchParams.append('printing', strprinting); 

        // 2. Perform the fetch request
        fetch('query/dtimporttagjb.php?fk_prodid=0&page=1&per_page=1&printing=' + strlastprinting,
          {method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json' // Example: if sending JSON data
              }
          })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                //console.log(response.json());
                return response.json(); // Assuming the API returns JSON
            })
            .then(data => {
                // 3. Update the UI with the fetched data
                if (data.status === 'success') {
                    tableData = data.data.users;
                    //console.log(tableData);
                    filterTable();
                } else {
                    //console.error('API Error:', data.message);
                    alert('Error: ' + data.message);
                    //outputArea.textContent = 'Error fetching data: ' + data.message;
                }})
            .catch(error => {
                // 4. Handle any errors
                //console.error('Fetch error:', error);
                alert('AJAX Error: ' + error);
                //outputArea.textContent = 'Error fetching data: ' + error.message;
            });
        });
        //select.addEventListener('change', function() {
            //console.log('Selected:', this.value, this.options[this.selectedIndex].text);
        //});

        // Append to container
                // Add event listeners
            searchFieldsContainer.appendChild(select);
        }
  // Create date range search input
  function createDateRangeInput(index) {
    const container = document.createElement('div');
    container.className = 'date-range-container';
    container.style.display = 'inline-block';

    const fromLabel = document.createElement('span');
    fromLabel.textContent = 'From : ';
    container.appendChild(fromLabel);

    const fromInput = document.createElement('input');
    fromInput.type = 'date';
    fromInput.id = 'date-from';
    fromInput.className = 'search-input date-range-input';
    fromInput.dataset.columnIndex = index;
    fromInput.dataset.rangeType = 'from';
    container.appendChild(fromInput);

    const toLabel = document.createElement('span');
    toLabel.textContent = 'To : ';
    toLabel.style.marginLeft = '5px';
    container.appendChild(toLabel);

    const toInput = document.createElement('input');
    toInput.type = 'date';
    toInput.id = 'date-to';
    toInput.className = 'search-input date-range-input';
    toInput.dataset.columnIndex = index;
    toInput.dataset.rangeType = 'to';
    container.appendChild(toInput);

    const today = new Date();
    //const threeYearsLater = new Date();
    //threeYearsLater.setFullYear(today.getFullYear() + 3);

    fromInput.valueAsDate = new Date(today.getFullYear(), today.getMonth(), 2);
    toInput.valueAsDate = new Date(today.getFullYear(), today.getMonth()+1, 1);
    // Add event listeners
    fromInput.addEventListener('change', filterTable);
    toInput.addEventListener('change', filterTable);

    searchFieldsContainer.appendChild(container);
  }

  // Populate table with data
  function populateTable(data) {
    //tableBody.innerHTML = '';
    let tableBody = $('#userTableBody');
    tableBody.empty();
    let startIndex2, endIndex2;

    if (data.length === 0) {
      noResultsMessage.style.display = 'block';
      refreshPaginate();
      return;
      //startIndex2 = 0;
      //endIndex2 = Math.min(startIndex2 + recordsPerPage, data.length);
    }
    else {
      noResultsMessage.style.display = 'none';

      startIndex2 = (currentPage - 1) * recordsPerPage;
      endIndex2 = Math.min(startIndex2 + recordsPerPage, data.length);
    }

      //console.log("Populating table from index " + startIndex + " to " + endIndex);
      for (let i = startIndex2; i < endIndex2; i++) {
        const rows = document.createElement('tr');
        const row = data[i];
        // if (row.itemid==null) { 
        // rows.style.backgroundColor = "red";}
        
        rows.innerHTML = `
             <td align='right'>${i+1}</td>
             <td align='center'>${row.notag}</td>
             <td>${row.nama}</td>             
             <td align='center' ${row.itemid == null ? "style='background-color:red'" : ""}>${row.itemid}</td>             
             <td align='right'>${formatter.format(row.berat)}</td>
             <td align='center'>${(row.tgltag)}</td>
             <td align='center'>${(row.adddate)}</td>
             <td align='center'>${row.addby}</td>
             <td align='left'>${row.noref}</td>
             <td align='center'><button id="btn-hapus" 
             class="btn btn-danger btn-sm rounded-pill px-3 me-2 mb-1" 
             onclick="hapusImport('${row.notag}','0','${row.printing==1 ? "PRINTING":"BLOWING"}')">Hapus</button>
</td>
             <td align='center'>${row.printing == 1 ?"PRINTING":"BLOWING"}</td>
          `;
            //  <button id="btn-hapus2" 
            //  class="btn btn-danger btn-sm rounded-pill px-3 me-2 mb-1" 
            //  onclick="hapusImport('${row.noref}','1','${row.printing==1 ? "PRINTING":"BLOWING"}')">Hapus ALL</button>        
            tbody.appendChild(rows);
    }
    //<td align='left' style="display:none;">${row.noref}</td>
            //  <button align='center' class="btn btn-primary btn-sm rounded-pill px-3 me-2 mb-1" onclick="loadDetail(${row.notag})">Detail</button>
            //  <td align='center'>${(row.noplan==0 || row.namablowing=='' ? "<span class=\"badge bg-success\">berhasil</span>" : "<span class=\"badge bg-danger\">gagal</span>")}</td>
            //  <td align='center'>${(row.namablowing=='' ? "<span>tidak ditemukan nama itemnya</span>" : row.noplan==0 ? "<span class=\"badge bg-danger\">gagal</span>" : "")}</td>

    ///);
  //highlightMatches();
    // Update pagination and page info
    refreshPaginate();
  }

  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  // Filter table based on search inputs
  function filterTable() {
    const searchInputs = document.querySelectorAll('.search-input:not(.date-range-input)');
    const dateRangeInputs = document.querySelectorAll('.date-range-input');
    const filters = [];

    // Collect all filters
    searchInputs.forEach(input => {
      const columnIndex = parseInt(input.dataset.columnIndex);
      const searchText = input.value.trim().toLowerCase();
      filters.push({
        columnIndex,
        searchText
      });
    });

    // Collect date range filters
    const dateFilters = {};
    dateRangeInputs.forEach(input => {
      const columnIndex = parseInt(input.dataset.columnIndex);
      const rangeType = input.dataset.rangeType;
      const dateValue = input.value;

      if (!dateFilters[columnIndex]) {
        dateFilters[columnIndex] = {};
      }
      dateFilters[columnIndex][rangeType] = dateValue;
    });

    // Add to filterTable() before applying date filters
    Object.entries(dateFilters).forEach(([colIndex, range]) => {
      if (range.from && range.to && range.from > range.to) {
        alert('"From" date cannot be after "To" date');
        return false;
      }
    });

    // Apply filters to data
    const filteredData = tableData.filter(row => {
      // Apply text filters
      strlastitem ="";
      const textMatch = filters.every(filter => {
        if (!filter.searchText) return true;
        const columnValue = getColumnValue(row, filter.columnIndex);
        if (filter.columnIndex == 2) strlastitem = filter.searchText;
        return columnValue.toLowerCase().includes(filter.searchText);
      });

      // Apply date range filters
      const dateMatch = Object.entries(dateFilters).every(([colIndex, range]) => {
        const columnValue = (row.tgltag); // For our example, we know it's joinDate
        const fromDate = range.from || '2000-01-01';
        const toDate = range.to || '9999-12-31';
        return columnValue >= fromDate && columnValue <= toDate;
      });

      return textMatch && dateMatch;
    });

    // Highlight matching text
    highlightMatches(filters);
    filterData = filteredData;
    // Update table with filtered data
    populateTable(filterData);
  }

  // Get column value from row data
  function getColumnValue(row, columnIndex) {
    switch (columnIndex) {
      //case 0: return row.rownum.toString();
      case 1:
        return row.notag;
      case 2:
        return row.nama;
      //case 4: return formatDate(row.tgljb);
        //case 3: return `$${row.saldo2.toLocaleString()}`;
        // case 4: return `$${row.SupPrice.toLocaleString()}`;
        // case 5: return `$${row.psnmodal.toLocaleString()}`;
        // case 6: return `$${row.saldopo.toLocaleString()}`;
      //case 5:
      //  return row.notrans.toString();
    //   case 8:
    //     return row.kapmesin.toString();
      //case 9:
      // return row.noplan.toString();  
      default:
        return '';
    }
  }

  // Highlight matching text in cells
  function highlightMatches(filters) {
    const rows = tableBody.querySelectorAll('tr');

    //const rows = document.querySelectorAll('#userTableBody tr');
    rows.forEach(row => {
      const cells = row.querySelectorAll('td');

      cells.forEach((cell, cellIndex) => {
        // Remove previous highlights
        cell.innerHTML = cell.textContent;

        // Apply new highlights if there's a filter for this column
        const filter = filters.find(f => f.columnIndex === cellIndex);
        if (filter && filter.searchText) {
          const cellText = cell.textContent;
          const regex = new RegExp(`(${escapeRegExp(filter.searchText)})`, 'gi');
          cell.innerHTML = cellText.replace(regex, '<span class="highlight">$1</span>');
        }
      });
    });
  }

  // Reset all search filters
  function resetSearch() {
    strlastitem = '';
    const inputs = document.querySelectorAll('.search-input:not(.date-range-input)');
    inputs.forEach(input => {
      input.value = '';
    });

    // Reset date range inputs
    var today = new Date();
    var firstDay = new Date(today.getFullYear(), today.getMonth(), 2);
    var lastDay = new Date(today.getFullYear(), today.getMonth()+1, 1);;
    //const threeYearsLater = new Date();

    //today2.setFullYear(today.getFullYear() - 1);
    //threeYearsLater.setFullYear(today.getFullYear() + 1);

    document.querySelectorAll('.date-range-input[data-range-type="from"]').forEach(input => {
      input.valueAsDate = firstDay;
      //console.log("Reset from date to: " + input.value);
    });

    document.querySelectorAll('.date-range-input[data-range-type="to"]').forEach(input => {
      input.valueAsDate = lastDay;
      //console.log("Reset from date to: " + input.value);
    });

    filterTable();
  }

  // Helper function to format date
  function formatDate(dateString) {
    const options = {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    };
    return new Date(dateString).toLocaleDateString('en-ID', options);
  }

  // Helper function to escape regex special characters
  function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function refreshPaginate() {
      renderPagination();
      renderPageInfo();
  }
  // Event listeners
  resetButton.addEventListener('click', resetSearch);
  function getFstDayOfMonthFnc(){
            var date = new Date();
            return new Date(date.getFullYear(), date.getMonth(), 1)
        }
  function getLstDayOfMonthFnc(){
            var date = new Date();
            return new Date(date.getFullYear(), date.getMonth()+1, 0)
        }
        
  function formatDate2(date){
            const monthNames=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            var d = new Date(date), month = '' + (d.getMonth()+1),
                day= '' + d.getDate(), year = d.getFullYear();
            if (day.length<2)
                day = "0" + day;
            return [year, d.getMonth()+1,day].join('-');
        }

</script>