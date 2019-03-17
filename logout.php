<?php
session_start();
// セッション情報を削除
$_SESSION = array();
session_destroy();
header('Location: index.php'); 
exit();