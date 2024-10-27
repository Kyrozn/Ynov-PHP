<?php
require_once './Router.php';

$router = new Router();

$voucherId = $_GET['id'] ?? null;

$router->dispatch($_SERVER['REQUEST_URI']);
