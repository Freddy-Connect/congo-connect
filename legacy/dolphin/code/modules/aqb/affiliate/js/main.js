/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

var AqbAffItem = (function($){
function AqbItems(){
	this._price = 0;
	this._points_num = 0;
}

AqbItems.prototype.showPopup = function(sUrl) {
   var oPopupOptions = {
        fog: {color: '#fff', opacity: .7},
		closeElement:'.dbClose a'
    };

	$(oPopupOptions.closeElement).remove();

	$('<div id="sys_popup_ajax" style="display: none;"></div>').prependTo('body').load(
		sUrl.match('^http[s]{0,1}:\/\/') ? sUrl : site_url + sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
		}
	);
}
AqbItems.prototype.getCommission = function(sMessage, sUrl) {
   if (!confirm(sMessage)) return false;
   
   var oDate = new Date();
   $.post(sUrl,{_t:oDate.getTime()},
   function(oData){
		alert(oData.message);
		if (oData.code == 0) document.location.reload();
   },
	'json'
   );
   return false;
}

AqbItems.prototype.sumUpdate = function(element, fPrice){
  try{
	  this._points_num = parseInt($(element).val());
	  this._price = parseFloat(fPrice)*this._points_num;
	  
	  if (isNaN(this._price)) this._price = 0;
		$('#aqb_price_counter').html(this._price);	
	  }catch(e){
		$('#aqb_price_counter').html(0);	
   }
}

AqbItems.prototype.onSubmitInvitation = function(eForm, iVal, sMesEmpty, sMes){
  var $this = this;
   if( !eForm )
        return false;

	if ($('#aqb_emails').val().length == 0) {
		alert(sMesEmpty);
		return false;
	}
	
	if ($('#aqb_message').length == 1 && $('#aqb_message').val().length > parseInt(iVal)) {
		alert(sMes); 
		return false;
	}	
	
	$('#aqb_send_emails').attr('disabled', true);

	$(eForm).ajaxSubmit( {
        iframe: false, // force no iframe mode
		dataType:'json',
        success: function(oData) {
          alert(oData.message);
		  if (oData.code == 0){
				$('#aqb_popup').dolPopupHide();
		  }else $('#aqb_send_emails').attr('disabled', false);
        }
    } );
    
    return false;
}

AqbItems.prototype.onSubmitPresent = function(sWrongPoints, sConfirm, sUrl){
  try{
	   $('#aqb_present_points_button').attr('disabled', true);
	   var mybalance = parseInt($('#aqb_current_balance').val());
	   var profile_id = parseInt($('#aqb_profile_id').val());
	   var present_points = parseInt($('#aqb_points_num').val()) 
	   if (isNaN(present_points)) present_points = 0;
	   
	   if (present_points > mybalance || present_points <= 0) 
	   {
		 alert(sWrongPoints);
		 return; 
	   }
	   if (confirm(sConfirm.replace('{0}', present_points))) 
	   {
		  var oDate = new Date();
		  $.post(sUrl + profile_id + '/' + present_points,		
					{
						_t:oDate.getTime()
					},
		        function(oData){
		     		alert(oData.message);
					if (oData.code == 0) 
					{
						$('#login_div').dolPopupHide();
					}
					$('#aqb_present_points_button').attr('disabled', false);
			    },
		        'json'
		     );
		}
	  }catch(e){
   }
}

AqbItems.prototype.onSubmitPrice = function(sUrl, sRedirect, sMessage){
	if (!this._price || !this._points_num) return false;  
	$('#aqb_buy_points_button').attr('disabled', true);
	
	$.get(sUrl + this._points_num + '/' + this._price, function(data) {   
		 if (data.length == 0) 
		 {
			alert(sMessage);
			$('#aqb_buy_points_button').attr('disabled', false);
			return;
		 }	
		 var oDate = new Date();
	
		 $.post(
				data.toString(),		
				{
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					window.location = sRedirect;
				}catch(e){}
	        },
	        'json'
	     );
	 });
}

AqbItems.prototype.onExchangePoints = function(sUrl, sConfirm, sRedirect){
	if (!confirm(sConfirm))  return;
	var oDate = new Date();
	$.post(sUrl,		
				{
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					if (oData.code == 0) window.location = sRedirect;
				}catch(e){}
	        },
	        'json'
	     );
}
 return new AqbItems();
})(jQuery);

var AqbMain = (function($)
{
function AqbF(){
	this._sActionsUrl = '';
    this._sViewType = '';
	this._sFilter = '';
    this._sOrderBy = 'asc';
    this._sAnimationEffect = 'fade';
    this._iAnimationSpeed = 'slow';
	this._iMemberId = 0;
    this._iStart = 0;
    this._iPerPage = 30;

};

AqbF.prototype.changeFilterSearch = function () {
    var sValue = $("#aqb_ref_search_item").val();    
    if(sValue.length <= 0)
        return;
		
	this._sFilter = sValue;
	this._iStart = ''; 
    this._iPerPage = ''; 

	this.getReferrals(function() {
        $('#div.ref-table:hidden').html('');
    });	
}

AqbF.prototype.refresh = function () {
    this._sFilter = '';
	this._iStart = 0; 
    this._iPerPage = ''; 

	this.getReferrals();	
}


AqbF.prototype.orderByField = function(sOrderBy, sFieldName) {
    this._sViewType = sOrderBy;
	this._sOrderBy = sFieldName;
	this._iStart = 0;
	this.getReferrals();
}

AqbF.prototype.showPopup = function(sUrl) {
    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}  
    };

	var oDate = new Date();
	$.get(sUrl, { _t:oDate.getTime() }, function(data) {   
  	 	 $('#aqb_popup').remove();
		 $(data).appendTo('body').dolPopup(oPopupOptions); 
	});
}

AqbF.prototype.addBlockToMyProfile = function(sBlockID, sUrl){
	var oDate = new Date();
	$.post(sUrl + '/' + sBlockID,{_t:oDate.getTime()},
	        function(oData){
	           try{ 
					alert(oData.message);
					if (oData.code == 0) $('#' + sBlockID).animate({opacity: 0}, 
																	function () {
										                            $(this).slideUp(function () {$(this).remove();});
										                        });
				}catch(e){}
	        },
	        'json'
	     );
}

AqbF.prototype.changePage = function(iStart) {
    this._iStart = iStart;
    this.getReferrals();
}

AqbF.prototype.getReferrals = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();
    
	if ($('#items-search').css('display') == 'none') this._sFilter = '';

	
    var oOptions = {
        action: 'referrals_panel', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		filter: this._sFilter,
		member_id: this._iMemberId
    }

   $.post(
        this._sActionsUrl + 'referrals_panel/' + this . _iMemberId,
        oOptions,
        function(oResult) {
			
			$('#div-loading').bx_loading();
				
			$('div.ref-table').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('div.ref-table').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

return new AqbF();
})(jQuery);