<?php    
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
  include BX_DIRECTORY_PATH_MODULES.'ibdw/schoolcover/config.php';
  $idalbum = $_POST['id_album_predef']; 
  $mioid = $_POST['user'];
  $SchoolID=$_POST['SchoolID'];
  $modulepath="modules/ibdw/schoolcover/";
  $text_too_big=str_replace("AMOUNTMB",$maxfilesize,_t("_ibdw_schoolcover_max_size"));  
?> 
<div class="covercontainer">
 <div id="upboxtitle"><?php echo _t('_ibdw_schoolcover_uploadtitle');?></div>
 <div id="upboxdisc"><?php echo _t('_ibdw_schoolcover_uploaddesc');?></div>
<div onclick="closeuploader();stopmn=0;" class="exit_alb"><i class="sys-icon remove"></i></div>
    <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="sys-icon plus"></i>
                    <span><?php echo _t('_bx_files_add');?></span>
                    <input type="file" name="files[]" multiple id="selector">
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped" id="prwtable"><tbody class="files"></tbody></table>
    </form>
    <button id="ok" onclick="$('#loading_div').css('display','block');update_schoolcore();" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon chevron-right"></i><span><?php echo _t("_ibdw_schoolcover_formcontinue");?></span></button>
    <button id="no" onclick="cancel_schoolcore();stopmn=0;" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon remove"></i><span><?php echo _t("_ibdw_schoolcover_cancelupload");?></span></button>
    <div style="clear:both"></div>
</div>



<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size"><?php echo _t('_ibdw_schoolcover_processing');?></p>        
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="sys-icon upload"></i>
                    <span><?php echo _t('_ibdw_schoolcover_start');?></span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="sys-icon remove"></i>
                    <span><?php echo _t('_ibdw_schoolcover_cancelupload');?></span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script> 
<script id="template-download" type="text/x-tmpl">
</script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/jquery.fileupload.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/tmpl.min.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/load-image.all.min.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/jquery.fileupload-process.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/jquery.fileupload-image.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/jquery.fileupload-validate.js"></script>
<script src="<?php echo BX_DOL_URL_ROOT.$modulepath;?>js/jquery.fileupload-ui.js"></script>
<script>
var filenameis="";

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'modules/ibdw/schoolcover/temp/',
        maxFileSize: <?php echo $maxfilesize*1048576;?>,
        messages: {
                maxNumberOfFiles: '<?php echo _t("_ibdw_schoolcover_max_num_file");?>',
                acceptFileTypes: '<?php echo _t("_ibdw_schoolcover_wrongtype");?>',
                maxFileSize: '<?php echo $text_too_big;?>',
                minFileSize: '<?php echo _t("_ibdw_schoolcover_file_too_small");?>'
            },
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        autoUpload: true,
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $("#prwtable").css("display","none");
                filenameis=file.name;
                $("#ok").css("display","block");
                $("#no").css("display","block");
                $(".progress").css("display","none");
            });
        },
        stop: function (e) {
                if (e.isDefaultPrevented()) {
                    return false;
                }
                var that = $(this).data('blueimp-fileupload') ||
                        $(this).data('fileupload'),
                    deferred = that._addFinishedDeferreds();
                $.when.apply($, that._getFinishedDeferreds())
                    .done(function () {
                        that._trigger('stopped', e);
                    });
                that._transition($(this).find('.fileupload-progress')).done(
                    function () {
                        $(this).find('.progress')
                            .attr('aria-valuenow', '0')
                            .children().first().css('width', '0%');
                        $(this).find('.progress-extended').html('&nbsp;');
                        deferred.resolve();
                    }
                );
               $(".fileinput-button").css("display","inline-block");
               if ($('#ok').css('display') == 'block') $(".fileinput-button").css("display","none");
               else $(".fileinput-button").css("display","inline-block");  
            },
        progressall: function (e, data) {
            
            $(".fileinput-button").css("display","none");
            if (e.isDefaultPrevented()) {
                    return false;
                }
                var $this = $(this),
                    progress = Math.floor(data.loaded / data.total * 100),
                    globalProgressNode = $this.find('.fileupload-progress'),
                    extendedProgressNode = globalProgressNode
                        .find('.progress-extended');
                if (extendedProgressNode.length) {
                    extendedProgressNode.html(
                        ($this.data('blueimp-fileupload') || $this.data('fileupload'))
                            ._renderExtendedProgress(data)
                    );
                }
                globalProgressNode
                    .find('.progress')
                    .attr('aria-valuenow', progress)
                    .children().first().css(
                        'width',
                        progress + '%'
                    );
        }
    });
});

function update_block(SchoolID){
  $.ajax({
      type: "POST",
      data: "ajax=1"+"&SchoolID="+SchoolID,
      url: "modules/ibdw/schoolcover/core.php",
      success: function(data) {
        $("#pfblockconteiner").html(data);
      }
  });
}
function cancel_schoolcore()
{
 $("#modificaalbums").css("display","none");
 var r = confirm("<?php echo _t('_ibdw_schoolcover_delete_confirm');?>")
    if(r == true)
    { 
        $.ajax({
          url: '<?php echo BX_DOL_URL_ROOT.$modulepath."delete.php";?>',
          data: {'file' : "<?php echo BX_DIRECTORY_PATH_ROOT.$modulepath . 'temp/files/'?>" + filenameis, 'thumb' : "<?php echo BX_DIRECTORY_PATH_ROOT.$modulepath . 'temp/files/thumbnail/'?>" + filenameis },
          success: function(data) {
            //  $('#pfblockconteiner').append(data);
             
          },
          error: function (data) {
          //$('#pfblockconteiner').append(data);
             //alert("<?php echo _t('_ibdw_schoolcover_failed_delete');?>");
          }
        });
    }
}
function update_schoolcore()
{ 
 $.ajax({
          type: "POST",
          url: '<?php echo BX_DOL_URL_ROOT.$modulepath."upload/upload.php";?>',
          data: {'file' : "<?php echo BX_DIRECTORY_PATH_ROOT.$modulepath . 'temp/files/'?>" + filenameis, 'tempfile' : "<?php echo BX_DIRECTORY_PATH_ROOT.$modulepath . 'temp/files/thumbnail/'?>" + filenameis, 'user' : '<?php echo $mioid;?>','album':'<?php echo $idalbum;?>','SchoolID':'<?php echo $SchoolID;?>','filename' : filenameis},
          success: function (data) {
              
            closeuploader();
            update_block('<?php echo $SchoolID;?>'); 
          },
          error: function (data) {
             //alert("<?php echo _t('_ibdw_schoolcover_failed_upload');?>");
          }
        });
}
</script>

<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->

<!-- ENABLE ONLY FOR SOUND AND VIDEO-->
<!--
<script src="js/jquery.fileupload-audio.js"></script>
<script src="js/jquery.fileupload-video.js"></script>
-->