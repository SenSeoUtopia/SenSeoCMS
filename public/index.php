<?php

require_once("../vendor/autoload.php");

$f3 = Base::instance();

/* Main Config File */
$f3->config("../app/config.ini");
/* Database */
$f3->config("../app/database.ini");
/* Routes */
$f3->config("../app/routes.ini");
/* Settings */
$f3->config("../app/settings.ini");

$f3->run();