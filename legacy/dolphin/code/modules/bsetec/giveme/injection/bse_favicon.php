<?php 

$favicon=getParam('Giveme_sys_main_favicon');
$sBaseUrl = BX_DOL_URL_ROOT;
if($favicon)
{
?>

<link href="<?php echo $sBaseUrl; ?>media/images/<?php echo $favicon; ?>" type="image/x-icon" rel="shortcut icon"/>

<?php 
}
?>