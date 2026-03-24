<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolModule');

class NsysEmailAutoLoginModule extends BxDolModule {
	var $params;
	var $addKey;
	var $currentNick;
	
	function NsysEmailAutoLoginModule(&$aModule) {
			$this->params = array('mailId','userId','valid','rUrl');
			$this->addKey = getParam("EmailAutoLogin_hash");
			$this->currentNick = '';
			parent::BxDolModule($aModule);
	}
		
	function _redirectToCleanUrl(){
		global $site;
		
		$removeParams = $this->params;
		if(substr($site['url'],strlen($site['url'])-1,1) == '/'){
			$url = substr($site['url'],0,strlen($site['url'])-1).$_SERVER['REQUEST_URI'];
		}else{
			$url = $site['url'].$_SERVER['REQUEST_URI'];		
		}
		
		list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
		parse_str($qspart, $qsvars);

		foreach($removeParams as $varname){
			if(!isset($qsvars[$varname])) return;
			unset($qsvars[$varname]);
		}

		$newqs = http_build_query($qsvars);
		
		$target = $urlpart . ($newqs ? '?'.$newqs : "");
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' ); 
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( 'Location: '.$target );
		exit();

	}
	
	function _redirect($target){
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' ); 
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( 'Location: '.$target );
		exit();
	
	}
		
	function actionHome(){
		if($this->_init() < 0 || $_GET['rUrl'] == ''){
			$this->_redirect(BX_DOL_URL_ROOT.'member.php');	
		}else{
			$this->_redirect(urldecode($_GET['rUrl']));	
		}
	}
	
	function _init(){
		if(isset($_GET['rUrl']) && strpos($_GET['rUrl'],'profile_activate.php') !== false){
			$this->_redirect(urldecode($_GET['rUrl']));
			exit();	
		}
		if((isset($_COOKIE['memberID']) || isset($_COOKIE['memberPassword']))) return 0; // already logged in
		//uId = md5(username);
		//valid = timestamp
		//mailId = hash(sha256 , userId.md5($site['url']).md5(valid);
		//$params = array('mailId','userId','valid');
		foreach($this->params as $key=>&$val){
			if(!isset($_GET[$val]) || $_GET[$val] == ''){
				return -1;
			}
		}
		if(!is_numeric($_GET['valid'])) return -2;
		if(!$this->_checkMailHash($_GET['mailId'],$_GET['userId'],$_GET['valid'],$_GET['rUrl'])) return -3;
		if(time() > strtotime(getParam("EmailAutoLogin_expiry"),$_GET['valid'])) return -4;
		
		if(!preg_match('/^[a-f0-9]{32}$/', $_GET['userId'])) return -5;
		$iMemID = $this->_oDb->hash2userId($_GET['userId']);
		if(!$iMemID) return -6;
		
		bx_login((int)$iMemID);
		
		return 1;
	}
	
	function _checkMailHash(&$mailHash,&$userHash,&$valid,&$url){
		global $site;
		if($mailHash == hash('sha256',$userHash.md5($site['url']).md5($valid).$this->addKey.urldecode($url))){
			return true;
		}else{
			return false;
		}
	}
	
	function _genMailHash($userName,$valid,$url){
		global $site;
		return hash('sha256',md5($userName).md5($site['url']).md5($valid).$this->addKey.urldecode($url));
	}
	
	function _genUrl($redirUrl){
		$userName = $this->currentNick;
		$valid = time();
		$redirUrl = urlencode($redirUrl);
		$url = BX_DOL_URL_ROOT.'m/emailautologin/?';
		$url.= 'userId='.md5($userName).'&mailId='.$this->_genMailHash($userName,$valid,$redirUrl).'&valid='.$valid.'&rUrl='.$redirUrl;
		return $url;
	}

	function UrlReplace($a){
		return '<a'.$a[1].'href="'.$this->_genUrl($a[2]).'"'.$a[3].'>';
	}	

	function _replaceMailBody(&$mailBody,$userName){
		if(strpos($mailBody,'profile_activate.php') !== false) return false; // activation mails
		if(strlen(getParam('EmailAutoLogin_exclude')) != 0){
			$excludes = explode(',',getParam('EmailAutoLogin_exclude'));
			if(is_array($excludes)){
				foreach($excludes as $exclude){
					if(strpos($mailBody,trim($exclude)) !== false) return false; // activation mails					
				}
			}
		}

		$this->currentNick = $userName;
		$mailBody = preg_replace_callback('/<a(.*?)href="(.*?)"(.*?)>/is', array(&$this, 'UrlReplace'), $mailBody);
		return true;
	}
}

// add in profiles.inc.php:
/*
//fmc-start
if(getParam("EmailAutoLogin_activated") == 'on') {
	$oAutoLogin = BxDolModule::getInstance('NsysEmailAutoLoginModule');  
	$oAutoLogin->_init();
	$oAutoLogin->_redirectToCleanUrl();
	unset($oAutoLogin);
}
//fmc-end 
*/

?>