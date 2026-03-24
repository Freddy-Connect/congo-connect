subs_core = {
	load_arb_form: function(mlevel){
		window.location.href= site_url + 'm/memberships/order/?action=an_order&mlevel=' + mlevel;
	},
	submit_arb_payment: function(form){
		var aErrors = 0;
		$( '.error', form ).removeClass( 'error' );
		$('input').each(function(index){
			if($(this).val() == '' || $(this).val() == 'undefined'){
				var field = $(this).attr('name');
				console.log(field);
				if(field == 'cc_country' || field == 'phone'){
					return true; // same as continue;
				}
				
				doShowError( form, field, index, 'Must not be empty');
				aErrors++;				
			}
		});
		
		if(aErrors == 0){
			var string = $(form).serialize();
			var ajax_url = site_url + 'm/memberships/ajax?action=an_process&' + string;
			getHtmlData(submit_result, ajax_url, function(data){
				$('#submit_result').html(data);
			});
		}
		return false;

	},
	redirect: function(){
		location.href = site_url + 'm/memberships/';
	}
}


function showMenuAccess(sUrl) {
    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}
    };

	$('<div id="menu_access" style="display: none;"></div>').prependTo('body').load(
		site_url + sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
		}
	);
}
function saveMenuItem(oForm) {
	$('#formItemEditLoading').bx_loading();
	var sQueryString = $(oForm).formSerialize();
	$.post($(oForm).attr('action'), sQueryString, function(oData){
        $('#formItemEditLoading').bx_loading();
     	$('#menu_access').fadeOut('fast', function(){
			$('#menu_access').html(oData).fadeIn('slow');
	        setTimeout(function () {
	            $('#menu_access').dolPopupHide({});
	        }, 1000);

		});

    });
}
function loadPopUp(sUrl) {
    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}
    };
	$('#menu_access').load(
		site_url + sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
	        setTimeout(function () {
	            $('#menu_access').dolPopupHide({});
	        }, 3000);

		}
	);
}
function showOrderForm(sUrl) {
    var oPopupOptions = {
        fog: {color: '#000', opacity: .7}
    };
	$('<div id="order_form" style="display: none;"></div>').prependTo('body').load(
		site_url + sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
		}
	);
}

// Taken from pedit.js
function doShowError( eForm, sField, iInd, sError ) {
    var $Field = $( "[name='" + sField + "']", eForm ); // single (system) field
    if( !$Field.length ) // couple field
        $Field = $( "[name='" + sField + '[' + iInd + ']' + "']", eForm );
    if( !$Field.length ) // couple multi-select
        $Field = $( "[name='" + sField + '[' + iInd + '][]' + "']", eForm );
    if( !$Field.length ) // couple range (two fields)
        $Field = $( "[name='" + sField + '[' + iInd + '][0]' + "'],[name='" + sField + '[' + iInd + '][1]' + "']", eForm );
    
    $Field
    .parents('td:first')
        .addClass( 'error' )
        .children( 'img.warn' )
            .attr('float_info', sError)
            //.show()
            ;
}

