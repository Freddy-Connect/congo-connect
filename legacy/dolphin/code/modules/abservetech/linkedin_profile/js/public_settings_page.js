$(document).ready(function()
{
	var cont = $('.go_back_to_edit').html();
	$(cont).insertBefore( ".sys_main_content" );
	$('.go_back_to_edit').remove();
	if($('.checked').val() == 'private')
		hideall();

	$('.hideit').each(function()
	{
		var div = $(this).data('div');
		console.log($('.'+div).parents('.page_block_container'));
		$('.'+div).parents('.page_block_container').hide();
	});

	$('.sys_page_public-profile-settings').addClass('can-show');

	$('#picture').change(function()
	{
		if($("#picture").prop('checked') == true){
			$('.linkin_avatar_image').show();
		}
		else
		{
			$('.linkin_avatar_image').hide();
		}
		
	});

	$('#headline').change(function()
	{
		if($("#headline").prop('checked') == true){
			$('.user_title').show();
		}
		else
		{
			$('.user_title').hide();
		}
		
	});

    $('#skills').change(function()
	{
		if($("#skills").prop('checked') == true){
			$('.skill_display_area').parents('.page_block_container').show();
			$('.skill_display_area').removeClass('hideit');
			$('.skill_display_area').addClass('showit');
		}
		else
		{
			$('.skill_display_area').parents('.page_block_container').hide();
			$('.skill_display_area').removeClass('showit');
			$('.skill_display_area').addClass('hideit');
		}
		
	});

    $('#edu').change(function()
	{
		if($("#edu").prop('checked') == true){
			$('.edu_display_area').parents('.page_block_container').show();
			$('.edu_display_area').removeClass('hideit');
			$('.edu_display_area').addClass('showit');
		}
		else
		{
			$('.edu_display_area').parents('.page_block_container').hide();
			$('.edu_display_area').removeClass('showit');
			$('.edu_display_area').addClass('hideit');
		}
		
	});

   
    $('#lang').change(function()
	{
		if($("#lang").prop('checked') == true){
			$('.lang_display_area').parents('.page_block_container').show();
			$('.lang_display_area').removeClass('hideit');
			$('.lang_display_area').addClass('showit');
		}
		else
		{
			$('.lang_display_area').parents('.page_block_container').hide();
			$('.lang_display_area').removeClass('showit');
			$('.lang_display_area').addClass('hideit');
		}
		
	});


  
  $('#curr_pos').change(function()
	{
		if($("#curr_pos").prop('checked') == true){
			if($('.past_position').is(':visible') == true)
			{
				$('.current_position').show();
			}
			else
			{
				$('.current_position').show();
				$('.current_position').parents('.page_block_container').show();
				$('.exp_display_area').removeClass('hideit');
				$('.exp_display_area').addClass('showit');
			}
		}
		else
		{
			if($('.past_position').is(':visible') == true)
			{
				$('.current_position').hide();
			}
			else
			{
				$('.current_position').hide();
				$('.current_position').parents('.page_block_container').hide();
				$('.exp_display_area').removeClass('showit');
				$('.exp_display_area').addClass('hideit');
			}
		}
		
	});

   $('#past_pos').change(function()
	{
		if($("#past_pos").prop('checked') == true){
			if($('.current_position').is(':visible') == true)
				$('.past_position').show();
			else
			{
				$('.past_position').show();
				$('.past_position').parents('.page_block_container').show();
				$('.exp_display_area').removeClass('hideit');
				$('.exp_display_area').addClass('showit');
			}

		}
		else
		{
			if($('.current_position').is(':visible') == true)
				$('.past_position').hide();
			else
			{
				$('.past_position').hide();
				$('.past_position').parents('.page_block_container').hide();
				$('.exp_display_area').removeClass('showit');
				$('.exp_display_area').addClass('hideit');
			}
		}
		
	});

   $('#project').change(function()
	{
		if($("#project").prop('checked') == true){
			$('.projects_display_area').parents('.page_block_container').show();
			$('.projects_display_area').removeClass('hideit');
			$('.projects_display_area').addClass('showit');
		}
		else
		{
			$('.projects_display_area').parents('.page_block_container').hide();
			$('.projects_display_area').removeClass('showit');
			$('.projects_display_area').addClass('hideit');
		}
		
	});

   $('#cert').change(function()
	{
		if($("#cert").prop('checked') == true){
			$('.certicates_display_area').parents('.page_block_container').show();
			$('.certicates_display_area').removeClass('hideit');
			$('.certicates_display_area').addClass('showit');
		}
		else
		{
			$('.certicates_display_area').parents('.page_block_container').hide();
			$('.certicates_display_area').removeClass('showit');
			$('.certicates_display_area').addClass('hideit');
		}
		
	});
});

$(document).on('click','.is_viewable',function()
{
	$(this).removeClass('checked');
	if( $(this).val() == 'public')
	{
		$('.prv_mode').hide();
		$('.pub_mode').addClass('checked').show();
		displayall();
	}
	else
	{
		$('.pub_mode').hide();
		$('.prv_mode').addClass('checked').show();
		hideall();
	}
});

function displayall()
{
	$('.showit').each(function()
	{
		var div = $(this).data('div');
		console.log($('.'+div).parents('.page_block_container'));
		$('.'+div).parents('.page_block_container').show();
	});
	$('.settings_group').show();
}

function hideall()
{
	$('.skill_display_area').parents('.page_block_container').hide();
	$('.past_position').parents('.page_block_container').hide();
	$('.lang_display_area').parents('.page_block_container').hide();
	$('.edu_display_area').parents('.page_block_container').hide();
	$('.projects_display_area').parents('.page_block_container').hide();
	$('.certicates_display_area').parents('.page_block_container').hide();
	$('.settings_group').hide();
}