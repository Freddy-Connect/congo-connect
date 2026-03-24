function aqb_conditional_fields_get_add_popup() {
	if ($('#aqb_conditional_fields_popup').length) $('#aqb_conditional_fields_popup').remove();

    $.get(site_url + 'm/aqb_conditional_fields/action_get_add_popup/', function(sResponse) {
        $(sResponse).dolPopup({fog: {color: '#444', opacity: .7}, closeOnOuterClick: true});
    }, 'html');
}

function aqb_conditional_fields_add(oForm) {
	$('#aqb_cf_loading').bx_loading();
	$.post($(oForm).attr('action'), $(oForm).serialize(), function(oResponse) {
		if (oResponse.status == 'ok') {
			$('#aqb_conditional_fields_rows').html(oResponse.rows);
        	$('#aqb_conditional_fields_popup').dolPopupHide();
		} else {
			$('#aqb_conditional_fields_form').html(oResponse.form);
		}
    }, 'json');
}

function aqb_conditional_fields_fill_values_list(field) {
	$('#aqb_conditional_fields_values_selector').html('').attr('disabled', true);
	$('#aqb_cf_loading').bx_loading();

	$.get(site_url + 'm/aqb_conditional_fields/action_get_field_values/'+field, function(sResponse) {
		eval(sResponse);

		if (values && values['value'] && values['value'].length) {
			var el = $('#aqb_conditional_fields_values_selector').get(0);
			for (var i = 0; i < values['value'].length; i++)
				el.options.add(new Option(values['name'][i], values['value'][i], true, false));

			$('#aqb_conditional_fields_values_selector').attr('disabled', false);

			$('#aqb_conditional_fields_popup')._dolPopupSetPosition({position:'centered'});
			$('#aqb_cf_loading').bx_loading();
		}
    }, 'html');
}

function aqb_conditional_fields_remove(field, depends_on, show_if_value) {
	if (confirm(_t('_Are_you_sure'))) {
		$.post(site_url + 'm/aqb_conditional_fields/action_remove/'+field+'/'+depends_on+'/'+show_if_value, function(sResponse) {
			$('#aqb_conditional_fields_rows').html(sResponse);
	    }, 'html');
	}
}