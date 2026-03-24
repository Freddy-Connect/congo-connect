$(document).ready(function()
{

	$("#selectpdf").on('click', function(event) {

		$.ajax({
			url:'modules/?r=linkedin_profile/serviceGeneratePDF',
			data: {expid:''},
			type: 'post',
			success:function(data)
			{
				
			}
		});

});
	
});

// starting of projects js	
$(document).on('click','.add-pro',function()
   {

   	/*alert('ok');*/
	/*if($('.prof_exp_form').is(':visible') == true)
		$('.prof_exp_form').hide();
	else
	{*/
		//typeof $(".prof_exp_form")[0].reset == "function" ? $(".prof_exp_form")[0].reset() : $(".prof_exp_form")[0].reset.click();
		//$('.Exp_Form').remove();
		$.ajax({
			url:'modules/?r=linkedin_profile/Getprojectform',
			data: {user_id:''},
			type: 'post',
			success:function(data)
			{
				$('.prof_project_form').html(data).show();
			}
		});

		return false;
		//('.prof_exp_form')[0].reset();
	//}
});

$(document).on('click','.add-cert',function()
   {

   	/*alert('ok');*/
	/*if($('.prof_exp_form').is(':visible') == true)
		$('.prof_exp_form').hide();
	else
	{*/
		//typeof $(".prof_exp_form")[0].reset == "function" ? $(".prof_exp_form")[0].reset() : $(".prof_exp_form")[0].reset.click();
		//$('.Exp_Form').remove();
		$.ajax({
			url:'modules/?r=linkedin_profile/getCertificateForm',
			data: {user_id:''},
			type: 'post',
			success:function(data)
			{
				$('.prof_certificate_form').html(data).show();
			}
		});

		return false;
		//('.prof_exp_form')[0].reset();
	//}
});

  $(document).on('click','.pro-form-cancel',function()
  {
	$('.prof_project_form').hide();
  });

  $(document).on('click','.cert-form-cancel',function()
  {
	$('.prof_certificate_form').hide();
  });



$(document).on('change','.prof_exp_form .still-going',function()
{
	if($(this).prop('checked') == true)
	{
		$(this).parents('.prof_exp_form').find('.end-date').hide();
		$(this).parents('.prof_exp_form').find('.EndDateMonth').val('');
		$(this).parents('.prof_exp_form').find('.EndYear').val('');
	}
	else
		$(this).parents('.prof_exp_form').find('.end-date').show();
});

$(document).on('change','.prof_certificate_form .still-going',function()
{
	if($(this).prop('checked') == true)
	{
		$(this).parents('.prof_certificate_form').find('.end-date').hide();
		$(this).parents('.prof_certificate_form').find('.EndDateMonth').val('');
		$(this).parents('.prof_certificate_form').find('.EndYear').val('');
	}
	else
		$(this).parents('.prof_certificate_form').find('.end-date').show();
});


$(document).on('submit','.project_form',function(e)
{
	e.preventDefault();
	hideformerrors($(this));
	if(validateProjectForm() == true)
	{
		$('.loading1').show();
		var serialized_vals = $(this).serialize();

		$.ajax({
			url:'modules/?r=linkedin_profile/postproject',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				$('.loading1').hide();
				//$('.prof_exp_form').hide();
				//alert(data);
				$('.prof_pro_main').html(data);	
			}
		});
		return false;
	}
	return false;
});

$(document).on('submit','.certificate_form',function(e)
{
	e.preventDefault();
	hideformerrors($(this));
	if(validateCertificateForm() == true)
	{
		$('.loading1').show();
		var serialized_vals = $(this).serialize();

		$.ajax({
			url:'modules/?r=linkedin_profile/postcertificate',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				$('.loading1').hide();
				//$('.prof_exp_form').hide();
				//alert(data);
				$('.prof_certificate_main').html(data);	
			}
		});
		return false;
	}
	return false;
});

function validateCertificateForm()
{
	var errors = [];
	/*Name Field*/
	if( $('#certificateName').val() == '')
		errors.push('certificateName');

	

	/*Date*/
	if( $('#cert_startDateMonth').val() == '' )
		errors.push('cert_startDateMonth');
	else if( $('#cert_StartYear').val() == '' )
		errors.push('cert_StartYear');
	else
	{
		var cur_date = new Date();
		var cur_year = cur_date.getFullYear();
		var cur_month = cur_date.getMonth();
		cur_month = parseInt(cur_month)+1;
		var st_month = $('#cert_startDateMonth').val();
		var st_year = $('#cert_StartYear').val();
		st_month = parseInt(st_month);
		if(st_year > cur_year)
			errors.push('cert_Fatal');
		else if(st_year == cur_year && st_month > cur_month)
			errors.push('cert_Fatal');
		else if($('.still-going').prop('checked') == false)
		{
			var end_month = $('#cert_EndDateMonth').val();
			var end_year = $('#cert_EndYear').val();
			end_month = parseInt(end_month);
			if(end_year != '' && end_month != '')
			{
				if(end_year > cur_year || end_year < st_year )
					errors.push('cert_Fatal');
				else if( (end_year == cur_year || end_year == st_year) && end_month < st_month)
					errors.push('cert_Fatal');
				else if( end_year == cur_year && end_year == st_year && end_month > cur_month)
					errors.push('cert_Fatal');
			}
		}
	}

	if(errors.length > 0)
	{
		$.each(errors,function(i,v)
		{
			$('#'+v+'-error').show();
		});
		return false;
	}

	return true;
}


function validateProjectForm()
{
	var errors = [];
	/*Name Field*/
	if( $('#projectName').val() == '')
		errors.push('projectName');

	

	/*Date*/
	if( $('#pro_startDateMonth').val() == '' )
		errors.push('pro_startDateMonth');
	else if( $('#pro_StartYear').val() == '' )
		errors.push('pro_StartYear');
	else
	{
		var cur_date = new Date();
		var cur_year = cur_date.getFullYear();
		var cur_month = cur_date.getMonth();
		cur_month = parseInt(cur_month)+1;
		var st_month = $('#pro_startDateMonth').val();
		var st_year = $('#pro_StartYear').val();
		st_month = parseInt(st_month);
		if(st_year > cur_year)
			errors.push('pro_Fatal');
		else if(st_year == cur_year && st_month > cur_month)
			errors.push('pro_Fatal');
		else if($('.still-going').prop('checked') == false)
		{
			var end_month = $('#pro_EndDateMonth').val();
			var end_year = $('#pro_EndYear').val();
			end_month = parseInt(end_month);
			if(end_year != '' && end_month != '')
			{
				if(end_year > cur_year || end_year < st_year )
					errors.push('pro_Fatal');
				else if( (end_year == cur_year || end_year == st_year) && end_month < st_month)
					errors.push('pro_Fatal');
				else if( end_year == cur_year && end_year == st_year && end_month > cur_month)
					errors.push('pro_Fatal');
			}
		}
	}

	if(errors.length > 0)
	{
		$.each(errors,function(i,v)
		{
			$('#'+v+'-error').show();
		});
		return false;
	}

	return true;
}

  //   function hideformerrors(obj)
   //  {
//	   obj.find('.error').hide();
 //    }

$(document).on('click','.prof_pro_main .each_container',function()
{
	var pro_id = $(this).data('expid');
	$.ajax({
		url:'modules/?r=linkedin_profile/getprojectform',
		data: {id:pro_id},
		type: 'post',
		success:function(data)
		{
			$('.prof_project_form').html(data).show();
			$('.pro-form-delete').show();
		}
	});
});

$(document).on('click','.prof_certificate_main .each_container',function()
{
	var certid = $(this).data('certid');
	$.ajax({
		url:'modules/?r=linkedin_profile/getcertificateform',
		data: {id:certid},
		type: 'post',
		success:function(data)
		{
			$('.prof_certificate_form').html(data).show();
			$('.cert-form-delete').show();
		}
	});
});

 $(document).on('click','.pro-form-delete',function()
{
	var id = $(this).parents('.project_form').find('input[name="pro_id"]').val();
	$.ajax({
		url:'modules/?r=linkedin_profile/removeproject',
		data: {pro_id:id},
		type: 'post',
		success:function(data)
		{
			$('.loading').hide();
			$('.prof_pro_main').html(data);	
		}
	});
});


 $(document).on('click','.cert-form-delete',function()
{
	var id = $(this).parents('.prof_certificate_main').find('input[name="cert_id"]').val();
	$.ajax({
		url:'modules/?r=linkedin_profile/removecertificate',
		data: {cert_id:id},
		type: 'post',
		success:function(data)
		{
			$('.loading').hide();
			$('.prof_certificate_main').html(data);	
		}
	});
});





//ending of projects js





/*11 jan 2026 chatgpt on commente pour faire fonctionner le modal
$(document).on('click','.add-exp',function()
{
	
		$.ajax({
			url:'modules/?r=linkedin_profile/getexpeditform',
			data: {expid:''},
			type: 'post',
			success:function(data)
			{
				$('.prof_exp_form').html(data).show();
			}
		});
		
});
*/



$(document).on('click','.add-edu',function()
{
	/*if($('.prof_education_form').is(':visible') == true)
		$('.prof_education_form').hide();
	else
	{*/
		//typeof $(".prof_exp_form")[0].reset == "function" ? $(".prof_exp_form")[0].reset() : $(".prof_exp_form")[0].reset.click();
		//$('.Education_Form').remove();
		$.ajax({
			url:'modules/?r=linkedin_profile/getedueditform',
			data: {eduid:''},
			type: 'post',
			success:function(data)
			{
				$('.prof_education_form').html(data).show();
			}
		});
	//}
});

/*$('.exp-form-cancel').click(function()
{
	$('.prof_exp_form').hide();
});*/

/*11 jan 2026 chatgpt on commente pour faire fonctionner le modal
$(document).on('click','.prof_exp_main .exp_info',function()
{
	var exp_id = $(this).parent().data('expid');
	$.ajax({
		url:'modules/?r=linkedin_profile/getexpeditform',
		data: {expid:exp_id},
		type: 'post',
		success:function(data)
		{
			$('.prof_exp_form').html(data).show();
			$('.exp-form-delete').show();
		}
	});
});
*/

$(document).on('click','.prof_edu_main .edu_info',function()
{
	var edu_id = $(this).parent().data('eduid');
	$.ajax({
		url:'modules/?r=linkedin_profile/getedueditform',
		data: {eduid:edu_id},
		type: 'post',
		success:function(data)
		{
			$('.prof_education_form').html(data).show();
			$("#educa_id").val(edu_id);
			$('.edu-form-delete').show();
		}
	});
});

function validateExpForm()
{
	var errors = [];
	/*Name Field*/
	if( $('#companyName').val() == '')
		errors.push('companyName');

	/*Title*/
	if( $('#title').val() == '')
		errors.push('title');

	/*Date*/
	if( $('#startDateMonth').val() == '' )
		errors.push('startDateMonth');
	else if( $('#StartYear').val() == '' )
		errors.push('StartYear');
	else
	{
		var cur_date = new Date();
		var cur_year = cur_date.getFullYear();
		var cur_month = cur_date.getMonth();
		cur_month = parseInt(cur_month)+1;
		var st_month = $('#startDateMonth').val();
		var st_year = $('#StartYear').val();
		st_month = parseInt(st_month);
		if(st_year > cur_year)
			errors.push('Fatal');
		else if(st_year == cur_year && st_month > cur_month)
			errors.push('Fatal');
		else if($('.still-here').prop('checked') == false)
		{
			var end_month = $('#EndDateMonth').val();
			var end_year = $('#EndYear').val();
			end_month = parseInt(end_month);
			if(end_year != '' && end_month != '')
			{
				if(end_year > cur_year || end_year < st_year )
					errors.push('Fatal');
				else if( (end_year == cur_year || end_year == st_year) && end_month < st_month)
					errors.push('Fatal');
				else if( end_year == cur_year && end_year == st_year && end_month > cur_month)
					errors.push('Fatal');
			}
		}
	}

	if(errors.length > 0)
	{
		$.each(errors,function(i,v)
		{
			$('#'+v+'-error').show();
		});
		return false;
	}

	return true;
}

function hideformerrors(obj)
{
	obj.find('.error').hide();
}

$(document).on('click','.exp-form-cancel',function()
{
	$('.prof_exp_form').hide();
});

$(document).on('click','.edu-form-cancel',function()
{
	$('.prof_education_form').hide();
});

$(document).on('submit','.Exp_Form',function()
{
	hideformerrors($(this));


	if(validateExpForm() == true)
	{
		$('.loading').show();
		var serialized_vals = $(this).serialize();
		$.ajax({
			url:'modules/?r=linkedin_profile/postexp',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				$('.loading').hide();
				//$('.prof_exp_form').hide();
				//alert(data);

				$('.prof_exp_main').html('');
				$('.prof_exp_main').html(data);	
			}
		});
		return false;
	}

	return false;
});

$(document).on('change','.prof_project_form .still-going',function()
{
	if($(this).prop('checked') == true)
	{
		$(this).parents('.prof_project_form').find('.end-date').hide();
		$(this).parents('.prof_project_form').find('.EndDateMonth').val('');
		$(this).parents('.prof_project_form').find('.EndYear').val('');
	}
	else
		$(this).parents('.prof_project_form').find('.end-date').show();
});

$(document).on('submit','.Education_Form',function()
{
	hideformerrors($(this));

	if(validateEduForm() == true)
	{
		$('.loading').show();
		var serialized_vals = $(this).serialize();
		$.ajax({
			url:'modules/?r=linkedin_profile/postedu',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				$('.loading').hide();
				$('.prof_edu_main').html('');
				$('.prof_edu_main').html(data);	
			}
		});
		return false;
	}

	return false;

});

function validateEduForm()
{
	var errors = [];
	/*Name Field*/
	if( $('#school_name').val() == '')
		errors.push('school_name');

	/*Date*/
	if( $('#startDate').val() != '' && $('#endDate').val() != '')
	{
		if( $('#startDate').val() > $('#endDate').val() )
		{
			errors.push('period');
		}
	}

	if(errors.length > 0)
	{
		$.each(errors,function(i,v)
		{
			$('#'+v+'-error').show();
		});
		return false;
	}

	return true;

}

/*
$(document).on('click','.exp-form-delete',function()
{
	var id = $(this).parents().find('input[name="exp_id"]').val();
	$.ajax({
		url:'modules/?r=linkedin_profile/removeexp',
		data: {exp_id:id},
		type: 'post',
		success:function(data)
		{
			$('.loading').hide();
			$('.prof_exp_main').html(data);	
		}
	});
});
*/

$(document).on('click','.edu-form-delete',function()
{
	var id = $(this).parents().find('input[name="edu_id"]').val();
	$.ajax({
		url:'modules/?r=linkedin_profile/removeedu',
		data: {edu_id:id},
		type: 'post',
		success:function(data)
		{
			$('.loading').hide();
			$('.prof_edu_main').html(data);	
		}
	});
});
<!-- Skills -->

$(document).on('click','.edit_skill',function(e){    
   $('.skill_display_area').toggle();
   $('.prof_skill_form').toggle();
});

$(document).on('click','.edit_lang',function(e){    
   $('.lang_display_area').toggle();
   $('.prof_lang_form').toggle();
});

$(document).ready(function() {

	$('.profile-activities button').mouseover(function()
	{
		$('.profile-activities').addClass('active');
	});

	$('.profile-activities .menu').mouseleave(function()
	{
		$('.profile-activities').removeClass('active');
	});


$(document).on('submit','.editSkillsForm',function()
{
		var serialized_vals = $(this).serialize();
		$.ajax({
			url:'modules/?r=linkedin_profile/postskil',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				/*$('.prof_skill_form').hide();
				$('.skill_display_area').show();
				//$('.prof_skill_main').html(data);*/
				$('.prof_skill_main').html(data);
				$('#skills_edit').tagsinput('refresh');
			}
		});
		return false;
});

$(document).on('click','#edit-skills-add-btn',function(event) {
	var tag = $('#edit-skills-add-ta').val();
	$('#skills_edit').tagsinput('add', tag);
	$.ajax({
		url:'modules/?r=linkedin_profile/addskill',
		data: {tag:tag},
		type: 'post',
		success:function(data)
		{
			$('.loading').hide();
			$('#skills_container').tagsinput('refresh');
			//$('.prof_edu_main').html(data);	
		}
	});
});

/*$("#skills_edit").on('itemRemoved', function(event) {
    //console.log('item removed : '+event.item);
	var serialized_vals = $('#editSkillsForm').serialize();
		$.ajax({
			url:'modules/?r=linkedin_profile/postskil',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
			}
		});
});*/


$(document).on('submit','#editLanguageForm',function()
{
		var serialized_vals = $(this).serialize();
		$.ajax({
			url:'modules/?r=linkedin_profile/postlangs',
			data: serialized_vals,
			type: 'post',
			success:function(data)
			{
				$('.update_lang').html(data);
				$('.prof_lang_form').hide();
				$('.lang_display_area').show();
			}
		});
		return false;
});

$(".addlang").on('click', function(event) {
	$('.add-lang-container').append($('.firstlang').html());
});

$(".remove").live('click', function(event) {
	$(this).parent('.langrow').remove();
});

$('.media_area .media_info li').live('click', function(event) {

	$('.title_err').hide();
	$('.link_err').hide();
	$('#'+$(this).parent('ul').data('media')).find('.upload_using_browse').show();
	$('#'+$(this).parent('ul').data('media')).find('.exp_upload_link').css('width','');
	$('.media_area ul li').removeClass('active');
	$(this).addClass('active');
	var media_name = $(this).data('medianame');

	//Hide Upload Button for link and video
	if(media_name == 'link' || media_name == 'video')
	{
		$('#'+$(this).parent('ul').data('media')).find('.upload_using_browse').hide();
		$('#'+$(this).parent('ul').data('media')).find('.exp_upload_link').css('width','80%');
	}

	$('.media_container').hide();
	
	$('#'+$(this).parent('ul').data('media')).find('.media_type').val(media_name);
	$('#'+$(this).parent('ul').data('media')).show();
});
$('.media_area .btn_cancel').live('click', function(event) {
	$('.media_container').hide();
	$('.media_area ul li').removeClass('active');
});

});

$(document).ready(function()
{
	
});

function validateURL(url) {
  return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

$(document).on('submit','#media_save_form',function(e)
{
	e.preventDefault();
	frm = $(this);
	var formData  = new FormData(frm[0]);
	formData.append('upload_type','browse_done');
	$('.ajax_loading').show();
	$.ajax({
    	url: site_url+'/modules/?r=linkedin_profile/addMedia',
    	data: formData,
    	type: "POST", 
    	contentType: false,
        cache: false,
        processData:false,
        success: function(data) 
        {
        	//frm.parent('.media_container').hide();
        	$('.ajax_loading').hide();
        	if(frm.find('.media_for').val() == 'experience')
        	{
        		$('.prof_exp_main').html(data);
        	}
        	else
        	{
        		$('.prof_edu_main').html(data);
        	}
        	frm.remove();
        }
   	});
});

$(document).on('click','.media-save-form-cancel,.media-save-form-delete',function()
{
	var frm = $('#media_save_form');
	var img_name = frm.find('.media_name').val();
	var media_for = frm.find('.media_for').val();
	var media_id = frm.find('.media_id').val();
	$.ajax({
    	url: site_url+'/modules/?r=linkedin_profile/removeMedia',
    	data: {media_id:media_id,img_name:img_name,media_for:media_for},
    	type: "POST",
        success: function(data) 
        {
        	//frm.parent('.media_container').hide();
        	if(frm.find('.media_for').val() == 'experience')
        	{
        		$('.prof_exp_main').html(data);
        	}
        	else
        	{
        		$('.prof_edu_main').html(data);
        	}
        	frm.remove();
        	$('.each_media_info').removeClass('notactive');
        }
   	});
});

$(document).on('click','.media-edit-form-cancel',function()
{
	$('#media_save_form').remove();
	$('.each_media_info').removeClass('notactive');
});

$(document).on('change','.exp_upload_file',function()
{
	var frm = $(this).parents('.exp_media_form');
	var formData  = new FormData(frm[0]);
	formData.append('upload_type','browse');
	$('.ajax_loading').show();
	$.ajax({
    	url: site_url+'/modules/?r=linkedin_profile/addMedia',
    	data: formData,
    	type: "POST", 
    	contentType: false,
        cache: false,
        processData:false,
        dataType: "json",
        success: function(data) 
        {
        	console.log(data);
        	if(data.iserror == 'none')
        	{
        		console.log(frm);
        		$('.ajax_loading').hide();
        		frm.parent().next( $('.media_post_upload_form') ).find('.media-save-form-delete').remove();
        		frm.parent().next( $('.media_post_upload_form') ).html(data.result).show();
        		frm.hide();
        	}
        }
   	});
});

$(document).on('submit','.exp_media_form',function(e)
{
	e.preventDefault();
	var frm = $(this);
	var link = frm.find('.exp_upload_link').val();
	//alert(link);return false;

	if(link == '')
	{
		frm.find('.exp_media_error').find('.title_err').show();
		return false;
	}
	else if( validateURL(link) == false )
	{
		frm.find('.exp_media_error').find('.link_err').show();
		return false;
	}
	else
	{
		var formData  = new FormData(frm[0]);
		formData.append('upload_type','link');
		$('.ajax_loading').show();
		$.ajax({
        	url: site_url+'/modules/?r=linkedin_profile/addMedia',
        	data: formData,
        	type: "POST", 
        	contentType: false,
            cache: false,
            processData:false,
            dataType: "json",
            success: function(data) 
            {
            	console.log(data);
            	if(data.iserror == 'none')
            	{
            		console.log(frm.parent());
            		$('.ajax_loading').hide();
            		frm.parent().next( $('.media_post_upload_form') ).find('.media-save-form-delete').remove();
            		frm.parent().next( $('.media_post_upload_form') ).html(data.result).show();
            		frm.hide();
            	}
            }
        });
	}
	return false;
});

$(document).on('change','.edu_upload_file',function()
{
	var frm = $(this).parents('.edu_media_form');
	var formData  = new FormData(frm[0]);
	formData.append('upload_type','browse');
	$('.ajax_loading').show();
	$.ajax({
    	url: site_url+'/modules/?r=linkedin_profile/addMedia',
    	data: formData,
    	type: "POST", 
    	contentType: false,
        cache: false,
        processData:false,
        dataType: "json",
        success: function(data) 
        {
        	console.log(data);
        	if(data.iserror == 'none')
        	{
        		console.log(frm);
        		$('.ajax_loading').hide();
        		frm.parent().next( $('.media_post_upload_form') ).find('.media-save-form-delete').remove();
        		frm.parent().next( $('.media_post_upload_form') ).html(data.result).show();
        		frm.hide();
        	}
        }
   	});
});

$(document).on('submit','.edu_media_form',function(e)
{
	e.preventDefault();
	var frm = $(this);
	var link = frm.find('.edu_upload_link').val();

	if(link == '')
	{
		frm.find('.edu_media_error').find('.title_err').show();
		return false;
	}
	else if( validateURL(link) == false )
	{
		frm.find('.edu_media_error').find('.link_err').show();
		return false;
	}
	else
	{
		var formData  = new FormData(frm[0]);
		formData.append('upload_type','link');
		$('.ajax_loading').show();
		$.ajax({
        	url: site_url+'/modules/?r=linkedin_profile/addMedia',
        	data: formData,
        	type: "POST", 
        	contentType: false,
            cache: false,
            processData:false,
            dataType: "json",
            success: function(data) 
            {
            	console.log(data);
            	if(data.iserror == 'none')
            	{
            		console.log(frm);
            		$('.ajax_loading').hide();
            		frm.parent().next( $('.media_post_upload_form') ).find('.media-save-form-delete').remove();
            		frm.parent().next( $('.media_post_upload_form') ).html(data.result).show();
            		frm.hide();
            	}
            }
        });
	}
});

$(document).on('click','.each_media_info',function()
{
	$('.each_media_info').addClass('notactive');
	$(this).removeClass('notactive');
	$('.media_post_upload_form').html('').hide();
	var media_id = $(this).data('mediaid');
	$.ajax({
		url:'modules/?r=linkedin_profile/getMediaEditForm',
		data: {media_id:media_id,edit_mode:'yes'},
		type: 'post',
		success:function(data)
		{
			console.log($('.each_media_info_'+media_id).parent('.media_info_area').next('.media_area'));
			$('.each_media_info_'+media_id).parent('.media_info_area').next('.media_area').find('.media_post_upload_form').html(data).show();
		}
	});
});

$(document).on('change','#lang_id',function()
{
	var uid = $("#uid").val();
    var lang_name = $(this).find("option:selected").text();
    var lang_id = $(this).val();
     //*/var data = {"languagename":lang_name,"lang_id":lang_id,"uid":uid};
    //alert("Selected Text: " + selectedText + " Value: " + selectedValue + " uid: " + uid);
/*            alert(data);*/
    $.ajax({
        type: 'POST',
        url:'modules/?r=linkedin_profile/getOtherLangInfo',
        data: {"languagename":lang_name,"lang_id":lang_id,"uid":uid},
        success: function (data) {
          $( '#status_text' ).html( data );
        }
	});
});

//Other Language
/*$(function () {
    $("#lang_id").change(function () {
       	
    });
});*/

$(document).on('submit', '#langform', function() {

	event.preventDefault();
	var lang_id = $('#lang_id').val();
	var uid = $("#uid").val();
	if(lang_id == '')
	{
		alert(_t('_abs_plinkin_error_select_a_language'));
		return false;
	}
 	//make the postdata
    var postData = $("#langform").serialize();
 
 	$.ajax({
	    url:'modules/?r=linkedin_profile/postcreateprofinotherlang',
	    type: "POST",
	    data : postData,
	   	success: function(data){
	        window.location="pedit.php?ID="+uid;
	     }
	}); 
});

$(document).on('click','.endorse_skill',function(){
	var skill_user_id = $(this).data('skill_user_id');
	var logged_id = $('#logged_id').val();
	if(skill_user_id == logged_id)
	{
		alert(_t('_abs_plinkin_error_can_not_endorse_yourself'));
		return false;
	}

	var isfriends = $("#is_friends").val();
	if(isfriends == 'no')
	{
		alert(_t('_abs_plinkin_error_not_his_friend_to_endorse_him'));
		return false;
	}

	var skill_id = $(this).data('skillid');

	$.ajax({
		url:'m/linkedin_profile/endorseuser',
		data:{skill_id:skill_id,skill_user_id:skill_user_id,logged_id:logged_id},
		type:'POST',
		success:function(data)
		{
			//alert("Endorsed");
			$('.skill_display_area').html(data);
		}
	})
});

$(document).on('click','.remove_endorse',function()
{
	var skill_user_id = $(this).data('skill_user_id');
	var logged_id = $('#logged_id').val();
	if(skill_user_id == logged_id)
	{
		return false;
	}

	var isfriends = $("#is_friends").val();
	if(isfriends == 'no')
	{
		return false;
	}

	var skill_id = $(this).data('skillid');

	$.ajax({
		url:'m/linkedin_profile/removeendorse',
		data:{skill_id:skill_id,skill_user_id:skill_user_id,logged_id:logged_id},
		type:'POST',
		success:function(data)
		{
			//alert("Endorsed");
			$('.skill_display_area').html(data);
		}
	})
});


//Other Language

$(document).on('mouseover','.endorse_content',function()
{
	$(this).find('.member_names').show();
});

$(document).on('mouseleave','.endorse_content',function()
{
	$(this).find('.member_names').hide();
});

/* summary area*/
$(document).on('click','.sum_cancel',function()
{
	$('.summary_display_area').show();
	$('.prof_summary_form').html('').hide();
	$('.prof_summary_main').removeClass('edit_active');
});

$(document).on('click','.summary_edit_icon, .summary_edit,.summary_display_area',function()
{
	$.ajax({
		url:'modules/?r=linkedin_profile/Getsummaryform',
		data: {user_id:''},
		type: 'post',
		success:function(data)
		{
			$('.prof_summary_form').html(data).show();
			$('.summary_display_area').hide();
			$('.prof_summary_main').addClass('edit_active');
		}
	});
});

$(document).on('submit','#summary_form',function(e)
{
	e.preventDefault();
	var data = $(this).serialize();
	$.ajax({
		url:'modules/?r=linkedin_profile/postSummary',
		data: data,
		type: 'post',
		success:function(data)
		{
			$('.prof_summary_main').removeClass('edit_active');
			$('.prof_summary_main').html(data);
		}
	});

	return false;
});

/*summary area*/






/* ===========================
   J243 Experience Modal (JS)
   - remplace l'ouverture derrière
   - Add + Edit via getexpeditform
   - stopImmediatePropagation() pour bloquer anciens handlers
   - scroll restore (anti-freeze)
   =========================== */
(function($){

  var lastScrollY = 0;
  var isOpen = false;

  function ensureModalEXP(){
    if($('#j243ExpOverlay').length) return;

    var html = ''
      + '<div id="j243ExpOverlay" aria-hidden="true">'
      + '  <div id="j243ExpModal" role="dialog" aria-modal="true">'
      + '    <div class="j243ExpHeader">'
      + '      <h3 id="j243ExpTitle">Expérience professionnelle</h3>'
      + '      <button type="button" class="j243ExpClose" aria-label="Fermer">×</button>'
      + '    </div>'
      + '    <div class="j243ExpBody">'
      + '      <div class="j243ExpLoading">Chargement...</div>'
      + '      <div class="j243ExpContent"></div>'
      + '    </div>'
      + '  </div>'
      + '</div>';

    $('body').append(html);

    // Click outside closes
    $(document).on('click', '#j243ExpOverlay', function(e){
      if(e.target && e.target.id === 'j243ExpOverlay'){
        closeModalExp();
      }
    });

    $(document).on('click', '#j243ExpOverlay .j243ExpClose', function(){
      closeModalExp();
    });

    // Cancel button closes
    $(document).on('click', '#j243ExpOverlay .exp-form-cancel', function(e){
      e.preventDefault();
      closeModalExp();
      return false;
    });

    // ESC closes
    $(document).on('keydown', function(e){
      if(e.key === 'Escape' && isOpen){
        closeModalExp();
      }
    });
  }

  function lockScroll(){
    lastScrollY = window.scrollY || window.pageYOffset || 0;

    // Méthode anti-freeze (Android/Chrome): body fixed
    $('body').css({
      position: 'fixed',
      top: (-lastScrollY) + 'px',
      left: '0',
      right: '0',
      width: '100%'
    });
  }

  function unlockScroll(){
    // restore
    var top = $('body').css('top');
    $('body').css({ position:'', top:'', left:'', right:'', width:'' });

    var y = lastScrollY;
    if(top && top.indexOf('px') !== -1){
      // fallback si top avait changé
      y = Math.abs(parseInt(top, 10)) || lastScrollY;
    }
    window.scrollTo(0, y);
  }

  function openModalExp(title){
    ensureModalEXP();
    if(title) $('#j243ExpTitle').text(title);

    isOpen = true;
    lockScroll();

   $('#j243ExpOverlay')
  .addClass('active')
  .attr('aria-hidden','false')
  .stop(true, true)
  .hide()
  .fadeIn(200);

  }
  
  // ==== DELETE FIX: always delete the exp_id from the modal form (not from the page)
$(document).on('click', '#j243ExpOverlay .exp-form-delete', function(e){
  e.preventDefault();
  e.stopPropagation();
  e.stopImmediatePropagation(); // bloque l'ancien handler

  var $form = $(this).closest('form.Exp_Form');
  var expid = $.trim($form.find('input[name="exp_id"]').val() || '');

  if(!expid || expid === '0'){
    alert("Erreur: ID de l'expérience introuvable.");
    return false;
  }

  if(!confirm("Voulez-vous vraiment supprimer cette expérience ?")){
    return false;
  }

  // Affiche un petit loading si tu veux
  setLoading(true);

  $.ajax({
    url: 'modules/?r=linkedin_profile/removeexp',
    type: 'post',
    data: { exp_id: expid },
    success: function(data){
      setLoading(false);

      // refresh list
      $('.prof_exp_main').html(data);

      // close popup
      closeModalExp();
    },
    error: function(){
      setLoading(false);
      alert("Erreur: suppression impossible.");
    }
  });

  return false;
});


  function closeModalExp(){
  isOpen = false;

  $('#j243ExpOverlay')
    .removeClass('active')
    .attr('aria-hidden','true')
    .stop(true, true)
    .fadeOut(300, function () {
      $('#j243ExpOverlay .j243ExpContent').empty();
      unlockScroll();
    });

    // IMPORTANT: cacher le form derrière si le module l'a montré
    $('.prof_exp_form').hide().empty();
  }

  function setLoading(v){
    if(v) $('#j243ExpOverlay .j243ExpLoading').show();
    else $('#j243ExpOverlay .j243ExpLoading').hide();
  }

 function normalizeFormUI(){
  var $content = $('#j243ExpOverlay .j243ExpContent');
  var $form = $content.find('form.Exp_Form').first();
  if(!$form.length) return;

  // --- Detect mode edit/add via hidden exp_id (dans prof_exp_form.html)
  var expIdVal = $.trim($form.find('input[name="exp_id"]').val() || '');
  var isEdit = (expIdVal !== '' && expIdVal !== '0');

  // --- Show delete only on edit
  var $deleteBtn = $form.find('.exp-form-delete');
  if($deleteBtn.length){
    if(isEdit){
      $deleteBtn.css('display','inline-block');
    }else{
      $deleteBtn.css('display','none');
    }
  }

  // --- Footer sticky
  var $footer = $('<div class="j243ExpFooter"></div>');

  // Prend les boutons du form et les met en bas (dans le footer sticky)
  var $btns = $form.find('input[type="submit"], input[type="button"], input[type="reset"], button');
  $btns.each(function(){
    $footer.append($(this));
  });

  $form.append($footer);
}


 function loadExpForm(expid){
  var $title = $('#j243ExpModalTitle');

  var title = expid ? 'Modifier l’expérience' : 'Ajouter une experience professionnelle' ;
  // fallback: if translations are not loaded, use data-* attributes if present
  if(title === 'Modifier l’expérience' || title === 'Ajouter une experience professionnelle '){
    title = expid ? ($title.data('title-edit') || title) : ($title.data('title-add') || title);
  }
 openModalExp(title);
$('#j243ExpOverlay')
  .addClass('active')
  .hide()
  .fadeIn(200);
setLoading(true);


    $.ajax({
      url: 'modules/?r=linkedin_profile/getexpeditform',
      type: 'post',
      data: { expid: (expid ? expid : '') },
      success: function(data){
      setLoading(false);
      $('#j243ExpOverlay .j243ExpContent').html(data);
      // If we opened the form in ADD mode, force exp_id empty so UI doesn't think it's EDIT mode
      if(!expid){
        $('#j243ExpOverlay').find('input[name="exp_id"]').val('');
      }
      normalizeFormUI();
    },
      error: function(){
        setLoading(false);
        $('#j243ExpOverlay .j243ExpContent').html(
          '<p style="color:#b91c1c;font-weight:800;margin:0;">Erreur: impossible de charger le formulaire.</p>'
        );
      }
    });
  }

  /* ===========================
     INTERCEPTIONS (bloquer ancien JS)
     =========================== */

  // Add experience
  $(document).on('click', '.add-exp', function(e){
    e.preventDefault();
    e.stopPropagation();
    if(e.stopImmediatePropagation) e.stopImmediatePropagation();

    // Empêcher affichage derrière
    $('.prof_exp_form').hide().empty();

    loadExpForm('');
    return false;
  });

  // Edit experience (pencil)
  $(document).on('click', '.scroll_edit, .scroll_con_edit', function(e){
    e.preventDefault();
    e.stopPropagation();
    if(e.stopImmediatePropagation) e.stopImmediatePropagation();

    var expid = $(this).closest('.each_container').attr('data-expid') ||
                $(this).parents('.each_container').attr('data-expid');

    $('.prof_exp_form').hide().empty();
    if(expid) loadExpForm(expid);
    return false;
  });

  // Edit experience (click on box)
  $(document).on('click', '.exp_info', function(e){
    // seulement si c'est dans une expérience
    var expid = $(this).closest('.each_container').attr('data-expid');
    if(!expid) return;

    e.preventDefault();
    e.stopPropagation();
    if(e.stopImmediatePropagation) e.stopImmediatePropagation();

    $('.prof_exp_form').hide().empty();
    loadExpForm(expid);
    return false;
  });

  /* ===========================
     SAVE / DELETE: fermer modal après action AJAX
     =========================== */
  $(document).ajaxSuccess(function(event, xhr, settings){
    if(!settings || !settings.url) return;

    // Le module poste généralement sur postexp
    if(settings.url.indexOf('modules/?r=linkedin_profile/postexp') !== -1){
      closeModalExp();
    }
  });
  
  $(document).ajaxSuccess(function(event, xhr, settings){
  if(!settings || !settings.url) return;

  if(settings.url.indexOf('modules/?r=linkedin_profile/postexp') !== -1){
    closeModalExp();
  }

  if(settings.url.indexOf('modules/?r=linkedin_profile/removeexp') !== -1){
    closeModalExp();
  }
});

  
  

})(jQuery);
