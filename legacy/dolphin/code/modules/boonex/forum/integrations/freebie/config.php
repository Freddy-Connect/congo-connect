<?php

/**
 *
 * Overwrite necessary variables or add new in this file
 *
 *******************************************************************************/

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../base/config.php');

$gConf['url']['base'] = $site['url'] . 'forum/freebie/';	// base url
$gConf['db']['prefix'] = 'modzzz_freebie_'; // tables names prefix

?>
