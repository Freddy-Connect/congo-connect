$(document).ready(function()
{
	$('body').addClass('plink_edit');
	var main = $('.plink_edit');
	main.find('#edit_form').hide();

	$('#edit_form').parents('.disignBoxFirst').find('.boxFirstHeader .dbTitle').append('<a class="base_info" href="javascript:void(0)">'+_t("_abs_plinkin_basic_Info")+'</a>');

	if($('#edit_form').parents('.page_block_container').index() < $('#linkin_profile_info').parents('.page_block_container').index())
		$('#linkin_profile_info').parents('.page_block_container').insertBefore($('#edit_form').parents('.page_block_container'));

});

$(document).on('click','.base_info',function()
{
	var main = $('.plink_edit');
	if(main.find('#edit_form').is(":visible") == true)
		main.find('#edit_form').fadeOut(1000);
	else
		main.find('#edit_form').fadeIn(1000);
});