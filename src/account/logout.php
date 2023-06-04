<?php

session_start();
session_destroy();

// redirect to the home page
header('Location: /');

?>