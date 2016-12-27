<?php

// Config
error_reporting(E_ALL);
ini_set('display_errors', 1);
defined('ROOT') or define('ROOT', dirname(__FILE__));
defined('PATH') or define('PATH', '/apps/note');

// Session
$lifetime = 7 * 24 * 60 * 60;
session_start([
	'cookie_path'     => PATH,
	'cookie_lifetime' => $lifetime
]);
setcookie(session_name(), session_id(), time() + $lifetime, PATH);

// Classes
require_once ROOT . '/app/model.php';
require_once ROOT . '/app/controller.php';

// Router
require_once ROOT . '/vendor/AltoRouter.php';
$router = new AltoRouter();
$router->setBasePath(PATH);

$router->addRoutes([

	['GET',  '/',                      'View::main'],
	['GET',  '/[login:action]',        'View::main'],
	['GET',  '/[registration:action]', 'View::main'],
	['GET',  '/user',                  'View::user'],
	['GET',  '/[i:id]',                'View::note'],
	['GET',  '/[new:action]',          'View::edit'],
	['GET',  '/[edit:action]/[i:id]',  'View::edit'],
	['GET',  '/exit',                  'UserController::logout'],
	['POST', '/api/[*:action]',        'Api::post'],

]);

$match = $router->match();

if ($match && is_callable($match['target'])) {

	call_user_func_array($match['target'], $match['params']);

} else {

	header("Location: " . PATH);
	exit;

}

?>