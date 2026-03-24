<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

require_once BX_DIRECTORY_PATH_INC . 'profiles.inc.php';

check_logged();

bx_import('BxDolRequest');
BxDolRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
