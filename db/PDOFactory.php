<?php 

class PDOFactory {
	
	public static function GetPDO($strDSN, $strUser, $strPass, $arParms) {
		$strKey = md5(serialize(array($strDSN, $strUser, $strPass, $arParms)));
		if (!isset($GLOBALS["PDOS"][$strKey])) {
			try {
				$GLOBALS["PDOS"][$strKey] = new PDO($strDSN, $strUser,$strPass, $arParms);
			} catch (PDOException $e) {
				return false;
			}
			
		}
		return ($GLOBALS["PDOS"][$strKey]);
	}
}
?>