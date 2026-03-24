<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

$sId = isset($_REQUEST['id']) ? process_db_input($_REQUEST['id']) : "";
$aIds = explode("_", $sId);
$sGroupId = $aIds[0];
$sUserId = $aIds[1];

$sNick = isset($_REQUEST['nick']) ? process_db_input($_REQUEST['nick']) : "";
$sPassword = isset($_REQUEST['password']) ? process_db_input($_REQUEST['password']) : "";
$sType = isset($_REQUEST['type']) ? process_db_input($_REQUEST['type']) : "";
$sOnline = isset($_REQUEST['online']) ? process_db_input($_REQUEST['online']) : USER_STATUS_ONLINE;

$sMsg = isset($_REQUEST['msg']) ? process_db_input($_REQUEST['msg']) : "";
$sSmileset = isset($_REQUEST['smileset']) ? process_db_input($_REQUEST['smileset']) : "";
$sSender = $_REQUEST['sender'] ? process_db_input($_REQUEST['sender']) : "";
$sRcp = $_REQUEST['recipient'] ? (int)$_REQUEST['recipient'] : "";
$sMessage = isset($_REQUEST['message']) ? process_db_input($_REQUEST['message']) : "";

$iRoomId = isset($_REQUEST['roomId']) ? (int)$_REQUEST['roomId'] : 0;
$sRoom = isset($_REQUEST['room']) ? process_db_input($_REQUEST['room']) : "";
$sDesc = isset($_REQUEST['desc']) ? process_db_input($_REQUEST['desc']) : "";

$sParamName = isset($_REQUEST['param']) ? process_db_input($_REQUEST['param']) : "";
$sParamValue = isset($_REQUEST['value']) ? process_db_input($_REQUEST['value']) : "";

$sSkin = isset($_REQUEST['skin']) ? process_db_input($_REQUEST['skin']) : "";
$sLanguage = isset($_REQUEST['language']) ? process_db_input($_REQUEST['language']) : "english";

switch ($sAction) {
    case 'getPlugins':
        $sFolder = isset($_REQUEST["app"]) && $_REQUEST["app"] == "admin" ? "/pluginsAdmin/" : "/plugins/";
        $sContents = "";
        $sPluginsPath = $sModulesPath . $sModule . $sFolder;
        if(is_dir($sPluginsPath)) {
            if($rDirHandle = opendir($sModulesPath . $sModule . $sFolder))
                while(false !== ($sPlugin = readdir($rDirHandle)))
                    if(strpos($sPlugin, ".swf") === strlen($sPlugin)-4)
                        $sContents .= parseXml(array(1 => '<plugin><![CDATA[#1#]]></plugin>'), $sModulesUrl . $sModule . $sFolder . $sPlugin);
            closedir($rDirHandle);
        }
        $sContents = makeGroup($sContents, "plugins");
        break;

    /**
    * gets skins
    */
    case 'getSkins':
        $sContents = printFiles($sModule, "skins", false, true);
        break;

    /**
    * Sets default skin.
    */
    case 'setSkin':
        setCurrentFile($sModule, $sSkin, "skins");
        break;

    /**
    * gets languages
    */
    case 'getLanguages':
        $sContents = printFiles($sModule, "langs", false, true);
        break;

    /**
    * Sets default language.
    */
    case 'setLanguage':
        setCurrentFile($sModule, $sLanguage, "langs");
        break;

    /**
    * Get chat's config.
    */
    case 'config':
        $sFileName = $sModulesPath . $sModule . "/xml/config.xml";
        $rHandle = fopen($sFileName, "rt");
        $sContents = fread($rHandle, filesize($sFileName)) ;
        fclose($rHandle);

        $iFileSize = (int)getSettingValue($sModule, "fileSize");
        $iMaxFileSize = min((ini_get('upload_max_filesize') + 0), (ini_get('post_max_size') + 0), $iFileSize);
        $sContents = str_replace("#fileMaxSize#", $iMaxFileSize, $sContents);
        $sContents = str_replace("#userVideo#", getUserVideoLink(), $sContents);
        $sContents = str_replace("#userMusic#", getUserMusicLink(), $sContents);
        $sContents = str_replace("#soundsUrl#", $sSoundsUrl, $sContents);
        $sContents = str_replace("#smilesetsUrl#", $sSmilesetsUrl, $sContents);
        $sContents = str_replace("#filesUrl#", $sFilesUrl, $sContents);
        $sContents = str_replace("#serverUrl#", $sServerUrl, $sContents);
        $sContents = str_replace("#loginUrl#", $sRootURL . "member.php", $sContents);
		
		$aRoom = getGroupInfo($sGroupId);
        $sTitle = isset($aRoom['title']) ? $aRoom['title'] : "Untitled";
        $sContents = str_replace("#groupName#", $sTitle, $sContents);
        break;
		
    case 'RayzFontSet':
        $sKey = isset($_REQUEST['key']) ? $_REQUEST['key'] : "";
        $sValue = isset($_REQUEST['value']) ? $_REQUEST['value'] : "";
        if(empty($sKey) || $sValue == "") break;
        setCookie("RayzFont" . $sKey, $sValue, time() + 31536000);
        break;

    case 'RayzFontGet':
        $aSettings = array (
            8 => '<settings bold="#1#" italic="#2#" underline="#3#" color="#4#" font="#5#" size="#6#" volume="#7#" muted="#8#" />'
        );
        $sContents = parseXml($aSettings, $_COOKIE["RayzFontbold"], $_COOKIE["RayzFontitalic"], $_COOKIE["RayzFontunderline"], $_COOKIE["RayzFontcolor"], $_COOKIE["RayzFontfont"], $_COOKIE["RayzFontsize"], $_COOKIE["RayzFontvolume"], $_COOKIE["RayzFontmuted"]);
        break;

    /**
    * Authorize user.
    */
	case 'userAuthorize':
        $bOwner = isGroupOwner($sGroupId, $sUserId);
        if(!$bOwner && !isGroupMember($sGroupId, $sUserId))
        {
            $sContents = parseXml($aXmlTemplates['result'], "msgGroupJoin", FAILED_VAL);
            break;
        }
        if(loginUser($sUserId, $sPassword) == TRUE_VAL && ($bBanned = doBan("check", $sId)) != TRUE)
        {
            $aUser = getUserInfo($sUserId);
            $aUser['id'] = $sId;
            $aUser['sex'] = $aUser['sex'] == 'female' ? "F" : "M";
        }
        else
        {
            $sContents = parseXml($aXmlTemplates['result'], $bBanned ? "msgBanned" : "msgUserAuthenticationFailure", FAILED_VAL);
            break;
        }
		getResult("DELETE FROM `" . MODULE_DB_PREFIX . "CurrentUsers` WHERE `User`='" . $sId . "' LIMIT 1");
        $aUser = initUser($aUser);
        if($bOwner)
            $aUser['type'] = CHAT_TYPE_ADMIN;
		else if(empty($aUser['type']))
			$aUser['type'] = CHAT_TYPE_FULL;
        $sContents = parseXml($aXmlTemplates['result'], "", SUCCESS_VAL);
		$sContents .= parseXml($aXmlTemplates['user'], $aUser['id'], USER_STATUS_NEW, $aUser['nick'], $aUser['sex'], $aUser['age'], $aUser['desc'], $aUser['photo'], $aUser['profile'], $aUser['type'], USER_STATUS_ONLINE);
        break;

    case 'banUser':
        $sBanned = isset($_REQUEST["banned"]) ? process_db_input($_REQUEST['banned']) : FALSE_VAL;
        $sUserId = getValue("SELECT `ID` FROM `" . MODULE_DB_PREFIX ."Profiles` WHERE `ID` = '" . $sId . "' LIMIT 1");
        getResult(empty($sUserId)
            ? "INSERT INTO `" . MODULE_DB_PREFIX . "Profiles`(`ID`, `Banned`) VALUES('" . $sId . "', '" . $sBanned . "')"
            : "UPDATE `" . MODULE_DB_PREFIX . "Profiles` SET `Banned`='" . $sBanned . "' WHERE `ID`='" . $sId . "'");
        break;

    case 'kickUser':
        getResult("UPDATE `" . MODULE_DB_PREFIX . "CurrentUsers` SET `Status`='" . USER_STATUS_KICK . "', `When`='" . time() . "' WHERE `ID`='" . $sId . "'");
        break;

    case 'changeUserType':
        $sUserId = getValue("SELECT `ID` FROM `" . MODULE_DB_PREFIX ."Profiles` WHERE `ID` = '" . $sId . "' LIMIT 1");
        getResult(empty($sUserId)
            ? "INSERT INTO `" . MODULE_DB_PREFIX . "Profiles`(`ID`, `Type`) VALUES('" . $sId . "', '" . $sType . "')"
            : "UPDATE `" . MODULE_DB_PREFIX . "Profiles` SET `Type`='" . $sType . "' WHERE `ID`='" . $sId . "'");
        break;

    case 'searchUser':
        $sContents = parseXml($aXmlTemplates['result'], "No User Found.", FAILED_VAL);
        $sUserId = searchUser($sParamValue, $sParamName);
        if(empty($sUserId)) break;
        
        $aUser = getUserInfo($sUserId);
        $aUser['sex'] = $aUser['sex'] == "female" ? "F" : "M";
        $aProfile = getArray("SELECT * FROM `" . MODULE_DB_PREFIX ."Profiles` WHERE `ID` = '" . $sGroupId . "_" . $sUserId . "' LIMIT 1");
        if(!is_array($aProfile) || count($aProfile) == 0) $aProfile = array("Banned" => FALSE_VAL, "Type" => CHAT_TYPE_FULL);
        
        $sContents = parseXml($aXmlTemplates['result'], "", SUCCESS_VAL);
        $sContents .= parseXml($aXmlTemplates['user'], $sGroupId . "_" . $sUserId, $aUser['nick'], $aUser['sex'], $aUser['age'], $aUser['photo'], $aUser['profile'], $aProfile['Banned'], $aProfile['Type']);
        break;

    /**
    * Get sounds
    */
    case 'getSounds':
        $sFileName = $sModulesPath . $sModule . "/xml/sounds.xml";
        if(file_exists($sFileName)) {
            $rHandle = fopen($sFileName, "rt");
            $sContents = fread($rHandle, filesize($sFileName));
            fclose($rHandle);
        } else $sContents = makeGroup("", "items");
        break;

    /**
    * gets smilesets
    */
    case 'getSmilesets':
        $sConfigFile = "config.xml";
        $sContents = parseXml($aXmlTemplates['smileset'], "", "") . makeGroup("", "smilesets");
        $aSmilesets = array();
        if($rDirHandle = opendir($sSmilesetsPath))
            while(false !== ($sDir = readdir($rDirHandle)))
                if($sDir != "." && $sDir != ".." && is_dir($sSmilesetsPath . $sDir) && file_exists($sSmilesetsPath . $sDir . "/" . $sConfigFile))
                    $aSmilesets[] = $sDir;
        closedir($rDirHandle);
        if(count($aSmilesets) == 0) break;

        if(isset($_COOKIE["RayzFontsmileset"]))
            $sDefSmileset = substr($_COOKIE["RayzFontsmileset"], 0, -1);
        if(!in_array($sDefSmileset, $aSmilesets))
            $sDefSmileset = $aSmilesets[0];
        $sUserSmileset = getValue("SELECT `Smileset` FROM `" . MODULE_DB_PREFIX . "Profiles` WHERE `ID`='" . $sId . "'");
        if(empty($sUserSmileset) || !file_exists($sSmilesetsPath . $sUserSmileset)) $sUserSmileset = $sDefSmileset;

        $sContents = parseXml($aXmlTemplates['smileset'], $sUserSmileset . "/", $sSmilesetsUrl);
        $sData = "";
        for($i=0; $i<count($aSmilesets); $i++) {
            $sName = getSettingValue(GLOBAL_MODULE, "name", "config", false, $sDataDir . $sSmilesetsDir . $aSmilesets[$i]);
            $sData .= parseXml($aXmlTemplates['smileset'], $aSmilesets[$i] . "/", $sConfigFile, empty($sName) ? $aSmilesets[$i] : $sName);
        }
        $sContents .= makeGroup($sData, "smilesets");
        break;

    /**
    * Sets default smileset.
    */
    case 'setSmileset':
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Profiles` SET `Smileset`='" . $sSmileset . "' WHERE `ID`='" . $sId . "'");
        break;

    case 'getOnlineUsers':
        //--- Check RayChatMessages table and drop autoincrement if it is possible. ---//
        $rResult = getResult("SELECT `ID` FROM `" . MODULE_DB_PREFIX . "CurrentUsers`");
        if(mysql_num_rows($rResult) == 0) getResult("TRUNCATE TABLE `" . MODULE_DB_PREFIX . "CurrentUsers`");
        $rResult = getResult("SELECT `ID` FROM `" . MODULE_DB_PREFIX . "Messages`");
        if(mysql_num_rows($rResult) == 0) getResult("TRUNCATE TABLE `" . MODULE_DB_PREFIX . "Messages`");
        //--- Update user's info and return info about all online users. ---//
        $sContents = refreshUsersInfo($sId);
        break;
		
	    /**
    *	set user online status
    */
    case 'setOnline':
        getResult("UPDATE `" . MODULE_DB_PREFIX . "CurrentUsers` SET `Online`='" . $sOnline . "', `When`='" . time() . "', `Status`='" . USER_STATUS_ONLINE . "' WHERE `ID`='" . $sId . "'");
        break;

    /**
    * Check for chat changes: new users, rooms, messages.
    * Note. This action is used in XML mode and by ADMIN.
    */
    case 'update':
        $sFiles = "";
        $res = getResult("SELECT * FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `Type`='file' AND `Recipient`='" . $sId . "'");
        while($aFile = mysql_fetch_assoc($res)) {
            $sFileName = $aFile['ID'] . ".file";
            if(!file_exists($sFilesPath . $sFileName)) continue;
            $sFiles .= parseXml($aXmlTemplates['file'], $aFile['Sender'], $sFileName, $aFile['Message'], $aFile['Count']);
        }
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `Type`='file' AND `Recipient`='" . $sId . "'");
        $sContents = makeGroup($sFiles, "files");

        //--- update user's info ---//
        $sContents .= refreshUsersInfo($sId, 'update');
        //--- check for new rooms ---//
        $sContents .= makeGroup("", "rooms");
        $sContents .= makeGroup("", "roomsUsers");
        //$sContents .= makeGroup(getRooms('updateUsers', $sId), "roomsUsers");

        //--- check for new messages ---//
        $iUpdateInterval = (int)getSettingValue($sModule, "updateInterval");
        $sMsgs = "";
        $sSql = "SELECT * FROM `" . MODULE_DB_PREFIX . "Messages` WHERE `Type`='text' AND `Sender`<>'" . $sId . "' AND ((`Room`=" . $sGroupId . " AND `Whisper`='" . FALSE_VAL . "') OR `Recipient`='" . $sId . "') AND `When`>='" . (time() - $iUpdateInterval) . "' ORDER BY `ID`";
        $res = getResult($sSql);
        while($aMsg = mysql_fetch_assoc($res)) {
            $aStyle = unserialize($aMsg['Style']);
            $sMsgs .= parseXml($aXmlTemplates['message'], $aMsg['ID'], stripslashes($aMsg['Message']), $aMsg['Room'], $aMsg['Sender'], $aMsg['Recipient'], $aMsg['Whisper'], $aStyle['color'], $aStyle['bold'], $aStyle['underline'], $aStyle['italic'], $aStyle['size'], $aStyle['font'], $aStyle['smileset'], $aMsg['When'], $aMsg['Count']);
        }
        $sContents .= makeGroup($sMsgs, "messages");
        break;

    /**
    * Add message to database.
    */
    case 'newMessage':
        if(empty($sSender)) break;
        $sWhisper = isset($_REQUEST['whisper']) ? process_db_input($_REQUEST['whisper']) : FALSE_VAL;
        $iCount = $_REQUEST['count'] ? (int)$_REQUEST['count'] : 0;
        $sColor = $_REQUEST['color'] ? (int)$_REQUEST['color'] : 0;
        $sBold = $_REQUEST['bold'] ? process_db_input($_REQUEST['bold']) : FALSE_VAL;
        $sUnderline = $_REQUEST['underline'] ? process_db_input($_REQUEST['underline']) : FALSE_VAL;
        $sItalic = $_REQUEST['italic'] ? process_db_input($_REQUEST['italic']) : FALSE_VAL;
        $iSize = $_REQUEST['size'] ? (int)$_REQUEST['size'] : 12;
        $sFont = $_REQUEST['font'] ? process_db_input($_REQUEST['font']) : "Arial";
        $sStyle = serialize(array('color' => $sColor, 'bold' => $sBold, 'underline' => $sUnderline, 'italic' => $sItalic, 'smileset' => $sSmileset, 'size' => $iSize, 'font' => $sFont));
        getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Messages`(`Room`, `Count`, `Sender`, `Recipient`, `Message`, `Whisper`, `Style`, `When`) VALUES('" . $iRoomId . "', '" . $iCount . "', '" . $sSender . "', '" . $sRcp . "', '" . $sMessage . "', '" . $sWhisper . "', '" . $sStyle . "', '" . time() . "')");
        break;
	
	/**
    * Get rooms.
    */
    case 'getRooms':
        $aRoom = getGroupInfo($sGroupId);
        $sTitle = isset($aRoom['title']) ? $aRoom['title'] : "Untitled";
        $sDesc = str_replace("#group#", $sTitle, getSettingValue($sModule, "txtRoomDesc", "english", false, "langs"));
        $sOwner = isset($aRoom['author_id']) ? $aRoom['author_id'] : "0";
		$iCurrentTime = time();
		$rRes = getResult("SELECT * FROM `" . MODULE_DB_PREFIX . "CurrentUsers` WHERE `ID` LIKE '" . $sGroupId . "_%' AND `ID`<>'" . $sId . "' AND `Status` IN ('" . USER_STATUS_NEW . "','" . USER_STATUS_OLD . "') AND `When`>=" . ($iCurrentTime - 2*(int)getSettingValue($sModule, "updateInterval")));
		$aUsers = array();
		$aTimes = array();
		for($i=0; $i<mysql_num_rows($rRes); $i++)
		{
			$aUser = mysql_fetch_assoc($rRes);
			$aUsers[] = $aUser['ID'];
			$aTimes[] = $iCurrentTime - $aUser['Start'];
		}
        $sRooms = parseXml($aXmlTemplates['room'], $sGroupId, $sOwner, FALSE_VAL, stripslashes($sTitle), $sDesc, count($aUsers)>0 ? implode(',', $aUsers) : "", count($aTimes)>0 ? implode(',', $aTimes) : "");
        $sContents = makeGroup($sRooms, "rooms");
        break;

    /**
    * Creats new room.
    * Note. This action is used in both modes and by admin.
    */
    case 'createRoom':
        $sContents = parseXml($aXmlTemplates['result'], "msgErrorCreatingRoom", FAILED_VAL);
        break;

    case 'editRoom':
        $sContents = parseXml($aXmlTemplates['result'], "", SUCCESS_VAL);
        break;

    /**
    * Delete room from database.
    * Note. This action is used in both modes and by admin.
    */
    case 'deleteRoom':
        $sContents = parseXml($aXmlTemplates['result'], TRUE_VAL);
        break;

    case 'checkRoomPassword':
        $sContents = parseXml($aXmlTemplates['result'], "", SUCCESS_VAL);
        break;

    case 'uploadFile':
        if(empty($sSender)) break;
        if(is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $sFilePath = $sFilesPath . $sSender . ".temp";
            @unlink($sFilePath);
            move_uploaded_file($_FILES['Filedata']['tmp_name'], $sFilePath);
            @chmod($sFilePath, 0644);
        }
        break;

    case 'initFile':
        $sFilePath = $sFilesPath . $sSender . ".temp";
        $sContents = parseXml($aXmlTemplates['result'], "msgErrorUpload", FAILED_VAL);
        if(empty($sSender) || !file_exists($sFilePath) || filesize($sFilePath) == 0) break;

		$iCount = $_REQUEST['count'] ? (int)$_REQUEST['count'] : 0;
        getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Messages`(`Count`, `Sender`, `Recipient`, `Message`, `Type`, `When`) VALUES('" . $iCount . "', '" . $sSender . "', '" . $sRcp . "', '" . $sMessage . "', 'file', '" . time() . "')");
        $sFileName = getLastInsertId() . ".file";
        if(!@rename($sFilePath, $sFilesPath . $sFileName)) break;

        $sContents = parseXml($aXmlTemplates['result'], $sFileName, SUCCESS_VAL);
        break;

    case 'removeFile':
        $sId = str_replace(".file", "", $sId);
        removeFile($sId);
        break;

    case 'help':
        $sApp = isset($_REQUEST['app']) ? process_db_input($_REQUEST['app']) : "user";
        $sContents = makeGroup("", "topics");
        $sFileName = $sModulesPath . $sModule . "/help/" . $sApp . ".xml";
        if(file_exists($sFileName)) {
            $rHandle = @fopen($sFileName, "rt");
            $sContents = @fread($rHandle, filesize($sFileName)) ;
            fclose($rHandle);
        }
        break;
}
