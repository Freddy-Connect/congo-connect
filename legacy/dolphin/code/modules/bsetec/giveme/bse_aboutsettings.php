<?php 

$abouttext= getParam('sys_main_abouttext');
?>

<form  enctype="multipart/form-data" action="<?php echo $sBaseUrl ?>modules/bse/bsecustom_theme/view_bsepanel.php" method="post">
<div class="formrow">		
<div class="formrowcotent-left clsFloatLeft">		
					<label>image </label> 
                    </div>
                   <div class="formrowcotent-right clsFloatLeft">	 
						<?php  echo $oBse->getfav_icon() ?>
                    				
					<input type="file" value="" name="imageabout" >
                    </div>
                    <div class="clear"></div>
                    </div>
                    
                  
					<input type="hidden" value="general" name="pagetab">
                    <div class="formrow buttonrow">
                    <div class="formrowcotent-left clsFloatLeft">
                    <label>&nbsp;</label>
                    </div>
                     <div class="formrowcotent-right clsFloatLeft">
					<input type="submit" value="upload" name="upload_about">
					<input type="submit" value="delete" name="delete_about">	
                    </div>	
                    <div class="clear"></div>						
                    </div>
					</form>       			