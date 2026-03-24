<?php

require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
 
if (isAdmin()) {

    $iAdminId = getLoggedId();
 
	$iProfileId = bx_get('ID');
	$sProfileLink = getProfileLink($iProfileId);
	bx_login ((int)$iProfileId); // autologin here


echo '
<html>
<head></head> 
<frameset rows="34,*" framespacing="0" border="0" frameborder="0">
	<frame name="topframe" scrolling="no" noresize target="main" src="top.php?ID=' . $iAdminId . '" marginwidth="1" marginheight="1">
	<frame name="bottomframe" src="'. $sProfileLink .'" marginwidth="1" marginheight="1" scrolling="auto" noresize>
	<noframes><body><p>This page uses frames, but your browser does not support them.</p></body></noframes>
</frameset>
</html>
';
 
} else { 
	$_page['name_index'] = 0;
	$_page['header'] = $site['title'];
	$_page['header_text'] = $site['title'];
	$_page_cont[0]['page_main_code'] = MsgBox(_t('_Access denied'));
	PageCode();
}
