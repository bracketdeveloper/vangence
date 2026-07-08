<?php require_once("includes/head.php"); ?>
<?php
$name = ucwords(strtolower($_SESSION["name"]));
$email = $_SESSION["email"];
$role = $_SESSION["role"];
?>

<!-- Page Wrapper -->
<div id="wrapper">

  <!-- Sidebar -->
  <?php require_once("includes/sidebar.php"); ?>
  <!-- End of Sidebar -->

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

      <!-- Topbar -->
      <?php require_once("includes/topbar.php"); ?>
      <!-- End of Topbar -->

      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Settings</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
              <!-- Account -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Name</label>
                    <input class="form-control" type="text" disabled value="<?php echo $name; ?>"/>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="text" disabled value="<?php echo $email; ?>"/>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Role</label>
                    <input class="form-control" type="text" disabled value="<?php echo $role; ?>"/>
                  </div>
                </div>
              </div>
            </div>
            <div class="card mb-4">
              <!-- Account -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label">New Password</label>
                    <input class="form-control" type="password" id="new-password" required"/>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input class="form-control" type="password" id="confirm-password" readonly"/>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Current Password</label>
                    <input class="form-control" type="password" id="current-password" required/>
                  </div>
                </div>
                <div class="row">
                  <div class="mt-3 col-md-6">
                    <input type="button" onclick="return validateChangePassword()" class="btn btn-primary"
                           value="Change Password">
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Content Row -->

        <div class="row">

        </div>

        <!-- Content Row -->
        <div class="row">


        </div>

      </div>
      <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <?php require_once("includes/footer.php"); ?>
