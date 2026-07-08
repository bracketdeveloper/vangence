<?php require_once("includes/head.php"); 
require_once("model/functions.php");
$allCategories = getAllCategories($conn);
?>

<div id="wrapper">

<?php require_once("includes/sidebar.php"); ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php require_once("includes/topbar.php"); ?>

<div class="container-fluid">

<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Add New Category</h1>
</div>

<div class="row">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">

      <div class="card-body">
        
        <div class="row">

          <!-- Category Name -->
          <div class="mb-3 col-md-6">
            <label class="form-label">Category</label>
            <input class="form-control" type="text" id="new-category" required/>
          </div>

          <!-- Parent Category -->
          <div class="mb-3 col-md-6">
            <label class="form-label">Parent Category</label>
            <select class="form-control" id="parent-category">
              <option value="" disabled selected>Select Parent Category</option>

              <?php foreach($allCategories as $cat) { ?>
                <option value="<?php echo $cat['category_id']; ?>">
                  <?php echo $cat['category']; ?>
                </option>
              <?php } ?>

            </select>
          </div>

        </div>

        <div class="row">
          <div class="mt-3 col-md-6">
            <input type="button" onclick="return validateNewCategory()" class="btn btn-primary" value="Add Category">
            <a class="btn btn-success" href="categories.php">All Categories</a>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

</div>
</div>

<?php require_once("includes/footer.php"); ?>