<?php
session_start();
session_destroy();
header('Location: /login.php?e=logoutok');
exit;
?>