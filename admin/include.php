<?php
include '../init.php';

isLogin('../admin_login.php');

// load and render admin dashboard template
$loader = new \Twig\Loader\FilesystemLoader('../templates/admin');
$twig = new \Twig\Environment($loader);