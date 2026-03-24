function AqbPltMain(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oAqbPltMain' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._oHtmlIds = oOptions.oHtmlIds == undefined ? {} : oOptions.oHtmlIds;

    this._iStart = 0;
    this._iPerPage = 10;
    this._sFilter = '';
}

AqbPltMain.prototype.changeFilter = function(sType, sFilter, oLink) {
	var sId = $(oLink).attr('id');
	$(oLink).parent().siblings('.active:visible').hide().siblings('.notActive:hidden').show().siblings('#' + sId + '-pas:visible').hide().siblings('#' + sId + '-act:hidden').show();

	this._iStart = 0;
	this._sFilter = sFilter;
	this.getProfiles(sType);
};

AqbPltMain.prototype.changePage = function(sType, iStart, iPerPage) {
	this._iStart = iStart;
	this._iPerPage = iPerPage;
	this.getProfiles(sType);
};

AqbPltMain.prototype.getProfiles = function(sType) {
	var $this = this;
	var oDate = new Date();

	var sLoadingId = '#' + this._oHtmlIds['loading_' + sType];
	$(sLoadingId).bx_loading();

	oParams = {
		start: this._iStart,
		per_page: this._iPerPage,
		filter: this._sFilter,
		_t: oDate.getTime()
	};

	$.get(
        this._sActionsUrl + 'act_get_profiles/' + sType + '/',
        oParams,
        function(oData){
        	$(sLoadingId).bx_loading();

            $this.processResult(oData);
        },
        'json'
    );
};

AqbPltMain.prototype.onGetProfiles = function(sType, oData) {
	var $this = this;

	$('#' + this._oHtmlIds['profiles_' + sType]).bx_anim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
		$(this).html(oData.content).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
	});
};

AqbPltMain.prototype.processResult = function(oData) {
	var $this = this;

	if(oData && oData.message != undefined && oData.message.length != 0)
    	alert(oData.message);

    if(oData && oData.reload != undefined && parseInt(oData.reload) == 1)
    	document.location = document.location;

    if(oData && oData.popup != undefined) {
    	var oPopup = $(oData.popup).hide(); 

    	$('#' + oPopup.attr('id')).remove();
        oPopup.prependTo('body').dolPopup({
            fog: {
				color: '#fff',
				opacity: .7
            },
            closeOnOuterClick: false
        });
    }

    if (oData && oData.eval != undefined)
        eval(oData.eval);
};