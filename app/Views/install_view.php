<?php 
    require_once 'header_table_view.php';
?>
  <body>
    <div class="container-scroller">
      
      <!-- partial:partials/_sidebar.html -->
      <?php require_once 'nav_sidebar_view.php'; ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
       
        <!-- partial:partials/_navbar.html -->
        <?php require_once 'nav_view.php';?>

        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Data Install </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data Install</li>
                </ol>
              </nav>
            </div>
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Data Install</h4>
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                            <th>Id#</th>
                            <th>UUID</th>
                            <th>Platform</th>
                            <th>Status</th>
                            <th>Updated at</th>
                            <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>  
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->
      <?php require_once 'footer_top_view.php';?>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
      <?php require_once 'footer_all_view.php';?>

      <script>
        (function($) {
          'use strict';
          $(function() {
            loadDataTable();
          });
        })(jQuery);


        loadDataTable = function() {

          var table = $('#example').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'desc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "/install/get_data", // URL file untuk proses select datanya
                "type": "POST"
            },
            "deferRender": true,
            "aLengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ], 
            "columns": [{
                        "data": "id_install"
                    },
                    {
                        "data": "uuid"
                    },
                    {
                        "data": "os_platform"
                    },
                    {
                        "render": function(data, type, row) {
                            var html = row['status'] == '1' ? '<div class="badge badge-success">Active</div>' : '<div class="badge badge-danger">NonActive</div>';
                            return html;
                        }
                    },
                    {
                        "data": "updated_at"
                    },
                ],
          });
        };

        loadTempTable = function() {
          $('#order-listing').DataTable({
              "aLengthMenu": [
                [5, 10, 15, -1],
                [5, 10, 15, "All"]
              ],
              "iDisplayLength": 10,
              "language": { 
                search: ""
              }
            });
            $('#order-listing').each(function() {
              var datatable = $(this);
              // SEARCH - Add the placeholder for Search and Turn this into in-line form control
              var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
              search_input.attr('placeholder', 'Search');
              search_input.removeClass('form-control-sm');
              // LENGTH - Inline-Form control
              var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
              length_sel.removeClass('form-control-sm');
            });
        };

      </script>  