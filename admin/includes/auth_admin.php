<?php
if(!isset($_SESSION))
{
  session_start();
}
if (!(isset($_SESSION["role"]) && $_SESSION["role"] == "admin")) {
  echo "<script>alert(`Unauthorized Access`);
          window.location.href= 'index.php'</script>";
}
