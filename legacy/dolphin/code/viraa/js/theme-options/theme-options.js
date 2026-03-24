
/*	Name: Theme Options Script
	Written by: Balakanna - (http://www.abservetech.com)
	Email: balakanna@abservetech.com
	Version: 1.0
*/
 var $;

//=================================== Sticky Header Options ====================================//
 (function($) {
    var defaults = {
            topSpacing: 0,
            bottomSpacing: 0,
            className: 'is-sticky',
            wrapperClassName: 'sticky-wrapper'
        },
        $window = $(window),
        $document = $(document),
        sticked = [],
        windowHeight = $window.height(),
        scroller = function() {
            var scrollTop = $window.scrollTop(),
                documentHeight = $document.height(),
                dwh = documentHeight - windowHeight,
                extra = (scrollTop > dwh) ? dwh - scrollTop : 0;
            for (var i = 0; i < sticked.length; i++) {
                var s = sticked[i],
                    elementTop = s.stickyWrapper.offset().top,
                    etse = elementTop - s.topSpacing - extra;
                if (scrollTop <= etse) {
                    if (s.currentTop !== null) {
                        s.stickyElement
                            .css('position', '')
                            .css('top', '')
                            .removeClass(s.className);
                        s.stickyElement.parent().removeClass(s.className);
                        s.currentTop = null;
                    }
                }
                else {
                    var newTop = documentHeight - s.stickyElement.outerHeight()
                        - s.topSpacing - s.bottomSpacing - scrollTop - extra;
                    if (newTop < 0) {
                        newTop = newTop + s.topSpacing;
                    } else {
                        newTop = s.topSpacing;
                    }
                    if (s.currentTop != newTop) {
                        s.stickyElement
                            .css('position', 'fixed')
                            .css('top', newTop)
                            .addClass(s.className);
                        s.stickyElement.parent().addClass(s.className);
                        s.currentTop = newTop;
                    }
                }
            }
        },
        resizer = function() {
            windowHeight = $window.height();
        },
        methods = {
            init: function(options) {
                var o = $.extend(defaults, options);
                return this.each(function() {
                    var stickyElement = $(this);

                    stickyId = stickyElement.attr('id');
                    wrapper = $('<div></div>')
                        .attr('id', stickyId + '-sticky-wrapper')
                        .addClass(o.wrapperClassName);
                    stickyElement.wrapAll(wrapper);
                    var stickyWrapper = stickyElement.parent();
                    stickyWrapper.css('height', stickyElement.outerHeight());
                    sticked.push({
                        topSpacing: o.topSpacing,
                        bottomSpacing: o.bottomSpacing,
                        stickyElement: stickyElement,
                        currentTop: null,
                        stickyWrapper: stickyWrapper,
                        className: o.className
                    });
                });
            },
            update: scroller
        };

    // should be more efficient than using $window.scroll(scroller) and $window.resize(resizer):
    if (window.addEventListener) {
        window.addEventListener('scroll', scroller, false);
        window.addEventListener('resize', resizer, false);
    } else if (window.attachEvent) {
        window.attachEvent('onscroll', scroller);
        window.attachEvent('onresize', resizer);
    }

    $.fn.sticky = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.sticky');
        }
    };
    $(function() {
        setTimeout(scroller, 0);
    });
})(jQuery);

$(document).ready(function($) {
$("header").sticky({topSpacing:0});

	//=================================== Theme Options ====================================//

	$('.wide').click(function() {
		$('.boxed').removeClass('active');
		$('.boxed-margin').removeClass('active');
		$(this).addClass('active');
		$('.patterns,.boxed_image').css('display' , 'none');
		$('#layout').removeClass('layout-boxed').removeClass('layout-boxed-margin').addClass('layout-wide');
	});
	$('.boxed').click(function() {
		$('.wide').removeClass('active');
		$('.boxed-margin').removeClass('active');
		$(this).addClass('active');
		$('.patterns,.boxed_image').css('display' , 'block');
		$('#layout').removeClass('layout-boxed-margin').removeClass('layout-wide').addClass('layout-boxed');
	});
	$('.boxed-margin').click(function() {
		$('.boxed').removeClass('active');
		$('.wide').removeClass('active');
		$(this).addClass('active');
		$('.patterns,.boxed_image').css('display' , 'block');
		$('#layout').removeClass('layout-wide').removeClass('layout-boxed').addClass('layout-boxed-margin');
	});

	//=================================== Skins Changer ====================================//

	google.setOnLoadCallback(function(){

		'use strict';

    // Color changer
    $(".red").click(function(){
    $(".skin").attr("href", "css/skins/red/red.css");
        return false;
   });
    
   $(".blue").click(function(){
        $(".skin").attr("href", "css/skins/blue/blue.css");
        return false;
	});
    
	$(".violet").click(function(){
         $(".skin").attr("href", "css/skins/violet/violet.css");
         return false;
  });

	$(".green").click(function(){
        $(".skin").attr("href", "css/skins/green/green.css");
        return false;
  });

  $(".orange").click(function(){
        $(".skin").attr("href", "css/skins/orange/orange.css");
        return false;
  });

  $(".lavender").click(function(){
       $(".skin").attr("href", "css/skins/lavender/lavender.css");
       return false;
  });

 $(".pink").click(function(){
       $(".skin").attr("href", "css/skins/pink/pink.css");
        return false;
 });

	$(".brown").click(function(){
        $(".skin").attr("href", "css/skins/brown/brown.css");
        /*$(".logo_img").attr("src", "css/skins/cocoa/logo.png");*/
        return false;
   });
 });

	//=================================== Background Options ====================================//
	
	$('#theme-options ul.backgrounds li').click(function(){
	var 	$bgSrc = $(this).css('background-image');
		if ($(this).attr('class') == 'bgnone')
			$bgSrc = "none";

		$('body').css('background-image',$bgSrc);
		$.cookie('background', $bgSrc);
		$.cookie('backgroundclass', $(this).attr('class').replace(' active',''));
		$(this).addClass('active').siblings().removeClass('active');
	});

	//=================================== Header Options ====================================//
	
	$('#theme-options .head li.fixed').click(function(){
		$('#theme-options .head li').removeClass('active');
		$(this).addClass('active');
		$('header').removeClass('is-sticky_no');
	});

	$('#theme-options .head li.no-fixed').click(function(){
		$('#theme-options .head li').removeClass('active');
		$(this).addClass('active');
		$('header').addClass('is-sticky_no');
	});

	//=================================== Panel Options ====================================//

	$('#theme-options .title').click(function(){
		if ($('#theme-options').css('left') == "-222px")
		{
			$left = "0px";
			$.cookie('displayoptions', "0");
		} else {
			$left = "-222px";
			$.cookie('displayoptions', "1");
		}
		$('#theme-options').animate({
			left: $left
		},{
			duration: 500,
			easing: "easeInOutExpo"
		});

	});

	$(function(){
		$('#theme-options').fadeIn();
		$bgSrc = $.cookie('background');
		$('body').css('background-image',$bgSrc);

		if ($.cookie('displayoptions') == "1")
		{
			$('#theme-options').css('left','-222px');
		} else if ($.cookie('displayoptions') == "0") {
			$('#theme-options').css('left','0');
		} else {
			$('#theme-options').delay(800).animate({
				left: "-222px"
			},{
				duration: 500,
				easing: "easeInOutExpo"
			});
			$.cookie('displayoptions', "1");
		}
		$('#theme-options ul.backgrounds').find('li.' + $.cookie('backgroundclass')).addClass('active');

	});

});
