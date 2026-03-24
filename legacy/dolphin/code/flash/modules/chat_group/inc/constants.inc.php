<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

//--- User statuses ---//
if(!defined("USER_STATUS_NEW")) define("USER_STATUS_NEW", "new");
if(!defined("USER_STATUS_OLD")) define("USER_STATUS_OLD", "old");
if(!defined("USER_STATUS_KICK")) define("USER_STATUS_KICK", "kick");
if(!defined("USER_STATUS_IDLE")) define("USER_STATUS_IDLE", "idle");
if(!defined("USER_STATUS_TYPE")) define("USER_STATUS_TYPE", "type");

if(!defined("USER_STATUS_ONLINE")) define("USER_STATUS_ONLINE", "online");
if(!defined("USER_STATUS_BUSY")) define("USER_STATUS_BUSY", "busy");
if(!defined("USER_STATUS_AWAY")) define("USER_STATUS_AWAY", "away");

//--- Chat user types ---//
if(!defined("CHAT_TYPE_MODER")) define("CHAT_TYPE_MODER", "moder");
if(!defined("CHAT_TYPE_FULL")) define("CHAT_TYPE_FULL", "full");
if(!defined("CHAT_TYPE_ADMIN")) define("CHAT_TYPE_ADMIN", "admin");

$aInfo = array(
    'mode' => "as3",
    'title' => "Rayz A/V Group Chat",
    'version' => "2.0.0001",
    'code' => "rayzchatgroup_2.0.0000",
    'author' => "Rayz",
    'authorUrl' => "http://rayzzz.com/redirect.php?action=author"
);
$aModules = array(
    'adm' => array(
        'caption' => 'Group Chat Admin',
        'parameters' => array('id', 'password'),
        'js' => array(),
        'inline' => false,
        'vResizable' => true,
        'hResizable' => true,
        'reloadable' => true,
		'holder' => 'none',
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 600),
                                'minSize' => array('width' => 700, 'height' => 600),
        'div' => array()
    ),
    'user' => array(
        'caption' => 'Group Chat',
        'parameters' => array('id', 'password'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
		'holder' => 'none',
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 600),
                                'minSize' => array('width' => 700, 'height' => 600),
        'div' => array(),
    )
);
