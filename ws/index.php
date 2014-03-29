<?php
include ('slim_boot.php');

include ('../db/Database.php');

$app->db = Database::Load();

/**
 * Azione per pagine non trovate
 */
$app->notFound(function () use ($app) {
    $app->redirect($app->baseUrl);
});

/**
 * Invoked before each function
 */
$app->hook('slim.before', function () use ($app) {
	
});

$app->get('/', function() use ($app) {
    echo 'OK';
});

$app->get('/generi', function () use ($app) {
	$get = $app->request()->get('s');
	$st = $app->db->prepare("SELECT * FROM Genre WHERE name LIKE :name ORDER BY name LIMIT 8");
	$st->bindValue(":name", "%" . $get . "%", PDO::PARAM_STR);
	$st->execute();
	
	$data = array();
	while ($d = $st->fetchObject())
	{
		$data[] = array('id' => $d->id, 'nome' => utf8_encode($d->name));
	}
	
	echo json_encode($data);
});

$app->get('/registi', function () use ($app) {
	$get = $app->request()->get('s');
	$st = $app->db->prepare("SELECT * FROM Director WHERE name LIKE :name ORDER BY name LIMIT 8");
	$st->bindValue(":name", "%" . $get . "%", PDO::PARAM_STR);
	$st->execute();
	
	$data = array();
	while ($d = $st->fetchObject())
	{
		$data[] = array('id' => $d->id, 'nome' => utf8_encode($d->name));
	}
	
	echo json_encode($data);
});


$app->run();


?>