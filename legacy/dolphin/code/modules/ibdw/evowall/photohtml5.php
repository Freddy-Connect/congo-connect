<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';

$namephotoalbum=trim($_POST['namephotoalbum']);
?>
<script>
 window.FileAPI = 
 {
     debug: false,
     cors: false,
     media: false,
     staticPath: '/plugins/'
 };
</script>
<script src="plugins/file-api/dist/FileAPI.min.js" type="text/javascript"></script>
<script src="plugins/file-api/plugins/FileAPI.exif.js" type="text/javascript"></script>

<div class="ibdw_evo_bt_list_choose" onclick="open_bt_list('x_evo_list');" id="fade_bt_listx_evo_list">
      <input type="hidden" value="0" id="mm_setmenux_evo_list" />
      <a class="bt_openx_evo_list" id="bt_open"><i alt="" class="sys-icon chevron-down"></i></a>
   
     <div class="ibdw_bt_superlist_swt" id="lista_btx_evo_list">
      <?php if($PhotoRegularM == 'on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciaregularfoto(\''.$namephotoalbum.'\');">'._t('_ibdw_evowall_uploadregular').'</a>';}?>
      <?php if($PhotoOtherM=='on') { echo '<a id="bottone_sub_elimina" href="m/photos/albums/my/add_objects/'.$namephotoalbum.'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?> 
     </div> 
</div>
<div id="photoconthtml5">
<iframe name="upload_file_frame" style="display: none;"></iframe>
            <script language="javascript" type="text/javascript" src="modules/boonex/photos/js/upload.js"></script>
            <script type="text/javascript">
                var oPhotoUpload = new BxPhotoUpload({
                    iOwnerId: <?php echo $_COOKIE['memberID'];?>
                });
            </script> 
              
	<div style="display: none; margin: 10px; padding: 10px; border: 2px solid #f60; border-radius: 4px;" id="oooops"><?php echo _t('_sys_txt_uploader_html5_not_supported');?>
	</div>


    <div class="b-button js-fileapi-wrapper bx-btn" id="buttons-panel">
        <div class="b-button__text"><?php echo _t('_sys_txt_select_files');?></div>
        <input type="file" multiple="" accept="image/*" class="b-button__input" name="files">
    </div>

    <div class="bx-def-margin-top clearfix" id="bx-files-preview">
        <div id="upload-loading-container" class="sys-loading">
    <div class="sys-loading-smog"></div>
    <div class="sys-loading-icon">
		<div class="spinner bx-def-margin-topbottom bx-def-margin-sec-leftright"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
    </div>
</div><form class="form_advanced" target="upload_file_frame" enctype="multipart/form-data" method="post" action="m/photos/albums/my/add_objects/<?php echo trim($namephotoalbum); ?>/owner/<?php echo getUsername($_COOKIE['memberID']); ?>?action=accept_multi_html5" name="upload" id="photo_upload_form"><input type="hidden" value="<?php echo trim($namephotoalbum); ?>" name="extra_param_album" class="form_input_hidden bx-def-font-inputs"><input type="hidden" value="<?php echo $tokenfinale;?>" name="csrf_token" class="form_input_hidden bx-def-font-inputs"><div class="form_advanced_wrapper photo_upload_form_wrapper">
	<div class="form_advanced_table"><fieldset class="bx-form-fields-wrapper"><div class="bx-form-element bx-form-element-submit bx-def-margin-top clearfix">
	<div class="bx-form-combined clearfix">
		<span class="bx-form-error"><i float_info=" " class="sys-icon exclamation-circle"></i></span><div class="input_wrapper input_wrapper_submit clearfix"><input type="submit" value="<?php echo _t('_bx_photos_submit');?>" name="submit_form" class="form_input_submit bx-btn"></div>

		
	</div>
</div></fieldset></div>
</div></form>
    </div>
</div>
	<script type="text/ejs" id="b-file-ejs">
		<div id="file-<%=FileAPI.uid(file)%>" class="js-file b-file b-file_<%=file.type.split('/')[0]%>">
			<div class="js-preview-container bx-def-border">
                <i class="sys-icon <%=icon[file.type.split('/')[0]]||icon.def%>"></i>                
			</div>
            <div class="b-progress bx-def-border"><div class="js-bar b-progress__bar"></div></div>
            <div>
				<div class="bx-def-margin-thd-top">
                    <div class="input_wrapper input_wrapper_text clearfix">
                        <input class="form_input_text bx-def-font-inputs" name="" value="<%=file.name.substr(0, file.name.lastIndexOf('.'))%>" disabled />
                    </div>
                </div> 
                <div class="bx-def-margin-thd-top b-file__abort clearfix"><a class="js-abort bx-btn bx-btn-small"><?php echo _t('_Cancel');?></a></div>
                <div class="bx-def-margin-thd-top b-file__info js-info"></div>
            </div>
		</div>
	</script>

	


            <div style="background-color:#ffdada;" class="bx-def-margin-top" id="accepted_files_block"></div>

            <div style="display:none;" id="photo_success_message"><div id="14412143904" class="MsgBox">
    <table class="MsgBox bx-def-margin-top bx-def-margin-bottom"><tbody><tr><td>
        <div class="msgbox_content bx-def-font-large bx-def-padding-sec">
            <?php echo _t('_bx_photos_upl_succ');?>
        </div>
    </td></tr></tbody></table>
	
</div>
</div>
            <div style="display:none;" id="photo_failed_file_message"><div id="1441214390492" class="MsgBox">
    <table class="MsgBox bx-def-margin-top bx-def-margin-bottom"><tbody><tr><td>
        <div class="msgbox_content bx-def-font-large bx-def-padding-sec">
            <?php echo _t('_sys_txt_upload_failed');?>
        </div>
    </td></tr></tbody></table>
	
</div>
</div>
            <div style="display:none;" id="photo_failed_message"><div id="1441214390316" class="MsgBox">
    <table class="MsgBox bx-def-margin-top bx-def-margin-bottom"><tbody><tr><td>
        <div class="msgbox_content bx-def-font-large bx-def-padding-sec">
            <?php echo _t('_bx_photos_upl_err');?>
        </div>
    </td></tr></tbody></table>
	
</div>
</div>
            <div style="display:none;" id="photo_embed_failed_message"><div id="1441214390542" class="MsgBox">
    <table class="MsgBox bx-def-margin-top bx-def-margin-bottom"><tbody><tr><td>
        <div class="msgbox_content bx-def-font-large bx-def-padding-sec">
            <?php echo _t('_bx_photos_emb_err');?>
        </div>
    </td></tr></tbody></table>
	
</div>
</div>

  <script>
		// Simple JavaScript Templating
		// John Resig - http://ejohn.org/ - MIT Licensed
		(function (){
			var cache = {};

			this.tmpl = function tmpl(str, data){
				// Figure out if we're getting a template, or if we need to
				// load the template - and be sure to cache the result.
				var fn = !/\W/.test(str) ?
						cache[str] = cache[str] ||
								tmpl(document.getElementById(str).innerHTML) :

					// Generate a reusable function that will serve as a template
					// generator (and which will be cached).
						new Function("obj",
								"var p=[],print=function(){p.push.apply(p,arguments);};" +

									// Introduce the data as local variables using with(){}
										"with(obj){p.push('" +

									// Convert the template into pure JavaScript
										str
												.replace(/[\r\t\n]/g, " ")
												.split("<%").join("\t")
												.replace(/((^|%>)[^\t]*)'/g, "$1\r")
												.replace(/\t=(.*?)%>/g, "',$1,'")
												.split("\t").join("');")
												.split("%>").join("p.push('")
												.split("\r").join("\\'")
										+ "');}return p.join('');");

				// Provide some basic currying to the user
				return data ? fn(data) : fn;
			};
		})();
	</script>

  <script type="text/javascript">
		jQuery(function ($){
			if( !(FileAPI.support.cors || FileAPI.support.flash) ){
				$('#oooops').show();
				$('#buttons-panel').hide();
			}

			$(document).on('mouseenter mouseleave', '.b-button', function (evt){
				$(evt.currentTarget).toggleClass('b-button_hover', evt.type == 'mouseenter');
			});

			if( FileAPI.support.dnd ) {
				$('#drag-n-drop').show();
				$(document).dnd(function (over){
					$('#drop-zone').toggle(over);
				}, function (files){
					onFiles(files);
				});
			}

			$('input[name="files"]').on('change', function (evt){
				var files = FileAPI.getFiles(evt);
				onFiles(files);
				FileAPI.reset(evt.currentTarget);
			});

			var FU = {
				icon: {
				    def:   'file-o',
                    image: 'file-image-o',
                    audio: 'file-audio-o',
                    video: 'file-video-o'
				},

                langs: {
                    abort: '<?php echo _t("_sys_txt_upload_abort");?>',
                    error: '<?php echo _t("_sys_txt_upload_failed");?>',
                    done: '<?php echo _t("_sys_txt_upload_done");?>',
                },

				files: [],
				index: 0,
				active: false,

                cleanup: function () {
                    if (FU.active)
                        return;
                    FU.files = [];
                    FU.index = 0;
                    FU.active = false;
                },

				add: function (file){
					FU.files.push(file);

					if( /^image/.test(file.type) ){
						FileAPI.Image(file).preview(240).rotate('auto').get(function (err, img){
							if( !err ){
								FU._getEl(file, '.js-preview-container')
									.html(img)
								;
							}
						});
					}
				},

				getFileById: function (id){
					var i = FU.files.length;
					while( i-- ){
						if( FileAPI.uid(FU.files[i]) == id ){
							return	FU.files[i];
						}
					}
				},

				start: function (){
					if (!FU.active && (FU.active = FU.files.length > FU.index))
						FU._upload(FU.files[FU.index]);
                    else if (FU.files.length && FU.files.length == FU.index)
                        onAllFilesComplete();
				},

				abort: function (id){
					var file = this.getFileById(id);
					if( file.xhr ){
						file.xhr.abort();
					}
				},

				_getEl: function (file, sel){
					var $el = $('#file-'+FileAPI.uid(file));
					return	sel ? $el.find(sel) : $el;
				},

				_upload: function (file){  
					if( file ) {
          var contenttypeis=file.type;
          var findvalcont = contenttypeis.indexOf("photo/");
          if (findvalcont != -1) 
          {
           myurl="<?php echo 'm/photos/albums/my/add_objects/'.trim($namephotoalbum).'/owner/'.getUsername($_COOKIE['memberID']).'?action=accept_html5';?>";
           mydata="<?php echo trim($namephotoalbum);?>";
           myiao="<?php echo 'Boolean(0)';?>";
           myimgt="<?php echo 'false';?>";
          }
          else
          {
           myurl="<?php echo 'm/photos/albums/my/add_objects/'.trim($namephotoalbum).'/owner/'.getUsername($_COOKIE['memberID']).'?action=accept_html5';?>";
           mydata="<?php echo trim($namephotoalbum);?>";
           myiao="<?php echo 'Boolean(1)';?>";
           myimgt=<?php echo '{"maxWidth":"2048","maxHeight":"2048","quality":0.86}';?>
          }
        
					file.xhr = FileAPI.upload({
							url: myurl,
              data: {"extra_param_album":mydata},
							files: { file: file },
              imageAutoOrientation: myiao,
              imageTransform: myimgt,
							upload: function () {
								FU._getEl(file).addClass('b-file_upload');
							},
							progress: function (evt) {
                                FU._getEl(file, '.js-bar').css('width', evt.loaded/evt.total*100+'%');
							},
							complete: function (err, xhr) {
                                var oResponse = $.parseJSON(xhr.response);
                                if (!err && oResponse && 'object' == typeof(oResponse.files) && !oResponse.files.length)
                                    err = xhr.statusText = 'error';
								var state = err ? 'error' : 'done';
                                var error = err ? (xhr.statusText || err) : state;

                                if (oResponse && oResponse.files) {
                                    for (var i=0 ; i < oResponse.files.length ; ++i) {
                                        if ('undefined' != typeof(oResponse.files[i].error) && oResponse.files[i].error.length) {
                                            state = 'error';
                                            error = oResponse.files[i].error;                                        
                                            continue;
                                        }

                                        if ('undefined' != typeof(oResponse.files[i].id) && parseInt(oResponse.files[i].id) > 0) {
                                            FU._getEl(file).find('input').removeAttr('disabled').attr('name', 'title-' + parseInt(oResponse.files[i].id))
                                        }
                                    }
                                }

								FU._getEl(file).removeClass('b-file_upload').addClass('b-file_completed');
                                FU._getEl(file, '.b-progress').animate({ opacity: 0 }, 200, function (){ $(this).hide() });
								FU._getEl(file, '.js-info').append('<b class="b-file__' + state + '">' + ('undefined' === typeof(FU.langs[error]) ? error : FU.langs[error]) + '</b>');

								FU.index++;
								FU.active = false;

								FU.start();
							}
						});
					}
				}
			};
            
            function reloadBlocks(){
                $('.page_block_container').filter(':not(#' + $('#accepted_files_block').parents('.page_block_container').attr('id') + ')').each(function () {  
                });                
            }

            function onAllFilesComplete(){
                $('#bx-files-preview input[type="submit"]').removeAttr('disabled');
                reloadBlocks();
            }

			function onFiles(files){
                $('#bx-files-preview input[type="submit"]').attr('disabled','disabled');

                var $Queue = $('#bx-files-preview form .bx-files-preview-container');
                if (!$Queue.size())
    				$Queue = $('<div class="bx-files-preview-container clearfix" />').prependTo('#bx-files-preview form');

				FileAPI.each(files, function (file){
					if( file.size >= parseInt('67108864') ){ 
						alert('File size can\'t be more than 64 Mb');
					}
					else if( file.size === void 0 ){
						$('#oooops').show();
						$('#buttons-panel').hide();
					}
					else {
                        $('#bx-files-preview').show();
						$Queue.prepend(tmpl($('#b-file-ejs').html(), { file: file, icon: FU.icon }));

						FU.add(file);
						FU.start();
					}
				});
			}


			$(document)
				.on('bx-files-cleanup', function () {
                    FU.cleanup();
                    $('.bx-files-preview-container').remove();
                    $('#bx-files-preview').hide();
                    reloadBlocks();
                    window.location.href=window.location.href;
				})
				.on('click', '.js-abort', function (evt) {
					FU.abort($(evt.target).closest('.js-file').attr('id').split('-')[1]);					
					evt.preventDefault();
				})
			;
		});
	</script>