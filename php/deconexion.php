<?php
session_start();
session_destroy();
header('Location: /projet-web-eseo/index.php');
exit;
?>
