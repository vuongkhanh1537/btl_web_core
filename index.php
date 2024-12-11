<?php

require_once 'config/Database.php';
require_once 'config/Authorization.php';
require_once 'src/core/Request.php';
require_once 'src/core/Response.php';
require_once 'src/core/Router.php';
require_once 'src/core/Validator.php';
require_once 'src/routes/api.php';
require_once 'src/app.php';
require_once 'src/models/ProductModel.php';
require_once 'src/models/UserModel.php';
require_once 'src/models/OrderModel.php';
require_once 'src/models/CollectionModel.php';
require_once 'src/models/PromotionModel.php';
require_once 'src/models/DashboardModel.php';
require_once 'src/models/CartModel.php';
require_once 'src/controllers/CollectionController.php';
require_once 'src/controllers/ProductController.php';
require_once 'src/controllers/UserController.php';
require_once 'src/controllers/OrderController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/PromotionController.php';

require_once 'src/controllers/CartController.php';


$app = new App();
$app->run();