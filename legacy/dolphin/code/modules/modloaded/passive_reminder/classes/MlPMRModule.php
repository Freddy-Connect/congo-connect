<?php

bx_import('BxDolModuleDb');
bx_import('BxDolModule');
bx_import('BxDolPaginate');
bx_import('BxDolPageView');

class MlPMRModule extends BxDolModule {

    function MlPMRModule(&$aModule) {        
        parent::BxDolModule($aModule);
          
    }
		function _getTimeDifferene( $iStart, $iEnd )
		{
		    $aUts['start']      =     $iStart;
		    $aUts['end']        =     $iEnd ;
		
		    if( $aUts['start']!==-1 && $aUts['end']!==-1 )
		    {
		        if( $aUts['end'] >= $aUts['start'] )
		        {
								$iDiff  =  $aUts['end'] - $aUts['start'];
		            if( $iMonth=intval((floor($iDiff/2629743))) )
		                $iDiff = $iDiff % 2629743;
		            if( $iWeek=intval((floor($iDiff/604800))) )
		                $iDiff = $iDiff % 604800;
		            if( $iDay=intval((floor($iDiff/86400))) )
		                $iDiff = $iDiff % 86400;
		            if( $iHour=intval((floor($iDiff/3600))) )
		                $iDiff = $iDiff % 3600;
		            if( $iMinute=intval((floor($iDiff/60))) )
		                $iDiff = $iDiff % 60;
		            $iDiff    =    intval( $iDiff ); 
		     
		            return( array('month'=>$iMonth,'week'=>$iWeek,'day'=>$iDay, 'hour'=>$iHour, 'minute'=>$iMinute, 'second'=>$iDiff) );
		        }
		        else
		        {
		            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
		        }
		    }
		    else
		    {
		        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
		    }
		    return( false );
		}
		
		function servicePassiveReminderFriendsBlock()
			{
				
		   	if (!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) { // check access to the page
		       $this->_oTemplate->displayAccessDenied ();
		       return;
		   	}

		   	$iMemberID = (int)$_COOKIE['memberID'];
		   	$iDaysInterval = getParam('ml_passive_reminder_notify_interval');
		   	$sWhereParam = " AND p.`Status` = 'Active' AND p.`DateLastNav` < SUBDATE(NOW(), INTERVAL {$iDaysInterval} DAY) ";
				$iTotalNum = getFriendNumber($iMemberID, 1, 0, $sWhereParam);

		    if ( isset($_GET['per_page']) ) 
	        $iPerPage = (int) $_GET['per_page'];

		    if ( $iPerPage	<= 0 )
		        $iPerPage = 10;

		    if ( $iPerPage > 10 )
		            $iPerPage = 10;

		    $iCurPage = ( isset($_GET['page']) )	
		        ? (int) $_GET['page']
		        : 1;

		    if ( $iCurPage	<= 0 )
		        $iCurPage = 1;

    		$sLimitFrom = ( $iCurPage - 1 ) * $iPerPage;
    		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";

    		$aAllFriends = $this->_oDb->getMyPassiveFriends($iMemberID, $sWhereParam, $sqlLimit);

				if ( empty( $aAllFriends ) )
					return;
				$this->_oTemplate->pageStart(); 
				$this->_oTemplate->addCss('passive_row.css');

     		foreach ($aAllFriends as $sKey => $aRow) 
     		{
					$iDiff=$this->_getTimeDifferene( strtotime($aRow['DateLastNav']), time() );
			  	if ( $iDiff['month'] >= 1 )  {$s = $iDiff['month'] > 1 ? _t("_ml_passive_reminder_s"):'';$sDateLastNav = $iDiff['month'] .' '._t("_ml_passive_reminder_month").$s.' '._t("_ml_passive_reminder_ago").'</span><br>';}
			  	elseif ( $iDiff['week'] >= 1 )  {$s = $iDiff['week'] > 1 ? _t("_ml_passive_reminder_s"):'';$sDateLastNav = $iDiff['week'] .' '. _t("_ml_passive_reminder_week").$s.' '._t("_ml_passive_reminder_ago").'</span><br>';}
			  	elseif ( $iDiff['day'] >= 1 ) {$s = $iDiff['day'] > 1 ? _t("_ml_passive_reminder_s"):'';$sDateLastNav = $iDiff['day'] .' '. _t("_ml_passive_reminder_day").$s.' '._t("_ml_passive_reminder_ago").'</span><br>';}
    			$sThumb = get_member_icon( $aRow['ID'], 'none' );
	        $aPassiveFrnds[$sKey]['thumbnail'] = $sThumb;
	        $aPassiveFrnds[$sKey]['nick'] = '<a href="'.$aRow['NickName'].'">'.$aRow['NickName'].'</a>';
	        $aPassiveFrnds[$sKey]['actions'] = '<span class=smalltxt><a href="mail.php?mode=compose&recipient_id='.$aRow['ID'].'">'._t("_ml_passive_reminder_message").'</a></span>';
					$aPassiveFrnds[$sKey]['lastlogin'] = $sDateLastNav;
					$aPassiveFrnds[$sKey]['sVisited'] = _t("_ml_passive_reminder_visited");
				}

    		$sRequest = $_SERVER['PHP_SELF'] . '?';

    		// gen pagination block ;
    		$sRequest = $sRequest . '&page={page}&per_page={per_page}';

				$iBlockId = $this->_oDb->getBlockId();
    		$oPaginate = new BxDolPaginate
    		(
    			array
    			(
    				'page_url'	 => $sRequest,
    				'count'		 => $iTotalNum,
    				'per_page'	 => $iPerPage,
    				'page'		 => $iCurPage,
						'per_page_changer'	 => false,
						'page_reloader'		 => true,
						'on_change_page' => 'return !loadDynamicBlock('.$iBlockId.', \''.$sRequest.'\');',
						'on_change_per_page' => null,
    			)
    		);
	   					
				$sPagination = $oPaginate -> getPaginate();
				$sPerPageBlock = $oPaginate -> getPages( $iPerPage );
    		$sRequest = str_replace('{page}', '1', $sRequest);
    		$sRequest = str_replace('{per_page}', $iPerPage, $sRequest);					

			   $aVars = array ( // define template variables
			       'bx_repeat:passive_row' => $aPassiveFrnds,
			       'pagination'	  => $sPagination,
			   );
			  //print_r($aPassiveFrnds);
			  echo $this->_oTemplate->parseHtmlByName('passive_row', $aVars); // output posts list
			}


    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

	    $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
	    if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_ml_passive_reminder'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_ml_passive_reminder'), $sResult); // dsiplay box
        
        $this->_oTemplate->pageCodeAdmin (_t('_ml_passive_reminder')); // output is completed, admin page will be displaed here
    }    
}

?>
