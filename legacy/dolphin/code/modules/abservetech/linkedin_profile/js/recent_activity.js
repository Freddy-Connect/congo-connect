/*$(document).ready(function()
{
	$('.wall-comments').each(function()
	{
		$('<a class="wall-like" href="javascript:void(0)">like</a><span class="sys-bullet"></span>').insertBefore($(this).find('.wall-delete'));
	});

	//$('.wall-event').addClass('recent');

	$('.wall-event').each(function()
	{
		$(this).addClass('recent');
	});

	$('.wall-divider').each(function()
	{
		$(this).addClass('recent');
	});
});

$(document).on('click','.bx-btn',function()
{
	$('.activity_loading').show();
	$('#load_count').val(parseInt($('#load_count').val())+1).trigger('change');;
});

$(document).on('change','#load_count',function()
{
	setTimeout(function(){
		$('.wall-comments').each(function()
		{
			if($(this).find('.wall-like').length == 0)
				$('<a class="wall-like" href="javascript:void(0)" onclick="javascript:oWallView.deletePost(120);">like</a><span class="sys-bullet"></span>').insertBefore($(this).find('.wall-delete'));
		});
		$('.wall-event').each(function()
		{
			if($(this).hasClass('recent'))
			{
			}
			else
			{
				$(this).addClass('recent');
			}
		});
		$('.wall-divider').each(function()
		{
			if($(this).hasClass('recent'))
			{
			}
			else
			{
				$(this).addClass('recent');
			}
		});
		$('.activity_loading').hide();
	},2000);
});*/

$(document).on('click','.wall-like',function()
{
	var parent_id = $(this).parents('.wall-event').attr('id');
	var parent_id_array = parent_id.split('-');
	var wall_id = parent_id_array[parent_id_array.length-1];
	var action = $(this).data('action');
	$.ajax({
		url:'modules/?r=linkedin_profile/updateUserlike',
		type:'post',
		data:{wall_id:wall_id,action:action},
		success:function(data)
		{
			/*if(action == 'like')
			{
				$('#cmts-box-bx_wall-'+wall_id).find('.wall-like').html('unlike');
				$('#cmts-box-bx_wall-'+wall_id).find('.wall-like').data('action','unlike');
			}
			else
			{
				$('#cmts-box-bx_wall-'+wall_id).find('.wall-like').html('like');
				$('#cmts-box-bx_wall-'+wall_id).find('.wall-like').data('action','like');
			}*/

			$('#cmts-box-bx_wall-'+wall_id).find('.like-content').html(data);
		}
	});
});

$(document).on('mouseover','.wall-like-users',function()
{
	$(this).next('.members_list').show();
});

$(document).on('mouseleave','.members_list',function()
{
	$(this).hide();
});

