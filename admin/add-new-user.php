<?php require_once("includes/head.php"); ?>
<?php require_once ("includes/auth_admin.php"); ?>

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
          <h1 class="h3 mb-0 text-gray-800">Add New User</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
              <!-- Account -->
              <div class="card-body">
                  <div class="row">
                    <div class="mb-3 col-md-6">
                      <label class="form-label">Name</label>
                      <input class="form-control" type="text" id="name"
                             autofocus required/>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label class="form-label">Email</label>
                      <input class="form-control" type="text" id="email"
                             autofocus required/>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label class="form-label">Role</label>
                      <select class="form-control" id="role">
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                      </select>
                    </div>

                  </div>
                  <div class="row">
                    <div class="mt-3 col-md-6">
                      <input type="button" onclick="return validateNewUser()" class="btn btn-primary" value="Add User">
                      <a  class="btn btn-success" href="users.php">All Users</a>
                    </div>
                  </div>
              </div>
              <!-- /Account -->
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
