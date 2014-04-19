<?php
session_cache_limiter(false);
session_start();

error_reporting(E_ALL);

/**
* Risolvere il problema di conflitto tra autoloaders!
* require ('libs/autoloader.php');
*/

require ('../libs/Slim/Slim.php');


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'mode' 		=> 'development',
    'debug' 	=> true,
));

$app->documentRoot		= $_SERVER['DOCUMENT_ROOT'];
$app->appRoot			= '/ngMovieNet';
$app->baseUrl 			= $app->baseUrl . '/ws';
$app->uploadFolder		= $app->documentRoot . $app->appRoot . '/uploads/';
$app->baseHttpPath 		= $app->environment['slim.url_scheme'] . '://' . $app->environment['HTTP_HOST'] . $app->baseUrl;

?>