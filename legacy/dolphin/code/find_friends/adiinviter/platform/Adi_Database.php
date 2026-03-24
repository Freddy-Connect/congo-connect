<?php

class Adi_Database_Platform extends Adi_Database_Base
{
	function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
	{
		if(mysql_ping())
		{
			$this->db_allowed = true;
			return true;
		}
		else {
			return false;
		}
	}
}

?>