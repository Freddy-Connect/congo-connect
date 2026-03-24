<?php
$idprofile=getID($_REQUEST['ID']);
$buttonheight=28;
?>  
<script>
pastedev=0;
function pasted(element) 
{
 setTimeout(function() {inviaurl(element.value,element.value,<?php echo $idprofile?>);}, 0);
 chiuditutto();
 pastedev=1;
}
</script>
<div class="divspacer">   
 <div id="bloccotubox" style="clear: both;"></div>
</div>
 <?php if ($UrlPlugin=='on') {
?>
<div id="frammento_url" style="display:none;">
<div id="url_container">
<form action="javascript:inviaurl(<?php echo $idprofile;?>);">
<input id="urlspecial" type="text" onfocus="focuss()" onblur="blurr()" value="<?php echo _t('_ibdw_evowall_urlspecial');?>" onchange="if(pastedev==0){pasted(this);} else{pastedev=0;}" onpaste="pasted(this);">
</form>
</div>
</div>
<?php } ?>
 <div class="clear"></div>