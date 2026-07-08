<?php require_once("includes/head.php"); ?>
<?php require_once ("includes/auth_admin.php"); ?>
<?php
  require_once ("model/functions.php");
  $allUsers = getAllUsers($conn);
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
          <h1 class="h3 mb-0 text-gray-800">Users</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
          <div class="container-fluid">

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                  <a href="add-new-user.php" class="btn btn-success btn-icon-split">
                    <span class="text">Add new user</span>
                  </a>
                </h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                      <th>Sr#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;
                    foreach ($allUsers as $user):
                      $i++;
                      ?>
                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo ucwords(strtolower($user['user_name']))?></td>
                        <td><?php echo $user['email']?></td>
                        <td><?php echo $user['role']?></td>
                        <td><a onclick="return validateDeleteCategory(<?php echo $user['user_id'];?>)" class="btn btn-danger">Delete</i></a></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
      <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <?php require_once("includes/footer.php"); ?>
