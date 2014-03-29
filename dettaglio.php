<?php

include ("db/Database.php");
$db = Database::Load();

$url_locandina = "http://tiziano.patriziatrevisiartgallery.it/archivio-film/data/immagini/locandine/";

$id = 0;
if (isset($_GET['id']) && $_GET['id'] > 0)
{
	$id = $_GET['id'];
}

$film = array();

$q = "SELECT f.*, r.name AS regista, g.name AS genere 
		FROM movie f
		LEFT JOIN Genre g ON f.id_genere = g.id
		LEFT JOIN Director r ON f.id_regista = r.id 
		WHERE f.id = :id";
$st = $db->prepare($q);
$st->bindValue(":id", $id, PDO::PARAM_INT);
if ($st->execute())
{
	if ($st->rowCount() > 0)
	{
		$d = $st->fetchObject();
		$film = array(
			"id" => 			$d->id,
			"posizione" => 		prepareForJSON($d->supporto),
			"tipo_supporto" => 	prepareForJSON($d->tipo_supporto),
			"titolo" => 		prepareForJSON($d->titolo),
			"sotto_titolo" => 	prepareForJSON($d->sottotitolo),
			"regista" =>		prepareForJSON($d->regista),
			"genere" =>			prepareForJSON($d->genere),
			"cast" =>			prepareForJSON($d->cast),
			"data_uscita" =>	prepareForJSON($d->data_uscita),
			"locandina" =>		$d->locandina? prepareForJSON($url_locandina . $d->locandina) : 0,
			"trama" =>			prepareForJSON($d->trama),
			"durata" =>			prepareForJSON($d->durata)
		);
	}
}

echo json_encode($film);


function prepareForJSON($s)
{
	return ($s? utf8_encode($s) : "");
}

?>