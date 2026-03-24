;(function(e){e.fn.visible=function(t,n,r){var i=e(this).eq(0),s=i.get(0),o=e(window),u=o.scrollTop(),a=u+o.height(),f=o.scrollLeft(),l=f+o.width(),c=i.offset().top,h=c+i.height(),p=i.offset().left,d=p+i.width(),v=t===true?h:c,m=t===true?c:h,g=t===true?d:p,y=t===true?p:d,b=n===true?s.offsetWidth*s.offsetHeight:true,r=r?r:"both";if(r==="both")return!!b&&m<=a&&v>=u&&y<=l&&g>=f;else if(r==="vertical")return!!b&&m<=a&&v>=u;else if(r==="horizontal")return!!b&&y<=l&&g>=f}})(adjq);


adi=(function(j,a,pp,nt,acp){
	// Invitation Preview
	a.newPopup('adi_invPreview', 'ip', 30, 2, {
		ntr:'ip',hoc:1,iphtml:'',
		uact:'adi_do=invite_preview',
		preShow:function(){
			var  html = this.iphtml;
			var id = 'adi_invite_preview_iframe';
			var ifrm = document.getElementById(id),bd;
			adjq('#'+id).width(600);
			adjq('#'+id).height(400);
			ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : ( (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument);
			ifrm.document.open();
			ifrm.document.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head> <body style="margin:0px;padding:0px;width:auto;height:auto;font-family:verdana;font-size:13px;">'+html+'</body></html>');
			ifrm.document.close();
			// this.childWin = ifrm;
			var nn = ifrm.document.getElementsByTagName('body')[0];
			var wd = nn.scrollWidth != 0 ? nn.scrollWidth : adjq(nn).width();
			var ht = nn.scrollHeight != 0 ? nn.scrollHeight : adjq(nn).height();
			wd = wd != 0 ? wd : adjq(ifrm.document).width();
			ht = ht != 0 ? ht : adjq(ifrm.document).height();
			adjq('#adi_invite_preview_iframe').width(wd);
			adjq('#adi_invite_preview_iframe').height(ht);
		},
		postShow:function(){
			var id = 'adi_invite_preview_iframe';
			var ifrm = document.getElementById(id);
			ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : ( (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument);
			var nn = ifrm.document.getElementsByTagName('body')[0];
			var wd = nn.scrollWidth != 0 ? nn.scrollWidth : adjq(nn).width();
			var ht = nn.scrollHeight != 0 ? nn.scrollHeight : adjq(nn).height();
			wd = wd != 0 ? wd : adjq(ifrm.document).width();
			ht = ht != 0 ? ht : adjq(ifrm.document).height();
			adjq('#adi_invite_preview_iframe').width(wd);
			adjq('#adi_invite_preview_iframe').height(ht);
			adjq('#adi_invite_preview_iframe').css('max-height', adjq('#adi_mask2').height() - 250);
			
			adi.call_event('invitation_preview_load');
		}
	});
	return a;
})(adjq,adi,adipps,adintrs,adiconts);


var adirs = (function(j){
	var aa = {
		set_serv_list: function(){
			var m=this;
			// Textbox with default values
			j('.adirs_deftxt').each(function(i,m){
				var v = j(this).attr('data-default');
				if(v) {j(this).val(j(this).attr('data-default')).addClass('adirs_def_txt');}
				j(m).focus(function(){
					if(j(this).attr('data-default') == j(this).val())
					{
						j(this).val('').removeClass('adirs_def_txt');
					}
				}).blur(function(){
					var v = j(this).val().replace(/\s+/, '');
					if(v == '')
					{
						j(this).val(j(this).attr('data-default')).addClass('adirs_def_txt');
					}
				}).removeClass('adirs_deftxt');
			});

			// focus switching between password input and note.
			j('.adirs_nc_shownode').focus(function(){
				j(this).hide();
				j(this).siblings('.adirs_nc_editnode').show().focus();
			})
			j('.adirs_nc_editnode').blur(function(){
				var v = j(this).val(), dt=j(this).attr('data-default');
				if(v == '' || v == dt)
				{
					j(this).hide();
					j(this).siblings('.adirs_nc_shownode').show();
				}
			});


			j('.adi_nc_user_email_input').keyup(function(e){
var kk = e.which;
if(kk && kk != 189 && kk != 190 && (kk < 65 || kk > 90)) { return false; }
var dm=j(this).val(),dmn='';
dm=dm.toLowerCase();
if(typeof dm == 'string' && dm.length > 0) {
	dmn = dm.replace(/^[^@]*@/g,'');
}
if(m.last_tp_search != dmn && m.type_search_time_fn != undefined) {
	clearTimeout(m.type_search_time_fn);
}
if(dmn == '') { return false; }
else if(m.last_tp_search != dmn)
{
	m.last_tp_search = dmn;
	var csid = j('.adi_service_key_val').val();
	if(csid != '' && adi.services[csid][1][0] == '*') { return true; }
	for(var i in adi.services)
		if(typeof i == 'string' && typeof adi.services[i] == 'object')
			if(adi.services[i][1][0] != '*')
				for(var l in adi.services[i][1])
					if(adi.indexOf(adi.services[i][1][l], dmn) === 0)
					{
						adi_search.setKey(i);
						return true;
					}

	if(dmn.length > 3 && adi.indexOf(dmn, '.') != -1)
	{
		m.type_search_time_fn = setTimeout(function(){
			adjq.ajax({type: 'POST', data: {query:dmn}, url: adi.ajaxUrl('adi_do=type_search'),
				success: function(list)
				{
					var cf=0;
					for(var i in list)
					if(typeof adi.services[i] == 'object')
					{
						if(cf == 0) {
							adi_search.setKey(i);
							cf = 1;
						}
						if(adi.services[i][1][0] != '*')
						{
							adjq.merge(adi.services[i][1], list[i]);
						}
					}
				},
				error : function(d) {},
				dataType: 'json'
			});
		},400);
	}
}
			});


			//  Addressbook Form Submit
			j('.adi_nc_addressbook_form').submit(function(e){
				var einp = j('.adi_user_email', this);
				var pinp = j('.adi_user_password', this);
				var sinp = j('.adi_service_key_val', this);
				var sk = sinp.val();
				var os = j('.adi_oauth_submit').val() || 0;
				if(sk != '' && adi.services[sk] && (adi.services[sk][0][2] == 1 || adi.services[sk][0][2] == 2) && os == 0)
				{
					adi_oauth_login(sk);
					var frm = this;
					if(adi.services[sk][0][2] !== 2){ adirs_send_effect(frm); }
					e.preventDefault();
					return false;
				}

				if(adi.trim(einp.val()) == '' || einp.val() == einp.attr('data-default'))
				{
					adi.show_pp_err(adi.phrases['adi_msg_empty_email_address']);
				}
				else if(adi.trim(pinp.val()) == '' || pinp.val() == pinp.attr('data-default'))
				{
					adi.show_pp_err(adi.phrases['adi_msg_empty_password']);
				}
				else if(adi.trim(sinp.val()) == '' || sinp.val() == sinp.attr('data-default'))
				{
					adi.show_pp_err(adi.phrases['adi_msg_invalid_service']);
				}
				else
				{
					var frm = this;
					adirs_send_effect(frm);
					var frm_dt=j(this).serialize();
					j('.adi_captcha_text_cls', this).val('');
					adi.hide_pp_err();
					j.ajax({
						type: 'POST', data: frm_dt,
						url: adi.ajaxUrl('adi_do=get_contacts'),
						success: function(code)
						{
							// adirs_send_effect(frm);
							adi.eval(code);
						},
						error: function(d) {
							// adirs_send_effect(frm);
						},
						dataType: 'text'
					});
				}
				e.preventDefault();
			});

			// Contact File Submit
			j('.adi_nc_contact_file_form').submit(function(e){
				var cl = j('.adi_contact_file', this).val();
				if(adi.trim(cl) == '')
				{
					adi.show_pp_err(adi.phrases['adi_msg_contact_file_not_selected']);
				}
				else if(j('.adi_contact_file', this).get(0).files[0].size > adi.cflt) {
					adi.show_pp_err(adi.phrases['adi_msg_contact_file_size_limit_exceeded']);
				}
				else if(!cl.toLowerCase().match(/\.csv$|\.ldif$|\.vcf$|\.txt$/))
				{
					adi.show_pp_err(adi.phrases['adi_msg_invalid_contact_file_format']);
				}
				else 
				{
					adirs_send_effect(this);
					return true;
				}
				e.preventDefault();
				return false;
			});

			// Manual Inviter
			j('.adi_nc_manual_form').submit(function(e){
				var cl = j('.adi_textarea', this).val();
				if(adi.trim(cl) == '' || cl == j('.adi_textarea', this).attr('data-default')) {
					adi.show_pp_err(adi.phrases['adi_msg_empty_contacts_list']);
				}
				else if(cl.length > adi.cllt) {
					adi.show_pp_err(adi.phrases['adi_error_contact_list_length_limit_exceeded']);
				}
				else if(!cl.match(/@/)) {
					adi.show_pp_err(adi.phrases['adi_msg_empty_contacts_list']);
				}
				else
				{
					var frm = this;
					adirs_send_effect(frm);
					j.ajax({
						type: 'POST', data: j(this).serialize(), 
						url: adi.ajaxUrl('adi_do=get_contacts'),
						success: function(code)
						{
							// adirs_send_effect(frm);
							adi.eval(code);
						},
						error: function(d) {
							// adirs_send_effect(frm);
						},
						dataType: 'text'
					});
				}
				e.preventDefault();
			});

			// Accordian
			j('.adi_serv_item').click(function(){
				adirs.expand_serv(this);
			});

			j('.adi_close_block').click(function(){
				adirs.expand_serv(this);
			});

			j('.adi_change_block').click(function(){
				adjq('.adi_type2_instr_entity').hide();
				adjq('.adi_dflt_inter').show();
			});

			adi.call_event('login_form_load');
		},
		get_offset: function(){
			
		},
		expand_serv: function(m){
			adi_search.unset_sk();
			var pp = j(m).parents('.adi_serv_expand_block');
			j('.adi_serv_sep2').css('visibility', 'visible');
			if(pp.hasClass('adi_serv_inner_sec_ex'))
			{
				pp.removeClass('adi_serv_inner_sec_ex');
				j('.adi_serv_inner_sect', pp).hide();
				j('.adi_err_msg').hide();
			}
			else
			{
				j('.adi_serv_inner_sec_ex').removeClass('adi_serv_inner_sec_ex');
				j('.adi_serv_inner_sect').hide();
				j('.adi_err_msg').hide();

				var sno = pp.attr('data-sno');
				if(sno == 1 || sno == 2)
					j('.adi_serv_sect_sep1').css('visibility', 'hidden');
				if(sno == 3 || sno == 2)
					j('.adi_serv_sect_sep2').css('visibility', 'hidden');

				pp.addClass('adi_serv_inner_sec_ex');
				j('.adi_serv_inner_sect', pp).slideDown(250,function(){
					for(var i=0;i<3;i++)
					{
						if(!j('.adi_btn1', pp).visible(true))
						{
							j(window).scrollTop(j(window).scrollTop() + 70);
						}
					}
				});
			}
		}
	};
	return aa;
})(adjq);

function set_cf_block()
{
	adjq('.adi_expand_instr').click(function(){
		var id = adjq(this).attr('rel') || '';
		if(id != '') {
			var cr = adjq('.'+id);
			if(cr.css('display') == 'none') {
				adjq('.adi_nc_ct_sect_out').hide();
				cr.show();
			}
			else {
				cr.hide();
			}
		}
	});
	adjq('.adi_dwn_sample_file').click(function(){
		var sn = adjq(this).attr('rel');
		if(!isNaN(sn)){
			var url = adi.ajaxUrl('adi_do=download_sample&v='+sn);
			if(adjq('#adi_dwn_sample').length == 0) {
				adjq('body').append('<iframe id="adi_dwn_sample" style="display:none;" src="'+url+'"></iframe>');
			}
			else {
				adjq('#adi_dwn_sample').attr('src',url);
			}
		}
		return false;
	});

	adi.call_event('contact_file_instructions_load');
}


function adirs_send_effect(frm, flg)
{
	flg=flg?1:0;
	var p = adjq('.adi_off_effect',frm);
	// if(p.css('display') == 'none')
	if(flg)
	{
		p.show();
		p.siblings('.adi_on_effect').hide();
	}
	else
	{
		p.hide();
		p.siblings('.adi_on_effect').show();
	}
}


function adirs_cf_response(cd)
{
	if(cd) {
		var frm = adjq('.adirs_show_conts_form');
		adjq('.adi_list_cache_id', frm).val(cd);
		frm.submit();
	}
}


adi.show_pp_err = function(msg){
	adjq('.adi_err_msg').html(msg).show();
	adjq('.adi_on_effect').hide();
	adjq('.adi_off_effect').show();
};

adi.hide_pp_err = function(){
	adjq('.adi_err_msg').hide();
};

adi.show_ip_err = function(msg){
	adjq('.adi_err_msg').html(msg).show();
};

adi.hide_ip_err = function(){
	adjq('.adi_err_msg').hide();
};

// 97458235

var adirs_cd = {
	cd_id: 'is',
	c_model: 1,
	cache_id: '',
	search_pl: '',
	chtml:'',dfhtml:'',
	pgsz: 10,
	pno:1,cont_typ:1,
	is_data:{},
	get_model: function(){ return (this.c_model = (adjq(window).width() > 800 ? 2 : 1) ); },
	reg_friend_sender: function(){
		var dt=this.is_data;
		this.get_model();

		this.dfhtml = adi.member_html;

		if(this.dfhtml != '')
		{
			var ln=dt.length;
			var strt=0,lst=ln-1,ssr='';
			if(this.c_model == 1)
			{
				strt=(this.pno-1)*this.pgsz;
				lst=(this.pno*this.pgsz)-1;

				if(dt[this.pgsz] != undefined)
				{
					var tot = Math.ceil((this.is_data.length / this.pgsz));
					this.set_pagination_links(1,tot);
				}
			}
			else
			{
				adjq('.adi_nc_conts_panel').css('max-height', '689px');
			}
			for(var i=0;i<ln;i++)
			{
				this.search_pl += ' '+dt[i][1]+' '+((this.cont_typ == 1 || this.cont_typ == 2) ? dt[i][0] : '')+"<<<"+(i+1)+"\xFE";
			}
		}

		adjq('.adi_nc_conts_html').html(this.build_fa_html(strt,lst));

		adjq('.adi_pagi_link').click(function(){
			var pno = adjq(this).attr('data-page');
			if(!isNaN(pno))
			{
				pno=parseInt(pno);
				if(adirs_cd.last_query_nums[0])
				{
					adirs_cd.show_search_results(pno);
				}
				else
				{
					var strt=(pno-1)*adirs_cd.pgsz;
					var lst=(pno*adirs_cd.pgsz)-1;
					adjq('.adi_nc_conts_html').html(adirs_cd.build_fa_html(strt,lst));
					var tot = Math.ceil((adirs_cd.is_data.length / adirs_cd.pgsz));
					adirs_cd.set_pagination_links(pno,tot);
				}
			}
		});

		// Common
		this.reg_common_conts();

		adi.call_event('friend_adder_form_loaded');
	},
	build_fa_html: function(strt,lst){
		var ids=[];
		for(var i=strt;i<=lst;i++)
		{
			ids.push(i);
		}
		return this.get_fa_html(ids);
	},
	get_fa_html:function(ids){
		var dt=this.is_data;
		var tt='',acl=false,html='',ln=ids.length;
		for(var j=0;j<ln;j++)
		{
			i=ids[j];
			if(dt[i] == undefined) {break;}
			acl=!acl; tt=this.dfhtml;

			tt = tt.replace(/\[member_userid\]/g   , dt[i][0]);
			tt = tt.replace(/\[member_username\]/g , dt[i][1]);
			tt = tt.replace(/\[member_email\]/g    , dt[i][2]);
			tt = tt.replace(/\[member_name\]/g     , dt[i][3]);
			tt = tt.replace(/\[member_avatar\]/g   , dt[i][4]);
			if( dt[i][7] == 1) {tt=tt.replace(/\[is_sent\]/g , 'adi_invite_sent');}
			else {tt=tt.replace(/\[is_sent\]/g , '');}
			if(acl) {tt=tt.replace(/\[alternate_cls\]/g , 'adi_odd');}
			else {tt=tt.replace(/\[alternate_cls\]/g , 'adi_even');}
			tt=tt.replace(/\[contact_cacheid\]/g , this.cache_id);
			tt=tt.replace(/\[contact_no\]/g , i+1);
			html+=tt;
		}
		return html;
	},
	add_friend:function(m){
		var eid = adjq(m).attr('data-adiid'), lid=adjq(m).attr('data-listid');
		if(eid!='' && lid!='')
		{
			var mm=m;
			adjq(m).hide();
			adjq(m).siblings('.adi_working').show();
			adjq.ajax({
				type: 'POST',
				data: 'adi_list_id='+lid+'&add_as_friend=Add&adi_reg_ids%5B%5D='+eid,
				url: adi.ajaxUrl('adi_do=send_friend_requests'),
				success: function(code) {
					var id = parseInt(adjq(mm).attr('data-locid'));
					adjq(mm).siblings('.adi_working').hide();
					adjq(mm).siblings('.adi_done').show();
					if(!isNaN(id))
					{
						if(adirs_cd.is_data[id-1]) { adirs_cd.is_data[id-1][7]=1; }
						adjq('#adi_sent_invite_'+id).addClass('adi_invite_sent');
					}
				},
				error: function(d){},
				dataType: 'text'
			});
		}
		return false;
	},
	reg_invite_sender: function(){
		var dt=this.is_data;
		
		if(this.cont_typ == 1){ this.dfhtml=adi.email_avatar_html; }
		if(this.cont_typ == 2){ this.dfhtml=adi.email_html; }
		if(this.cont_typ == 3){ this.dfhtml=adi.social_avatar_html; }
		if(this.cont_typ == 4){ this.dfhtml=adi.social_html; }

		this.get_model();

		if(this.dfhtml != '')
		{
			var ln=dt.length;
			var strt=0,lst=ln-1,ssr='';
			if(this.c_model == 1)
			{
				strt=(this.pno-1)*this.pgsz;
				lst=(this.pno*this.pgsz)-1;

				if(dt[this.pgsz] != undefined)
				{
					var tot = Math.ceil((this.is_data.length / this.pgsz));
					this.set_pagination_links(1,tot);
				}
			}
			else
			{
				adjq('.adi_nc_conts_panel').css('max-height', '689px');
			}
			for(var i=0;i<ln;i++)
			{
				this.search_pl += ' '+dt[i][1]+' '+((this.cont_typ == 1 || this.cont_typ == 2) ? dt[i][0] : '')+"<<<"+(i+1)+"\xFE";
			}
		}

		adjq('.adi_nc_conts_html').html(this.build_is_html(strt,lst));

		adjq('.adi_pagi_link').click(function(){
			var pno = adjq(this).attr('data-page');
			if(!isNaN(pno))
			{
				pno=parseInt(pno);
				if(adirs_cd.last_query_nums[0])
				{
					adirs_cd.show_search_results(pno);
				}
				else
				{
					var strt=(pno-1)*adirs_cd.pgsz;
					var lst=(pno*adirs_cd.pgsz)-1;
					adjq('.adi_nc_conts_html').html(adirs_cd.build_is_html(strt,lst));
					var tot = Math.ceil((adirs_cd.is_data.length / adirs_cd.pgsz));
					adirs_cd.set_pagination_links(pno,tot);
				}
			}
		});

		adjq('.adi_goto_inviter_page_link').click(function(e){
			e.preventDefault();
			adi_submit_back_form(e);
			return false;
		});

		adjq('.adi_invite_preview_link').click(function(){
			adipps.ip.show(); return false;
		});

		// Add all as friends
		this.reg_common_conts();

		adi.call_event('invite_sender_form_loaded');
	},
	set_pagination_links: function(pno,total){
		if(pno <= total)
		{
			adjq('.adi_pagination_lnks').show();
			adjq('.adi_pagi_prev_lnk').attr('data-page', pno-1).css('visibility', 'visible');
			adjq('.adi_pagi_next_lnk').attr('data-page', pno+1).css('visibility', 'visible');
			if(pno < 2) {
				adjq('.adi_pagi_prev_lnk').hide();
				adjq('.adi_pagi_prevoff_lnk').show();
			}
			else {
				adjq('.adi_pagi_prev_lnk').show();
				adjq('.adi_pagi_prevoff_lnk').hide();
			}
			if(pno >= total) {
				adjq('.adi_pagi_next_lnk').hide();
				adjq('.adi_pagi_nextoff_lnk').show();
			}
			else {
				adjq('.adi_pagi_next_lnk').show();
				adjq('.adi_pagi_nextoff_lnk').hide();
			}
			adjq('.adi_current_page').html(pno);
			adjq('.adi_total_pages').html(total);
		}
		else
		{
			adjq('.adi_pagination_lnks').hide();
		}
	},
	build_is_html: function(strt,lst){
		var ids=[];
		for(var i=strt;i<=lst;i++)
		{
			ids.push(i);
		}
		return this.get_is_html(ids);
	},
	get_is_html:function(ids){
		var dt=this.is_data;
		var tt='',acl=false,html='',ln=ids.length;
		for(var j=0;j<ln;j++)
		{
			i=ids[j];
			if(dt[i] == undefined) {break;}
			acl=!acl; tt=this.dfhtml;
			tt=tt.replace(/\[contact_id\]/g      , dt[i][0]);
			tt=tt.replace(/\[contact_name\]/g    , dt[i][1]);
			tt=tt.replace(/\[contact_avatar\]/g  , dt[i][2]);
			if( dt[i][3] == 1) {tt=tt.replace(/\[is_sent\]/g , 'adi_invite_sent');}
			else {tt=tt.replace(/\[is_sent\]/g , '');}
			if(acl) {tt=tt.replace(/\[alternate_cls\]/g , 'adi_odd');}
			else {tt=tt.replace(/\[alternate_cls\]/g , 'adi_even');}
			tt=tt.replace(/\[contact_cacheid\]/g , this.cache_id);
			tt=tt.replace(/\[contact_no\]/g , i+1);
			html+=tt;
		}
		return html;
	},
	
	last_query:'',
	last_query_nums:[],
	show_search_results:function(pno){
		pno=parseInt(pno);
		var strt=(pno-1)*this.pgsz;
		var lst=(pno*this.pgsz)-1;
		var ln=this.last_query_nums.length,ids=[];
		for(var i=strt; i<=lst;i++)
		{
			if(this.last_query_nums[i])
			{
				ids.push(parseInt(this.last_query_nums[i])-1);
			}
		}
		if(this.cd_id == 'is')
			adjq('.adi_nc_conts_html').html(this.get_is_html(ids));
		else
			adjq('.adi_nc_conts_html').html(this.get_fa_html(ids));
		var tot = Math.ceil((ln / this.pgsz));
		this.set_pagination_links(pno,tot);
	},

	search_is_conts: function(){
		var q=adjq('.adi_search_friend').val();
		if(adi.trim(q) != '' && q != this.last_query && q != adjq('.adi_search_friend').attr('data-default'))
		{
			q=adi.trim(q.toLowerCase());
			this.last_query = q;

			var patt = new RegExp('\\s'+q+'[^\\xFE]*','ig');
			var r = this.search_pl.match(patt), ct=0;
			adjq('.adi_cont_blk').hide();
			adjq('.adi_conts_altern').hide();
			this.last_query_nums = [];
			
			for(var i in r)
			{
				if(typeof r[i] == 'string')
				{
					var c=r[i].match(/[0-9]+$/ig);
					if(c[0] != '')
					{
						this.last_query_nums.push(c[0]);
						adjq('#adi_sent_invite_'+c[0]).show();
						ct++;
					}
				}
			}
			if(ct==0){ adjq('.adi_conts_altern').show(); adjq('.adi_reset_search_out').hide(); adjq('.adi_pagination_lnks').hide(); }
			else {
				adjq('.adi_reset_search_out').show();
				adjq('.adi_search_results_cnt').html(ct);
				adjq('.adi_search_results_query').html('"'+q+'"');
				if(this.c_model == 1) {
					this.show_search_results(1);
				}
			}
		}
		else if(adi.trim(q) == '' || q == adjq('.adi_search_friend').attr('data-default'))
		{
			this.reset_search_results({});
		}
	},
	reg_common_conts: function(){
		// Textbox with default values
		adjq('.adirs_deftxt').each(function(i,m){
			var v = adjq(this).attr('data-default');
			if(v) {adjq(this).val(adjq(this).attr('data-default')).addClass('adirs_def_txt');}
			adjq(m).focus(function(){
				if(adjq(this).attr('data-default') == adjq(this).val())
				{
					adjq(this).val('').removeClass('adirs_def_txt');
				}
			}).blur(function(){
				var v = adjq(this).val().replace(/\s+/, '');
				if(v == '')
				{
					adjq(this).val(adjq(this).attr('data-default')).addClass('adirs_def_txt');
				}
			}).removeClass('adirs_deftxt');
		});

		adjq('.adi_goto_inviter_page_link').click(function(e){
			e.preventDefault();
			adi_submit_back_form(e);
			return false;
		});
	},
	last_query: '',
	reset_search_results:function(e){
		if(e.preventDefault) { e.preventDefault(); }
		adjq('.adi_search_friend').val('');
		adjq('.adi_reset_search_out').hide();
		adjq('.adi_cont_blk').show();
		adjq('.adi_conts_altern').hide();
		this.last_query='';
		this.last_query_nums=[];
		if(this.c_model == 1)
		{
			if(this.cd_id == 'is')
				adjq('.adi_nc_conts_html').html(this.build_is_html(0,(this.pgsz-1)));
			else
				adjq('.adi_nc_conts_html').html(this.build_fa_html(0,(this.pgsz-1)));
			var tot = Math.ceil((this.is_data.length / this.pgsz));
			this.set_pagination_links(1,tot);
		}
		return false;
	},
	send_invite: function(m){
		var eid = adjq(m).attr('data-adiid'), lid=adjq(m).attr('data-listid');
		if(eid!='' && lid!='')
		{
			var mm=m;
			adjq(m).hide();
			adjq(m).siblings('.adi_working').show();
			adjq.ajax({
				type: 'POST', 
				data: 'adi_list_id='+encodeURIComponent(lid)+'&adi_cont_id='+encodeURIComponent(eid),
				url: adi.ajaxUrl('adi_do=send_user_invitation'),
				success: function(code) {
					adjq(mm).siblings('.adi_working').hide();
					adjq(mm).siblings('.adi_done').show();
					var id = parseInt(adjq(mm).attr('data-locid'));
					if(!isNaN(id))
					{
						if(adirs_cd.is_data[id-1]) { adirs_cd.is_data[id-1][3]=1; }
						adjq('#adi_sent_invite_'+id).addClass('adi_invite_sent');
					}
				},
				error: function(d){},
				dataType: 'text'
			});
		}
		return false;
	},
};




// Invite History

var adiih = {
	expand_cont: function(m){
		m=adjq(m);
		if(!m.hasClass('adiih_removing_node'))
		{
			if(m.hasClass('adiih_cont_blk_ext')) {
				m.removeClass('adiih_cont_blk_ext');
				adjq('.adiih_cont_email', m).hide();
				adjq('.adiih_cont_issued', m).hide();
			}
			else {
				m.addClass('adiih_cont_blk_ext');
				adjq('.adiih_cont_email', m).slideDown('100');
				adjq('.adiih_cont_issued', m).slideDown('100');
			}
		}
	},
	remove_invite: function(e,m){
		if(e.preventDefault) {e.preventDefault();}
		var mm=adjq(m);
		var cid=mm.attr('data-cid'),pno=mm.attr('data-pno'),pp=adjq(mm).parents('.adiih_cont_blk');
		if(cid != '' && confirm("Are you sure you want to delete this contact?") == true)
		{
			pp.addClass('adiih_removing_node');
			mm.hide();
			mm.siblings('.adiih_removing').show();
			adjq.ajax({
				type: 'POST',
				data: 'adi_ih_ids_list%5B%5D='+encodeURIComponent(cid)+'&page_no='+pno,
				url: adi.dataUrl('adi_do=paginate'),
				success: function(code) {
					if(code == '') {
						adjq('.adi_ih_error_table_out').show();
						adjq('.adi_nc_ih_panel_outer').hide();
					}
					else {
						adjq('.adi_nc_invites_table_out').html(code);
					}
				},
				error: function(d){},
				dataType: 'text'
			});
		}
		return false;
	},
	remove_dk_invite: function(e,m){
		if(e.preventDefault) {e.preventDefault();}
		var mm=adjq(m);
		var cid=mm.attr('data-cid'),pno=mm.attr('data-pno');
		if(cid != '' && confirm("Are you sure you want to delete this contact?") == true)
		{
			mm.hide();
			mm.siblings('.adiih_removing').show();
			adjq.ajax({
				type: 'POST',
				data: 'adi_ih_ids_list%5B%5D='+encodeURIComponent(cid)+'&page_no='+pno,
				url: adi.dataUrl('adi_do=paginate'),
				success: function(code) {
					if(code == '') {
						adjq('.adi_ih_error_table_out').show();
						adjq('.adi_nc_ih_panel_outer').hide();
					}
					else {
						adjq('.adi_nc_invites_table_out').html(code);
					}
				},
				error: function(d){},
				dataType: 'text'
			});
		}
		return false;
	},
	paginate_invites: function(pno){
		pno=parseInt(pno);
		if(!isNaN(pno))
		{
			adjq('.adi_pagination_lnks').hide();
			adjq('.adiih_loading_invites').show();
			adjq.ajax({
				type: 'POST',
				data: 'page_no='+pno,
				url: adi.dataUrl('adi_do=paginate'),
				success: function(code) {
					adjq('.adi_nc_invites_table_out').html(code);
				},
				error: function(d){},
				dataType: 'text'
			});
		}
	},
};


var adi_oauth_resp = {
	respond: function(msg)
	{
		if(typeof msg == 'string' && msg != '1')
		{
			if(adipps.lg && adipps.lg.isopen == true)
			{
				adi.show_pp_err(msg);
			}
			else
			{
				adi.show_ip_err(msg);
			}
		}
		else if(parseInt(msg) == 1)
		{
			adjq('#adi_ouath_form').submit();
		}
	}
};

var adi_irc = {
	oi:null,
	pp:null,
	init: function(){
		adjq('.adi_irc_loading_out').hide();
		adjq('.adi_irc_form_out').show();

		adjq('.adi_importer_captcha_form').submit(function(e) {
			var ct = adjq('.adi_importer_captcha_text', this).val();
			if(ct != '')
			{
				adjq('.adi_importer_cap_info_pass').html(adjq('.adi_importer_cap_info', this).html());
				adjq('.adi_captcha_text_cls').val(ct);
				adjq('.adi_importer_captcha_frm').hide();
				adjq('.adi_dflt_inter').show();
				adjq('.adi_nc_irc_parent_form').submit();
				return false;
			}
			// e.preventDefault();
			return false;
		});

		adjq('.adi_irc_cancel').click(function(){
			adjq('.adi_importer_captcha_frm').hide();
			adjq('.adi_dflt_inter').show();

			adirs_send_effect(adjq('.adi_nc_irc_parent_form'), true);
			adi_show_default();
		});
	},
	reset: function(){
		var m = this;
		adjq('.adi_irc_loading_out').show();
		adjq('.adi_irc_form_out').hide();

		adjq('.adi_importer_captcha_frm').show();
		adjq('.adi_dflt_inter').hide();
		
		adjq.ajax({
			type: 'POST', data: adjq('.adi_nc_irc_parent_form').serialize(), 
			url: adi.ajaxUrl('adi_do=get_importer_captcha'),
			success: function(code) {
				adjq('.adi_irc_form_out').html(code);
				m.init();
			},
			error : function(d) {},
			dataType: 'text'
		});
	}
};


var adi_search = {
	show_services: function(){
		var m=this;
		adjq('.adi_nc_services_panel_out').slideDown(200);
		adjq('.adi_nc_up_arrow', m.rtf).show();
		adjq('.adi_nc_down_arrow', m.rtf).hide();
	},
	hide_services: function(){
		adjq('.adi_nc_services_panel_out').slideUp(200);

		var m=this;
		adjq('.adi_nc_up_arrow', m.rtf).hide();
		adjq('.adi_nc_down_arrow', m.rtf).show();
		if(adjq('.adi_nc_service_select_hoverd').length > 0)
		{
			var v = adjq('.adi_nc_service_select_hoverd').parent().attr('data');
			m.setKey(v);
			adjq('.adi_nc_service_select_hoverd').addClass('adi_nc_service_select').removeClass('adi_nc_service_select_hoverd');
		}
		else {
			m.setKey(adjq('.adi_service_key_val',m.rtf).val());
		}

		var t = adjq('.adi_nc_service_input', m.rtf);
		if(t.val() == '')
		{
			t.val(adi.phrases['adi_ab_service_field_default_txt']);
			t.addClass('adi_nc_service_note');
		}
		m.reset_searchresults();
	},
	stxt: '',
	rt: undefined,
	rtf: undefined,
	init: function(){
		var m=this;
		m.service_ids_list = [];
		m.service_mapping = {};
		if(adi.services != undefined)
		{
			for(var i in adi.services)
			{
				if(typeof adi.services[i] == 'object')
				{
					m.service_ids_list.push(adi.services[i][0][1]);
					m.service_mapping[adi.services[i][0][1]] = i;
				}
			}
		}
		m.service_ids_list = ' '+m.service_ids_list.join(' ')+' ';

		m.rt = m.rtf = adjq('.adi_nc_addressbook_form');
		adjq('.adi_nc_service_input').focusin(function(){
			m.show_services();
			if(adjq(this).val()==adi.phrases['adi_ab_service_field_default_txt'])
			{
				adjq(this).val('').removeClass('adi_nc_service_note');
				m.stxt='';
			}
			var sk = adjq('.adi_service_key_val').val();
			adjq(this).removeClass(sk+'_si').val('');
			adjq('.adi_search_icon').show();

		}).focusout(function(){
			var sk = adjq('.adi_service_key_val').val();
			if(adjq('.adi_nc_service_select_hoverd').size() > 0)
			{
				sk=adjq('.adi_nc_service_select_hoverd').parent().attr('data');
				adjq('.adi_nc_service_select_hoverd').removeClass('adi_nc_service_select_hoverd');
			}
			else if(sk!='' && adi.services[sk])
			{
				m.setKey(sk);
			}
			else if(adjq(this).val() == '')
			{
				adjq(this).val(adi.phrases['adi_ab_service_field_default_txt']).addClass('adi_nc_service_note');
			}
			setTimeout(function(){
				m.stxt='';
			},20);
		}).keyup(function(e){
			m.search_serv(adjq(this).val(), e);
			
		}).keydown(function(e){
			if(e != undefined && e.which == 9)
			{
				m.hide_services();
			}
		});

		adjq('.adi_nc_services_panel_out').click(function(e){
			if(adjq(e.target).hasClass('adi_nc_service_select_out')) {
				var el = adjq(e.target);
			}
			else {
				var el = adjq(e.target).parents('.adi_nc_service_select_out');
			}
			if(el.size())
			{
				sk = el.attr('data');
				m.hide_services();
				m.setKey(sk);
				e.preventDefault();
				adjq('.adi_nc_service_input').blur();
			}
		});
	},
	setKey: function(sk){
		if(sk != '' && adi.services[sk])
		{
			var m=this,sinp=adjq('.adi_nc_service_input', m.rtf);
			if(sinp.val() == adi.phrases['adi_ab_service_field_default_txt'])
			{
				sinp.val('').removeClass('adi_nc_service_note');
			}
			pk=adjq('.adi_service_key_val',m.rtf).val();
			if(pk != '')
			{
				sinp.removeClass(pk+'_si');
			}
			adjq('.adi_search_icon',m.rtf).hide();
			adjq('.adi_service_key_val',m.rtf).val(sk);
			var stxt=adi.services[sk][0][1];
			sinp.addClass(sk+'_si').val(stxt).removeClass('adi_service_input_'+adi.orie).addClass('adi_service_input_'+adi.orie);
			adjq('.adi_search_icon', m.rtf).hide();
			if(adi.services[sk][0][2] == 1)
			{
				adjq('.adi_nc_password_input',m.rtf).hide();
				adjq('.adi_nc_password_note',m.rtf).show();
				var t = adi.phrases['adi_oauth_service_submit_btn_label'];
				adjq('.adi_nc_submit_addressbook',m.rtf).val(t.replace(/\[service_name\]/g,adi.services[sk][0][1]));
			}
			else
			{
				adjq('.adi_nc_password_input',m.rtf).show();
				adjq('.adi_nc_password_note',m.rtf).hide();
				adjq('.adi_nc_submit_addressbook',m.rtf).val(adi.phrases['adi_ab_submit_form_btn_text']);
			}
			adi.call_event('importer_service_set');
		}
	},
	unset_sk: function(){
		var m=this,sinp=adjq('.adi_nc_service_input', m.rtf);
		sinp.val(adi.phrases['adi_ab_service_field_default_txt']).addClass('adi_nc_service_note');
		pk=adjq('.adi_service_key_val',m.rtf).val();
		if(pk != '')
		{
			sinp.removeClass(pk+'_si');
		}
		adjq('.adi_search_icon',m.rtf).show();
	},
	search_serv: function(v,e) {
		var m=this;
		if(e != undefined && e.which == 13) {
			e.preventDefault();
			return false;
		}
		adjq('.adi_nc_service_select_hoverd').addClass('adi_nc_service_select').removeClass('adi_nc_service_select_hoverd');
		v = v.replace(/[^a-z0-9_\.]*/ig,'');
		if(v == '' || v != m.last_v) {
			m.reset_searchresults();
		}
		if(v.length > 0)
		{
			v = v.replace('.','\\.');
			var patt = new RegExp('\\s'+v+'[^\\s]*','ig'),r,s,i=''; 
			adjq('<div class="adi_nc_sr_service_select_sep" style="display:none;"></div>').prependTo(adjq('.adi_nc_services_panel_out', m.rt))
			while(r = patt.exec(m.service_ids_list))
			{
				s = adi.trim(r[0]);
				if(m.service_mapping[s] != undefined)
				{
					i=m.service_mapping[s];
					var ss = adjq('.adi_sserv_'+i);
					ss.parents('.adi_nc_service_select_out').hide();
					adjq('<div class="adi_nc_service_select_out adi_nc_sr_service_select_out" data="'+i+'"><div class="adi_nc_service_select_hoverd adi_sserv_'+i+'"><div class="adi_service_select_name '+i+'_si">'+adi.services[i][0][1]+'</div></div></div>').insertBefore(adjq('.adi_nc_sr_service_select_sep', m.rt));
				}
			}
		}
		return true;
	},
	reset_searchresults: function(){
		var m=this;
		adjq('.adi_nc_sr_service_select_out', m.rt).remove();
		adjq('.adi_nc_sr_service_select_sep', m.rt).remove();
		adjq('.adi_nc_service_select_out', m.rt).show();
	},
};


/*

Javscript events available in this theme :

	global_init
	login_form_load
	contact_file_instructions_load
	friend_adder_form_loaded
	invite_sender_form_loaded
	invite_history_loaded
	final_message_displayed
	topic_redirect_loaded

*/