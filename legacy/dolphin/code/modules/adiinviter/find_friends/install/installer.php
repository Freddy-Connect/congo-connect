<?php
bx_import("BxDolInstaller");

class Adi_Dolphin_Installer extends BxDolInstaller
{
	function Adi_Dolphin_Installer($aConfig)
	{
		parent::BxDolInstaller($aConfig);
	}

	function uninstall($aParams)
	{
		$query = 'DESCRIBE profiles';
		$table_columns = array();
		if($res = mysql_query($query))
		{
			while($row = mysql_fetch_assoc($res))
			{
				$table_columns[] = $row['Field'];
			}
		}
		if(in_array('adi_num_invites', $table_columns))
		{
			$query = 'ALTER TABLE `profiles` DROP `adi_num_invites`';
			mysql_query($query);
		}
		parent::uninstall($aParams);
	}
}

?>