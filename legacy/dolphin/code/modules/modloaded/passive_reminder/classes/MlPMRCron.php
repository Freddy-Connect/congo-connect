<?php

    bx_import('BxDolCron');
		bx_import('BxDolEmailTemplates');

    require_once('MlPMRModule.php');

    class MlPMRCron extends BxDolCron 
    {
        var $oModule;

        /** 
         * Class constructor;
         */
        function MlPMRCron()
        {
        	$this -> oModule = BxDolModule::getInstance('MlPMRModule'); 
        }

        function processing() 
        {
        		$iDaysInterval = getParam('ml_passive_reminder_notify_interval');
        		$iSendInterval = getParam('ml_passive_reminder_send_interval');
        		$aPassiveMembers = $this -> oModule -> _oDb -> getAllPassiveMembers($iDaysInterval, $iSendInterval);
        		$aEmailTemplate = $this -> oModule -> _oDb -> getEmailTemplate();

            if(!empty($aPassiveMembers)) 
            {
            	$aPlus['NickName'] = $aRow['NickName'];
            	foreach ($aPassiveMembers as $sKey => $aRow)
            	{
            		if ($aPlus || $aRow['ID']) 
	            	{
	            		$sSentDate = $this -> oModule -> _oDb ->getSentDate($aRow['ID']);
									
									if (!$sSentDate)
										$this -> oModule -> _oDb -> insertSentDate($aRow['ID'], $aRow['DateLastNav']);
									else
									{
										$iDiff = time() - strtotime($sSentDate);
										$iDay = intval(floor($iDiff/86400));
										if ($iDay < $iSendInterval)
											continue;
									}

					    		if(!is_array($aPlus))
					            $aPlus = array();
						    	$oEmailTemplates = new BxDolEmailTemplates();
							    $sMailSubject = $oEmailTemplates->parseContent($aEmailTemplate['Subject'], $aPlus, $aRow['ID']);
						    	$sMailBody = $oEmailTemplates->parseContent($aEmailTemplate['Body'], $aPlus, $aRow['ID']);
						    	$this -> oModule -> _oDb -> insertEmailQueue($aRow['Email'], $sMailSubject, $sMailBody);
					    		$this -> oModule -> _oDb -> updateSentDate($aRow['ID'], date( 'Y-m-d H:i:s' ));
					    	}
              }
            }
        }
    }