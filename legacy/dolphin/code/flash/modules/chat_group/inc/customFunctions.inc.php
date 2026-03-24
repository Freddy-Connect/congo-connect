<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

require_once(BX_DIRECTORY_PATH_INC . "utils.inc.php");
require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstallerUtils.php");
/*
function getAdminIds()
{
    $rResult = getResult("SELECT `ID` FROM `Profiles` WHERE (`Role` & 2)");
    $aIds = array();
    for($i=0; $i<mysql_num_rows($rResult); $i++) {
        $aId = mysql_fetch_assoc($rResult);
        $aIds[] = (int)$aId['ID'];
    }
    return $aIds;
}

function isUserAdmin($iId)
{
    $aIds = getAdminIds();
    return in_array((int)$iId, $aIds);
}*/

function getUserVideoLink()
{
    global $sModulesUrl;
    if(BxDolInstallerUtils::isModuleInstalled("videos"))
        return $sModulesUrl . "video/videoslink.php?id=#user#";
    return "";
}

function getUserMusicLink()
{
    global $sModulesUrl;
    if(BxDolInstallerUtils::isModuleInstalled("sounds"))
        return $sModulesUrl . "mp3/soundslink.php?id=#user#";
    return "";
}

function getGroupInfo($sGroupId)
{
    $aGroup = getArray("SELECT * FROM `bx_groups_main` WHERE `ID`='" . $sGroupId . "'");
    if(!is_array($aGroup) || count($aGroup) == 0) $aGroup = array();
    return $aGroup;
}

function getGroupName($sGroupId)
{
    $aGroup = getGroupInfo($sGroupId);
    return isset($aGroup['title']) ? $aGroup['title'] : "";
}

function isGroupOwner($sGroupId, $sMemberId)
{
    $aGroup = getGroupInfo($sGroupId);
    return isset($aGroup['author_id']) && $aGroup['author_id'] == $sMemberId;
}

function isGroupMember($sGroupId, $sMemberId)
{
    $rResult = getResult("SELECT * FROM `bx_groups_fans` WHERE `id_profile`='" . $sMemberId . "' AND `id_entry` = '" . $sGroupId . "' AND `confirmed`=1");
    return mysql_num_rows($rResult) > 0;
}
