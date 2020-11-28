<?php
  session_start();
  unset($_SESSION['MM_Username_Mapa']);
  unset($_SESSION['MM_UserGroup_Mapa']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
?>
