<?php
use liuguang\mvc\Application;
define('APP_PATH', dirname(__FILE__));
define('APP_DEBUG', TRUE);
include '../mvc/core.php';
Application::init();