<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/BaseController.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/BaseModel.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';

$router = new Router();

require __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);
