<?php
declare(strict_types=1);

use Attinge\Framework\Http\Kernel;
use Attinge\Framework\Http\Request;
use Attinge\Framework\Routing\Router;

define('BASE_PATH', dirname(__DIR__));
require_once dirname(__DIR__) . '/vendor/autoload.php';

$request  = Request::createFromGlobals();
$router   = new Router();
$kernel   = new Kernel($router);

$response = $kernel->handle($request);

$response->send();
