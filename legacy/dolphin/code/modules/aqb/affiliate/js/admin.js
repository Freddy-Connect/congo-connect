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
var Item = new Items();

function Items() {    
    this._sActionsUrl = '';
	this._iCountryNumber = 0 ;
    this._sObjName = '';
    this._sViewType = '';
	this._sFilter = '';
	this._sSection = '';
	this._iAttemptId = 0;
    this._iStart = 0 ;
    this._iPerPage = 30;
    this._sOrderBy = 'asc' ;
    this._sAnimationEffect = 'fade';
    this._iAnimationSpeed = 'slow';
	this._sShowOnly = '';
	this._intervalID = 0;
}
/*--- Paginate Functions ---*/
Items.prototype.changePage = function(iStart) {
    this._iStart = iStart;
    if (this._sSection == 'commission') this.getCommissions();
	else if (this._sSection == 'members') this.getMembers();
	else this.getItems();
}

Items.prototype.showOnly = function(item) {
    this._sShowOnly = $(item).val();
	this._sFilter = $("[name='aff-filter-input']").val();
	this.getCommissions();
}

Items.prototype.changeFilterSearch = function () {
    var sValue = $("[name='aff-filter-input']").val();    
    if(sValue.length <= 0)
        return;
		
	this._sFilter = sValue;
	this._iStart = ''; 
    this._iPerPage = ''; 

    if (this._sSection == 'commission') this.getCommissions();
	else if (this._sSection == 'members') this.getMembers(function() {
        $('#items-members-form > .items-members-wrapper:hidden').html('');
    });
	else this.getItems(); 
}
Items.prototype.changeOrder = function(oSelect) {
    this._sOrderBy = oSelect.value;
    if (this._sSection == 'commission') this.getCommissions();
	else if (this._sSection == 'members') this.getMembers();
	else this.getItems();

}
Items.prototype.changePerPage = function(oSelect) {
    this._iPerPage = parseInt(oSelect.value);
    if (this._sSection == 'commission') this.getCommissions();
	else if (this._sSection == 'members') this.getMembers();
	else this.getItems();
}

Items.prototype.orderByField = function(sOrderBy, sFieldName) {
    this._sViewType = sOrderBy;
	this._sOrderBy = sFieldName;
    if (this._sSection == 'commission') this.getCommissions();
	else if (this._sSection == 'members') this.getMembers();
	else this.getItems();
}

Items.prototype.getItems = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();

    var oOptions = {
        action: 'banners', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		filter: this._sFilter,
    }

   $.post(
        this._sActionsUrl + 'all_banners/',
        oOptions,
        function(oResult) {			
			$('#div-loading').bx_loading();
  
			$('#items-content').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#items-content').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

Items.prototype.startRefreshing = function(sUrl, sMessage){
	$this = this;
	
	$('#div-loading').bx_loading();
	
	var oDate = new Date();
	$.post(sUrl + 'start_refresh', {_t:oDate.getTime()},
		function(oData){
			$('#div-loading').bx_loading();
			$('#aqb_popup').dolPopupHide({});
			
			if (parseInt(oData.code) == 1) alert(sMessage);
			if (parseInt(oData.code) == 0) alert(oData.message);
			
	},'json');	

	return true;			
}

Items.prototype.sendMassPayment = function(sUrl){
	$this = this;
	var oDate = new Date();
	var sStr = '';
	
	$("#mass_pay_form input[name='members[]']:checked").each(function(){
		sStr += $(this).val() + ',';
	});
	
	if (sStr.length == 0) return '';
	
	$('#div-loading-mass').bx_loading();
		
	var oOptions = { _t:oDate.getTime(), items: sStr};
		
	$.post(sUrl, oOptions,
		function(oData){
			$('#div-loading-mass').bx_loading(); 
			alert(oData.message);					
			$('#aqb_popup').dolPopupHide({});
			if (oData.code == 0) $this.getCommissions();
			
	}, 'json');	
}


Items.prototype.onFromSubmit = function (eForm) {
    var $this = this;
	if( !eForm )
        return false;

	$(eForm).ajaxSubmit( {
        iframe: true, // force no iframe mode
		dataType:'json',
        success: function(oData) {
           alert(oData.message);
		   if (oData.code == 0 && oData.form_name == 'params') $this.getItems();
        }
    } );
    
    return false;
}

Items.prototype.cleanMemberHistory = function(sUrl, sMessage){
	$this = this;
	if (!confirm(sMessage)) return;
	
	var oDate = new Date();
	$.post(sUrl, {_t:oDate.getTime()},
		function(oData){
			alert(oData.message); 
			if (oData.code == 0) $this.getMembers();
	}, 'json');	
}

Items.prototype.getMembers = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();
    
	if ($('#items-search').css('display') == 'none') this._sFilter = '';

	
    var oOptions = {
        action: 'members', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		filter: this._sFilter,
    }

   $.post(
        this._sActionsUrl + 'members/',
        oOptions,
        function(oResult) {
			
			$('#div-loading').bx_loading();
				
			$('#items-members-common').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#items-members-common').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

Items.prototype.getCommissions = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();
    
	if ($('#items-search').css('display') == 'none') this._sFilter = '';

	
    var oOptions = {
        action: 'commissions', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		filter: this._sFilter,
		commission:this._sShowOnly
    }

   $.post(
        this._sActionsUrl + 'commissions/',
        oOptions,
        function(oResult) {
			$('#div-loading').bx_loading();
					
			$('#items-members-common').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#items-members-common').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

Items.prototype.showAll = function(){
	this._sViewType = ''; 
    this._iStart = '';
    this._iPerPage = '';
    this._sOrderBy = ''; 
    this._sCtlType = '';
	this._sFilter = '';
    this._oCtlValue  = '';
	this.getMembers();
}