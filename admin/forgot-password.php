<?php
if(!isset($_SESSION))
{
  session_start();
}
if ((isset($_SESSION['user']) && $_SESSION['user'] == "True")) {
  echo "<script>window.location.replace('index.php')</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Forgot Password</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
    rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to right, #4e73df, #224abe);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .card {
      width: 100%;
      max-width: 500px;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      padding: 2.5rem;
    }

    .btn-user {
      border-radius: 25px;
    }

    h1 {
      font-weight: 700;
    }
  </style>
</head>

<body>

<div class="card">
  <div class="card-body text-center">
    <h1 class="h4 text-gray-900 mb-3">Forgot Your Password?</h1>
    <p class="mb-4">Enter your email below and we’ll send you a reset link.</p>
    <form class="user">
      <div class="form-group">
        <input type="email" class="form-control form-control-user" id="exampleInputEmail"
               aria-describedby="emailHelp" placeholder="Enter Email Address...">
      </div>
      <a href="login.html" class="btn btn-primary btn-user btn-block">Reset Password</a>
    </form>
    <hr>
    <div>
      <a class="small d-block" href="add-new-user.php">Create an Account</a>
      <a class="small d-block" href="login.php">Already have an account? Login</a>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>

</html>
