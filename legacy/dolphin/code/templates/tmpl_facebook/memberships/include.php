<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/

bx_import('BxBaseMenu');
$iId = $_COOKIE['memberID'];
$menu = new BxBaseMenu;
$menu->getMenuInfo();
$iCurrentUserId = $menu->aMenuInfo['memberID'];
$iTopMenuItem = $menu->aMenuInfo['currentTop'];
$iCurrentMenuItem = ($menu->aMenuInfo['currentCustom'] == '-1') ? $menu->aMenuInfo['currentTop'] : $menu->aMenuInfo['currentCustom'];
bx_import('BxDolAlerts');
$sCurUrl = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$oZ = new BxDolAlerts('subs', 'cur_page', $iCurrentMenuItem, $iCurrentUserId, array('current_url' => $sCurUrl));
$oZ->alert();