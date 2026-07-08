<?php require_once("includes/head.php"); ?>
<?php
require_once("model/functions.php");
$allCategories = getAllCategories($conn);

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
                    <h1 class="h3 mb-0 text-gray-800">Categories</h1>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <div class="container-fluid">

                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <a href="add-new-category.php" class="btn btn-success btn-icon-split">
                                        <span class="text">Add New Category</span>
                                    </a>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="dataTable" width="100%"
                                           cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Sr#</th>
                                            <th>Category</th>
                                            <th>Parent Category</th>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            <?php endif; ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($allCategories as $category):
                                            $i++;
                                            ?>
                                            <tr>
                                                <td><?php echo $i ?></td>

                                                <td><?php echo $category['category'] ?></td>

                                                <td>
                                                    <?php
                                                    echo $category['parent_name'] ? $category['parent_name'] : 'NULL';
                                                    ?>
                                                </td>

                                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                                    <td>
                                                        <a href="edit-category.php?category_id=<?php echo $category['category_id']; ?>"
                                                           class="btn btn-success">Edit</a>
                                                    </td>

                                                    <td>
                                                        <a onclick="return validateDeleteCategory(<?php echo $category['category_id']; ?>)"
                                                           class="btn btn-danger">Delete</a>
                                                    </td>
                                                <?php endif; ?>
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
