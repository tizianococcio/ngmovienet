<?php

include ("db/Database.php");
$db = Database::Load();
$film = array();


$q = "SELECT id, supporto, tipo_supporto, titolo FROM movie ORDER BY titolo";
$st = $db->prepare($q);
$st->execute();

while ($d = $st->fetchObject())
{
	$film[] = array(
				"id" => $d->id, 
				"posizione" => $d->supporto,
				"supporto" => $d->tipo_supporto,
				"titolo" => utf8_encode($d->titolo)
			  );
}

$dati = $film;
echo json_encode($dati);



?>