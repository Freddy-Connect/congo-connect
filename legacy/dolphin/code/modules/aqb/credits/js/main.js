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
AqbCredit = new AqbCredits();

function AqbCredits(){}

AqbCredits.prototype.showPopup = function(sUrl) {
   var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}       
    };

    $.get(sUrl, function(data) {   
		 $('#aqb_popup').remove();
		  $(data).dolPopup(oPopupOptions); 
	 });

}

AqbCredits.prototype.dontShow = function(el){
	$.get('modules/?r=aqb_credits/set_interval/', {set:+el.checked});	
}

AqbCredits.prototype.showExchangeCreditsForm = function(sUrl){
	if (!$("div.aqb-credits-actions-list-table input[type='checkbox']:checked").length) return false;
	
	var str = '';	
	$("div.aqb-credits-actions-list-table input[type='checkbox']:checked").each(function(){
		str += $(this).val() + ',';
	});
	
	$.post(sUrl + 'check_for_exchange', {items: str}, function(oData){
			if (!oData.code){
				if (confirm(oData.message))
				$.post(sUrl + 'exchange_credits_for_actions', {items:str}, function(oData){
					alert(oData.message);
					if (!oData.code) window.location.reload();
				}, 'json');
			} else alert(oData.message);			
		},'json');		
}

AqbCredits.prototype.onSubmitCredits = function(sUrl, sType){
	var credits = parseInt($("[name='amount']").val()),	
		price = parseFloat($("[name='price']").val()) * credits;
		
	if (!price || !credits) return false;  
	$('#aqb_buy_button').attr('disabled', true);
	
	$.get(sUrl + sType + '/' + credits, function(oData) {   
				if (oData.message) alert(oData.message);
				
				if (oData.code == 0){	
					if (!oData.link) window.location.reload(); 
						else 
							$.post(oData.link,function(oData){
								alert(oData.message); 
								if (!oData.code) window.location = 'modules/?r=payment/cart/'; 
								else $('#aqb_buy_button').attr('disabled', false);},
							'json');
				}		
				else 
					$('#aqb_buy_button').attr('disabled', false);
			},
	        'json'
	     );	
}

AqbCredits.prototype.onPresentCredits = function(iProfileID){
	var credits = parseInt($("[name='amount']").val());	
		
	if (!credits) return false;  
	
	$.get('modules/?r=aqb_credits/check_for_present/' + iProfileID, {amount: credits}, function(oData){
			if (!oData.code){
				if (confirm(oData.message))
				$.post('modules/?r=aqb_credits/present_credits/' + iProfileID, {amount: credits}, function(oData){
					alert(oData.message);
					$('#sys_popup_ajax').dolPopupHide({});
				}, 'json');
			} else alert(oData.message);			
		},'json');
}

var AqbMain = new AqbF();

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

AqbF.prototype.orderByField = function(sOrderBy, sFieldName) {
    this._sViewType = sOrderBy;
	this._sOrderBy = sFieldName;
	this._iStart = 0;
	this.getCredits();
}


AqbF.prototype.changePage = function(iStart) {
    this._iStart = iStart;
    this.getCredits();
}

AqbF.prototype.getCredits = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();
	
    var oOptions = {
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		member_id: this._iMemberId
    }

   $.post(
        this._sActionsUrl + 'credits_panel/' + this . _iMemberId,
        oOptions,
        function(oResult) {
			$('#div-loading').bx_loading();
				
			$('#aqb-credits-list-table').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#aqb-credits-list-table').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}