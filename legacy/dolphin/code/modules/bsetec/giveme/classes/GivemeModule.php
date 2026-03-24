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
set_time_limit(1000000);
session_start();
bx_import('BxDolInstallerUtils');
bx_import('BxDolModule');

class GivemeModule extends BxDolModule {

    function GivemeModule(&$aModule) {
        parent::BxDolModule($aModule);
    }

    function actionAdministration($sUrl = '', $sParam1 = '', $sParam2 = '') {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'colorization' => array(
                'title' => 'Colorization',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/colorization',
                '_func' => array('name' => 'actionAdministrationColorization', 'params' => array($sParam1)),
            ),
            'slider' => array(
                'title' => 'Slider',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/slider',
                '_func' => array('name' => 'actionAdministrationSlider', 'params' => array($sParam1, $sParam2)),
            ),
            'general' => array(
                'title' => 'General',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/general',
                '_func' => array('name' => 'actionAdministrationGeneral', 'params' => array()),
            ),
            'splashpage' => array(
                'title' => 'Splashpage',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/splashpage',
                '_func' => array('name' => 'actionAdministrationSplashpage', 'params' => array()),
            ),
            'footer' => array(
                'title' => 'footer',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/footer',
                '_func' => array('name' => 'actionAdministrationFooter', 'params' => array()),
            ),
            'map' => array(
                'title' => 'Map', 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/map',
                '_func' => array ('name' => 'actionAdministrationMap', 'params' => array()),
            ),
            'ajax_block' => array(
                'title' => 'Social', 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/ajax_block',
                '_func' => array ('name' => 'actionAdministrationAjax_block', 'params' => array($sParam1)),
            ),
            'donation'=>array(
                'title'=>'donation',
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/donation',
                '_func' => array ('name' => 'actionAdministrationDonation', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'general';

        $aMenu[$sUrl]['active'] = 1;

        $sContent = call_user_func_array(array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
        echo $this->_oTemplate->adminBlock($sContent, '', $aMenu);
        $this->_oTemplate->addCssAdmin(array('genius.css'));
        $this->_oTemplate->pageCodeAdmin('');
    }

    ##GiveME Splash Page Map
    function actionAdministrationMap(){
         if(isset($_POST['save_map'])) {
            $this->_oDb->mapupdate($_POST);
        } 

        if(isset($_POST['reset_map'])) {
            $this->_oDb->mapreset($_POST);
        }

        $Map =$this->_oDb->getMap();
        $aVars['map']=$Map;
       
        $sContent .= $this->_oTemplate->parseHtmlByName('giveme_map',$aVars);
        return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));    
    }
     function actionEditmenuitem() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $getSingleItem = $this->_oDb->getMenuItem($id);
            $aVars['menu_id'] = $getSingleItem['id'];
            $aVars['menu_parent_id'] = $getSingleItem['parent_id'];
            $aVars['menu_title'] = $getSingleItem['title'];
            $aVars['menu_url'] = $getSingleItem['url'];
            $aVars['menu_class'] = $getSingleItem['class'];
            $aVars['menu_position'] = $getSingleItem['position'];
            $aVars['menu_group_id'] = $getSingleItem['group_id'];
            echo $this->_oTemplate->parseHtmlByName('menu_edit_item', $aVars);
        }
    }
        function actionUpdatemenuitem() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_POST['title'])) {
            $data[MENU_TITLE] = trim($_POST['title']);
            if (!empty($data[MENU_TITLE])) {
                $data[MENU_ID] = $_POST['menu_id'];
                $data[MENU_URL] = $_POST['url'];
                $data[MENU_CLASS] = $_POST['class'];
                if ($this->_oDb->updateMenuItem($data)) {
                    $response['status'] = 1;
                    $d['title'] = $data[MENU_TITLE];
                    $d['url'] = $data[MENU_URL];
                    $d['klass'] = $data[MENU_CLASS]; //klass instead of class because of an error in js
                    $response['menu'] = $d;
                } else {
                    $response['status'] = 2;
                    $response['msg'] = 'Edit menu error.';
                }
            } else
                $response['status'] = 3;
            header('Content-type: application/json');
            echo json_encode($response);
        }
    }

        function actionUpdateposition() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_POST['easymm'])) {
            $easymm = $_POST['easymm'];
            $this->_oDb->updatePosition(0, $easymm);
            echo true;
        }
        echo false;
    }


    ## GiveMe Splash Page Donation Settings
    function actionAdministrationDonation(){
        if(isset($_POST['save_donation'])) {
           $_POST['goal_reached'] = ($_POST['goal_reached']=='')? '':$_POST['goal_reached'];
           $_POST['donation_status'] = ($_POST['donation_status']=='')? '':$_POST['donation_status'];
           $this->_oDb->Donationupdate($_POST);
        } 

        if(isset($_POST['reset_donation'])) {
           $this->_oDb->DonationReset($_POST);
        }

        $lists = $this->_oDb->Donorlist();

        if(count($lists)>0) {
           $table='<table id="donorlist" class="display" cellspacing="0" width="100%">';
           $table.='<thead><tr><th>Title</th><th>Transaction ID</th><th>Vendor</th><th>Client</th><th>Type</th><th>Created</th></tr></thead><tbody>';
           foreach($lists as $list) {
            $user_id = $list['user_id'];
            $table.='<tr id="donorlist-'.$list['id'].'" alt="'.$list['id'].'"><td>Donation</td><td>'.$list['order_id'].'</td><td><a href="'.getProfileLink($user_id).'">'.getNickName($user_id).'</a></td><td><a href="'.getProfileLink($list['user_id']).'">'.getNickName($list['user_id']).'</a></td><td>Donation</td><td>'.$list['created'].'</td></tr>';
            $amount += $list['amount'];
           }
           $table.='</tbody></table>';
        } else {
           $table=MsgBox('No Value Exist');
        }

        $donation = $this->_oDb->getDonation();
       
        $aVars=array();
        
        foreach($donation as $donate) {
            $_re = strcmp($donate['Name'],'goal_amount');
            if($_re == 0){
               $raised_status = ($donate['Value'] <= $amount)? 'Yes': 'No' ;
            }
            $aVars[$donate['Name']]=$donate['Value'];

        }
        $aVars['site_url'] = BX_DOL_URL_ROOT;
        $aVars['raised_amount'] = $amount;
        $aVars['raised_status'] = $raised_status;
        $sContent .= $this->_oTemplate->parseHtmlByName('giveme_donation',$aVars);
        return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));     
    }

    // Amount Donor name list

    function actionResetdonorist()
    {
        return $donation = $this->_oDb->getresetdonation();
     }
     
    function actionDonorlist(){
        $donation = $this->_oDb->getDonationTitle();
        
        $offet = $_POST['start'];
        $limit = $_POST['length'];
        $search = $_POST['search']['value'];    
        $getDonorlist = $this->_oDb->getDonationlist($offet,$limit,$search);
       
        $data =array();
        foreach ($getDonorlist as $value) {
                $data[]=array(
                'ID'=>$value['id'],
                'user_id'=>$value['user_id'],
                'order_id'=>$value['order_id'],
                'amount'=>'$'.$value['amount'],
                'credited'=>$value['created'],
                'title'=>$donation['value'],
                'nickname'=>$value['NickName'],
                );
        }
        $return_data = array(
            "draw"            => intval( $_POST['draw'] ),
            "recordsTotal"    => intval( $this->_oDb->getdonorlistsearch($search)),
            "recordsFiltered" => intval( $this->_oDb->getdonorlistsearch($search)),
            "data"            => $data
        );
        
            echo json_encode($return_data); exit();

    }

    // Slider page
    function actionAdministrationSlider($sParam1, $sParam2) {

        if ($sParam1 == 'resource') {
            // slider resource
            $slider = '';
            $sliderimg = '';
            $text = '';
            $link = '';
            $cancel = '';
            $unique = uniqid();

            if ($sParam2 == '') {
                //echo '<pre>';print_r($_POST);exit;
                if (isset($_POST['save_slidersettings'])) {
                    $imagearray = $this->imageupload($unique);
                    if ($imagearray['msg'] != 'error') {
                        $message = "<span style='color: green;'>Slider image Successfully Added</span>";
                        $this->_oDb->sliderresourceinsert($unique.str_replace(' ', '_',$imagearray['data']), $_POST['giveme_bigtxt_one'], $_POST['giveme_datalnk_one']);
                    } else {
                        $message = "<span style='color: red;'>".$imagearray['data']."</span>";
                    }
                }
                $sliderdatas = $this->_oDb->getslidersource();
                if (count($sliderdatas) > 0) {

                    $data = '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="small1 slidercontentlist"><thead><tr><th>Images</th><th>Bold Text</th><th>Action</th></tr></thead><tbody>';
                    foreach ($sliderdatas as $sliderdata) {
                        $data.='<tr class="slidearr' . $sliderdata['ID'] . '"><td class="simage"> <img class="lazy" src="' . BX_DOL_URL_ROOT . 'modules/bsetec/giveme/images/slider/' . $sliderdata['image'] . '"></td><td class="bgtext"> ' . $sliderdata['text'] . '</td><td> <a class="slideedit" href="' . BX_DOL_URL_ROOT . 'modules/?r=giveme/administration/slider/resource/' . $sliderdata['ID'] . '" id="slidearr' . $sliderdata['ID'] . '">Edit</a>|<a class="slidedelete" href="#" id="dslidearr' . $sliderdata['ID'] . '" onclick="deleterow(' . $sliderdata['ID'] . '); return false;">Delete</a></td></tr>';
                    }
                    $data.='</tbody></table>';
                }
            } else {

               
                $rowdata = $this->_oDb->getslidersourcebyid($sParam2);
                if (count($rowdata) > 0) {
                    if (isset($_POST['save_slidersettings'])) {
                      
                        
                        $imagearray = $this->imageupload($unique);
                             
                         
                        if ($imagearray['msg'] != 'error') {
                            $message = "<span style='color: green;'>Slider image Successfully Added</span>";
                            $this->_oDb->sliderresourceupdate($unique.str_replace(' ', '_', $imagearray['data']), $_POST['givme_bigtxt_one'], $_POST['giveme_datalnk_one'], $sParam2);
                            header('Location:' . BX_DOL_URL_ROOT . 'modules/?r=giveme/administration/slider/resource');
                        } else {
                            $this->_oDb->sliderresourceupdate($rowdata[0]['image'], $_POST['giveme_bigtxt_one'], $_POST['giveme_datalnk_one'], $sParam2);
                            if ($imagearray['data'] != 'Invalid file') {
                                $message = "<span style='color: red;'>".$imagearray['data']."</span>";
                            } else {
                                header('Location:' . BX_DOL_URL_ROOT . 'modules/?r=giveme/administration/slider/resource');
                            }
                        }
                    }
                    $slider = $rowdata[0]['image'];
                    $sliderimg = '<p><img width="125px" class="lazy" height="125px" src="' . BX_DOL_URL_ROOT . 'modules/bsetec/giveme/images/slider/' . $rowdata[0]['image'] . '" /></p>';
                    $text = $rowdata[0]['text'];
                    $link = $rowdata[0]['link'];
                } else {
                    $message = "slider resource not available";
                }
                $cancel = '<input type="button" name="cancel_slidersettings" value="Cancel" onclick="reloadurl();">';
            }

            $aVars = array(
                'message' => $message,
                'data' => $data,
                'site_url' => BX_DOL_URL_ROOT,
                'slider' => $slider,
                'sliderimg' => $sliderimg,
                'text' => $text,
                'link' => $link,
                'id' => $sParam2,
                'cancel' => $cancel
            );
            $sContent .= $this->_oTemplate->parseHtmlByName('genius_slider_resource', $aVars);
        } else {
            // slider settings
            if (isset($_POST['save_bannersettings'])) {
                $this->_oDb->slidersettingsupdate($_POST);
            }
            $sliderdatas = $this->_oDb->getslidersettings();
            $aVars = array();
            foreach ($sliderdatas as $sliderdata) {
                $aVars[$sliderdata['Name']] = $sliderdata['Value'];
            }
            $sContent .= $this->_oTemplate->parseHtmlByName('genius_slider', $aVars);
        }
        return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
    }

    function actionAdministrationAjax_block() 
    {
      //   echo $block = $_POST['block'];
    
        ob_start();
        $parent_id = $_POST['parent_id'];
        $block = $_POST['block'];
        $block_type = $_POST['block_type'];
        $block_name = $_POST['block_name'];
        if($block_type=='giveme' && $_POST['bg_image']!=''){
            $bg_image = BX_DOL_URL_ROOT.'modules/bsetec/giveme/images/bg_images/mastero/'.$_POST['bg_image'];
            $old_bg_image = $_POST['bg_image'];
        }
        if($splash_block['block']=='GiveME Join Today'){
            include('ajax_join_block.php');
        }else if($block!='GiveME Photos' && $block!='GiveME Newsletter'){
             include('ajax_block.php');
        }else{
            include('ajax_block_photos_news.php');
        }


        $ajax_block = ob_get_contents();
        ob_end_clean ();
        echo $ajax_block;exit;

    }
    // Home Page
    function actionAdministrationSplashpage() {
        $aVars = array();
        
        //save blcoks in DB
        if(isset($_POST['submit_block'])){
            $this->_oDb->save_blocks($_POST, $_FILES);
            $this->cleaecache();
        }elseif(isset($_POST['save_mastero_settings'])){
            $this->_oDb->updateMasteroSettings($_POST); 
        }elseif(isset($_POST['save_mastero_reset_settings'])){
            $this->_oDb->updateMasteroReset();
            $this->cleaecache();
        }

        $mastero_modules = $this->_oDb->getmodules('giveme');
        $default_modules = $this->_oDb->getmodules('default');
        
        $splash_blocks = $this->_oDb->getsplashblocks(); 
            
        $ajax_block = '';
        if(!empty($splash_blocks))
        {
        
            foreach ($splash_blocks as $splash_block) 
            {
                    ob_start();
                    $parent_id = $splash_block['parent_id'];
                    $block = $splash_block['block'];
                    $block_type = $splash_block['block_type'];
                    unset($bg_image);
                    $path = BX_DOL_URL_ROOT.'modules/bsetec/giveme/images/bg_images/';
                    $folderpath = BX_DIRECTORY_PATH_MODULES.'bsetec/giveme/images/bg_images/';
                    $old_bg_image = $splash_block['bg_image'];
                    if(!empty($splash_block['bg_image'])){
                        if(file_exists($folderpath.$splash_block['bg_image'])){
                            $bg_image = $path.$splash_block['bg_image'];
                        }else{
                            $bg_image = $path.'mastero/'.$splash_block['bg_image']; 
                        }   
                    }
                    
                    if($splash_block['block']=='GiveME Join Today'){
                        include('ajax_join_block.php');
                    }else if($splash_block['block']!='GiveME Photos' && $splash_block['block']!='GiveME Newsletter'){
                         include('ajax_block.php');
                    }else{
                         include('ajax_block_photos_news.php');
                    }
                    
                    $ajax_block.= ob_get_contents();
                    ob_end_clean ();
            }
        }
   
        $aVars = array(
            'bx_repeat:mastero_modules'=>$this->_oDb->getmodules('giveme'),
            'bx_repeat:default_modules'=>$default_modules,
            'ajax_block'=>$ajax_block,
            'no_image'=>BX_DOL_URL_ROOT.'modules/bsetec/giveme/images/no-image.png'
        );

        $aGetSettings   = $this->_oDb->getMasterSettings();
        foreach ($aGetSettings as $key => $value) {
            $aVars[$value['Name']] = $value['Value'];
        }

       $sContent .= $this->_oTemplate->parseHtmlByName('genius_mastero',$aVars);
       return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', 
       array('content' => $sContent));
    }

    
  

    // General page
    function actionAdministrationGeneral() {
        bx_import('BxDolAdminSettings');
        $oSettings = new BxDolAdminSettings(7);
        $meta_oSettings = new BxDolAdminSettings(0);

        //site favicon
        if (isset($_POST['upload_favicon']) && isset($_FILES['new_favicon'])){
            $mixedResultLogo = $this->setfavicon($_POST, $_FILES);
        }else if (isset($_POST['delete_favicon']))
            $this->deletefavicon();

        //--- Site's settings saving ---//
        if (isset($_POST['save']) && isset($_POST['cat']))
            $sResult = $oSettings->saveChanges($_POST);

        //--- Logo uploading ---//
        if (isset($_POST['upload']) && isset($_FILES['new_file']))
            $mixedResultLogo = $this->setLogo($_POST, $_FILES);
        else if (isset($_POST['delete']))
            $this->deleteLogo();

        //--- Promo text saving ---//
        if (isset($_POST['save_splash'])) {
            setParam('splash_editor', (process_db_input($_POST['editor']) == 'on' ? 'on' : ''));
            setParam('splash_code', process_db_input($_POST['code'], BX_TAGS_VALIDATE));
            setParam('splash_visibility', process_db_input($_POST['visibility']));
            setParam('splash_logged', (process_db_input($_POST['logged']) == 'on' ? 'on' : ''));
        }

        $aVars = array('page_code_logo' => $this->PageCodeLogo($mixedResultLogo),
            'page_code_favicon' => $this->PageCodeFavicon($mixedResultFavicon),
            'page_code_settings' => $oSettings->getForm(),
            'page_code_promo' => $this->PageCodePromo($mixedResultPromo)
                //'page_code_meta' => $oSettings->getForm(array('1','3','6','9','11','12','14','25','26'))
        );

        return $this->_oTemplate->parseHtmlByName('genius_general', $aVars);
    }

    //Favicon
    function PageCodeFavicon($mixedResultFavicon) {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'adm-settings-form-logo',
                'name' => 'adm-settings-form-logo',
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/general',
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array(),
            'inputs' => array(
                'upload_header_beg' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_settings_logo_header'),
                    'collapsable' => false,
                    'collapsed' => false
                ),
                'old_file' => array(
                    'type' => 'custom',
                    'content' => $this->getfav_icon(),
                    'colspan' => true
                ),
                'new_file' => array(
                    'type' => 'file',
                    'name' => 'new_favicon',
                    'caption' => _t('_adm_txt_settings_logo_upload'),
                    'value' => '',
                ),
                'upload' => array(
                    'type' => 'submit',
                    'name' => 'upload_favicon',
                    'value' => _t("_adm_btn_settings_upload"),
                )
            )
        );

        if ($this->isFavUploaded()) {
            $aControls = array(
                'type' => 'input_set',
                'name' => 'controls',
            );
            $aControls[] = $aForm['inputs']['upload'];
            $aControls[] = array(
                'type' => 'submit',
                'name' => 'delete_favicon',
                'value' => _t("_adm_btn_settings_delete"),
            );

            $aForm['inputs']['upload'] = $aControls;
        }

        $oForm = new BxTemplFormView($aForm);
        $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));

        if ($mixedResultLogo !== true && !empty($mixedResultLogo))
            $sResult = MsgBox(_t($mixedResultLogo), 3) . $sResult;
        return $sResult;
    }

     function isFavUploaded() {
        global $dir;

        $sFileName = getParam('sys_main_favicon');
        return $sFileName && file_exists($dir['mediaImages'] . $sFileName);
    }

    function setfavicon(&$aData, &$aFile) {
      
        global $dir;

        $aFileInfo = @getimagesize($aFile['new_favicon']['tmp_name']);
        
        if(empty($aFileInfo))
            return _t('_adm_txt_settings_file_not_image');

        $sExt = '';
        switch( $aFileInfo['mime'] ) {
           case 'image/jpeg': $sExt = 'jpg'; break;
           case 'image/gif':  $sExt = 'gif'; break;
           case 'image/png':  $sExt = 'png'; break;
           case 'image/vnd.microsoft.icon':  $sExt = 'ico'; break;
           case 'image/x-icon':  $sExt = 'ico'; break;
        }
       if(empty($sExt))
         return _t('_adm_txt_settings_file_wrong_format');

         $sExtfav = 'ico';
        
        if($sExt == 'ico'){
            $sFileName = mktime() . '.' . $sExt;
            $sFilePath = $dir['mediaImages'] . $sFileName;
            if(!move_uploaded_file($aFile['new_favicon']['tmp_name'], $sFilePath))
                return _t('_adm_txt_settings_file_cannot_move');
        }else{
            $sFileName = mktime() . '.' . $sExt;
            $sFilePath = $dir['mediaImages'] . $sFileName;

            if(!move_uploaded_file($aFile['new_favicon']['tmp_name'], $sFilePath))
                return _t('_adm_txt_settings_file_cannot_move');
               
            $iWidth = 16;
            $iHeight = 16;
            if(imageResize($sFilePath, $sFilePath, $iWidth, $iHeight) != IMAGE_ERROR_SUCCESS){
                return _t('_adm_txt_settings_image_cannot_resize');
            }
            $sFileName = mktime() . '.' .$sExtfav;
            $newName = $dir['mediaImages'] . $sFileName;
            @rename($sFilePath,$newName);
        }

        @unlink($dir['mediaImages'] . getParam('Giveme_sys_main_favicon'));
        setParam('Giveme_sys_main_favicon',$sFileName);
        $this->cleaecache();
        return true;
    }

    //delete favicon
    function deletefavicon() {
        global $dir;
        @unlink($dir['mediaImages'] . getParam('Giveme_sys_main_favicon'));
        setParam('Giveme_sys_main_favicon', '');
    }

    // get favicon
    function getfav_icon() {
        global $dir, $site;
        $sFileName = getParam('Giveme_sys_main_favicon');
        if (!$sFileName || !file_exists($dir['mediaImages'] . $sFileName))
            return '<a class="mainfaviconText" href="' . BX_DOL_URL_ROOT . '">' . getParam('site_title') . '</a>';

        return '<a href="' . BX_DOL_URL_ROOT . '"><img class="lazy" src="' . $site['mediaImages'] . $sFileName . '" class="mainfavicon" width="25" height="25" alt="fav_icon" /></a>';
    }

    //Logo
    function PageCodeLogo($mixedResultLogo) {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'adm-settings-form-logo',
                'name' => 'adm-settings-form-logo',
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/general',
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array(),
            'inputs' => array(
                'upload_header_beg' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_settings_logo_header'),
                    'collapsable' => false,
                    'collapsed' => false
                ),
                'old_file' => array(
                    'type' => 'custom',
                    'content' => $GLOBALS['oFunctions']->genSiteLogo(),
                    'colspan' => true
                ),
                'new_file' => array(
                    'type' => 'file',
                    'name' => 'new_file',
                    'caption' => _t('_adm_txt_settings_logo_upload'),
                    'value' => '',
                ),
                'resize_header_beg' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_settings_resize_header'),
                    'collapsable' => false,
                    'collapsed' => false
                ),
                'resize' => array(
                    'type' => 'checkbox',
                    'name' => 'resize',
                    'caption' => _t('_adm_txt_settings_resize_enable'),
                    'value' => 'yes',
                    'checked' => true
                ),
                'new_width' => array(
                    'type' => 'text',
                    'name' => 'new_width',
                    'caption' => _t('_adm_txt_settings_resize_width'),
                    'value' => '64'
                ),
                'new_height' => array(
                    'type' => 'text',
                    'name' => 'new_height',
                    'caption' => _t('_adm_txt_settings_resize_height'),
                    'value' => '64'
                ),
                'resize_header_end' => array(
                    'type' => 'block_end'
                ),
                'upload' => array(
                    'type' => 'submit',
                    'name' => 'upload',
                    'value' => _t("_adm_btn_settings_upload"),
                )
            )
        );

        if ($this->isLogoUploaded()) {
            $aControls = array(
                'type' => 'input_set',
                'name' => 'controls',
            );
            $aControls[] = $aForm['inputs']['upload'];
            $aControls[] = array(
                'type' => 'submit',
                'name' => 'delete',
                'value' => _t("_adm_btn_settings_delete"),
            );

            $aForm['inputs']['upload'] = $aControls;
        }

        $oForm = new BxTemplFormView($aForm);
        $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));

        if ($mixedResultLogo !== true && !empty($mixedResultLogo))
            $sResult = MsgBox(_t($mixedResultLogo), 3) . $sResult;
        return $sResult;
    }

    function isLogoUploaded() {
        global $dir;

        $sFileName = getParam('sys_main_logo');
        return $sFileName && file_exists($dir['mediaImages'] . $sFileName);
    }

    function setLogo(&$aData, &$aFile) {
        global $dir;

        $aFileInfo = @getimagesize($aFile['new_file']['tmp_name']);
        if (empty($aFileInfo))
            return '_adm_txt_settings_file_not_image';

        $sExt = '';
        switch ($aFileInfo['mime']) {
            case 'image/jpeg': $sExt = 'jpg';
                break;
            case 'image/gif': $sExt = 'gif';
                break;
            case 'image/png': $sExt = 'png';
                break;
        }
        if (empty($sExt))
            return '_adm_txt_settings_file_wrong_format';

        $sFileName = mktime() . '.' . $sExt;
        $sFilePath = $dir['mediaImages'] . $sFileName;
        if (!move_uploaded_file($aFile['new_file']['tmp_name'], $sFilePath))
            return '_adm_txt_settings_file_cannot_move';

        if (!empty($aData['resize'])) {
            $iWidth = (int) $aData['new_width'];
            $iHeight = (int) $aData['new_height'];
            if ($iWidth <= 0 || $iHeight <= 0)
                return '_adm_txt_settings_logo_wrong_size';

            if (imageResize($sFilePath, $sFilePath, $iWidth, $iHeight) != IMAGE_ERROR_SUCCESS)
                return '_adm_txt_settings_image_cannot_resize';
        }

        @unlink($dir['mediaImages'] . getParam('sys_main_logo'));
        setParam('sys_main_logo', $sFileName);

        return true;
    }

    function deleteLogo() {
        global $dir;

        @unlink($dir['mediaImages'] . getParam('sys_main_logo'));
        setParam('sys_main_logo', '');
    }

    //Splash page
    function PageCodePromo($mixedResultPromo) {
        $bEditor = getParam('splash_editor') == 'on';
        $aForm = array(
            'form_attrs' => array(
                'id' => 'adm-settings-form-splash',
                'name' => 'adm-settings-form-splash',
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/general',
                'method' => 'post',
            ),
            'params' => array(),
            'inputs' => array(
                'editor' => array(
                    'type' => 'checkbox',
                    'name' => 'editor',
                    'caption' => _t('_adm_txt_settings_splash_editor'),
                    'info' => _t('_adm_dsc_settings_splash_editor'),
                    'value' => 'on',
                    'checked' => $bEditor,
                    'attrs' => array(
                        'onchange' => 'javascript:splashEnableEditor(this)'
                    )
                ),
                'code' => array(
                    'type' => 'textarea',
                    'name' => 'code',
                    'caption' => '',
                    'value' => getParam('splash_code'),
                    'html' => $bEditor ? 2 : 0,
                    'colspan' => 2,
                    'tr_attrs' => array(
                        'id' => 'adm-bs-splash-editor-wrp'
                    ),
                    'attrs_wrapper' => array(
                        'style' => 'height:300px; width:100%;',
                    ),
                    'attrs' => array(
                        'id' => 'adm-bs-splash-editor',
                        'style' => 'height:300px; width:100%;',
                    )
                ),
                'visibility' => array(
                    'type' => 'radio_set',
                    'name' => 'visibility',
                    'caption' => _t('_adm_txt_settings_splash_visibility'),
                    'value' => getParam('splash_visibility'),
                    'values' => array(
                        BX_DOL_SPLASH_VIS_DISABLE => _t('_adm_txt_settings_splash_visibility_disable'),
                        BX_DOL_SPLASH_VIS_INDEX => _t('_adm_txt_settings_splash_visibility_index'),
                        BX_DOL_SPLASH_VIS_ALL => _t('_adm_txt_settings_splash_visibility_all')
                    ),
                    'dv' => '<br />'
                ),
                'logged' => array(
                    'type' => 'checkbox',
                    'name' => 'logged',
                    'caption' => _t('_adm_txt_settings_splash_logged'),
                    'value' => 'on',
                    'checked' => getParam('splash_logged') == 'on',
                ),
                'save_splash' => array(
                    'type' => 'submit',
                    'name' => 'save_splash',
                    'value' => _t("_adm_btn_settings_save"),
                )
            )
        );
        $oForm = new BxTemplFormView($aForm);

        $sContent = '';
        $sContent .= MsgBox(_t('_adm_txt_settings_splash_warning'));
        $sContent .= $oForm->getCode();
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('splash.html', array('content' => $sContent));
    }
    function actionAdministrationFooter() {
        $aVars = array();
        $group_id = 1;
        if (isset($_GET['group_id']))
            $group_id = (int) $_GET['group_id'];

        include BX_DIRECTORY_PATH_MODULES . 'bsetec/giveme/lib/tree.php';

        $menu = $this->_oDb->getMenu($group_id);
        $aVars['menu_ul'] = '<ul id="easymm"></ul>';
        if ($menu) {
            $tree = new Tree;
            foreach ($menu as $row) {
                $row[MENU_ID] = $row['id'];
                $row[MENU_PARENT] = $row['parent_id'];
                $row[MENU_TITLE] = $row['title'];
                $row[MENU_URL] = $row['url'];
                $row[MENU_CLASS] = $row['class'];
                $row[MENU_GROUP] = $row['group_id'];
                $row[MENU_POSITION] = $row['position'];
                $tree->add_row($row[MENU_ID], $row[MENU_PARENT], ' id="menu-' . $row[MENU_ID] . '" class="sortable"', $this->get_label($row));
            }
            $aVars['menu_ul'] = $tree->generate_list('id="easymm"');
        }

        //column manager start
        $columns = $this->_oDb->getMenuGroupsAll();
        // echo '<pre>';print_r($columns);exit;
        $aVars['column_ul'] = '<ul id="column-manager"></ul>';
        if ($columns) {
            $tree_col = new Tree;
            foreach ($columns as $column) {
                $column[MENU_ID] = $column['id'];
                $column[MENU_TITLE] = $column['title'];
                $column[SORT_ORDER] = $column['sort_order'];
                $column[IS_ACTIVE] = $column['is_active'];
                $column[SLUG] = $column['slug'];
                $tree_col->add_row($column[MENU_ID],0, ' id="menu-' . $column[MENU_ID] . '" class="sortable"', $this->get_label_column($column));
            }
            $aVars['column_ul'] = $tree_col->generate_list('id="column-manager"');
        }
        // echo '<pre>';print_r($aVars['column_ul']);exit;
        //column manager start

        $aVars['bx_if:group'] = array(
            'condition' => $group_id > 1,
            'content' => array()
        );
        $aVars['group_id'] = $group_id;
        $aVars['group_title'] = $this->_oDb->getMenuGroupTitle($group_id);
        $footerContent = $this->_oDb->getfootercontact();
        $aVars['footer_content'] = $footerContent[0]['Value'];
        $allMenuGroups = $this->_oDb->getMenuGroups();
        // echo '<pre>';print_r($allMenuGroups);exit;
        foreach ($allMenuGroups as $allMenuGroup) {
            $allResult .= '<li id="group-' . $allMenuGroup['id'] . '"><a href = "' . BX_DOL_URL_ROOT . 'modules/?r=giveme/administration/footer&group_id=' . $allMenuGroup['id'] . '">' . $allMenuGroup['title'] . '</a></li>';
        }
        $aVars['menu_groups'] = $allResult;
        $sContent .= $this->_oTemplate->parseHtmlByName('genius_footer', $aVars);
        return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
    }
     function actionAddfootercontent() {
        $aVars = array();
        $content = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_POST['footer-content'])) {
            $content['easy_menu_footer_content'] = $_POST['footer-content'];
            $this->_oDb->footercontactupdate($content);
            echo true;
        }
    }

 function actionAddmenu() {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        $aVars = array();
        if (isset($_POST['title'])) {
            $data[MENU_TITLE] = trim($_POST['title']);
            if (!empty($data[MENU_TITLE])) {
                $data[MENU_URL] = $_POST['url'];
                $data[MENU_CLASS] = $_POST['class'];
                $data[MENU_GROUP] = $_POST['group_id'];
                $data[MENU_POSITION] = $this->_oDb->getLastPosition($_POST['group_id']) + 1;
                $insertQuery = $this->_oDb->setEasyMenu($data);
                if ($insertQuery && $insertQuery > 0) {
                    $data[MENU_ID] = $insertQuery;
                    $response['status'] = 1;
                    $li_id = 'menu-' . $data[MENU_ID];
                    $response['li'] = '<li id="' . $li_id . '" class="sortable">' . $this->get_label($data) . '</li>';
                    $response['li_id'] = $li_id;
                } else {
                    $response['status'] = 2;
                    $response['msg'] = 'Add menu error.';
                }
            } else {
                $response['status'] = 3;
            }
            echo json_encode($response);
        }
    }
      function actionUpdatepositioncolumn($jsondata = '') {
       
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        $this->_oDb->updateStatuscolumn($jsondata);
        if (isset($_POST['column-manager'])) {
            $easymm = $_POST['column-manager'];
            $this->_oDb->updatePositioncolumn(0, $easymm);
            //echo true;
        }
        echo true;
    }
     function actionResetfooter() 
    {
        $this->_oDb->resetfooter();
        header('Location:' . BX_DOL_URL_ROOT . 'modules/?r=giveme/administration/footer');
    }

      function actionAddmenugroup() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_POST['title'])) {
            if (!empty($_POST['title'])) {
                $insertMenuGroup = $this->_oDb->setMenuGroup($_POST);
                if ($insertMenuGroup && $insertMenuGroup > 0) {
                    $response['status'] = 1;
                    $response['id'] = $insertMenuGroup;
                } else {
                    $response['status'] = 2;
                    $response['msg'] = 'Add menu group error.';
                }
            } else {
                $response['status'] = 3;
            }
            echo json_encode($response);
        } else {
            echo $this->_oTemplate->parseHtmlByName('menu_group_add', $aVars);
        }
    }
function actionDeletemenugroup() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $delete = $this->_oDb->deleteMenuGroup($id);
            if ($delete) {
                $deleteAllMenuItem = $this->_oDb->deleteAllMenuGroupItem($id);
                $response['success'] = true;
            } else
                $response['success'] = false;
            header('Content-type: application/json');
            echo json_encode($response);
        }
    }
  function actionDeletemenuitem() {
        $aVars = array();
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $menuIds = $this->_oDb->getDescendentMenuItem($id);
            foreach ($menuIds as $menuId) {
                $getAllId[] = $menuId['id'];
            }
            $implodeItem = (!empty($getAllId)) ? implode(',', $getAllId) . ',' . $id : $id;
            $delete = $this->_oDb->deleteItem($implodeItem);
            $response['success'] = ($delete) ? true : false;
            header('Content-type: application/json');
            echo json_encode($response);
        }
    }
     private function get_label_column($row) {
        $checked = $row[IS_ACTIVE] ? 'checked' : '';
        $un_checked = $row[IS_ACTIVE] ? '' : 'checked';
        $delete = $row[SLUG] ? '' : '<a href="#" class="delete-group" data-id="'.$row[MENU_ID].'" data-title="'.$row[MENU_TITLE].'">Delete</a>';
        $label = '<div class="ns-row">' .
                '<div class="ns-title">' . $row[MENU_TITLE] . '</div>' .
                '<div class="ns-class">' . $delete . '</div>' .
                '<div class="ns-url">' .
                'ON<input type="radio" class="sel-data" data-id=' ."$row[MENU_ID]" .' name=radio-' ."$row[MENU_ID]" . ' '.$checked.'/>'.
                'OFF<input type="radio" data-id=' ."$row[MENU_ID]" .' name=radio-' ."$row[MENU_ID]" . ' '.$un_checked.'/>'.
                '</div>' .
                '</div>';
        return $label;
    }

    // Colorization page
    function actionAdministrationColorization() {
        if (isset($_POST['save_color'])) {
          
           $colors = array('orange'=>'#ec5538',
                            'blue'=>'#2078bb',
                            'yellow'=>'#fff001',
                            'rose'=>'#ff65b0',
                            'green'=>'#00c8bd',
                            'custom'=> $_POST['giveme_custom_color'],
                            );

            $_POST['giveme_custom_color'] = !empty($_POST['giveme_bse_skin']) ? $colors[$_POST['giveme_bse_skin']] : '#f7931d';
           
            $_POST['giveme_bse_skin'] = $_POST['giveme_bse_skin'];
            $this->_oDb->colorupdate($_POST);  

        }

        if (isset($_POST['reset_color'])) {
            $_POST['giveme_custom_color'] = '#f7931d';
            $this->_oDb->colorreset($_POST);
        }

        $colordatas = $this->_oDb->getcolor();
        $aVars = array();
        foreach ($colordatas as $colordata) {
            $aVars[$colordata['Name']] = $colordata['Value'];
        }

        $aVars['custom_active'] = ($aVars['giveme_bse_skin'] == 'custom') ? 'active' : '';
        $sContent .= $this->_oTemplate->parseHtmlByName('genius_colorization', $aVars);
        return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
    }

    function actionDeleteslider() {
        if (isset($_POST['id'])) {
            $rowdata = $this->_oDb->getslidersourcebyid($_POST['id']);
            if (count($rowdata) > 0) {
                unlink(BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/slider/" . $rowdata[0]['image']);
            }
            $result = $this->_oDb->deleteslider($_POST['id']);
            echo $result;
        }
    }

    function actionReadimages($param1 = '') {
        $var = '<div id="mediaImages"><ul class="clsClearfix">';
        $directory = BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/parallax/";
        if ($handle = opendir($directory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $var.='<li><img  class="lazy" src="' . BX_DOL_URL_ROOT . 'modules/bsetec/giveme/images/parallax/' . $entry . '" width="150px" height="150px" alt="' . $entry . '" />';
                }
            }
            $var.='</ul><div class="mediaimageaction"><input type="button" value="insert" id="insertfrommedia" onclick="imagefrommedia(\'' . $param1 . '\')"><input type="button" value="Cancel" id="cancelfrommedia" onclick="parent.$.colorbox.close();"></div></div>';
            echo $var;
            closedir($handle);
        }
    }

    function imageupload($unique = '') {
        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
            if ($_FILES["file"]["error"] > 0)
                return array('msg' => 'error', 'data' => 'Error');
            else {
                $image_info = getimagesize($_FILES["file"]["tmp_name"]);
                if ($image_info[0] >= 1000 && $image_info[1] >= 300) {
                    move_uploaded_file($_FILES["file"]["tmp_name"], BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/slider/" . $unique.str_replace(' ', '_',$_FILES["file"]["name"]));
                    return array('msg' => 'success', 'data' => $_FILES["file"]["name"]);
                } else
                    return array('msg' => 'error', 'data' => 'Image size must be more than 1000*300');
            }
        } else
            return array('msg' => 'error', 'data' => 'Invalid file');
    }

    function parallaximageupload() {
        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["files"]["name"][0]);
        $extension = end($temp);
        if ((($_FILES["files"]["type"][0] == "image/gif") || ($_FILES["files"]["type"][0] == "image/jpeg") || ($_FILES["files"]["type"][0] == "image/jpg") || ($_FILES["files"]["type"][0] == "image/pjpeg") || ($_FILES["files"]["type"][0] == "image/x-png") || ($_FILES["files"]["type"][0] == "image/png")) && in_array($extension, $allowedExts)) {
            if ($_FILES["files"]["error"][0] > 0)
                return array('msg' => 'error', 'data' => 'Error');
            else {
                $image_info = getimagesize($_FILES["files"]["tmp_name"][0]);
                if ($image_info[0] >= 1000 && $image_info[1] >= 300) {
                    if (!file_exists(BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/parallax/" . $_FILES["files"]["name"][0])) {
                        move_uploaded_file($_FILES["files"]["tmp_name"][0], BX_DIRECTORY_PATH_MODULES . "bsetec/giveme/images/parallax/" . $_FILES["files"]["name"][0]);
                        return array('msg' => 'success', 'data' => $_FILES["files"]["name"][0]);
                    } else
                        return array('msg' => 'error', 'data' => 'Image Name Already Exist');
                } else
                    return array('msg' => 'error', 'data' => 'Image size must be more than 1000*300');
            }
        } else
            return array('msg' => 'error', 'data' => 'Invalid file');
    }

    function actionImageupload() {
        $result = $this->parallaximageupload();
        echo json_encode($result);
    }
    
    //service Injections
    function serviceFavicon() {
        global $dir, $site;
        $sFileName = getParam('Giveme_sys_main_favicon');
        if (!$sFileName || !file_exists($dir['mediaImages'] . $sFileName)) return '';
        return '<link rel="shortcut icon" href="'.$site['mediaImages']. $sFileName . '">'; 
    }

    

    function serviceBodyclass() {
        $headerback = $this->_oDb->checkcolor('header_background_img');
        $bodyback = $this->_oDb->checkcolor('design_background_img');
        $userid = getLoggedId();
        if ($headerback != "" && $headerback != "none")
            echo " headerbackexist";
        if ($bodyback != "" && $bodyback != "none")
            echo " bodybackexist";

        echo ($userid) ? " memberin" : " membernot";

        $visible = BX_DOL_URL_ROOT;
        $home = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $home1 = explode("?", $home);
        if ($visible == $home || $visible . 'index.php' == $home1[0]) {
            echo " homepage";
            if ($this->_oDb->checkslidersettings('giveme_slide_visibility') != 'disable') {
                $sliderdatas = $this->_oDb->getslidersource();
                echo (count($sliderdatas) > 0) ? " bannertrue" : " bannerfalse";
            } else
                echo " bannerfalse";
        }
    }

function serviceSlider() {
        if($this->_oDb->checkslidersettings('giveme_slide_visibility')!='disable') {
           $visible = BX_DOL_URL_ROOT;
           $home = $this->curPageURL();
           $home1=explode("?", $home);
           if( $visible == $home || $visible.'index.php' == $home1[0]) {
              //slider for homepage
              $sliderdatas=$this->_oDb->getslidersettings();
              $aVars=array();
              foreach($sliderdatas as $sliderdata) {
                 $aVars[$sliderdata['Name']]=$sliderdata['Value'];
              }
              
         $donation = $this->_oDb->getDonation();
         foreach($donation as $donate) {
            if($donate['Name']== 'goal_date'){
                $date_status = $this->dateDiff(date('Y-m-d',time()), $donate['Value']);
            }
        }
        $datestatus = ($date_status==0)?'1 day':$date_status+1 .' days';

             
             if($date_status >= 0)
                $button = (getLoggedId()>0)? '<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myDonate">Donate</button>': '<div class="join-button"><a href="javascript:void(0)" onclick="showPopupJoinForm(); return false;">donate now</a></div>';
            else
                $button = (getLoggedId()>0)? '': '<div class="join-button"><a href="javascript:void(0)" onclick="showPopupJoinForm(); return false;">donate now</a></div>';
             
              $sliderdatas=$this->_oDb->getslidersource();
              $slidereffect = $this->_oDb->checkslidersettings('giveme_slide_transition');
              if(count($sliderdatas)>0) {
                  $banner_code.='<div class="tp-banner-container"><div class="tp-banner" ><ul style="visibility:hidden;">';
                 foreach($sliderdatas as $sliderdata) {
                     $content=$$sliderdata['link']==''?'<p>'.$sliderdata['text'].'</p>':'<p>'.$sliderdata['text'].'</p><p><a href="'.$sliderdata['link'].'">Read More</a></p>';
                     $banner_code.='<li data-transition="'.$slidereffect.'" data-slotamount="7" data-masterspeed="1500" >                   
                    <img src="'.BX_DOL_URL_ROOT.'modules/bsetec/giveme/images/slider/'.$sliderdata['image'].'"  alt="slidebg1"  data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
                    <div class="tp-caption  skewfromrightshort fadeout"
                        data-x="0"
                        data-y="340"
                        data-speed="500"
                        data-start="1200"
                        data-easing="Power4.easeOut">'.$content.'
						<div class="banner_button">'.$button.'</div>
						</div>
					
                </li> ';
                 }
                 $banner_code.='</ul></div></div>';
                 $slidetime = $this->_oDb->checkslidersettings('giveme_slide_trantime');

                 $aVars = array_merge($aVars, array (
                 'banner_code' => $banner_code,
                 'site_url' => BX_DOL_URL_ROOT,
                 'slidereffet' => $slidereffet,
                 'slidetime' => $slidetime,

                    
             ));
              return $this->_oTemplate->parseHtmlByName('slider',$aVars);
              }
           }
        }
    }

    function serviceTags(){
        return $this->_oTemplate->tagscontent();
    }

     function serviceEasyfooter() {
        $menugroup = $this->_oDb->getMenuGroupsAllActive();
        // echo '<pre>';print_r($menugroup);exit;
        $footercontact = $this->_oDb->checkfootercontact('easy_menu_footer_content');
        $rows = array_chunk($menugroup, '4'); 
        
        $result = '<div class="new_news clearfix footer-manage">
<div class="container">
<div class="row">
<div class="col-sm-12 newsletters clearfix">';
    foreach ($rows as $row) 
    {
           $logo=BX_DOL_URL_ROOT.'media/images/'.getParam('sys_main_logo');
          // $result.= '';
            $menugroup = $row;
            if (count($menugroup) > 0) {
     //echo '<pre>';print_r(count($menugroup));exit;
            $bootstrapno = 4;
           
            if (count($menugroup) > 0) 
            {
                foreach ($menugroup as $menulist) 
                {
                    if($menulist['slug'] == 'footer_content')
                    {
                        $footercontactdata = $this->_oDb->getfootercontact();
                        $result.='<div class="col-sm-4 addre givemefoot clearfix"><div class="foot"><a href=""><img alt="Giveme" class="mainLogo" src="'.$logo.'" style="max-width: 1150px;" /></a></div>'.$footercontactdata[0]['Value'] . '</div>';
                    }
                    elseif($menulist['slug'] == 'newsletter')
                    {
                         $result.=$this->serviceNews();
                    }
                    elseif($menulist['slug'] == 'donwloads')
                    {
                         $result.=$this->serviceDownloads();
                    }
                    elseif($menulist['slug'] == 'tags')
                    {
                         $result.=$this->serviceTags();
                    }
                    else
                    {
                        $result.='<div class="col-sm-4 quick givemefoot clearfix"><h3 class="title">' . $this->_oDb->getMenuGroupTitle($menulist['id']) . '</h3>' . $this->_oTemplate->getmenulinks($menulist['id']) . '</div>';
                    }
                }
            }
            $result.='';
        }
    }
    $result.='</div></div></div></div>';
        // echo '<pre>';print_r($rows);exit;
        $aVars = array(
            'result' => $result
        );

//echo '<pre>';print_r($result);exit;
        echo $this->_oTemplate->parseHtmlByName('footer', $aVars);
    }

     function serviceDownloads(){
        if(!BxDolInstallerUtils::isModuleInstalled("blogs"))
            return false;
        return $this->_oTemplate->getdownloads();
        
        //return $this->_oTemplate->getblogs();
    }

 // function serviceNewsletter(){

 //       $checkFooter = $this->_oDb->checkKabaliFooter();
 //       if( $checkFooter == 'disable')
 //         return false;

        
 //        bx_import('BxDolSubscription');
 //        global $site;
 //        $iUserId = isLogged() ? getLoggedId() : 0;
 //        $oSubscription = new BxDolSubscription();
        
 //        $sContent = '';
 //        if(!$oSubscription->_bDataAdded) {
 //            $sContent .= $oSubscription->_getJsCode();

 //            $aForm = array(
 //                'form_attrs' => array(
 //                    'id' => 'sbs_form',
 //                    'name' => 'sbs_form',
 //                    'action' => $oSubscription->_sActionUrl,
 //                    'method' => 'post',
 //                    'enctype' => 'multipart/form-data',
 //                    'onSubmit' => 'javascript: return ' . $oSubscription->_sJsObject . '.send(this);'
    
 //                ),
 //                'inputs' => array (
 //                    'direction' => array (
 //                        'type' => 'hidden',
 //                        'name' => 'direction',
 //                        'value' => ''
 //                    ),
 //                    'unit' => array (
 //                        'type' => 'hidden',
 //                        'name' => 'unit',
 //                        'value' => 'system'
 //                    ),
 //                    'action' => array (
 //                        'type' => 'hidden',
 //                        'name' => 'action',
 //                        'value' => ''
 //                    ),
 //                    'object_id' => array (
 //                        'type' => 'hidden',
 //                        'name' => 'object_id',
 //                        'value' => '0'
 //                    ),
 //                    'user_name' => array (
 //                        'type' => 'text',
 //                        'name' => 'user_name',
 //                        'caption' => _t('_sys_txt_sbs_name'),
 //                        'value' => '',
 //                        'attrs' => array (
 //                            'id' => 'sbs_name',
 //                            //'class'=>'form-control'
 //                        )
 //                    ),
 //                    'user_email' => array (
 //                        'type' => 'text',
 //                        'name' => 'user_email',
 //                        'caption' => _t('_sys_txt_sbs_email'),
 //                        'value' => '',
 //                        'attrs' => array (
 //                            'id' => 'sbs_email',
 //                            //'class'=>'form-control'

 //                        )
 //                    ),
 //                    'sbs_controls' => array (
 //                        'type' => 'input_set',
 //                        array (
 //                            'type' => 'submit',
 //                            'name' => 'sbs_subscribe',
 //                            'value' => _t('_sys_btn_sbs_subscribe'),
 //                            'attrs' => array(
 //                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'subscribe\')',
 //                                //'class'=>'form-control'
 //                            )
 //                        ),
 //                        array (
 //                            'type' => 'submit',
 //                            'name' => 'sbs_unsubscribe',
 //                            'value' => _t('_sys_btn_sbs_unsubscribe'),
 //                            'attrs' => array(
 //                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'unsubscribe\')',
 //                                //'class'=>'form-control'
 //                            )
 //                        ),
 //                    )
    
 //                )
 //            );
 //            $oForm = new BxTemplFormView($aForm);
 //            $sContent .= $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array(
 //                'content' => $oForm->getCode()
 //            ));

 //            $this->_bDataAdded = true;
 //        }
 //        $bDynamic = false;
 //        $sCssJs = '';
 //        $sCssJs .= $GLOBALS['oSysTemplate']->addCss('subscription.css', $bDynamic);
 //        $sCssJs .= $GLOBALS['oSysTemplate']->addJs('BxDolSubscription.js', $bDynamic);
 //        $sContent = ($bDynamic ? $sCssJs : '') . $sContent;
        
 //        $aVars['resul'] = $sContent;
 //        return $this->_oTemplate->parseHtmlByName('newsletter',$aVars);
 //    }

   

    /*
     * Changes Footer Management June 27 2015
     */

    function serviceFootermenu() {
       
        $aVars = array();
        $currentPage = explode('/', $_GET['r']);
        ($currentPage[2] == 'general' || $currentPage[2] == '') ? $aVars['general_active'] = 'class="current"' : $aVars['general_active'] = '';
        $currentPage[2] == 'splashpage' ? $aVars['homepage_active'] = 'class="current"' : $aVars['homepage_active'] = '';
        $currentPage[2] == 'footer' ? $aVars['footer_active'] = 'class="current"' : $aVars['footer_active'] = '';
        $currentPage[2] == 'colorization' ? $aVars['color_active'] = 'class="current"' : $aVars['color_active'] = '';
        $currentPage[2] == 'slider' ? $aVars['slider_active'] = 'class="current"' : $aVars['slider_active'] = '';
        $currentPage[2] == 'map' ? $aVars['map_active'] = 'class="current"' : $aVars['map_active'] = '';
        $currentPage[2] == 'donation' ? $aVars['donation_active'] = 'class="current"' : $aVars['donation_active'] = '';
        return $this->_oTemplate->parseHtmlByName('footer_menu', $aVars);
    }

   

    private function get_label($row) {
        $label = '<div class="ns-row">' .
                '<div class="ns-title">' . $row[MENU_TITLE] . '</div>' .
                '<div class="ns-url">' . $row[MENU_URL] . '</div>' .
                '<div class="ns-class">' . $row[MENU_CLASS] . '</div>' .
                '<div class="ns-actions">' .
                '<a href="#" class="edit-menu" title="Edit Menu">' .
                '<img class="lazy" src="' . BX_DOL_URL_ROOT . 'modules/bsetec/giveme/templates/base/css/images/edit.png" alt="Edit">' .
                '</a>' .
                '<a href="#" class="delete-menu">' .
                '<img class="lazy" src="' . BX_DOL_URL_ROOT . 'modules/bsetec/giveme/templates/base/css/images/cross.png" alt="Delete">' .
                '</a>' .
                '<input type="hidden" name="menu_id" value="' . $row[MENU_ID] . '">' .
                '</div>' .
                '</div>';
        return $label;
    }



    
    /*get splash*/
    function serviceGetSplash() {
       bx_import ('BxBaseIndexPageView');
       $module_blocks = $this->_oDb->getsplashblocks();
       $module_content = array();
       $path = BX_DOL_URL_ROOT . "modules/bsetec/giveme/images/bg_images/";
       $folderpath = BX_DIRECTORY_PATH_MODULES.'bsetec/giveme/images/bg_images/';
                
       foreach($module_blocks as $module_block){
            if(!empty($module_block['bg_image'])){
                if(file_exists($folderpath.$module_block['bg_image'])){
                    $bg_image = $path.$module_block['bg_image'];
                }else{
                    $bg_image = $path.'mastero/'.$module_block['bg_image']; 
                }   
            }else{
               $bg_image = '';
            }
           if($module_block['block_type']=='giveme'){
              $injection =  $this->_oDb->getGiveMEBlocks($module_block['parent_id']);
              $rendor_injection = $this->_oTemplate->processInjection($GLOBALS['_page']['name_index'],$injection['injection_key']);
              if($rendor_injection)
                $module_content[] = array('injections'=> $rendor_injection,'bg_image'=>$bg_image, 'bg_image_status'=>$bg_image ? 'common_bg' : 'common_bg');
           }else{
                $defaultblock =  $this->_oDb->getDefaultBlocks($module_block['parent_id']);
                $pageView = new BxBaseIndexPageView();
                $pageView->genBlock($module_block['parent_id'],$defaultblock);
                $page = $pageView->sCode;
                
                if(strpos($page, 'boxContent') !== false)
                    $module_content[] = array('injections'=>$page,'bg_image'=>$bg_image, 'bg_image_status'=>$bg_image ? 'common_bg' : '');
           }
        }
        $data = array(
            'bx_repeat:blocks'=>$module_content,
        );
        return $this->_oTemplate->parseHtmlByName('theme_splash', $data);
    }

    function serviceCombineModule(){
        return $this->_oTemplate->parseHtmlByName('combine_module', array('site_url'=>BX_DOL_URL_ROOT));
    }

    
    
    /*
    * Get Current Page Url
    */

    function curPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    function serviceNews(){
        $users=$this->_oDb->getemails(getLoggedId());
        bx_import('BxDolSubscription');
        global $site;
        $iUserId = isLogged() ? getLoggedId() : 0;
        $oSubscription = new BxDolSubscription();
        
        $sContent = '';
        if(!$oSubscription->_bDataAdded) {
            $sContent .= $oSubscription->_getJsCode();
         if(empty(getLoggedId())){
            $aForm = array(
                'form_attrs' => array(
                    'id' => 'sbs_form',
                    'name' => 'sbs_form',
                    'action' => $oSubscription->_sActionUrl,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                    'onSubmit' => 'javascript: return ' . $oSubscription->_sJsObject . '.send(this);'
    
                ),
                'inputs' => array (
                    'direction' => array (
                        'type' => 'hidden',
                        'name' => 'direction',
                        'value' => ''
                    ),
                    'unit' => array (
                        'type' => 'hidden',
                        'name' => 'unit',
                        'value' => 'system'
                    ),
                    'action' => array (
                        'type' => 'hidden',
                        'name' => 'action',
                        'value' => ''
                    ),
                    'object_id' => array (
                        'type' => 'hidden',
                        'name' => 'object_id',
                        'value' => '0'
                    ),
                    'user_name' => array (
                        'type' => 'text',
                        'name' => 'user_name',
                        'caption' => _t('_sys_txt_sbs_name'),
                        'value' => '',
                        
                        'attrs' => array (
                            'id' => 'sbs_name',
                            'placeholder'=>'Enter your first name',
                        )
                    ),
                    'user_email' => array (
                        'type' => 'text',
                        'name' => 'user_email',
                        'caption' => _t('_sys_txt_sbs_email'),
                        'value' => '',
                        
                        'attrs' => array (
                            'id' => 'sbs_email',
                            'placeholder'=>'Enter your email address',
                        )
                    ),
                    'sbs_controls' => array (
                        'type' => 'input_set',
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_subscribe',
                            'value' => _t('_sys_btn_sbs_subscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'subscribe\')',
                            )
                        ),
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_unsubscribe',
                            'value' => _t('_sys_btn_sbs_unsubscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'unsubscribe\')',
                            )
                        ),
                    )
    
                )
            );
        }else{

             $aForm = array(
                'form_attrs' => array(
                    'id' => 'sbs_form',
                    'name' => 'sbs_form',
                    'action' => $oSubscription->_sActionUrl,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                    'onSubmit' => 'javascript: return ' . $oSubscription->_sJsObject . '.send(this);'
    
                ),
                'inputs' => array (
                    'direction' => array (
                        'type' => 'hidden',
                        'name' => 'direction',
                        'value' => ''
                    ),
                    'unit' => array (
                        'type' => 'hidden',
                        'name' => 'unit',
                        'value' => 'system'
                    ),
                    'action' => array (
                        'type' => 'hidden',
                        'name' => 'action',
                        'value' => ''
                    ),
                    'object_id' => array (
                        'type' => 'hidden',
                        'name' => 'object_id',
                        'value' => '0'
                    ),
                    'user_name' => array (
                        'type' => 'text',
                        'name' => 'user_name',
                        'caption' => _t('_sys_txt_sbs_name'),
                        'value' => $users['NickName'],
                        'attrs' => array (
                            'id' => 'sbs_name',
                            'readonly' => true,
                        )
                         
                    ),
                    'user_email' => array (
                        'type' => 'email',
                        'name' => 'user_email',
                        'caption' => _t('_sys_txt_sbs_email'),
                        'value' => $users['Email'],
                        'attrs' => array (
                            'id' => 'sbs_email',
                            'readonly' => true,
                        )
                         

                    ),
                    'sbs_controls' => array (
                        'type' => 'input_set',
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_subscribe',
                            'value' => _t('_sys_btn_sbs_subscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'subscribe\')',
                            )
                        ),
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_unsubscribe',
                            'value' => _t('_sys_btn_sbs_unsubscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'unsubscribe\')',
                            )
                        ),
                    )
    
                )
            );


        }
            $oForm = new BxTemplFormView($aForm);
            $sContent .= $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array(
                'content' => $oForm->getCode()
            ));
            
            $this->_bDataAdded = true;
        }
        $bDynamic = false;
        $sCssJs = '';
        $sCssJs .= $GLOBALS['oSysTemplate']->addCss('subscription.css', $bDynamic);
        $sCssJs .= $GLOBALS['oSysTemplate']->addJs('BxDolSubscription.js', $bDynamic);
        $sContent = ($bDynamic ? $sCssJs : '') . $sContent;
        
       // echo '<pre>';print_r($sContent);exit;

        $aVars['resul'] = $sContent;
        $aVars['map'] = $this->_oDb->getMap();

        return $this->_oTemplate->parseHtmlByName('newsletter',$aVars);
    }
    
   

    // Newsletter injection
    function serviceNewsletter(){
        $users=$this->_oDb->getemails(getLoggedId());
        bx_import('BxDolSubscription');
        global $site;
        $iUserId = isLogged() ? getLoggedId() : 0;
        $oSubscription = new BxDolSubscription();
        
        $sContent = '';
        if(!$oSubscription->_bDataAdded) {
            $sContent .= $oSubscription->_getJsCode();
         if(empty(getLoggedId())){
            $aForm = array(
                'form_attrs' => array(
                    'id' => 'sbs_form',
                    'name' => 'sbs_form',
                    'action' => $oSubscription->_sActionUrl,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                    'onSubmit' => 'javascript: return ' . $oSubscription->_sJsObject . '.send(this);'
    
                ),
                'inputs' => array (
                    'direction' => array (
                        'type' => 'hidden',
                        'name' => 'direction',
                        'value' => ''
                    ),
                    'unit' => array (
                        'type' => 'hidden',
                        'name' => 'unit',
                        'value' => 'system'
                    ),
                    'action' => array (
                        'type' => 'hidden',
                        'name' => 'action',
                        'value' => ''
                    ),
                    'object_id' => array (
                        'type' => 'hidden',
                        'name' => 'object_id',
                        'value' => '0'
                    ),
                    'user_name' => array (
                        'type' => 'text',
                        'name' => 'user_name',
                        'caption' => _t('_sys_txt_sbs_name'),
                        'value' => '',
                        'attrs' => array (
                            'id' => 'sbs_name'
                        )
                    ),
                    'user_email' => array (
                        'type' => 'text',
                        'name' => 'user_email',
                        'caption' => _t('_sys_txt_sbs_email'),
                        'value' => '',
                        'attrs' => array (
                            'id' => 'sbs_email'
                        )
                    ),
                    'sbs_controls' => array (
                        'type' => 'input_set',
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_subscribe',
                            'value' => _t('_sys_btn_sbs_subscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'subscribe\')',
                            )
                        ),
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_unsubscribe',
                            'value' => _t('_sys_btn_sbs_unsubscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'unsubscribe\')',
                            )
                        ),
                    )
    
                )
            );
        }else{

             $aForm = array(
                'form_attrs' => array(
                    'id' => 'sbs_form',
                    'name' => 'sbs_form',
                    'action' => $oSubscription->_sActionUrl,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                    'onSubmit' => 'javascript: return ' . $oSubscription->_sJsObject . '.send(this);'
    
                ),
                'inputs' => array (
                    'direction' => array (
                        'type' => 'hidden',
                        'name' => 'direction',
                        'value' => ''
                    ),
                    'unit' => array (
                        'type' => 'hidden',
                        'name' => 'unit',
                        'value' => 'system'
                    ),
                    'action' => array (
                        'type' => 'hidden',
                        'name' => 'action',
                        'value' => ''
                    ),
                    'object_id' => array (
                        'type' => 'hidden',
                        'name' => 'object_id',
                        'value' => '0'
                    ),
                    'user_name' => array (
                        'type' => 'hidden',
                        'name' => 'user_name',
                        'caption' => _t('_sys_txt_sbs_name'),
                        'value' => $users['NickName'],
                        'attrs' => array (
                            'id' => 'sbs_name'
                        )
                    ),
                    'user_email' => array (
                        'type' => 'hidden',
                        'name' => 'user_email',
                        'caption' => _t('_sys_txt_sbs_email'),
                        'value' => $users['Email'],
                        'attrs' => array (
                            'id' => 'sbs_email'
                        )
                    ),
                    'sbs_controls' => array (
                        'type' => 'input_set',
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_subscribe',
                            'value' => _t('_sys_btn_sbs_subscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'subscribe\')',
                            )
                        ),
                        array (
                            'type' => 'submit',
                            'name' => 'sbs_unsubscribe',
                            'value' => _t('_sys_btn_sbs_unsubscribe'),
                            'attrs' => array(
                                'onClick' => 'javascript:$("input[name=\'direction\']").val(\'unsubscribe\')',
                            )
                        ),
                    )
    
                )
            );


        }
            $oForm = new BxTemplFormView($aForm);
            $sContent .= $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array(
                'content' => $oForm->getCode()
            ));
            
            $this->_bDataAdded = true;
        }
        $bDynamic = false;
        $sCssJs = '';
        $sCssJs .= $GLOBALS['oSysTemplate']->addCss('subscription.css', $bDynamic);
        $sCssJs .= $GLOBALS['oSysTemplate']->addJs('BxDolSubscription.js', $bDynamic);
        $sContent = ($bDynamic ? $sCssJs : '') . $sContent;
        

        $aVars['resul'] = $sContent;
        $aVars['map'] = $this->_oDb->getMap();

      //  return $this->_oTemplate->parseHtmlByName('giveme-newsletter',$aVars);
    }
    

	
	function cleaecache(){
		bx_import('BxDolCacheUtilities');
		$aCacheTypes = array (
			array('action' => 'all', 'title' => _t('_adm_txt_dashboard_cache_all')),
			array('action' => 'db', 'title' => _t('_adm_txt_dashboard_cache_db')),
			array('action' => 'pb', 'title' => _t('_adm_txt_dashboard_cache_pb')),
			array('action' => 'template', 'title' => _t('_adm_txt_dashboard_cache_template')),
			array('action' => 'css', 'title' => _t('_adm_txt_dashboard_cache_css')),
			array('action' => 'js', 'title' => _t('_adm_txt_dashboard_cache_js')),
			array('action' => 'users', 'title' => _t('_adm_txt_dashboard_cache_users')),
			array('action' => 'member_menu', 'title' => _t('_adm_txt_dashboard_cache_member_menu')),
		);
		$oCacheUtilities = new BxDolCacheUtilities();
		$oCacheUtilities->clear($r['action']);
		foreach ($aCacheTypes as $r) {
                $aResult = $oCacheUtilities->clear($r['action']);
        }
	}

    ## GiveME Template Splash Functionality

    public function serviceBlogs(){
     if(!BxDolInstallerUtils::isModuleInstalled("blogs"))
        return false;

     $blogs =  $this->_oDb->getSplashPageBlogs();

     $i=1;
     $blog_detail ='';
     foreach ($blogs as  $blog) {
         $_blogs[]=array(
            'photo'=>$blog['PostPhoto'],
            'id'=>$blog['PostID'],
            'title'=>substr($blog['PostCaption'],0,50),
            'uri'=>$blog['PostUri'],
            'content'=>substr(strip_tags($blog['PostText']),0,145),
            'date'=>date('d',$blog['PostDate']),
            'month'=>date('M',$blog['PostDate']),
            'year'=>date('Y',$blog['PostDate']),
            'url'=>BX_DOL_URL_ROOT,
            'arrow'=>$this->ArrowKey($i),
            );
         if($i==2)
            $down='float:right';
         else $down='';
         if($i==3)
            $top='float:right';
         else $top ='';

         $blog_detail.='<div class="col-sm-4" style="'.$top.'"><div class="latest-leftimg lazy" data-original="http://www.appelsiini.net/projects/lazyload/img/grey.gif"  style="background:url('.BX_DOL_URL_ROOT.'media/images/blog/orig_'.$blog['PostPhoto'].') no-repeat;height:285px;background-size: cover; ">
            <div class="latest-lefttext"><span class="left-text">'.date('d',$blog['PostDate']).'</span>
            <span class="right-text">'.date('M',$blog['PostDate']) .'<br/>'. date('Y',$blog['PostDate']).'</span>
            </div>
            </div>
            </div>
            <div class="col-sm-4" style="'.$down.'">
            <div class="latest-centerbg">
            <div class='.$this->ArrowKey($i).'></div>
            <h3><a href='.BX_DOL_URL_ROOT.'blogs/entry/'.$blog['PostUri'].'>'.substr($blog['PostCaption'],0,50).'</a></h3>
            <p>'.substr(strip_tags($blog['PostText']),0,145).'</p>
            <div class="giving-button"><a href='.BX_DOL_URL_ROOT.'blogs/entry/'.$blog['PostUri'].'>Read More</a></div>
            </div>
            </div>';
         $i++;
     }

      $aVars = array(

            'bx_if:blogs'=>array(
            'condition'=>!empty($_blogs),
                'content'=>array(
                'blog'=>$blog_detail,
                  'bx_repeat:blogs'=>$_blogs,
                        'bx_if:blog_count'=>array(
                            'condition'=>count($_blogs)>=3,
                            'content'=>array(
                            'url'=>BX_DOL_URL_ROOT,
                        ),
                    ),
                ),
            ),
        );
     
     return $this->_oTemplate->parseHtmlByName('latest-blog',$aVars); 
    }

    public function ArrowKey($id){
        $Key = array('1'=>'arrow-left','2'=>'arrow-up','3'=>'arrow-right');
        return $Key[$id];
    }

    ## GiveME Join Tdoay
    public function serviceJoin(){
        $LoggedID = getLoggedId();
        $aVars=array(
            'url'=>BX_DOL_URL_ROOT,
            'bx_if:auth'=>array(
                    'condition'=>($LoggedID==0),
                    'content'=>array(
                    ),
                ),
            );
      return $this->_oTemplate->parseHtmlByName('join-today',$aVars);  
    }

    ##GiveME Events
    public function serviceEvents(){
    if(!BxDolInstallerUtils::isModuleInstalled("events"))
        return false;
       $Events = $this->_oDb->getSplashEvents();
      //echo '<pre>';print_r($Events);exit;
       foreach ($Events as $event) {
        $aImage = BxDolService::call('photos', 'get_photo_array', array($event['PrimPhoto'], 'thumb'), 'Search');
            $_events[]=array(
                'id'=>$event['ID'],
                'title'=>$event['Title'],
                'uri'=>$event['EntryUri'],
                'date'=>date('F d, Y',$event['EventStart']),
                'place'=>$event['City'].', '.$event['Country'],
                'photo'=>$aImage['file'],
                'url'=>BX_DOL_URL_ROOT,
            );
       }
        $aVars = array(
            'bx_if:events'=>array(
            'condition'=>!empty($_events),
                'content'=>array(
                  'bx_repeat:events'=>$_events,
                        'bx_if:event_count'=>array(
                            'condition'=>count($_events)>6,
                            'content'=>array(
                            'url'=>BX_DOL_URL_ROOT,
                        ),
                    ),
                ),
            ),
        );

       return $this->_oTemplate->parseHtmlByName('latest-events',$aVars);
    }

    ##GiveME Gallery
    function serviceGallery(){
        if(!BxDolInstallerUtils::isModuleInstalled("events"))
            return false;
        $Gallery = $this->_oDb->getSplashGallery();
        $category_group = $this->_oDb->getCategoryGroup();
        foreach ($Gallery as $gallery) {
        $aImage = BxDolService::call('photos', 'get_photo_array', array($gallery['PrimPhoto'], 'browse2x'), 'Search');
       
            $_gallery[]=array(
                'id'=>$gallery['ID'],
                'photo'=>$aImage['file'],
                'url'=>BX_DOL_URL_ROOT,
                'category'=>$gallery['Categories'],
            );
       }

       $aVars = array(
            'bx_if:gallery'=>array(
            'condition'=>!empty($_gallery),
                'content'=>array(
                  'bx_repeat:gallery'=>$_gallery,
                  'bx_repeat:category'=>$category_group,
                ),
            ),
        );

       return $this->_oTemplate->parseHtmlByName('giveme-gallery',$aVars);
    }
    ##GiveME Splash page Donation 

    function serviceDonation(){
        $donation = $this->_oDb->getDonation();
        $total = $this->_oDb->DonationTotal();

        $aVars=array();
        foreach($donation as $donate) {
            if($donate['Name']== 'goal_date'){
                $aVars[$donate['Name']]= ($donate['Value']=='')?'Null':$this->dateDiff(date('Y-m-d',time()), $donate['Value']).' days';
                $date_status = $this->dateDiff(date('Y-m-d',time()), $donate['Value']);
            }
            else{
                $aVars[$donate['Name']]=$donate['Value'];
            }
        }
if($total=='')
    $total = 0;
 $datestatus = ($date_status==0)?'1 day':$date_status;
        $text = (getLoggedId()>0)?'<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myDonate">Donate</button>':'<div class="join-button"><a href="javascript:void(0)" onclick="showPopupJoinForm(); return false;">donate now</a>
</div>';
        $aVars['url'] =BX_DOL_URL_ROOT;
        $aVars['bx_if:donation_statuss'] =array(
                'condition' =>$aVars['donation_status']=='on',
                'content'=>array(
                        'title'=>$aVars['goal_title'],
                        'desc'=>$aVars['goal_desc'],
                        'amount'=>'$ '.$aVars['goal_amount'],
                        'days'=> ($datestatus < 0)? '0 days' : $datestatus,
                        'url'=>BX_DOL_URL_ROOT,
                        'raised'=>'$ '.$total,
                            'bx_if:donate_status'=>array(
                            'condition'=>($date_status >= 0),
                            'content'=>array(
                                    'url'=>BX_DOL_URL_ROOT,
                                    'popup'=>$text,
                                ),
                            ),
                    ),
            );   
        return $this->_oTemplate->parseHtmlByName('giveme-donation',$aVars);
    }
    //Get number of days deference between current date and given date.
    function dateDiff($start, $end) {
          $start_ts = strtotime($start);
          $end_ts = strtotime($end);
          
          $diff = $end_ts - $start_ts;
          return round($diff / 86400);
    }

    function actionDonateSubmit(){
       $amount =$_POST['amt'];
       $user_id = getLoggedId();
       if(isset($amount)){
         $this->_oDb->removeCart($user_id);
         //set session
         $oSession = BxDolSession::getInstance();
         $oSession->setValue('buyer_id',$user_id);
         $oSession->setValue('amount',$amount);
         $v= BxDolService::call('payment','add_to_cart', array(0,$this->_oConfig->getId(),$amount,1)); 
         return true;
        }
    }

    //payment gateway service call
    function serviceGetCartItem($user_id, $amount)
    {

        //check session
         $oSession = BxDolSession::getInstance();
            $amount = $oSession->getValue('amount',$amount);
        return array (
           'id' => $user_id,
           'title' => 'Donation',
           'description' => 'Donation for ',
           'url' => BX_DOL_URL_ROOT,
           'price' =>$amount,
        );
    }
    function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId)
    {
        $userid = getLoggedId();
        $oSession = BxDolSession::getInstance();
         $amount = $oSession->getValue('amount');
          $userid = getLoggedId();
          if($oSession->getValue('buyer_id')!=false){
             $this->_oDb->InsertDonation($iItemId,$oSession->getValue('buyer_id'), $sOrderId,$amount);
          }

          $oSession->unsetValue('amount');
          $users = getProfileInfo($userid);

          //Donation Notification mail
            $aTemplate = $this->getTemplate('t_Donation',$userid);
            $recipient  = $users['Email'];
            $aPlus = array();
            $aPlus['date'] = date('d-m-Y');
            $aPlus['donate_number'] = $sOrderId;
            $aPlus['customer_name'] = $users['FullName'];
            $aPlus['amount'] =$iItemId;
            $aPlus['donate_on'] = date('d-m-Y H:i:s');
            $aPlus['status'] = 'completed';
            $aPlus['email_to'] = $users['Email'];
            $aPlus['link'] = BX_DOL_URL_ROOT;
            
            sendMail($recipient,getParam('site_name'). ' ' .$aTemplate['Subject'], $aTemplate['Body'],$userid, $aPlus, 'html', false, true );
            sendMail(getParam('site_email_notify'), getParam('site_name'). ' ' .$aTemplate['Subject'], $aTemplate['Body'],$userid, $aPlus, 'html', false, true );
            
        $oSession->unsetValue('buyer_id');
        $Donation = array (
               'user_id' => $userid,
               'order_id' => $sOrderId,
               'status' => 1
            );
        $oSession->setValue('payment_success', $Donation);
        return array (
           'id' => $user_id,
           'title' => 'Donation',
           'description' => 'Donation for ',
           'url' => BX_DOL_URL_ROOT,
           'price' =>$amount,
        ); 
    }

    function servicePaymentSuccess(){
        $user_loged = getLoggedId();
        $oSession = BxDolSession::getInstance();
        if($user_loged && $oSession->getValue('payment_success')) {
            header('Location:' . BX_DOL_URL_ROOT . 'm/giveme/paymentsuccess');
            exit;
        }
   }

   function actionPaymentsuccess(){
        $this->_oTemplate->pageStart();
        $user_loged = getLoggedId();
        $oSession = BxDolSession::getInstance();
        if($user_loged && $oSession->getValue('payment_success')) {
            $this->_oDb->removeCart($user_loged);
             $return = $oSession->getValue('payment_success');
             $oSession->unsetValue('payment_success');
                $aVars['url'] =BX_DOL_URL_ROOT;
            $sContent = $this->_oTemplate->parseHtmlByName('giveme-payment-success',$aVars);
            echo DesignBoxContent(_t('giveme'), $sContent); // display box
            $this->_oTemplate->pageCode(_t(''));
        }
    }

    //get Email Template
    function getTemplate($sTemplateName, $iMemberId = 0 ) {
        bx_import('BxDolEmailTemplates');
        $oEmailTemplate = new BxDolEmailTemplates();
        return $oEmailTemplate->getTemplate($sTemplateName, $iMemberId);
    }

    ##GiveME Latest Photos
    function servicePhotos(){
        if(!BxDolInstallerUtils::isModuleInstalled("photos"))
         return false;
        $aVars['url']=BX_DOL_URL_ROOT;
        $Photos = $this->_oDb->getPhotos();
        foreach ($Photos as $Photo) {
        //change image issue
        $aImage = BxDolService::call('photos', 'get_photo_array', array($Photo['ID'], 'browse2x'), 'Search');
       
            $_photos[]=array(
                'id'=>$Photo['ID'],
                'photo'=>$aImage['file'],
                'url'=>BX_DOL_URL_ROOT,
                'title'=>$Photo['Title'],
            );
       }
       $aVars = array(
            'bx_if:photos'=>array(
            'condition'=>!empty($_photos),
                'content'=>array(
                  'bx_repeat:photos'=>$_photos,
                ),
            ),
        );
        return $this->_oTemplate->parseHtmlByName('giveme_photos',$aVars);
    }

 function serviceafterlogin(){
         $id = getLoggedId();
         $name = getNickName($id);
         if($id!=0){
            $browse = $GLOBALS['oFunctions']->getMemberAvatar($id);
             $thumb = str_replace('thumb', 'browse', $browse);
             $first_letter = substr($name, 0,1);
             $first_letter = ucfirst($first_letter);
              
            return $first_letter;
         }
         
     }

     function servicebeforelogin(){
        $id = getLoggedId();
        if(($id==0) || empty($id) ) {
            $data = array();
            return $this->_oTemplate->parseHtmlByName('beforelogin',$data);
        }
        return ;
     }
// end class

}
 
