<?php

require_once __DIR__.'/core/Jf.php';
require_once __DIR__.'/Rbac.php';

try
{
	if (DB_DRIVER == 'sqlite') {
		Jf::$Db = new PDO( DB_DRIVER.':'.DB_FILE );
	}
	else {
		Jf::$Db = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
	}
}
catch (PDOException $e)
{
	echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
	die();
}
