<?php

/* * *************************************************************************
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
 * ************************************************************************* */

bx_import('BxDolTwigTemplate');

class GivemeTemplate extends BxDolTwigTemplate {

    function GivemeTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function getmenulinks($groupid) {
        $menuitems = $this->_oDb->getMenu($groupid);
        $itemhtml = '';
        if (count($menuitems) > 0) {
            $itemhtml.='<ul>';
            foreach ($menuitems as $menuitem) {
                $itemhtml.='<li class="' . $menuitem['class'] . '"><a href="' . $menuitem['url'] . '"><i class="fa fa-caret-right" aria-hidden="true"></i>' . $menuitem['title'] . '</a></li>';
            }
            $itemhtml.='</ul>';
        }

        return $itemhtml;
    }

    function gethomepageblocks($result){
        $blockListHtml = '';
        if(count($result) > 0){
            $divLength = 12/count($result);
            foreach ($result as $block){
                if($block['Name'] == 'block_one' || $block['Name'] == 'block_two' || $block['Name'] == 'block_three' || $block['Name'] == 'block_four'){
                    $blockListHtml .= $block['Value'];
                }
            }
        }
        return $blockListHtml;
    }

     function getblogs(){

         $blogs= $result=$this->_oDb->getrecentBlogs();
        $paths=BX_DOL_URL_ROOT.'media/images/blog/big_';
         $scode="";
         foreach ($blogs as $key => $value) {
            $time=$value['PostDate'];
              $day=date("d M Y",$time);
            $scode.="<li class='recent-block2'>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <a href='' class='blog-img' style='background:url(".$paths.$value['PostPhoto'].") no-repeat;background-size:cover;background-position:center center;'></a>
                                    </div><!--end col-->
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                   ".$value['PostText']."
                                   
                                    <span class='gray-txt'>".$day."</span>
                                    </div><!--end col-->
                                </li>";
         }

        $aVars=array('blogs'=>$scode);
         return $this->parseHtmlByName('unit.html',  $aVars);
    }

    function unit_events ($aData) {

        $iMediaId = $aData['PrimPhoto'];
        if($iMediaId){           
            $files = $GLOBALS['MySQL']->getRow("SELECT `Hash`,`Ext` FROM `bx_photos_main` where `ID` = ".$iMediaId." ");  
            if($files){
                $checkinfolder=BX_DIRECTORY_PATH_ROOT."modules/boonex/photos/data/files/".$iMediaId.'.'.$files['Ext'];

            }
        }


        if(file_exists($checkinfolder) && (!empty($files))){
            if($files){   
                $src = BX_DOL_URL_ROOT . 'm/photos/get_image/file/';
                $sImage = $src.$files['Hash'].'.'.$files['Ext'];
            } else {
                $sImage = BX_DOL_URL_ROOT.'modules/boonex/events/templates/base/images/no-image-thumb.png';
            }  
        } else{
            $sImage = BX_DOL_URL_ROOT.'modules/boonex/events/templates/base/images/no-image-thumb.png'; 
        }

        $aData['Description'] =  substr(strip_tags($aData['Description']),0,800);  // filetered all html tags. 
        $place = $aData['Place'];
        $city = $aData['City'];
        $country = $aData['Country'];
        $authorName = getNickName($aData['ResponsibleID']);
        $current =  BX_DOL_URL_ROOT . 'm/events/view/' . $aData['EntryUri'];
        $redirectlink = BX_DOL_URL_ROOT . 'm/events/live_video/view/' .$aData['EntryUri'];

        $day=new DateTime('last day of this month'); 
        $scheduledTime = $day->format('M jS');
        $date = date("j F, Y", $aData['EventStart']);

        //date('M d h:i a', $aData['EventStart'])

        $list = '<div class="col-sm-6"><div class="event_section_image1 clearfix"> <div class="bx-twig-unit bx_events_unit bx-def-margin-top-auto"> <div class="bx-twig-unit-thumb-cont bx-def-margin-sec-right"> <a href="'.$current.'"><img src="'.BX_DOL_URL_ROOT.'templates/base/images/spacer.gif" style="background-image:url('.$sImage.')"></a> </div> <div class="bx-twig-unit-info"> <div class="even_image_bottom clearfix"> <span class="event_left_arrow"></span> <div class="first_yercd clearfix"> <div class="bx-twig-unit-title bx-def-font-h2"> <h4><a href="'.$current.'">'.$aData['Title'].'</a></h4> </div> <div class="bx-twig-unit-line bx-twig-unit-special"><span class="event_date">'.$date.'</span></div> <div class="bx-twig-unit-line"><span class="event_time">'.$place.', '.$city.', '.$country.'</span></div> <div class="bx-twig-unit-line"><span class="event_author"><a href="'.getProfileLink($aData['ResponsibleID']).'">'.$authorName.'</a></span></div> <div class="bx-twig-unit-line"><span class="event_more"><a href="'.$current.'">View More</a></span></div> </div> </div> </div> </div> </div></div>';

        return $list;
    }

    function unit_photos ($aData) {

        $iMediaId = $aData['ID'];
        $checkinfolder=BX_DIRECTORY_PATH_ROOT."modules/boonex/photos/data/files/".$iMediaId.'.'.$aData['Ext'];

        if(file_exists($checkinfolder)){
            $src = BX_DOL_URL_ROOT . 'm/photos/get_image/file/';
            $sImage = $src.$aData['Hash'].'.'.$aData['Ext']; 
        } else{
            $sImage = BX_DOL_URL_ROOT.'modules/boonex/events/templates/base/images/no-image-thumb.png'; 
        }

        $aData['Description'] =  substr(strip_tags($aData['Desc']),0,800);  // filetered all html tags. 
        $Title = $aData['Title'];
        $authorName = getNickName($aData['Owner']);
        $current =  BX_DOL_URL_ROOT . 'm/photos/view/' . $aData['Title'];

        $day=new DateTime('last day of this month'); 
        $scheduledTime = $day->format('M jS');
        $date = date("j F, Y", $aData['Date']);

        $list = '<div class="col-sm-6"><div class="event_section_image1 clearfix"> <div class="bx-twig-unit bx_events_unit bx-def-margin-top-auto"> <div class="bx-twig-unit-thumb-cont bx-def-margin-sec-right"> <a href="'.$current.'"><img src="'.BX_DOL_URL_ROOT.'templates/base/images/spacer.gif" style="background-image:url('.$sImage.')"></a> </div> <div class="bx-twig-unit-info"> <div class="even_image_bottom clearfix"> <span class="event_left_arrow"></span> <div class="first_yercd clearfix"> <div class="bx-twig-unit-title bx-def-font-h2"> <h4><a href="'.$current.'">'.$aData['Title'].'</a></h4> </div> <div class="bx-twig-unit-line bx-twig-unit-special"><span class="event_date">'.$date.'</span></div> <div class="bx-twig-unit-line"></div> <div class="bx-twig-unit-line"><span class="event_author"><a href="'.getProfileLink($aData['Owner']).'">'.$authorName.'</a></span></div> <div class="bx-twig-unit-line"><span class="event_more"><a href="'.$current.'">View More</a></span></div> </div> </div> </div> </div> </div></div>';

        return $list;
    }

    function unit_blogs ($aData) {

        $checkinfolder=BX_DIRECTORY_PATH_ROOT."media/images/blog/big_".$aData['PostPhoto'];

        if(file_exists($checkinfolder)){
            $sImage = BX_DOL_URL_ROOT . "media/images/blog/big_".$aData['PostPhoto'];
        } else{
            $sImage = BX_DOL_URL_ROOT.'modules/boonex/events/templates/base/images/no-image-thumb.png'; 
        }

        $Title = $aData['PostText'];
        $authorName = getNickName($aData['OwnerID']);
        $current =  BX_DOL_URL_ROOT . 'blogs/entry/'.$aData['PostUri'];

        $day=new DateTime('last day of this month'); 
        $scheduledTime = $day->format('M jS');
        $date = date("j F, Y", $aData['PostDate']);

        $list = '<div class="col-sm-6"><div class="event_section_image1 clearfix">  <div class="bx-twig-unit bx_events_unit bx-def-margin-top-auto"> <div class="bx-twig-unit-thumb-cont bx-def-margin-sec-right"> <a href="'.$current.'"><img src="'.BX_DOL_URL_ROOT.'templates/base/images/spacer.gif" style="background-image:url('.$sImage.')"></a> </div> <div class="bx-twig-unit-info"> <div class="even_image_bottom clearfix"> <span class="event_left_arrow"></span> <div class="first_yercd clearfix"> <div class="bx-twig-unit-title bx-def-font-h2"> <h4><a href="'.$current.'">'.$aData['PostCaption'].'</a></h4> </div> <div class="bx-twig-unit-line bx-twig-unit-special"><span class="event_date">'.$date.'</span></div> <div class="bx-twig-unit-line"></div> <div class="bx-twig-unit-line"><span class="event_author"><a href="'.getProfileLink($aData['OwnerID']).'">'.$authorName.'</a></span></div> <div class="bx-twig-unit-line"><span class="event_more"><a href="'.$current.'">View More</a></span></div> </div> </div> </div> </div> </div></div>';

        return $list;
    }
    function viewevents(){
        $aEvents = $this->_oDb->getupcomingevents();
        $i=0;

        foreach ($aEvents as $key => $event) {
            
            $event_img = BxDolService::call('photos', 'get_photo_array', array($event['PrimPhoto'], 'thumb'), 'Search');
            $e_img = "<img src='".$event_img['file']."' />";
            $count = $this->_oDb->eventParticipants($event['ID']);
            $time  = $event['EventStart'];
            $aEvents[$key]['date'] = date("d F Y",$time);
            $aEvents[$key]['count'] = $count; 
            $aEvents[$key]['img'] = $e_img;
            if ($i % 2 == 0) {
                $aEvents[$key]['trans'] =-400;
            } else {
                $aEvents[$key]['trans'] =400;
            }
            $i++;
        }
        
            return $aEvents;
    }
    function viewvideos(){
        $videos = $this->_oDb->getupcomingvideos();
            foreach ($videos as $key => $video) {
               $video_path = BxDolService::call('videos', 'get_video_array', array($video['ID']), 'Search');
               $aVideos[$key]['player'] = '';
               $aVideos[$key]['path'] = $video_path['file'];
               $aVideos[$key]['url']=$video_path['url'];
               $aVideos[$key]['file_path']= $video_path['file_path'];
               $aVideos[$key]['mediatype'] =$video['Source'];
               $aVideos[$key]['mediauri'] = $video['Video'];
               $aVideos[$key]['mediaid'] = $video['ID'];
            }
         return $aVideos;
    }
    function viewGroups(){

        $aGroups = $this->_oDb->getGroups();
        if(count($aGroups)>0) {
            foreach ($aGroups as $key => $group) {
             $aFotoId = $group['thumb'];
             $a = array ('ID' => '', 'Avatar' => $aFotoId);
             $aImageFile = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');
             $aGroups[$key]['link'] = $aImageFile['file'];
            }
        }
        return $aGroups;
    }
    /*
    * View album
    */
    function viewAlbum(){
        $aAlbums = $this->_oDb->getAlbum();
        
        if(count($aAlbums)>0){
            foreach($aAlbums as $key => $album){
                $med_id = $album['ID'];
                $music[$key] = BxDolService::call('sounds', 'get_music_array', array($med_id,'browse'), 'Search');
            }
        }
        
        return $music;
    }

    function viewphotos(){

        $aPhotos = $this->_oDb->getupcomingphotos();
        foreach ($aPhotos as $key => $photo) {
            $a = array ('ID' => '', 'Avatar' => $photo['ID']);
            $aImageFile = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');
            $aPhotos[$key]['link'] = $aImageFile['file'];
         }
         return $aPhotos;
    }
    /*
    * view quotes
    */
    function viewquotes(){
        $Quotes = $this->_oDb->getQuotes();
        foreach($Quotes as $key => $Quote){
            $aQuotes[$key]['text'] =$Quote['Text'];
            $aQuotes[$key]['author'] =$Quote['Author'];
        }
        return $aQuotes;
    }
    
    function viewmembers(){
        $iMaxNum = (int) getParam( "top_members_max_num" ); // number of profiles
        $aCode = $this->getMembers('Members', array(), $iMaxNum);
        return $aCode[0];
    }
	
	 // ----- non-block functions ----- //
    function getMembers ($sBlockName, $aParams = array(), $iLimit = 16, $sMode = 'last')
    {
        $aDefFields = array(
            'ID', 'NickName', 'Couple', 'Sex', 'DescriptionMe'
        );
        $sCode = '';

        $iOnlineTime = (int)getParam( "member_online_time" );

        //main fields
        $sqlMainFields = "";
        foreach ($aDefFields as $iKey => $sValue)
             $sqlMainFields .= "`Profiles`. `$sValue`, ";

        $sqlMainFields .= "if(`DateLastNav` > SUBDATE(NOW(), INTERVAL $iOnlineTime MINUTE ), 1, 0) AS `is_online`";

        // possible conditions
        $sqlCondition = "WHERE `Profiles`.`Status` = 'Active' and (`Profiles`.`Couple` = 0 or `Profiles`.`Couple` > `Profiles`.`ID`)";
        if (is_array($aParams)) {
             foreach ($aParams as $sField => $sValue)
                 $sqlCondition .= " AND `Profiles`.`$sField` = '$sValue'";
        }

        // top menu and sorting
        $aModes = array('last', 'top', 'online');
        $aDBTopMenu = array();

        if (empty($_GET[$sBlockName . 'Mode'])) {
            $sMode = 'last';
        } else {
            $sMode = (in_array($_GET[$sBlockName . 'Mode'], $aModes)) ? $_GET[$sBlockName . 'Mode'] : $sMode = 'last';
        }
        $sqlOrder = "";
        foreach( $aModes as $sMyMode ) {
            switch ($sMyMode) {
                case 'online':
                    if ($sMode == $sMyMode) {
                        $sqlCondition .= " AND `Profiles`.`DateLastNav` > SUBDATE(NOW(), INTERVAL ".$iOnlineTime." MINUTE)";
                        $sqlOrder = " ORDER BY `Profiles`.`Couple` ASC";
                    }
                    $sModeTitle = _t('_Online');
                break;
                case 'last':
                    if ($sMode == $sMyMode)
                        $sqlOrder = " ORDER BY `Profiles`.`Couple` ASC, `Profiles`.`DateReg` DESC";
                    $sModeTitle = _t('_Latest');
                break;
                case 'top':
                    if ($sMode == $sMyMode) {
                        $oVotingView = new BxTemplVotingView ('profile', 0, 0);
                        $aSql        = $oVotingView->getSqlParts('`Profiles`', '`ID`');
                        $sqlOrder    = $oVotingView->isEnabled() ? " ORDER BY `Profiles`.`Couple` ASC, (`pr_rating_sum`/`pr_rating_count`) DESC, `pr_rating_count` DESC, `Profiles`.`DateReg` DESC" : $sqlOrder;
                        $sqlMainFields .= $aSql['fields'];
                        $sqlLJoin    = $aSql['join'];
                        $sqlCondition .= " AND `pr_rating_count` > 1";
                    }
                    $sModeTitle = _t('_Top');
                break;
            }
            $aDBTopMenu[$sModeTitle] = array('href' => BX_DOL_URL_ROOT . "index.php?{$sBlockName}Mode=$sMyMode", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
        }
        if (empty($sqlLJoin)) $sqlLJoin = '';
        $iCount = (int)db_value("SELECT COUNT(`Profiles`.`ID`) FROM `Profiles` $sqlLJoin $sqlCondition");
        $aData = array();
        $sPaginate = '';
        if ($iCount) {
            $iLimit = (int)$iLimit > 0 ? (int)$iLimit : 8;
            $iPages = ceil($iCount/ $iLimit);
            $iPage = empty($_GET['page']) ? 1 : (int)$_GET['page'];
            if ($iPage > $iPages)
                $iPage = $iPages;
            if ($iPage < 1)
                $iPage = 1;
            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT $sqlFrom, $iLimit";

            $sqlQuery = "SELECT " . $sqlMainFields . " FROM `Profiles` $sqlLJoin $sqlCondition $sqlOrder $sqlLimit";
            $rData = db_res($sqlQuery);
            $iCurrCount = mysql_num_rows($rData);
            $aOnline = $aTmplVars = array();
            while ($aData = mysql_fetch_assoc($rData)) {
                $aOnline['is_online'] = $aData['is_online'];
				$mdesc=strip_tags($aData['DescriptionMe']);
                $aTmplVars[] = array(
                    'thumbnail' => get_member_thumbnail($aData['ID'], 'none', true, 'visitor', $aOnline),
					'desc' =>strlen($mdesc)>70?substr($mdesc,0,70).'...':$mdesc,
                );
            }
			$sCode = $this->parseHtmlByName('members_list.html', array(
                'bx_repeat:list' => $aTmplVars
            ));

            if ($iPages > 1) {
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => BX_DOL_URL_ROOT . 'index.php',
                    'count' => $iCount,
                    'per_page' => $iLimit,
                    'page' => $iPage,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'index.php?'.$sBlockName.'Mode='.$sMode.'&page={page}&per_page={per_page}\');',
                ));
                $sPaginate = $oPaginate->getSimplePaginate(BX_DOL_URL_ROOT . 'browse.php');
            }
        } else {
            $sCode = MsgBox(_t("_Empty"));
        }
		return array($sCode, $aDBTopMenu, $sPaginate, true);
    }
	
    function blogdetails($blogfeed) { 
       $PostImage=$blogfeed['PostPhoto']?'media/images/blog/big_'.$blogfeed['PostPhoto']:'templates/tmpl_mastero/images/nopreview.jpg';
       $PostText=strip_tags($blogfeed['PostText']);
       $PostText=strlen($PostText)>70?substr($PostText,0,70).'...':$PostText;
       $aVars=array(
          'PostUri'=>$blogfeed['PostUri'],
          'PostCaption'=>$blogfeed['PostCaption'],
          'PostText'=>$PostText,
          'PostImage'=>$PostImage
       );
       return $this->parseHtmlByName('blogdetails.html',  $aVars);
    }
    
    function forumdetails($forumfeed) { 
       $avatar=$this->_oDb->getUserAvatarByName($forumfeed['first_post_user']);
       $userid=$this->_oDb->getUserIdByname($forumfeed['first_post_user']);
       //$PostImage = $avatar?'modules/boonex/avatar/data/images/'.$avatar.'.jpg':'templates/tmpl_mastero/images/nopreview.jpg';
	   $PostImage = $GLOBALS['oFunctions']->getMemberAvatar($userid);
       $aVars=array(
          'PostUri'=>$forumfeed['topic_uri'],
          'PostCaption'=>$forumfeed['topic_title'],
          'PostText'=>'<a href="'.getProfileLink($userid).'">'.$forumfeed['first_post_user'].'</a><br/>'.defineTimeInterval($forumfeed['when']),
          'PostImage'=>$PostImage
       );
       return $this->parseHtmlByName('forumdetails.html',  $aVars);
    }

    function getdownloads(){
      
       $checkFooter = $this->_oDb->checkMasteroFooter();
       if( $checkFooter == 'disable')
         return false;
    
      $a = $this->_oDb->getDownloads();

      $s = '';
        foreach ($a as $r) {
            if ('_' == $r['title'][0])
                $r['title'] = _t($r['title']);
            if ('_' == $r['desc'][0])
                $r['desc'] = _t($r['desc']);

            if (0 == strncmp('php:', $r['url'], 4))
                $r['url'] = eval(substr($r['url'], 4));

            // echo '<pre>';print_r($GLOBALS['site']['ver']);exit;
            //$r['icon'] = $GLOBALS['oSysTemplate']->getIconUrl($r['icon']);
            $r['icon'] = $GLOBALS['site']['ver']=='7.3' ? $r['icon'] : $GLOBALS['oSysTemplate']->getIconUrl($r['icon']);
            $s .= $GLOBALS['oSysTemplate']->parseHtmlByName('download_box_unit.html', $r);
        }

        $aVars['downloads'] = $s;
        return $this->parseHtmlByName('downloads.html',  $aVars);
    }

    function tagscontent(){

       $checkFooter = $this->_oDb->checkMasteroFooter();

       if( $checkFooter == 'disable'){ 
         return false;
       }

        bx_import('BxTemplTags');
        $oTags = new BxTemplTags();
        $oTags->getTagObjectConfig(array('type' => ''));

        if(empty($oTags->aTagObjects))
            return '';

        $aParam = array(
            'type' => isset($_REQUEST['tags_mode']) && isset($oTags->aTagObjects[$_REQUEST['tags_mode']]) ? $_REQUEST['tags_mode'] : $oTags->getFirstObject(),
            'orderby' => 'popular',
            'limit' => getParam('tags_perpage_browse')
        );

        $sMenu = $oTags->getTagsTopMenu($aParam);
        $sContent = $oTags->display($aParam, $iBlockId);
        //return $sContent;

        $aVars['tags'] = $sContent;
        // echo '<pre>';print_r($sContent);exit;
        
        return $this->parseHtmlByName('tags.html',  $aVars);
    }

    function siteStatus($aVal,$sMode=''){
            if ( $sMode != 'admin' ) {
                $sBlockId = '';
                $iNum = strlen($aVal['query']) > 0 ? db_value($aVal['query']) : 0;
                $sHref = ($iNum>0) ? BX_DOL_URL_ROOT.$aVal['link'] : 'javascript:';
            } else {
                $sBlockId = "id='{$aVal['name']}'";
                $iNum  = strlen($aVal['adm_query']) > 0 ? db_value($aVal['adm_query']) : 0;
                if ( strlen($aVal['adm_link']) > 0 ) {
                    if( substr( $aVal['adm_link'], 0, strlen( 'javascript:' ) ) == 'javascript:' ) {
                        $sHref = 'javascript:void(0);';
                        $sOnclick = 'onclick="' . $aVal['adm_link'] . '"';
                    } else {
                        $sHref = $aVal['adm_link'];
                        $sOnclick = '';
                    }
                } 
            }
        $sImg = (false === strpos($aVal['icon'], '.') ? '<i class="sys-icon ' . $aVal['icon'] . '"></i>' : '<img src="' . getTemplateIcon($aVal['icon']) . '" alt="" />');
        $sCode ='<div class="item"><a href="'.$sHref.'" '.$sOnclick.'>'.$sImg.'</a><h4><a href="'.$sHref.'" '.$sOnclick.'>'._t('_'.$aVal['capt']).'</a></h4><p>'.$iNum.'</p></div>';
        return $sCode;
    }

    
}

