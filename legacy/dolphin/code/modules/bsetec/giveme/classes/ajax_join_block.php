<li class="single-block">
  <input type="hidden" name="parent_id[]" value="<?php echo $parent_id;?>">
  <input type="hidden" name="block_type[]" value="<?php echo $block_type;?>">
  <input type="hidden" class="block-name" name="block[]" value="<?php echo $block;?>">

  <input type="hidden" class="old-image" name="old_image[]" value="<?php echo isset($old_bg_image) ? $old_bg_image : '';?>">
<div class="top-blocks clearfix">
<h4 class="left-block"><?php echo $block;?></h4>
<ul class="clearfix">
  <li class="delete-block"><a href="javascript::"><i class="sys-icon trash"></i></a></li>
  <li class="handle-sort"><a href="javascript::"><i class="sys-icon arrows"></i></a></li>
</ul>
</div>
<div class="upload-img">
<!-- <div id="file-upload-text"></div> -->
<div style="position:relative">

<div class="browse_btn"><div class="<?php echo str_replace(' ', '_', $block);?> browse-section"><input type="file" name="bg_image[]" data-height="320" data-width="1400" data-id="<?php echo str_replace(' ', '_', $block);?>" onchange="document.getElementById('file-upload-text').innerHTML= this.value"><span>Change BG Image</span></div></div>

<img src="<?php echo isset($bg_image) ? $bg_image : BX_DOL_URL_ROOT.'modules/bsetec/giveme/images/no-image.png';?>" id="<?php echo str_replace(' ', '_', $block);?>" height="150px" width="100%" />
<!--<div class="<?php echo str_replace(' ', '_', $block);?> browse-section">
<input type="file" name="bg_image[]" class="file" data-id="<?php echo str_replace(' ', '_', $block);?>" data-height="450" data-width="1400" style="opacity:0;-moz-opacity:0 ;filter:alpha(opacity: 0); width: 100%; height: 150px;" onchange="document.getElementById('file-upload-text').innerHTML= this.value"/>
</div>-->


</div>
<ul class="edit-b clearfix">
  <li><a href="javascript::" class="delete-blockimage" data-id="<?php echo str_replace(' ', '_', $block);?>"><i class="sys-icon trash"></i></a></li>
</ul>
<h3>bg image</h3>
</div>
</li>