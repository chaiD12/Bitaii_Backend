<?php 
    require_once 'header_view.php';
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
            <div class="row">
              <div class="col-12 grid-margin">
                <div class="card card-statistics">
                  <div class="row">
                    <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6 border-right">
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                          <i class="mdi mdi-account-multiple-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
                          <div class="wrapper text-center text-sm-left">
                            <p class="card-text mb-0">New Users</p>
                            <div class="fluid-container">
                              <h3 class="mb-0 font-weight-semibold"><?php echo number_format($panel['user_total']);?></h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6 border-right">
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                          <i class="mdi mdi-checkbox-marked-circle-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
                          <div class="wrapper text-center text-sm-left">
                            <p class="card-text mb-0">New Installs</p>
                            <div class="fluid-container">
                              <h3 class="mb-0 font-weight-semibold"><?php echo number_format($panel['install_total']);?></h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6 border-right">
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                          <i class="mdi mdi-trophy-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
                          <div class="wrapper text-center text-sm-left">
                            <p class="card-text mb-0">Users Package</p>
                            <div class="fluid-container">
                              <h3 class="mb-0 font-weight-semibold"><?php echo number_format($panel['user_package_total']);?></h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6">
                      <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                          <i class="mdi mdi-currency-usd text-primary mr-0 mr-sm-4 icon-lg"></i>
                          <div class="wrapper text-center text-sm-left">
                            <p class="card-text mb-0">Total Payment</p>
                            <div class="fluid-container">
                              <h3 class="mb-0 font-weight-semibold">$<?php echo number_format($panel['payment_package_sum']);?> USD</h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Recent Users</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th> Email </th>
                            <th> Package </th>
                            <th> Max Counter </th>
                            <th> Expired Date </th>
                            <th> Payment </th>
                          </tr>
                        </thead>
                        <tbody>
                          
                        <?php foreach($panel['last_payment'] as $row) { ?>
                          <tr>
                            <td>
                              <img src="<?php echo $row['profile_pic'];?>" style="width: 35px; height: 35px;" class="me-2" alt="image"> <?php echo $row['display_name'];?> <br/><small class="text-muted"><?php echo $row['email'];?></small></td>
                            <td> <strong><?php echo $row['code_package'];?></strong> </td>
                            <td>
                              <label class="badge <?php echo ($row['counter_max'] > 10 ? 'badge-success' : 'badge-danger');?>"><?php echo $row['counter_max'];?></label>
                            </td>
                            <td> <?php echo $row['expired_at'];?> <br/> <strong><?php echo $row['os_platform'];?></strong></td>
                            <td> <?php echo $row['code_method'];?> <br/> <strong><?php echo $row['type_account'];?></strong></td>
                          </tr>
                        <?php }?>  
                        
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              

              <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-center">
                      <i class="mdi mdi-clock icon-lg text-primary d-flex align-items-center"></i>
                      <div class="d-flex flex-column ms-4">
                        <div class="d-flex flex-column">
                          <p class="mb-0">User Packages</p>
                          <h4 class="font-weight-bold"><?php echo number_format($panel['counter']['count_package']);?> users</h4>
                        </div>
                        <small class="text-muted">Counter all user packages</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-center">
                      <i class="mdi mdi-cart-outline icon-lg text-success d-flex align-items-center"></i>
                      <div class="d-flex flex-column ms-4">
                        <div class="d-flex flex-column">
                          <p class="mb-0">Trial Plan</p>
                          <h4 class="font-weight-bold"><?php echo number_format($panel['counter']['count_trial']);?> users</h4>
                        </div>
                        <small class="text-muted">All User trial counter</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-center">
                      <i class="mdi mdi-laptop icon-lg text-warning d-flex align-items-center"></i>
                      <div class="d-flex flex-column ms-4">
                        <div class="d-flex flex-column">
                          <p class="mb-0">Limited Plan</p>
                          <h4 class="font-weight-bold"><?php echo number_format($panel['counter']['count_limited']);?> users</h4>
                        </div>
                        <small class="text-muted">$<?php echo number_format($panel['counter']['sum_limited']);?> USD revenue</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-center">
                      <i class="mdi mdi-earth icon-lg text-danger d-flex align-items-center"></i>
                      <div class="d-flex flex-column ms-4">
                        <div class="d-flex flex-column">
                          <p class="mb-0">Max plan</p>
                          <h4 class="font-weight-bold"><?php echo number_format($panel['counter']['count_max']);?> users</h4>
                        </div>
                        <small class="text-muted">$<?php echo number_format($panel['counter']['sum_max']);?> USD revenue</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                      <i class="mdi mdi-facebook text-facebook icon-md"></i>
                      <div class="ms-3">
                        <h6 class="text-facebook">2.62 Subscribers</h6>
                        <p class="mt-2 text-muted card-text">You main list growing</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                      <i class="mdi mdi-linkedin text-linkedin icon-md"></i>
                      <div class="ms-3">
                        <h6 class="text-linkedin">5k connections</h6>
                        <p class="mt-2 text-muted card-text">You main list growing</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                      <i class="mdi mdi-twitter text-twitter icon-md"></i>
                      <div class="ms-3">
                        <h6 class="text-twitter">3k followers</h6>
                        <p class="mt-2 text-muted card-text">You main list growing</p>
                      </div>
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
      <?php require_once 'footer_view.php';?>