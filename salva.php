<?php

include ("boot.php");
include ("func.php");

$entityBody = json_decode(file_get_contents('php://input'), true);

//print_r($entityBody);
//die(); 
 
$tabella = "movie";
$get = $_GET;
$post = $entityBody;
$id_genere = $id_regista = 0;


// Da utilizzare in caso di utilizzo tabella di test: impostare parametro 'test' in get
if (isset($get['test']) && $get['test'] == 1)
{
	$tabella = "film4";
}

// GENERE
if (isset($post['genere']) && !empty($post['genere'])) 
{
	// Se $post['genere'] è un array si tratta di un genere già presente nel db
	if (is_array($post['genere']))
	{
		$id_genere = $post['genere']['id'];
	}
	else
	{
		// Inserisco il nuovo genere
		$id_genere = salvaGenere($post['genere'], $db);
	}
}

// REGISTA
if (isset($post['regista']) && !empty($post['regista']))
{
	// Se $post['regista'] è un array si tratta di un regista già presente nel db
	if (is_array($post['regista']))
	{
		$id_regista = $post['regista']['id'];
	} 
	else 
	{
		// Inserisco il nuovo regista
		$id_regista = salvaRegista($post['regista'], $db);
	}
}

// Inserimento database
$q = "INSERT INTO {$tabella} 
		(titolo, sottotitolo, titolo_originale, id_genere, id_regista, supporto, data_uscita, cast, trama, durata) 
	  VALUES 
	  	(:titolo, :sottotitolo, :titolo_originale, :genere, :regista, :supporto, :data_uscita, :cast, :trama, :durata)";
$st = $db->prepare($q);
$st->bindValue(":titolo", $post['titolo'], PDO::PARAM_STR);
$st->bindValue(":sottotitolo", $post['sottotitolo'], PDO::PARAM_STR);
$st->bindValue(":titolo_originale", $post['titolo_originale'], PDO::PARAM_STR);
$st->bindValue(":genere", $id_genere, PDO::PARAM_INT);
$st->bindValue(":regista", $id_regista, PDO::PARAM_INT);
$st->bindValue(":supporto", $post['supporto'], PDO::PARAM_STR);
$st->bindValue(":data_uscita", $post['data'], PDO::PARAM_STR);
$st->bindValue(":cast", $post['cast'], PDO::PARAM_STR);
$st->bindValue(":trama", $post['trama'], PDO::PARAM_STR);
$st->bindValue(":durata", $post['durata'], PDO::PARAM_STR);
$st->execute();

$output = array();
if ($st->rowCount() > 0)
{
	$id = $db->lastInsertId();
	$output = array('status' => 'ok', 'id' => $id);
} else {
	$output = array('status' => 'error', 'id' => -1);
	//echo ("INSERT INTO {$tabella} (titolo, id_genere, id_regista, supporto) VALUES ('{$post['titolo']}', {$id_genere}, {$id_regista}, '{$post['supporto']}')");
}

echo json_encode($output);
?>
