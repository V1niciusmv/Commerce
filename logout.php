<?php  

session_start();
session_destroy();

header ('location: views/welcome_page.php');
?>