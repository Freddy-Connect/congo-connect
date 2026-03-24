// JavaScript Document
$(document).ready(function(){
	
//check skin
if($('.giveme_bse_skin').val().length>0) {
	var skin=$('.giveme_bse_skin').val();
	$('.'+skin).parent().addClass('active');
}
	/*===========header pattern================*/

$('.himage').val($('#select_image_header').attr('data'));
	
	     $("#select_image_header").click(function () {
            $('#popup_image_header').slideToggle();
        });
		$('#popup_image_header div.item').click(function(){
        	var dataActive = $(this).attr('data');
        	var dataCurrent = $('#select_image_header').attr("data"); 
			var ptactive = $(this).find('img').attr('src'); 
			if(dataActive=='none'){
				$('.himage').val('none');
				$('#select_image_header').html('none');
			}else{
				$('.himage').val(dataActive);
				$('#select_image_header').html('<img src="'+ptactive+'"/>');
			} 
			$('#select_image_header').attr('data','');
            $('#select_image_header').attr('data', dataActive);
        }); 
		
/*===========body pattern================*/

$('.bimage').val($('#select_image_body').attr('data'));
	
	     $("#select_image_body").click(function () {
            $('#popup_image_body').slideToggle();
        });
		$('#popup_image_body div.item').click(function(){
        	var dataActive = $(this).attr('data');
        	var dataCurrent = $('#select_image_body').attr("data"); 
			var ptactive = $(this).find('img').attr('src'); 
			if(dataActive=='none'){
				$('.bimage').val('none');
				$('#select_image_body').html('none');
			}else{
				$('.bimage').val(dataActive);
				$('#select_image_body').html('<img src="'+ptactive+'"/>');
			} 
			$('#select_image_body').attr('data','');
            $('#select_image_body').attr('data', dataActive);
 }); 
 
 /*===========Designbox pattern================*/

$('.dimage').val($('#select_image_design').attr('data'));
	
	     $("#select_image_design").click(function () {
            $('#popup_image_design').slideToggle();
        });
		$('#popup_image_design div.item').click(function(){
        	var dataActive = $(this).attr('data');
        	var dataCurrent = $('#select_image_design').attr("data"); 
			var ptactive = $(this).find('img').attr('src'); 
			if(dataActive=='none'){
				$('.dimage').val('none');
				$('#select_image_design').html('none');
			}else{
				$('.dimage').val(dataActive);
				$('#select_image_design').html('<img src="'+ptactive+'"/>');
			} 
			$('#select_image_design').attr('data','');
            $('#select_image_design').attr('data', dataActive);
 }); 		
		
		
		/*===========body pattern================*/

$('.fimage').val($('#select_image_footer').attr('data'));
	
	     $("#select_image_footer").click(function () {
            $('#popup_image_footer').slideToggle();
        });
		$('#popup_image_footer div.item').click(function(){
        	var dataActive = $(this).attr('data');
        	var dataCurrent = $('#select_image_footer').attr("data"); 
			var ptactive = $(this).find('img').attr('src'); 
			if(dataActive=='none'){
				$('.fimage').val('none');
				$('#select_image_footer').html('none');
			}else{
				$('.fimage').val(dataActive);
				$('#select_image_footer').html('<img src="'+ptactive+'"/>');
			} 
			$('#select_image_footer').attr('data','');
            $('#select_image_footer').attr('data', dataActive);
}); 


 var styles = {
        		orange : {
        			bg_color: "181818", 
        			pattern_body_select: "none",
        			pattern_header_select: "none",
        			pattern_footer_select: "none", 
					
					text_color: "cccccc",
					link_color: "ffffff",
                    link_hover_color: "ec5538",
					link_active_color: "ec5538", 
					title_font_color:"ec5538",
					
					header_bg_color: "333333",
					header_text_color: "ffffff",
					header_link_color: "ffffff",
					header_link_hover_color: "ec5538",
					header_link_active_color: "ec5538",
					
					/*icons_bg_color: "#CFCFCF",
					icons_bg_hover_color: "#545454",
					
					buttons_bg_color: "#CFCFCF",
					buttons_bg_hover_color: "#545454", 
					
					addtocart_bg_color: "#545454",
					addtocart_bg_hover_color: "#CFCFCF", 
					
					mainmenu_bg_color: "#ffffff",
					mainmenu_dropdown_bg_color: "#FFFFFF",
					mainmenu_bg_hover_color: "#ffffff",
					mainmenu_bg_active_color: "#ffffff",
					mainmenu_text_color: "#424141",
					mainmenu_link_color: "#9e9e9e",
					mainmenu_link_hover_color: "#545454",
					mainmenu_link_sub_hover_color: "#545454",
					mainmenu_link_active_color: "#545454",*/ 
					
					menu_background_color: "333333",
					menu_font_color: "ffffff",
					menu_link_color: "ffffff",
					menu_hover_color: "ec5538",
					menu_active_color: "ec5538" ,
					
					footer_static_bg_color: "121212",
					footer_static_text_color: "cccccc",
					footer_static_link_color: "ffffff",
					footer_static_link_hover_color: "ec5538",
					footer_static_link_active_color: "ec5538" 
				},
				yellow : {
					bg_color: "181818",
					pattern_body_select: "none",
					pattern_header_select: "none",
					pattern_top_select: "none",
					pattern_footer_select: "none",
					
					text_color: "cccccc",
					link_color: "ffffff",
					link_hover_color: "fff001",
					link_active_color: "fff001",
					title_font_color:"fff001",
					
					header_bg_color: "333333",
					header_text_color: "ffffff",
					header_link_color: "ffffff",
					header_link_hover_color: "fff001",
					header_link_active_color: "fff001",
																			
					/*icons_bg_color: "#CFCFCF",
					icons_bg_hover_color: "#fff001",
					
					buttons_bg_color: "#CFCFCF",
					buttons_bg_hover_color: "#fff001",
					
					addtocart_bg_color: "#fff001",
					addtocart_bg_hover_color: "#CFCFCF",
					
					mainmenu_bg_color: "#ffffff",
					mainmenu_dropdown_bg_color: "#FFFFFF",
					mainmenu_bg_hover_color: "#ffffff",
					mainmenu_bg_active_color: "#ffffff",
					mainmenu_text_color: "#9e9e9e",
					mainmenu_link_color: "#9e9e9e",							
					mainmenu_link_hover_color: "#fff001",
					mainmenu_link_sub_hover_color: "#fff001",
					mainmenu_link_active_color: "#fff001",*/
					
					menu_background_color: "333333",
					menu_font_color: "ffffff",
					menu_link_color: "ffffff",
					menu_hover_color: "fff001",
					menu_active_color: "fff001" ,
					
					footer_static_bg_color: "121212",
					footer_static_text_color: "cccccc",
					footer_static_link_color: "ffffff",
					footer_static_link_hover_color: "fff001",
					footer_static_link_active_color: "fff001"
				},
				blue : {
					bg_color: "181818", 
        			pattern_body_select: "none",
        			pattern_header_select: "none",
        			pattern_footer_select: "none", 
					
					text_color: "cccccc",
					link_color: "ffffff", 
                    link_hover_color: "00bff3",
					link_active_color: "00bff3",
					title_font_color:"00bff3", 	
					
					header_bg_color: "333333",
					header_text_color: "ffffff",
					header_link_color: "ffffff",
					header_link_hover_color: "00bff3",
					header_link_active_color: "00bff3", 
					
					/*icons_bg_color: "#CFCFCF",
					icons_bg_hover_color: "#2078bb", 
					buttons_bg_color: "#CFCFCF",
					buttons_bg_hover_color: "#2078bb", 
					addtocart_bg_color: "#2078bb",
					addtocart_bg_hover_color: "#CFCFCF", 
					
					mainmenu_bg_color: "#2078bb",
					mainmenu_dropdown_bg_color: "#FFFFFF",
					mainmenu_bg_hover_color: "#ffffff",
					mainmenu_bg_active_color: "#ffffff",
					mainmenu_text_color: "#9e9e9e",
					mainmenu_link_color: "#9e9e9e",
					mainmenu_link_hover_color: "#2078bb",
					mainmenu_link_sub_hover_color: "#2078bb",
					mainmenu_link_active_color: "#2078bb", */
					
					menu_background_color: "333333",
					menu_font_color: "ffffff",
					menu_link_color: "ffffff",
					menu_hover_color: "00bff3",
					menu_active_color: "00bff3" ,
					
					footer_static_bg_color: "121212",
					footer_static_text_color: "cccccc",
					footer_static_link_color: "ffffff",
					footer_static_link_hover_color: "00bff3",
					footer_static_link_active_color: "00bff3"
				},
				
			rose : {
					bg_color: "181818",
					pattern_body_select: "none",
					pattern_header_select: "none",
					pattern_top_select: "none",
					pattern_footer_select: "none",
					
					text_color: "cccccc",
					link_color: "ffffff",
					link_hover_color: "ff65b0",
					link_active_color: "ff65b0",
					title_font_color:"ff65b0", 	
					
					header_bg_color: "333333",
					header_text_color: "ffffff",
					header_link_color: "ffffff",
					header_link_hover_color: "ff65b0",
					header_link_active_color: "ff65b0",
																			
					/*icons_bg_color: "#CFCFCF",
					icons_bg_hover_color: "#ff65b0",
					
					buttons_bg_color: "#CFCFCF",
					buttons_bg_hover_color: "#ff65b0",
					
					addtocart_bg_color: "#ff65b0",
					addtocart_bg_hover_color: "#CFCFCF",
					
					mainmenu_bg_color: "#ffffff",
					mainmenu_dropdown_bg_color: "#FFFFFF",
					mainmenu_bg_hover_color: "#ffffff",
					mainmenu_bg_active_color: "#ffffff",
					mainmenu_text_color: "#9e9e9e",
					mainmenu_link_color: "#9e9e9e",							
					mainmenu_link_hover_color: "#ff65b0",
					mainmenu_link_sub_hover_color: "#ff65b0",
					mainmenu_link_active_color: "#ff65b0",*/
					
					menu_background_color: "333333",
					menu_font_color: "ffffff",
					menu_link_color: "ffffff",
					menu_hover_color: "ff65b0",
					menu_active_color: "ff65b0" ,
					
					footer_static_bg_color: "121212",
					footer_static_text_color: "cccccc",
					footer_static_link_color: "ffffff",
					footer_static_link_hover_color: "ff65b0",
					footer_static_link_active_color: "ff65b0"
				},
					green : {
					bg_color: "181818",
					pattern_body_select: "none",
					pattern_header_select: "none",
					pattern_top_select: "none",
					pattern_footer_select: "none",
					
					text_color: "cccccc",
					link_color: "ffffff",
					link_hover_color: "00ffde",
					link_active_color: "00ffde",
					title_font_color:"00ffde", 	
					
					header_bg_color: "333333",
					header_text_color: "ffffff",
					header_link_color: "ffffff",
					header_link_hover_color: "00ffde",
					header_link_active_color: "00ffde",
																			
					/*icons_bg_color: "#CFCFCF",
					icons_bg_hover_color: "#00C8BD",
					
					buttons_bg_color: "#CFCFCF",
					buttons_bg_hover_color: "#00C8BD",
					
					addtocart_bg_color: "#00C8BD",
					addtocart_bg_hover_color: "#CFCFCF",
					
					mainmenu_bg_color: "#ffffff",
					mainmenu_dropdown_bg_color: "#FFFFFF",
					mainmenu_bg_hover_color: "#ffffff",
					mainmenu_bg_active_color: "#ffffff",
					mainmenu_text_color: "#9e9e9e",
					mainmenu_link_color: "#9e9e9e",							
					mainmenu_link_hover_color: "#00C8BD",
					mainmenu_link_sub_hover_color: "#00C8BD",
					mainmenu_link_active_color: "#00C8BD",*/
					
					menu_background_color: "333333",
					menu_font_color: "ffffff",
					menu_link_color: "ffffff",
					menu_hover_color: "ffffff",
					menu_active_color: "00ffde" ,
					
					footer_static_bg_color: "121212",
					footer_static_text_color: "cccccc",
					footer_static_link_color: "ffffff",
					footer_static_link_hover_color: "00ffde",
					footer_static_link_active_color: "00ffde"
				}	
				
				
				
		}

$('.scolors a').click(function(e) {
	var skinc=$(this).attr('class');
	$('.scolors li').removeClass('active');
	$(this).parent().addClass('active');
	$('.giveme_bse_skin').val(skinc);
	var urlbody = 'templates/base/images/pattern_body/';
    var urlheader = 'templates/base/images/pattern_header/';
    var urltop = 'templates/base/images/pattern_top/';
    var urlfooter = 'templates/base/images/pattern_footer/';

//background
		$('#select_image_header').attr("data", styles[skinc]['pattern_header_select']);
		$('.himage').val(styles[skinc]['pattern_header_select']);	
		$('#select_image_body').attr("data", styles[skinc]['pattern_body_select']);
		$('.bimage').val(styles[skinc]['pattern_body_select']);
		
		 if(styles[skinc]['pattern_body_select']=='none'){
                $('#select_image_body').html('none');
            }else{
                $('#select_image_body').html('<img src="'+urlbody+''+styles[skinc]['pattern_body_select']+'"/>');
            }
            if(styles[skinc]['pattern_header_select']=='none'){
                $('#select_image_header').html('none');
            }else{
                $('#select_image_header').html('<img src="'+urlheader+''+styles[skinc]['pattern_header_select']+'"/>');
            }
            if(styles[skinc]['pattern_top_select']=='none'){
                $('#select_image_top').html('none');
            }else{
                $('#select_image_top').html('<img src="'+urltop+''+styles[skinc]['pattern_top_select']+'"/>');
            }
            if(styles[skinc]['pattern_footer_select']=='none'){
                $('#select_image_footer').html('none');
            }else{
                $('#select_image_footer').html('<img src="'+urlfooter+''+styles[skinc]['pattern_footer_select']+'"/>');
            }
	
		
		
		
	
    //body	
	$('.body_background_color input.color').val(styles[skinc]['bg_color']);
	$('.body_background_color input.color').attr('style','background:#'+styles[skinc]['bg_color']);	
	$('.body_font_color input.color').val(styles[skinc]['text_color']);
	$('.body_font_color input.color').attr('style','background:#'+styles[skinc]['text_color']+';color:#FFFFFF;');
	$('.body_active_color input.color').val(styles[skinc]['link_active_color']);
	$('.body_active_color input.color').attr('style','background:#'+styles[skinc]['link_active_color']+';color:#FFFFFF;');
	$('.body_hover_color input.color').val(styles[skinc]['link_hover_color']);
	$('.body_hover_color input.color').attr('style','background:#'+styles[skinc]['link_hover_color']+';color:#FFFFFF;');
	$('.body_link_color input.color').val(styles[skinc]['link_color']);
	$('.body_link_color input.color').attr('style','background:#'+styles[skinc]['link_color']+';color:#FFFFFF;');	
	$('.title_font_color input.color').val(styles[skinc]['title_font_color']);
	$('.title_font_color input.color').attr('style','background:#'+styles[skinc]['title_font_color']+';color:#FFFFFF;');

           
	
	//header
	
	$('.header_background_color input.color').val(styles[skinc]['header_bg_color']);
	$('.header_background_color input.color').attr('style','background:#'+styles[skinc]['header_bg_color']);	
	$('.header_font_color input.color').val(styles[skinc]['header_text_color']);
	$('.header_font_color input.color').attr('style','background:#'+styles[skinc]['header_text_color']+';color:#FFFFFF;');
	$('.header_active_color input.color').val(styles[skinc]['header_link_active_color']);
	$('.header_active_color input.color').attr('style','background:#'+styles[skinc]['header_link_active_color']+';color:#FFFFFF;');
	$('.header_hover_color input.color').val(styles[skinc]['footer_static_link_hover_color']);
	$('.header_hover_color input.color').attr('style','background:#'+styles[skinc]['footer_static_link_hover_color']+';color:#FFFFFF;');
	$('.header_link_color input.color').val(styles[skinc]['header_link_color']);
	$('.header_link_color input.color').attr('style','background:#'+styles[skinc]['header_link_color']+';color:#FFFFFF;');

     
	 //footer
	 
	$('.footer_background_color input.color').val(styles[skinc]['footer_static_bg_color']);
	$('.footer_background_color input.color').attr('style','background:#'+styles[skinc]['footer_static_bg_color']);	
	$('.footer_font_color input.color').val(styles[skinc]['footer_static_text_color']);
	$('.footer_font_color input.color').attr('style','background:#'+styles[skinc]['footer_static_text_color']+';color:#FFFFFF;');
	$('.footer_active_color input.color').val(styles[skinc]['footer_static_link_active_color']);
	$('.footer_active_color input.color').attr('style','background:#'+styles[skinc]['footer_static_link_active_color']+';color:#FFFFFF;');
	$('.footer_hover_color input.color').val(styles[skinc]['header_link_hover_color']);
	$('.footer_hover_color input.color').attr('style','background:#'+styles[skinc]['header_link_hover_color']+';color:#FFFFFF;');
	$('.footer_link_color input.color').val(styles[skinc]['footer_static_link_color']);
	$('.footer_link_color input.color').attr('style','background:#'+styles[skinc]['footer_static_link_color']+';color:#FFFFFF;');
	
		 //menu
	 
	$('.menu_background_color input.color').val(styles[skinc]['menu_background_color']);
	$('.menu_background_color input.color').attr('style','background:#'+styles[skinc]['menu_background_color']);	
	$('.menu_font_color input.color').val(styles[skinc]['menu_font_color']);
	$('.menu_font_color input.color').attr('style','background:#'+styles[skinc]['menu_font_color']+';color:#FFFFFF;');
	$('.menu_active_color input.color').val(styles[skinc]['menu_active_color']);
	$('.menu_active_color input.color').attr('style','background:#'+styles[skinc]['menu_active_color']+';color:#FFFFFF;');
	$('.menu_hover_color input.color').val(styles[skinc]['menu_hover_color']);
	$('.menu_hover_color input.color').attr('style','background:#'+styles[skinc]['menu_hover_color']+';color:#FFFFFF;');
	$('.menu_background_color input.color').val(styles[skinc]['menu_background_color']);
	$('.menu_link_color input.color').val(styles[skinc]['menu_link_color']);
	$('.menu_link_color input.color').attr('style','background:#'+styles[skinc]['menu_link_color']+';color:#FFFFFF;');
	$('.menu_background_color input.color').attr('style','background:#'+styles[skinc]['menu_background_color']+';color:#FFFFFF;');
	
	
	e.preventDefault();
});


	
});