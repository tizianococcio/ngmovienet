<?php
/**
 * Funzioni
 */
 
function pulisci($t)
{
	return $t;
}
function npr($a)
{
	echo ("<pre>");
	print_r($a);
	echo ("</pre>");
}

function moviePlayerRequestHasData($data)
{
	$return = true;
	$return = (isset($data['error_message']));
	$num_film_trovati = count(array_pop($data));
	$return = ($num_film_trovati > 0);
	return $return;
}

function curlprovider($request)
{
		$endpoint = $request;
		$session = curl_init($endpoint);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($session);
		curl_close($session);
		$search_results = json_decode($data, true);
		if ($search_results === NULL) return 0;
		return $search_results;
}

function controlla($param)
{
	return (isset($param) && count($param) > 0);
}

function getLocandina($url)
{
	$filenameIn  = $url;
	$fileName = basename($filenameIn);
	$filenameOut = PERCORSO_IMMAGINI . $fileName;
	
	$contentOrFalseOnFailure   = file_get_contents($filenameIn);
	$byteCountOrFalseOnFailure = file_put_contents($filenameOut, $contentOrFalseOnFailure);	
	
	return (!$contentOrFalseOnFailure || !$byteCountOrFalseOnFailure)? FALSE : $fileName;
}

function salvaGenere($genere, $db)
{
	$id = 0;
	
	// CONTROLLO
	$st = $db->prepare("SELECT id FROM Genre WHERE name = :g");
	$st->bindValue(":g", $genere, PDO::PARAM_STR);
	$st->execute();
	if ($st->rowCount() > 0)
	{
		$id = $st->fetchObject()->id;
	} else {
		// INSERIMENTO
		$st = $db->prepare("INSERT INTO Genre (name) VALUES (:g)");
		$st->bindValue(":g", $genere, PDO::PARAM_STR);
		$st->execute();
		$id = $db->lastInsertId();
	}
	return $id;
}

function salvaRegista($regista, $db)
{
	$id = 0;
	
	// CONTROLLO
	$st = $db->prepare("SELECT id FROM Director WHERE name = :r");
	$st->bindValue(":r", $regista, PDO::PARAM_STR);
	$st->execute();
	if ($st->rowCount() > 0)
	{
		$id = $st->fetchObject()->id;
	} else {
		// INSERIMENTO
		$st = $db->prepare("INSERT INTO Director (name) VALUES (:r)");
		$st->bindValue(":r", $regista, PDO::PARAM_STR);
		$st->execute();
		$id = $db->lastInsertId();
	}
	return $id;
}

function setSearchedFor($id, $db)
{
	$db->query("UPDATE film SET searchedfor = 1 WHERE id = ".$id);
}
?>