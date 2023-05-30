<?php 

session_destroy();

// // remove the cookie
// setcookie('jwt', '', time() - 3600, "/");

// redirect to the home page
header('Location: /');

?>