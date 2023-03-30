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
              <h3 class="page-title"> Data User Package</h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">User Package</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data List UserPackage</li>
                </ol>
              </nav>
            </div>
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Data User Package</h4>
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                            <th>Id#</th>
                            <th>User</th>
                            <th>Package</th>
                            <th>Method</th>
                            <th>Price</th>
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
                "url": "/userpackage/get_data", // URL file untuk proses select datanya
                "type": "POST"
            },
            "deferRender": true,
            "aLengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ], 
            "columns": [{
                        "data": "id_user_package"
                    },
                    {
                        "render": function(data, type, row) {
                            var html = "Name: "+row['display_name'] + "<br/>"+row['email'] + "<br/><b>Counter Limit: "+row['limit_counter']+"</b>";
                            return html;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var html = "<strong>" + row['code_package'] + "</strong>";
                            return html;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var html = row['code_method'] + "<br/>EXP: "+row['expired_at'];
                            return html;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var html = row['price'] > 1 ? '<label class="badge badge-success">'+row['currency']+' '+row['price']+'</label>' : '<label class="badge badge-danger">'+row['currency']+' '+row['price']+'</label>';
                            return html;
                        }
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

      </script>  