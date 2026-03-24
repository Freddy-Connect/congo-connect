/**
 * @category    Dolphin V7.2 - Abserve Custom JS
 * @template    Facebook
 * @author      Abservetech
 * @license     http://www.abservetech.com/privacy-policy/
 * @copyright   Copyright (c) 2015 abservetech
 * @author url  http://www.abservetech.com
 * @skype       balakannav
 */

/**A****B****S****E****R****V****E**
*      @Animation and Custom       *
************************************/

$(document).ready(function() {
	
	
 /*  freddy ajout Google plus template*/
  $('.abserve-toggle,.menu_logo').click(function () {
  $('.abserve_menu,.menu_logo').toggleClass('abs_toggle');
  $('.abs_main').toggleClass('abs_left');
  });
   /* fin  freddy ajout Google plus template*/
	
 $('#extra_top_menu').parents('body').addClass('user_login');
 $('body').addClass('not_login');
 $('#extra_top_menu').parents('body').removeClass('not_login');
 $('.not_login #abserve_slider').remove();
 $('.home_page').parents('body').addClass('home_page');


 /*  freddy ajout Google plus template*/
  $('.abs_more').parent().addClass('icon_more');
 $('.icon_more span').append('<i class="app"></i>');
 $('.abs_more').insertBefore('.icon_more a span');
 /* fin  freddy ajout Google plus template*/
 
  /* Freddy copy de  template Lovley dating*/
 $('.input_wrapper_text,.input_wrapper_password').addClass('hvr-underline-from-left');
 $('.input_wrapper input.form_input_text,.input_wrapper input.form_input_password').addClass('input__field input__field--nao');
 $('.input_wrapper_text,.input_wrapper_password').append('<svg class="graphic graphic--nao" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path></svg>');
  /* Fin  Freddy copy de  template Lovley dating*/
 
 
 
 /* FREDDY RESPONSIVE SPLASH */
  $('#responsive_splash').parents('body').addClass('user_login');
 $('body').addClass('not_login');
 $('#responsive_splash').parents('body').removeClass('not_login');
 
 /*[END] FREDDY FIN RESPONSIVE SPLASH     */
 
 
 
$('.sys_page_header_submenu').on('click', function() {
    $('.sys_ph_submenu_submenu').addClass('animated bounceIn');
});
if ($("i").hasClass("abserve")) {
  $("#abserve_menu li a span").css('padding','0 10px');
}
  $('.abserve_toggle_menu').click(function(){
    $('.sys_main_menu').toggleClass('abs_fbmenu');
  });
});

/**A****B****S****E****R****V****E**
*          @Abserve Slider         *
************************************/

$( document ).ready(function( $ ) {
  $( '#abserve_slider' ).sliderPro({
    width: 1120,
    height: 500,
    fade: true,
    arrows: true,
    buttons: false,
    fullScreen: true,
    shuffle: true,
    smallSize: 500,
    mediumSize: 1000,
    largeSize: 3000,
    thumbnailArrows: true,
    autoplay: true
  });

  /**A****B****S****E****R****V****E**
  *       @Abserve Scroll to Top     *
  ************************************/
  $('#abserve_to_top').hide();
  $('#abserve_to_top').click(function () {
    $('body,html').animate( {
    scrollTop: 0
    }, 800);
    return false;
  });
  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
    $('#abserve_to_top').fadeIn().addClass('animated slideInUp');
    }
    else {
    $('#abserve_to_top').removeClass('slideInUp');
    $('#abserve_to_top').fadeOut().addClass('animated hinge');
    }
  });
});