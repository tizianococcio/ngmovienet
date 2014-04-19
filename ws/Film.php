<?php

class Film
{

	private $_db;

	public function __construct($db)
	{
		$this->_db = $db;
	}

	public function salvaGenere($genere)
	{
		$id = 0;
		
		// CONTROLLO
		$st = $this->_db->prepare("SELECT id FROM Genre WHERE name = :g");
		$st->bindValue(":g", $genere, PDO::PARAM_STR);
		$st->execute();
		if ($st->rowCount() > 0)
		{
			$id = $st->fetchObject()->id;
		} else {
			// INSERIMENTO
			$st = $this->_db->prepare("INSERT INTO Genre (name) VALUES (:g)");
			$st->bindValue(":g", $genere, PDO::PARAM_STR);
			$st->execute();
			$id = $this->_db->lastInsertId();
		}
		return $id;
	}

	public function salvaRegista($regista)
	{
		$id = 0;
		
		// CONTROLLO
		$st = $this->_db->prepare("SELECT id FROM Director WHERE name = :r");
		$st->bindValue(":r", $regista, PDO::PARAM_STR);
		$st->execute();
		if ($st->rowCount() > 0)
		{
			$id = $st->fetchObject()->id;
		} else {
			// INSERIMENTO
			$st = $this->_db->prepare("INSERT INTO Director (name) VALUES (:r)");
			$st->bindValue(":r", $regista, PDO::PARAM_STR);
			$st->execute();
			$id = $this->_db->lastInsertId();
		}
		return $id;
	}	

	// Return true if the movie exists
	public function checkExistance($titolo)
	{
		$st = $this->_db->prepare("SELECT COUNT(id) AS c FROM movie WHERE titolo = :t");
		$st->bindValue(":t", $titolo, PDO::PARAM_STR);
		$st->execute();
		return ($st->fetchObject()->c > 0);
	}
}