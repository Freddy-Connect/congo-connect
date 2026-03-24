/*Owl Carousel*/
$(".client-slider ").owlCarousel({ 
	navigation : false, // Show next and prev buttons
	slideSpeed : 300,
	paginationSpeed : 1000,
	autoPlay: true,
	pagination : false,items : 4,
	transitionStyle : "fade"
});
$(".team-slider ").owlCarousel({ 
	navigation : false, // Show next and prev buttons
	slideSpeed : 300,
	paginationSpeed : 1000,
	autoPlay: true,
	pagination : false,
	items : 3,
	itemsDesktop: [1199, 3],
	itemsDesktopSmall: [979, 3],
	itemsTablet: [768, 2],
	itemsMobile: [479, 1],
	transitionStyle : "fade"
});
$(document).ready(function () {
	function random(owlSelector) {
		owlSelector.children().sort(function () {
			return Math.round(Math.random()) - 0.5;
		}).each(function () {
			$(this).appendTo(owlSelector);
		});
	}
	$(".header-slider, .intro-slider, .testimonial-slider").owlCarousel({
		autoPlay: 7200,
		slideSpeed: 1000,
		items: 1,
		itemsDesktop: [1199, 1],
		itemsDesktopSmall: [979, 1],
		itemsTablet: [768, 1],
		itemsMobile: [479, 1],
		beforeInit: function (elem) {
			random(elem);
		}
	});
});

/*Animation*/			
jQuery(document).ready(function () {
	jQuery('.intro-down').appear(function() {
		jQuery('.intro-down').addClass('animated bounceInDown');  
	});
	jQuery('.intro-pulse').appear(function() {
		jQuery('.intro-pulse').addClass('animated pulse');  
	});
	jQuery('.contact-up').appear(function() {
		jQuery('.contact-up').addClass('animated bounceInUp');
	});	
	jQuery('.span-wobble').appear(function() {
		jQuery('.span-wobble').addClass('animated wobble');
	});	
	jQuery('.services-block-left').appear(function() {
		jQuery('.services-block-left').addClass('animated bounceInLeft');
	});
	jQuery('.services-block-right').appear(function() {
		jQuery('.services-block-right').addClass('animated bounceInRight');
	});
	jQuery('.about-left').appear(function() {
		jQuery('.about-left').addClass('animated bounceInLeft');
	});
	jQuery('.about-right').appear(function() {
		jQuery('.about-right').addClass('animated bounceInRight');
	});
	jQuery('.about-up').appear(function() {
		jQuery('.about-up').addClass('animated bounceInUp');
	});
	jQuery('h3').appear(function() {
		jQuery('h3').addClass('animated pulse');
	});
	jQuery('.team-down').appear(function() {
		jQuery('.team-down').addClass('animated fadeInDown');
	});			
	jQuery('.prices-down').appear(function() {
		jQuery('.prices-down').addClass('animated fadeInDown');
	});
	jQuery('.testimonial-down').appear(function() {
		jQuery('.testimonial-down').addClass('animated fadeInDown');
	});
	jQuery('.services-down').appear(function() {
		jQuery('.services-down').addClass('animated fadeInDown');
	});
	jQuery('.contact-down').appear(function() {
		jQuery('.contact-down').addClass('animated fadeInDown');
	});
	jQuery('.portfolio-down').appear(function() {
		jQuery('.portfolio-down').addClass('animated fadeInDown');
	});
	jQuery('.client-flip').appear(function() {
		jQuery('.client-flip').addClass('animated flipInY');
	});
	jQuery('.prices-flipy').appear(function() {
		jQuery('.prices-flipy').addClass('animated flipInY');
	});			
	jQuery('.team-container').appear(function() {
		jQuery('.team-container').addClass('animated flipInY');
	});
	jQuery('.facts-block').appear(function() {
		jQuery('.facts-block').addClass('animated flipInY');
	});
	jQuery('.flipx').appear(function() {
		jQuery('.flipx').addClass('animated flipInX');
	});			
	jQuery('.testimonial-block').appear(function() {
		jQuery('.testimonial-block').addClass('animated bounceIn');
	});
	jQuery('.social').appear(function() {
		jQuery('.social').addClass('animated fadeIn');
	});			
	jQuery('.fa-cog').appear(function() {
		jQuery('.fa-cog').addClass('animated spin');
	});
	$('#portfolio-2').mixItUp();				
});

/*Form Validation*/
$(document).ready(function() {
	$('#portfolio-2').mixItUp(); 
	$('.portfolio-item.image a').vanillabox();
	$('.portfolio-item.video a').vanillabox({type: 'iframe'});
});
$('#navbar a').click(function(){
	$('html, body').animate({
		scrollTop: $( $.attr(this, 'href') ).offset().top
	}, 500);
	return false;
});      