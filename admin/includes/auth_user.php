<?php
if(!isset($_SESSION))
{
  session_start();
}
if (!(isset($_SESSION['user']) && $_SESSION['user'] == "True")) {
  echo "<script>window.location.replace('login.php')</script>";
}
