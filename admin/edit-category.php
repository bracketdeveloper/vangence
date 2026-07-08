<?php require_once("includes/head.php"); ?>
<?php require_once("includes/auth_admin.php"); ?>
<?php
require_once("model/functions.php");
$category_id = $_GET['category_id'];
$category = getCategoryById($conn, $category_id);
$allCategories = getAllCategories($conn);
if ($category == null) {
    echo "<script>alert(`Invalid request`);
          window.location.href= 'categories.php'</script>";
}
?>
    <div id="wrapper">
<?php require_once("includes/sidebar.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php require_once("includes/topbar.php"); ?>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Edit Category</h1>
                </div>
                <div class="row">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="new-category-form" method="POST">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Category</label>
                                            <input class="form-control" type="text" id="edit-category" autofocus
                                                   required value="<?php echo $category[0]['category']; ?>"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Parent Category</label>
                                            <select class="form-control" id="edit-parent-category">
                                                <option value="">No Parent Category</option>
                                                <?php foreach ($allCategories as $cat) { ?>
                                                    <?php if ($cat['category_id'] != $category_id) { ?>
                                                        <option value="<?php echo $cat['category_id']; ?>" <?php if ($cat['category_id'] == $category[0]['parent_id']) echo "selected"; ?>>
                                                            <?php echo $cat['category']; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mt-3 col-md-6">
                                            <input type="button"
                                                   onclick="return validateEditCategory(<?php echo $category[0]['category_id']; ?>)"
                                                   class="btn btn-primary" value="Edit Category">
                                            <a class="btn btn-success" href="categories.php">All Categories</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php require_once("includes/footer.php"); ?>