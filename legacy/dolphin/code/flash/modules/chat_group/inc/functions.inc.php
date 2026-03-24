<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

function getChatUserInfo($sId)
{
	$aUserInfo = getUserInfo($sId);
	if(empty($aUserInfo['id']))
	{
		$aUser = getArray("SELECT * FROM `" . MODULE_DB_PREFIX . "CurrentUsers` WHERE `User`='" . $sId . "' LIMIT 1");
		$aUserInfo = array("id" => $sId, "nick" => $aUser['Nick'], "sex" => $aUser['Sex'], "age" => $aUser['Age'], "desc" => $aUser['Desc'], "photo" => $aUser['Photo'], "profile" => $aUser['Profile']);
	}
	return $aUserInfo;
}

function initUser($aUser)
{
    $aProfile = getArray("SELECT * FROM `" . MODULE_DB_PREFIX . "Profiles` WHERE `ID`='" . $aUser['id'] . "'");
    if(!is_array($aProfile) || count($aProfile) == 0)
        getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Profiles` SET `ID`='" . $aUser['id'] . "', `Type`='" . CHAT_TYPE_FULL . "', `Smileset`='" . $sDefSmileset . "'");
    else 
		$aUser['type'] = $aProfile["Type"];
    $iCurrentTime = time();
    getResult("REPLACE `" . MODULE_DB_PREFIX . "CurrentUsers` SET `ID`='" . $aUser['id'] . "', `Nick`='" . $aUser['nick'] . "', `Sex`='" . $aUser['sex'] . "', `Age`='" . $aUser['age'] . "', `Desc`='" . addslashes($aUser['desc']) . "', `Photo`='" . $aUser['photo'] . "', `Profile`='" . $aUser['profile'] . "', `Start`='" . $iCurrentTime . "', `When`='" . $iCurrentTime . "', `Status`='" . USER_STATUS_NEW . "'");

    $rFiles = getResult("SELECT `ID` FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `Recipient`='" . $aUser['id'] . "' AND `Type`='file'");
    while($aFile = mysql_fetch_assoc($rFiles)) removeFile($aFile['ID']);
    return $aUser;
}

/**
 * Ban actions.
 * Check if this user is banned, ban this user, unban this user.
 * @param sSwitch - the name of the action which will be processed.
 * @param iId - user's identifier;
 */
function doBan($sSwitch, $sId = "0")
{
    global $sModule;

    switch($sSwitch) {
        case 'check': //--- check if user specified by ID is banned or not.
            return getValue("SELECT `Banned` FROM `" . MODULE_DB_PREFIX . "Profiles` WHERE `ID` = '" . $sId . "' LIMIT 1") == TRUE_VAL;

        case 'ban': //--- ban the user specified by ID.
            $sBan = TRUE_VAL;
            //break shouldn't be here
        case 'unban': //--- unban the user, specified by ID.
            $sBan = FALSE_VAL;
        default:
            $sUserId = getValue("SELECT `ID` FROM `" . MODULE_DB_PREFIX ."Profiles` WHERE `ID` = '" . $sId . "' LIMIT 1");
            $sSql = empty($sUserId)
                ? "INSERT INTO `" . MODULE_DB_PREFIX . "Profiles`(`ID`, `Banned`, `Type`) VALUES('" . $sId . "', '" . $sBan . "', '" . CHAT_TYPE_FULL . "')"
                : "UPDATE `" . MODULE_DB_PREFIX . "Profiles` SET `Banned`='" . $sBan . "' WHERE `ID`='" . $sId . "'";
            return getResult($sSql);
    }
}

function refreshUsersInfo($sId = "", $sMode = 'all')
{
    global $aXmlTemplates;
    global $sModule;

    $iUpdateInterval = (int)getSettingValue($sModule, "updateInterval");
    $iIdleTime = $iUpdateInterval * 3;
    $iDeleteTime = $iUpdateInterval * 6;

    $iCurrentTime = time();
    //--- refresh current user's track ---//
    getResult("UPDATE `" . MODULE_DB_PREFIX . "CurrentUsers` SET `Status`='" . USER_STATUS_OLD . "', `When`='" . $iCurrentTime . "' WHERE `ID`='" . $sId . "' AND `Status`<>'" . USER_STATUS_KICK . "' AND (`Status` NOT IN('" . USER_STATUS_NEW . "', '" . USER_STATUS_TYPE . "', '" . USER_STATUS_ONLINE . "') || (" . $iCurrentTime . "-`When`)>" . $iUpdateInterval . ") LIMIT 1");

    //--- refresh other users' states ---//
    getResult("UPDATE `" . MODULE_DB_PREFIX . "CurrentUsers` SET `When`=" . $iCurrentTime . ", `Status`='" . USER_STATUS_IDLE . "' WHERE `Status`<>'" . USER_STATUS_IDLE . "' AND `When`<=(" . ($iCurrentTime - $iIdleTime) . ")");
    $rFiles = getResult("SELECT `files`.`ID` AS `FileID` FROM `" . MODULE_DB_PREFIX . "Messages` AS `files` INNER JOIN `" . MODULE_DB_PREFIX . "CurrentUsers` AS `users` WHERE `files`.`Recipient`=`users`.`ID` AND `files`.`Type`='file' AND `users`.`Status`='" . USER_STATUS_IDLE . "' AND `users`.`When`<=" . ($iCurrentTime - $iDeleteTime));
    while($aFile = mysql_fetch_assoc($rFiles)) removeFile($aFile['FileID']);
    //--- delete idle users, whose track was not refreshed more than delete time ---//
    getResult("DELETE FROM `" . MODULE_DB_PREFIX . "CurrentUsers` WHERE `Status`='" . USER_STATUS_IDLE . "' AND `When`<=" . ($iCurrentTime - $iDeleteTime));
    
    //--- delete old messages ---//
    getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `Type`='text' AND `When`<=(" . ($iCurrentTime - $iDeleteTime) . ")");
    //--- Get information about users in the chat ---//
    switch($sMode) {
        case 'update':
            $rRes = getResult("SELECT ccu.`ID` AS `ID`, ccu.`Nick` AS `Nick`, ccu.`Sex` AS `Sex`, ccu.`Age` AS `Age`, ccu.`Desc` AS `Desc`, ccu.`Photo` AS `Photo`, ccu.`Profile` AS `Profile`, ccu.`Status` AS `Status`, ccu.`Online` AS `Online`, rp.`Type` AS `Type` FROM `" . MODULE_DB_PREFIX . "Profiles` AS rp, `" . MODULE_DB_PREFIX . "CurrentUsers` AS ccu WHERE rp.`ID`=ccu.`ID` ORDER BY ccu.`When`");
            while($aUser = mysql_fetch_assoc($rRes)) {
                if($aUser['ID'] == $sId && !($aUser['Status'] == USER_STATUS_KICK || $aUser['Status'] == USER_STATUS_TYPE)) continue;
                switch($aUser['Status']) {
                    case USER_STATUS_NEW:
                        $sContent .= parseXml($aXmlTemplates['user'], $aUser['ID'], $aUser['Status'], $aUser['Nick'], $aUser['Sex'], $aUser['Age'], stripslashes($aUser['Desc']), $aUser['Photo'], $aUser['Profile'], $aUser['Type'], $aUser['Online']);
                        break;
                    case USER_STATUS_TYPE:
                        $sContent .= parseXml($aXmlTemplates['user'], $aUser['ID'], $aUser['Status'], $aUser['Type']);
                        break;
                    case USER_STATUS_ONLINE:
                        $sContent .= parseXml($aXmlTemplates['user'], $aUser['ID'], $aUser['Status'], $aUser['Type'], $aUser['Online']);
                        break;
                    case USER_STATUS_IDLE:
                    case USER_STATUS_KICK:
                        $sContent .= parseXml($aXmlTemplates['user'], $aUser['ID'], $aUser['Status']);
                        break;
                }
            }
            break;

        case 'all':
            $iRunTime = isset($_REQUEST['_t']) ? floor($_REQUEST['_t']/1000) : 0;
            $iCurrentTime -= $iRunTime;
            $rRes = getResult("SELECT ccu.`ID` AS `ID`, ccu.`Nick` AS `Nick`, ccu.`Sex` AS `Sex`, ccu.`Age` AS `Age`, ccu.`Desc` AS `Desc`, ccu.`Photo` AS `Photo`, ccu.`Profile` AS `Profile`, ccu.`Online` AS `Online`, rp.`Type` AS `Type`, (" . $iCurrentTime . "-`ccu`.`Start`) AS `Time` FROM `" . MODULE_DB_PREFIX . "Profiles` AS rp, `" . MODULE_DB_PREFIX . "CurrentUsers` AS ccu WHERE rp.`ID`=ccu.`ID` AND ccu.`Status` NOT IN ('" . USER_STATUS_IDLE . "', '" . USER_STATUS_KICK . "') AND rp.`Banned`='" . FALSE_VAL . "' ORDER BY ccu.`When`");
            while($aUser = mysql_fetch_assoc($rRes))
                $sContent .= parseXml($aXmlTemplates['user'], $aUser['ID'], USER_STATUS_NEW, $aUser['Nick'], $aUser['Sex'], $aUser['Age'], stripslashes($aUser['Desc']), $aUser['Photo'], $aUser['Profile'], $aUser['Type'], $aUser['Online'], $aUser['Time']);
            break;
    }
    return makeGroup($sContent, "users");
}

function removeFile($sFileId)
{
    global $sFilesPath;
    @getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `ID`='" . $sFileId . "'");
    @unlink($sFilesPath . $sFileId . ".file");
}
