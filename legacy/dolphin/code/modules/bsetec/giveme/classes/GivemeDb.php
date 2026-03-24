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

bx_import('BxDolModuleDb');

class GivemeDb extends BxDolModuleDb {

    function GivemeDb(&$oConfig) {
        parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    //colotization queries
    function colorupdate($values) {
        foreach ($values as $key => $value) {
            if ($key != 'save_color')
                $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . $value . "' WHERE `Name`='" . $key . "'");
             setParam($key, process_db_input($value));
        }
        return "updated";
    }

    function colorreset() {
          $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '' WHERE `type`='color'");
        setParam('giveme_design_header_font', process_db_input(''));
        setParam('giveme_header_font',  process_db_input(''));
        setParam('giveme_body_font', process_db_input(''));
        setParam('giveme_body_header_font', process_db_input(''));
        setParam('giveme_footer_font', process_db_input(''));
        setParam('giveme_menu_font', process_db_input(''));
        setParam('giveme_custom_color', process_db_input(''));  
        return "color data reverted";
    }

    function getcolor() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='color'");
    }

    function checkcolor($name) {
        return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `Name`='" . $name . "'");
    }

    //Footer Contact queries
    function footercontactupdate($values) {
        foreach ($values as $key => $value) {
            if ($key == 'easy_menu_footer_content') {
				$checkfooter=$this->getOne("SELECT `ID` FROM `" . $this->_sPrefix . "_options` WHERE `Name`='easy_menu_footer_content'");
				if($checkfooter) {
                   $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . process_db_input($value) . "' WHERE `Name`='" . $key . "'");
				} else {
					$this->query("INSERT INTO `" . $this->_sPrefix . "_options` (`ID`, `Name`, `Value`, `type`) VALUES (NULL,'" . $key . "','" . process_db_input($value) . "','footer');");
				}
			}
        }
        return "updated";
    }

    function getfootercontact() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `Name`='easy_menu_footer_content'");
    }

    function checkfootercontact($name) {
        return $this->getOne("SELECT count(`Value`) FROM `" . $this->_sPrefix . "_options` WHERE `Name`='" . $name . "'");
    }

    /*
     * Footer Management
     */

    function setEasyMenu($post) {
        $this->query("INSERT INTO `" . $this->_sPrefix . "_footer_menu` (`title`, `url`, `class`, `position`, `group_id`) VALUES ('" . $post[MENU_TITLE] . "','" . $post[MENU_URL] . "','" . $post[MENU_CLASS] . "','" . $post[MENU_POSITION] . "','" . $post[MENU_GROUP] . "');");
        return db_last_id();
    }

    function getLastPosition($currentID) {
        return $this->getOne("SELECT `position` FROM `" . $this->_sPrefix . "_footer_menu` WHERE `group_id`='" . $currentID . "'");
    }

    function getMenu($currentID) {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_footer_menu` WHERE `group_id`='" . $currentID . "' order by position asc");
    }

    function getMenuGroupTitle($currentID) {
        return $this->getOne("SELECT `title` FROM `" . $this->_sPrefix . "_footer_menu_group` WHERE `id`='" . $currentID . "'");
    }

    function getMenuGroups() {
        return $this->getAll("SELECT `id`,`title` FROM `" . $this->_sPrefix . "_footer_menu_group` where `slug` = '' OR `slug` IS NULL order by id asc");
    }

    

    function setMenuGroup($post) {
        $this->query("INSERT INTO `" . $this->_sPrefix . "_footer_menu_group` (`title`) VALUES ('" . $post['title'] . "');");
        return db_last_id();
    }

    function getMenuItem($itemId) {
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "_footer_menu` WHERE `id`='" . $itemId . "'");
    }

    function updateMenuItem($post) {
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu` SET `title` = '" . $post[MENU_TITLE] . "',`url` = '" . $post[MENU_URL] . "',`class` = '" . $post[MENU_CLASS] . "' WHERE `id`='" . $post[MENU_ID] . "'");
        return 1;
    }

    function getDescendentMenuItem($id) {
        return $this->getAll("SELECT `id` FROM `" . $this->_sPrefix . "_footer_menu` WHERE `parent_id`='" . $id . "'");
    }

    function deleteItem($ids) {
        $this->query("DELETE FROM `" . $this->_sPrefix . "_footer_menu` WHERE id IN (" . $ids . ")");
        return 1;
    }

    function editMenuGroupItem($post) {
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `title` = '" . $post[MENUGROUP_TITLE] . "' WHERE `id`='" . $post[ID] . "'");
        return 1;
    }

    function deleteMenuGroup($id) {
        $this->query("DELETE FROM `" . $this->_sPrefix . "_footer_menu_group` WHERE id IN (" . $id . ")");
        return 1;
    }

    function deleteAllMenuGroupItem($id) {
        $this->query("DELETE FROM `" . $this->_sPrefix . "_footer_menu` WHERE group_id IN (" . $id . ")");
        return 1;
    }

    function updatePosition($parent, $children) {
        $i = 1;
        foreach ($children as $k => $v) {
            $id = (int) $children[$k]['id'];
            $data[MENU_PARENT] = $parent;
            $data[MENU_POSITION] = $i;
            $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu` SET `parent_id` = '" . $data[MENU_PARENT] . "',`position` = '" . $data[MENU_POSITION] . "' WHERE `id`='" . $id . "'");
            if (isset($children[$k]['children'][0])) {
                $this->updatePosition($id, $children[$k]['children']);
            }
            $i++;
        }
    }

    //Slider queries
    function slidersettingsupdate($values) {
        foreach ($values as $key => $value) {
            if ($key != 'save_bannersettings') {
                $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . $value . "' WHERE `Name`='" . $key . "'");
            }
        }
        return "updated";
    }

    function getslidersettings() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='slider'");
    }

    function checkslidersettings($name) {
        return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `Name`='" . $name . "'");
    }

    //slider resource
    function sliderresourceinsert($image, $text, $link) {
        $this->query("INSERT INTO `" . $this->_sPrefix . "_slider` (`id` , `image`, `text`, `link`) VALUES (NULL , '" . $image . "','" . $text . "','" . $link . "');");
        return "success";
    }

    function getslidersource() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_slider`  order by `ID`");
    }

    function getslidersourcebyid($id) {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_slider` WHERE `ID`=" . $id);
    }

    function deleteslider($id) {
        $this->query("DELETE FROM `" . $this->_sPrefix . "_slider` WHERE `ID` =" . $id);
        return "deleted";
    }

    function sliderresourceupdate($image, $text, $link, $id) {
        $this->query("UPDATE `" . $this->_sPrefix . "_slider` SET `image` = '" . $image . "',`text` = '" . $text . "',`link` = '" . $link . "' WHERE `ID`=" . $id);
    }

    // Get Home page settings
  //   function gethomepagesettings($request = '') {
  //       if($request == 'blocks')
  //           return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Value` != '' ORDER BY `ID` ASC");
  //       else if($request == 'about_us')
  //           return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Name` = 'about_us'");
  //       else if($request == 'homepage_bottom_block')
  //           return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Name` = 'homepage_bottom_block'");
		// else if($request == 'school_layout')
  //           return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Name` = 'school_layout'");
		// else if($request == 'school_splash')
  //           return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Name` = 'school_splash'");
		// else if($request == 'splash_visibility')
  //           return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' AND `Name` = 'splash_visibility'");
  //       else if(empty($request))
  //           return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='homepage' ORDER BY `ID` ASC");
  //   }

    // Home page queries
    function inserthomepage($values) {
        foreach ($values as $key => $value) {
            if ($key != 'Btn_General_save') {
                $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . $value . "' WHERE `Name`='" . $key . "'");
            } else if ($key != 'Btn_Home_block') {

                $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . $value . "' WHERE `Name`='" . $key . "'");
            } else if ($key != 'Btn_Bottom_block') {

                $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '" . $value . "' WHERE `Name`='" . $key . "'");
            }
        }
        return "updated";
    }

    function getCustomEvents() {
        $datecurrent = strtotime(date('Y-m-d H:i:s'));
        return $this->getAll("SELECT * FROM `bx_events_main` WHERE `Status` = 'approved' AND `allow_view_event_to` = '3' AND `EventStart` >= '{$datecurrent}' ORDER BY `ID` DESC LIMIT 0,4");
    }

    function getrecentPhotos() {
        return $this->getAll("SELECT * FROM `bx_photos_main` WHERE `Title` != '' ORDER BY `ID` DESC LIMIT 0,4");
    }

    function getrecentBlogs() {
        return $this->getAll("SELECT * FROM `bx_blogs_posts` WHERE `PostStatus` = 'approval' AND `allowView` = '3' ORDER BY `PostID` DESC LIMIT 0,4");
    }

    function checkmodule($modName) {
        return $this->getOne("SELECT `id` FROM `sys_modules` WHERE `title` = '{$modName}'");
    }
    // ****************************** Mastero form Settings *******************//

    function checkMastero($name='enable_giveme_home'){

        return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `Name` = '".process_db_input($name)."'");
    }

    function updateMasteroSettings($values) {
        if($values['enable_giveme_home'] == 'disable'){
            foreach ($values as $key => $value) {
                if ($key != 'save_menu_settings') {
                    $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `Value` = 'disable' WHERE  `Name` = '".$key."'");
                }
             }
        }else{
            foreach ($values as $key => $value) {
                if ($key != 'save_menu_settings') {
                    $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `Value` = '" . $value . "' WHERE  `Name` = '".$key."'");
                }
            }
        }
          return "updated";
    }



        function getMasterSettings(){
           return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='giveme'");
        }

        function checkMasteroSetting(){

         $aValue = $this->getRow("SELECT `Value` FROM `" . $this->_sPrefix . "_options` WHERE `Name` = 'giveme_icon_visibility' ");
         return $aValue['Value'];
        }

        //   upcoming photos

        function isEnabledOfPhotos(){
             $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_photos' AND `type` = 'mastero'");
            return $aValue['Value'];
         }


        function getupcomingphotos(){
               return $this->getAll("SELECT * FROM `bx_photos_main` order by id desc limit 7"); 
        }  

        //  upcoming events

        function isEnabledOfEvents(){
            $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_events' AND `type` = 'mastero'");
            return $aValue['Value'];

         }

        function getupcomingevents(){
            
            return $this->getAll("SELECT `PrimPhoto`,`ID`,`Title`,`EntryUri`,`Place`,`EventStart` FROM 
                `bx_events_main`  Where `allow_view_event_to` = 3 order by ID LIMIT 5");
        }   

        function eventParticipants($id){
            $value = $this->getAll("SELECT count(*) FROM `bx_events_participants` WHERE `id_entry` =$id ");
            return $value[0]['count(*)'];
        } 

        //   upcoming videos

        function isEnabledOfVideos(){
             $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_videos' AND `type` = 'mastero'");
            return $aValue['Value'];

         }


        function getupcomingvideos($limit=6){
            return $this->getAll("SELECT * FROM `RayVideoFiles` where `Status`='approved' order by ID desc limit {$limit}");
        } 
        // latest videos
        function getlatestvideos(){
            return $this->getRow("SELECT * FROM `RayVideoFiles` where `Status`='approved' order by ID desc limit 1");
        }
        // upcoming groups

        // function isEnabledOfGroups(){
        //      $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_groups' AND `type` = 'mastero'");
        //     return $aValue['Value'];

        // }

        // upcoming album / sound
        // function isEnabledOfAlbum(){
        //      $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_groups' AND `type` = 'mastero'");
        //     return $aValue['Value'];

        // }
        function getAlbum(){
           return $this->getAll("SELECT * FROM `RayMp3Files` order by `ID` desc");
            //return $this->getAll("SELECT * FROM `sys_albums` JOIN `sys_albums_objects` ON `ID`=`id_album` where `sys_albums`.`Type`='bx_sounds'  ");
        }
        

        function getGroups(){
           if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'bx_groups_main'"))==1) {
              return $this->getAll("SELECT * FROM `bx_groups_main` order by id desc");
           }
        }  
        //   upcoming Members

        // function isEnabledOfMembers(){
        //      $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_members' AND `type` = 'mastero'");
        //     return $aValue['Value'];

        // }
        
        function getUserAvatarByName($name) {
            return $this->getOne("SELECT `Avatar` FROM `Profiles` WHERE `NickName`='".$name."'");
        }

        function getUserIdByname($name) {
            return $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='".$name."'");
        }
        
        function getstats() {
            $stat=array();
            $stat['member']=$this->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE `Status`='Active' AND (`Couple`='0' OR `Couple`>`ID`)");
            $stat['video']=$this->getOne("SELECT COUNT(`ID`) FROM `RayVideoFiles` WHERE `Status`='approved'");
            $stat['photo']=$this->getOne("SELECT COUNT(`ID`) FROM `bx_photos_main` WHERE `Status`='approved'");
            $stat['event']=$this->getOne("SELECT COUNT(`ID`) FROM `bx_events_main` WHERE `Status`='approved'");
            return $stat;
        }
        //  check Boonex News 

         // function isEnabledOfBoonexNews(){
         //     $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_boonex_news' AND `type` = 'mastero'");
         //    return $aValue['Value'];

         // }
         // get latest post
        
        function getblog() {
           if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'bx_blogs_posts'"))==1) {
              return $this->getAll("SELECT * FROM `bx_blogs_posts` order by PostID desc limit 10");
           }
        }
        
        function getforum() {
           if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'bx_forum_topic'"))==1) {
              return $this->getAll("SELECT * FROM `bx_forum_topic` order by topic_id desc limit 10");
           }
        }
         //   Site Status

        // function isEnabledOfSite(){
        //      $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_site' AND `type` = 'mastero'");
        //     return $aValue['Value'];

        //  }
         // *************************** Footer Setting *********************************** //

        // function checkMasteroFooter(){

        //  $aValue = $this->getRow("SELECT `Value` FROM `" . $this->_sPrefix . "_options`
        //  WHERE `Name` = 'mastero_footer_visibility' ");
        //  return $aValue['Value'];
        // }

        // function updateFooterSetting($value){

        // return $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `Value` = '" . $value . "' WHERE  `Name` = 'mastero_footer_visibility' ");
        // }

        function getsocialbyvalue() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='social' AND Value!=''");
        }
        function getDownloads(){

        return $GLOBALS['MySQL']->getAll('SELECT * FROM `sys_box_download` WHERE `disabled` = 0 ORDER BY `order`');
        }

            //social box queries
        function socialupdate($values) {

            foreach($values as $key=>$value) {
                if($key!='save_socialshare') {
                    $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '".$value."' WHERE `Name`='".$key."'");
                }
            }
            return "updated";
        }
        function socialreset() {
            $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '' WHERE `type`='social'");
            return "social data reverted";
        }
        function getsocial() {
            return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `type`='social'");
        }

        // *************************** Icon Setting *********************************** //
        function getIconSetting(){
           return $this->getALL("SELECT * FROM `" . $this->_sPrefix . "_options`
            WHERE `type` = 'mastero_icon' AND `Name` != 'giveme_icon_visibility' ");
        }

        function getIconSettingValue(){
         $aValue = $this->getRow("SELECT `Value` FROM `" . $this->_sPrefix . "_options`
         WHERE `Name` = 'giveme_icon_visibility' ");
         return $aValue['Value'];
        }
        // Tooltip frontend
        function getTooltip(){
           $aValue = $this->getALL("SELECT `Name`,`Value` FROM `" . $this->_sPrefix . "_options`
            WHERE `type` = 'giveme_icon' AND `Name` != 'giveme_icon_visibility' ");
           foreach ($aValue as $key => $value) {
            $aResult[$value['Name']]  = $value['Value'];
           }
           return $aResult;
        }

        // Get Serive Menu Icons

        function getTopMenu(){
            return $this->getAll("SELECT `Name`,`Link`,`Visible`  FROM `sys_menu_service` WHERE `Active` = 1");
         }
         // update icon values
         function updateIconSetting($data){
            foreach ($data as $key => $value) {
                if ($key != 'icon_setting') {
             $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `Value` = '" . $value . "' WHERE  `Name` = '".$key."'");
               }
            }
            return 'updated';
        }
        //get quotes
        function getQuotes(){
            return $this->getAll("SELECT * FROM `bx_quotes_units` ");

        }
		  //get quotes
     //    function isEnabledOfQuotes(){
     //       $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_quotes' AND `type` = 'mastero'");
		   // return $aValue['Value'];
     //    }

        //   upcoming outline

        // function isEnabledOfOutline(){
        //      $aValue = $this->getRow("SELECT `Value` FROM `bsetec_giveme_options` WHERE  `Name` = 'mastero_outline' AND `type` = 'mastero'");
        //      return $aValue['Value'];
        //  }
		 
		//get all users
		function getUsers(){
			return $this->getAll("SELECT * FROM `profiles` WHERE  `Status` = 'Active' ORDER BY `ID` DESC");
		}

        function updatePositioncolumn($parent, $children) {
        $i = 1;
        foreach ($children as $k => $v) {
            $id = (int) $children[$k]['id'];
            $data[MENU_PARENT] = $parent;
            $data[MENU_POSITION] = $i;
            $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `sort_order` = '" . $data[MENU_POSITION] . "' WHERE `id`='" . $id . "'");
            if (isset($children[$k]['children'][0])) {
                $this->updatePositioncolumn($id, $children[$k]['children']);
            }
            $i++;
        }
    }
     
        function checkMasteroFooter(){

         $aValue = $this->getRow("SELECT `Value` FROM `" . $this->_sPrefix . "_options`
         WHERE `Name` = 'givme_footer_visibility' ");
         return $aValue['Value'];
        }

    function getMenuGroupsAll() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_footer_menu_group` order by sort_order asc");
    }

    function getMenuGroupsAllActive() {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_footer_menu_group` where is_active='1' order by sort_order asc ");
    }

    function updateStatuscolumn($jsondata)
    {
        $result = json_decode($jsondata);
        foreach ($result as $key => $value) 
        {
           $value = $value ? 1 : 0;
           $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `is_active` = '" . $value . "' WHERE `id`='" . $key . "'");
        }
    }

    //check user
    function checkUserpagePermission($name){
         return $this->getOne("SELECT `Value` FROM `" . $this->_sPrefix . "_options`  WHERE `Name` = '".process_db_input($name)."' ");
    }

    function disablesplashpage($status){
        $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `Value` = '".$status."' WHERE  `Name` = 'enable_giveme_home'");
    }

    function getmodules($type){
        if($type=='giveme')
        {
            return $this->getAll("SELECT `ID`,`bg_image`, `block` as title FROM `" . $this->_sPrefix . "_mastero` ");
        }
        //get inactive blocks from page builder
        elseif($type=='default')
        {
            $in_actives = $this->getAll("SELECT `ID`, `Caption` FROM `sys_page_compose` WHERE `Page` = 'index' AND `Func` NOT IN('Subscribe','Members','LoginSection','SiteStats','QuickSearch')");
            $return_array1 = $return_array2 = array();
            if(!empty($in_actives))
            {
                foreach ($in_actives as $key => $value) 
                {
                    $return_array[$value['ID']] = _t($value['Caption']);
                }
                asort($return_array, SORT_STRING | SORT_FLAG_CASE);
                foreach ($return_array as $key => $value) 
                {
                    $return_array2[] =  array('ID'=>$key,'title'=>$value);
                    //$return_array2[]['title'] = $value;
                }
            }
            return $return_array2;
        }
    }

    function save_blocks($data, $files)
    {
        // echo '<pre>';print_r($data);
        $values = $data['parent_id'];
        $image_files = $files['bg_image']['name'];
        //  echo '<pre>';print_r($image_files);exit;
        //delete all records in table
        $this->query("DELETE FROM `" . $this->_sPrefix . "_splash` ;");

        $image_array = $old_images = array();
        //upload image
        if(!empty($image_files))
        {
            foreach ($image_files as $key => $value) 
            {
                //check if the block having image
                if(!empty($files['bg_image']['tmp_name'][$key]))
                {
                    $image_array[$key] = uniqid().'.'.end(explode(".", $files['bg_image']['name'][$key]));
                    move_uploaded_file($files['bg_image']['tmp_name'][$key], BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/bg_images/" . $image_array[$key]);
                }
                
            }
        }

        //save values in DB
        if(!empty($values))
        {
            foreach ($values as $key => $value) 
            {
                $parent_id = $data['parent_id'][$key];
                $block_type = $data['block_type'][$key];
                $block = $data['block'][$key];
				
				if($block_type=='default'){
					 $column_order = $this->getOne("SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `page` = 'index' AND `Column`=1");
					 $check = $this->getRow("SELECT * FROM `sys_page_compose` WHERE `page` = 'index' AND `ID`='".process_db_input($parent_id)."' AND `Column`!=0");
					 if(empty($check)){
						 $iOrder = $column_order + 1;
						 $this->query("UPDATE `sys_page_compose` SET `Column` = 1, `Order` =". $iOrder . " WHERE `ID` = ".process_db_input($parent_id)." AND `Page` = 'index'");
					 }
				}
                //update existing image to blocks if it have
                $old_image = isset($data['old_image'][$key]) ? $data['old_image'][$key] : '';

                //echo '<pre>';print_r($value);exit;
                //push all old images in one array
                $old_images[] = $bg_image = isset($image_array[$key]) ? $image_array[$key] : $old_image;
				$this->query("INSERT INTO `" . $this->_sPrefix . "_splash` (`block_type`, `parent_id`, `block`, `bg_image`) VALUES ('" . process_db_input($block_type) . "','" . $parent_id . "','" . process_db_input($block) . "','" . process_db_input($bg_image) . "');");
            } 
        }

        $uploadFilepath = BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/bg_images/";
        $directory = dir($uploadFilepath);
            while(false !== ($file_entry = $directory->read())) {
                $filepath = "{$uploadFilepath}/{$file_entry}";
                if(is_file($filepath) && filectime($filepath)) {
                    if (!in_array($file_entry, $old_images)) {
                        // chmod($filepath,0755);
                        @unlink($filepath);
                    }
                } 
            }
        
        // echo '<pre>';print_r($old_images);exit;
    }

    function getsplashblocks()
    {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_splash`");
    }

    function getGiveMEBlocks($id=''){
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "_mastero` WHERE `ID`='".process_db_input($id)."'");
    }

    function getDefaultBlocks($id=''){
        return $this->getRow("SELECT * FROM `sys_page_compose` WHERE `ID`='".process_db_input($id)."' AND `Column`!=0");
    }

    function updateMasteroReset(){
        $masteros = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_mastero`");
        $splashs = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_splash`");
        if(!empty($splashs)){
            $filepath = BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/bg_images/";
            foreach($splashs as $splash){
                if($splash['block']!='GiveME Join Today'){
                     if($splash['bg_image'] !=''){
                        if(file_exists($filepath.$splash['bg_image'])){
                            @unlink($filepath.$splash['bg_image']);
                        }
                    }
                }
            }
            $this->query("DELETE FROM `" . $this->_sPrefix . "_splash`");
        }
        foreach($masteros as $mastero){
           $this->query("INSERT INTO `" . $this->_sPrefix . "_splash` (`block_type`, `parent_id`, `block`, `bg_image`) VALUES ('" . process_db_input('giveme') . "','" . process_db_input($mastero['ID']) . "','" . process_db_input($mastero['block']) . "','" . process_db_input($mastero['bg_image']) . "')"); 
        }
        return true;
    }

    function resetfooter()
    {
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `is_active` = '0'");
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `is_active` = '1',`sort_order` = '1' WHERE `slug`='newsletter'");
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `is_active` = '1',`sort_order` = '2' WHERE `slug`='donwloads'");
        $this->query("UPDATE `" . $this->_sPrefix . "_footer_menu_group` SET `is_active` = '1',`sort_order` = '3' WHERE `slug`='tags'");
    }

    ## giveme 
    function  getSplashPageBlogs(){
        return $this->getAll("SELECT * FROM `bx_blogs_posts` order by `PostID` DESC Limit 3");
    }

    function getSplashEvents(){
        $date =  " AND `EventEnd` > '".time()."'";
        return $this->getAll(" SELECT * FROM `bx_events_main` WHERE `status`='approved' $date order by `EventStart` ASC limit 6 ");
    }

    function getSplashGallery(){
        return $this->getAll("SELECT * FROM `bx_events_main` WHERE `status` ='approved' AND `PrimPhoto`!='0' order by `ID` DESC limit 8");
    }

    function getCategoryGroup(){
        return $this->getAll("SELECT `Categories` FROM `bx_events_main` Group By  `Categories`");
    }

    function mapupdate($value){
         $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '".$value['giveme_content']."' WHERE `Name`='giveme_map'");
         return 'updated';
    }

    function mapreset($value){
        $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '' WHERE `Name`='giveme_map'");
         return 'map code Reverted';
    }

    function getMap(){
        return $this->getOne("SELECT `value` FROM `" . $this->_sPrefix . "_options` where `Name`='giveme_map' ");
    }

    function Donationupdate($values){
       foreach($values as $key=>$value) {
            if($key!='save_donation') {
                $count = $this->getOne(" SELECT * FROM `" . $this->_sPrefix . "_options` WHERE `Name`='".$key."' ");
                if($count>0)
                    $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '".$value."' WHERE `Name`='".$key."'");
                else
                    $this->query("INSERT INTO `" . $this->_sPrefix . "_options` (`Name`,`value`,`type`) VALUES ('".$key."','".$value."','donation') ");
            }
        }
        return "updated";
    }

    function DonationReset($values){
         $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '' WHERE `type`='donation'");
         return "Donation data reverted";
    }

    function getDonation(){
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_options` where `type`='donation' ");
    }

    function removeCart($user_id){
        $this->query("DELETE FROM `bx_pmt_cart` WHERE `client_id`='" . $user_id . "'");
    }

    function InsertDonation($iItemId,$buyerId, $sOrderId,$amount){
        $created = date('Y-m-d H:m:s');
        return $this->query(" INSERT INTO `" . $this->_sPrefix . "_donation` (`user_id`,`item_id`,`order_id`,`amount`,`created`) values ('".$buyerId."','".$iItemId."','".$sOrderId."','".$amount."','".$created."') ");
    }

    function Donorlist(){
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_donation`");
    }

    function getDonationlist($sqlFrom = '', $iLimit = '',$search=''){
       return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "_donation` JOIN `Profiles` ON `" . $this->_sPrefix . "_donation`.`user_id` = `Profiles`.`ID` where `Profiles`.`NickName` LIKE '".$search."%' limit {$sqlFrom},{$iLimit} ");  
    }
    // Donorlist Search
    function getdonorlistsearch($search=''){
        return $this->getOne("SELECT count(*) from `" . $this->_sPrefix . "_donation` JOIN `Profiles` ON `" . $this->_sPrefix . "_donation`.`user_id` = `Profiles`.`ID` where `Profiles`.`NickName` LIKE '".$search."%' ");
    }

    //Get Photos

    function getPhotos(){
        return $this->getAll("SELECT * from `bx_photos_main` where `Title`!=''  AND `Title`!='Avatar'  order by `ID` DESC limit 4");
    }

    function DonationTotal(){
        return $this->getOne(" SELECT sum(`amount`)as`total` from `" . $this->_sPrefix . "_donation` ");
    }

    function getDonationTitle(){
        return $this->getRow("SELECT `value` FROM `" . $this->_sPrefix . "_options` where `Name`='goal_title' ");
    }
    function getemails($id){
        return $this->getRow("SELECT * FROM `Profiles` where `ID`='".$id."' ");
    }
     function getresetdonation(){

     $this->query("UPDATE `" . $this->_sPrefix . "_options` SET `value` = '' WHERE `type`='donation'");
     $this->query("TRUNCATE `bsetec_giveme_donation`");
     return true;

     }

}
?>