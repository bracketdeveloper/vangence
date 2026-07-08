<?php
if (!isset($_SESSION)) {
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
  <title>Login</title>

  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
    rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <script src="js/form-requests.js"></script>

  <style>
    body {
      background: linear-gradient(135deg, #4e73df, #224abe);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      max-width: 420px;
      width: 100%;
    }

    .form-control-user {
      border-radius: 10px;
    }

    .btn-user {
      border-radius: 10px;
    }
  </style>
</head>
<body>
<div class="card shadow-lg border-0">
  <div class="card-body p-5">
    <div class="text-center mb-4">
      <h1 class="h4 text-gray-900">Welcome Back!</h1>
    </div>

    <div class="form-group">
      <input class="form-control form-control-user" id="email"
             placeholder="Enter Email Address...">
    </div>

    <div class="form-group">
      <input type="password" class="form-control form-control-user" id="password"
             placeholder="Password">
    </div>

    <button class="btn btn-primary btn-user btn-block" onclick="return validateUserLogin()">
      Login
    </button>
    <hr>

    <div class="text-center">
      <a class="small" href="forgot-password.php">Forgot Password?</a>
    </div>
  </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
