<?php

require_once 'config/Database.php';
require_once 'config/Authorization.php';
require_once 'src/core/Auth.php';
require_once 'src/core/Request.php';
require_once 'src/core/Response.php';
require_once 'src/core/Router.php';
require_once 'src/core/Validator.php';
require_once 'src/routes/api.php';
require_once 'src/app.php';
require_once 'src/models/ProductModel.php';
require_once 'src/models/UserModel.php';
require_once 'src/controllers/ProductController.php';
require_once 'src/controllers/UserController.php';


$app = new App();
$app->run();