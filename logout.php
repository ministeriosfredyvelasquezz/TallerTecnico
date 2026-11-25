<?php
session_start();
session_unset();   // Limpia los datos de tu sesión
session_destroy(); // Cierra la sesión
header("Location: login.php"); // Te devuelve al login
exit;
?>
