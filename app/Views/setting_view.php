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
              <h3 class="page-title"> Data Setting </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Setting</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data Setting</li>
                </ol>
              </nav>
            </div>
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Data Setting</h4>
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                            <th>Id#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Value</th>
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

      <!-- modal detail -->
      <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="detailModalLabel">Detail Setting</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="recipient-name" class="col-form-label" id="title-detail-label">Information</label>
                  <p id="pDetail"></p>
                </div>
                <div class="mb-3">
                  <label for="val-text" class="col-form-label">Value Setting</label>
                  <textarea class="form-control" id="val-text"></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save change!</button>
            </div>
          </div>
        </div>
      </div>
      <!-- modal detail -->

      <script>
        (function($) {
          'use strict';
          $(function() {
            loadDataTable();
          });
        })(jQuery);


        var storedName = "paramJSON";

        showModalDetail = function(id) {
          console.log('get id '+id);

          $.get( "/setting/get_byid?rn=120&id="+id, function( data ) {
            //console.log(data);
            var result = data['result'][0];
            //$( ".result" ).html( data );
            //alert( "Load was performed." );

            $('#val-text').html(result['val_setting']);
            $('#title-detail-label').html(result['title']);
          });

          $('#detailModal').modal('show');
          
          
        };


        loadDataTable = function() {

          var dataRows = [];

          var table = $('#example').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true, // Set true agar bisa di sorting
            "order": [
                [0, 'desc']
            ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
            "ajax": {
                "url": "/setting/get_data", // URL file untuk proses select datanya
                "type": "POST"
            },
            "deferRender": true,
            "aLengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ], 
            "columns": [{
                        "data": "id_setting"
                    },
                    {
                        "render": function(data, type, row) {
                            var html = '<strong>'+row['title']+"</strong>";
                            return html;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var html = "<strong>"+row['description']+"</strong>";
                            return html;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            let userObj = JSON.stringify(row);

                            if (dataRows.length < 1 ||  !dataRows.includes(userObj)) {
                              dataRows.push(userObj);
                            }

                            //console.log(dataRows);
                            localStorage.setItem(storedName, JSON.stringify(dataRows))

                            var html = '<a href="#" onclick="showModalDetail(' + row['id_setting'] + ');"><div class="badge badge-primary">Detail</div></a>';
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