// JavaScript Document

/* ---------------------- */
/* Form Validation
/* ---------------------- */

if ($().validate) {
    $("#comment-form").validate();
}

var contactForm = $("#contact-form");
if (contactForm && contactForm.length > 0) {
        var contactNotificationTimeout = 7000; //7 seconds
        var contactFormSubmit = contactForm.find("#submit");

        contactFormSubmit.bind("click", function (evt) {

            if (contactForm.valid()) {
                contactFormSubmit.attr('disabled', 'disabled');
                jQuery.ajax({
                    type: "POST",
                    url: "contact-submit.php",
                    data: getFormData(),
                    statusCode: {
                        200: function () {
                            var successBoxElement = $('#contact-notification-box-success');
                            successBoxElement.css('display', '');
                            contactFormSubmit.removeAttr('disabled', '');
                            resetFormData();
                            if (contactNotificationTimeout > 0) {
                                var timer = window.setTimeout(function () {
                                    window.clearTimeout(timer);
                                    successBoxElement.fadeOut("slow");
                                }, contactNotificationTimeout);
                            }
                        },
                        500: function (jqXHR, textStatus, errorThrown) {
                            var errorBoxElement = $('#contact-notification-box-error');
                            var errorMsgElement = $('#contact-notification-box-error-msg');
                            var errorMessage = jqXHR.responseText;
                            if (!errorMessage || errorMessage.length == 0) {
                                errorMessage = errorMsgElement.data('default-msg');
                            }
                            errorMsgElement.text(errorMessage);
                            errorBoxElement.css('display', '');
                            contactFormSubmit.removeAttr('disabled');
                            if (contactNotificationTimeout > 0) {
                                var timer = window.setTimeout(function () {
                                    window.clearTimeout(timer);
                                    errorBoxElement.fadeOut("slow");
                                }, contactNotificationTimeout);
                            }
                        }
                    }
                });
}

function getFormData() {
    var data = 'timestamp=' + evt.timeStamp;
    contactForm.find(":input").each(function () {
        var field = $(this);
        var add = true;
        if (field.is(':checkbox') && !field.is(':checked')) {
            add = false;
        }
        if (add) {
            var fieldName = field.attr('name');
            var fieldValue = $.trim(field.val());
            if (fieldValue.length > 0) {
                data += '&' + fieldName + '=' + fieldValue;
            }
        }
    });
    return data;
}

function resetFormData() {
    contactForm.find(":input").each(function () {
        var field = $(this);
        var tagName = field.prop("nodeName").toLowerCase();
        if (tagName == 'select') {
            field.prop('selectedIndex', 0);
        } else {
            if (field.is(':checkbox')) {
                field.attr("checked", field.prop("defaultChecked"));
            } else {
                var defaultValue = field.prop("defaultValue");
                if (defaultValue) {
                    field.val(defaultValue);
                } else {
                    field.val('');
                }
            }
        }
    });
}
return false;
});
}

/*Animation*/
$(document).ready(function() {
    $('#portfolio-2').mixItUp(); 

    $('.portfolio-item.image a').vanillabox();
    $('.portfolio-item.video a').vanillabox({type: 'iframe'});

    $('.text-image-top').appear(function() {
        $('.text-image-top').addClass('animated fadeInUp');
    });
    $('.text-image-left').appear(function() {
        $('.text-image-left').addClass('animated fadeInUp');
    });
    $('.team-member').appear(function() {
        $('.team-member').addClass('animated fadeInUp');
    });
    $('.client-logo').appear(function() {
        $('.client-logo').addClass('animated fadeInUp');
    });
    $('.number-image-left').appear(function() {
        $('.number-image-left').addClass('animated fadeInUp');
    });
    $('.map-pointer').appear(function() {
        $('.map-pointer').addClass('animated fadeInUp');
    });
    $('#quote blockquote').appear(function() {
        $('#quote blockquote').addClass('animated fadeInUp');
    });

    $('.mob-nav-toggle').click(function() {
        $('.mob-nav-wrapper').toggle();
    });

    /*Owl Carousel*/
  /*
    $(".text-slider").owlCarousel({
                    navigation : false,
                    slideSpeed : 800,
                    paginationSpeed : 800,
                    singleItem: true,
                    autoPlay: 4500,
                    transitionStyle : "fade"
                });
				*/
				
		$(".text-slider").owlCarousel({
  navigation: false,
  singleItem: true,
  autoPlay: 7500,        // ⏱️ 4,5 secondes par message
  slideSpeed: 800,       // animation douce
  paginationSpeed: 800,
  transitionStyle: "fade",
  stopOnHover: true      // optionnel mais pro
});
		
				
				
    $(".client-slider").owlCarousel({
                    navigation : false, // Show next and prev buttons
                    slideSpeed : 300,
                    paginationSpeed : 1000,
                    autoPlay: true,
                    pagination : false,items : 4,
                    transitionStyle : "fade"
					
					
                });
    $(".testimonial-slider").owlCarousel({
                    navigation : false, // Show next and prev buttons
                    slideSpeed : 300,
                    paginationSpeed : 1000,
                    autoPlay: true,
                    pagination : false,
                    singleItem: true,
                    transitionStyle : "fade"
                });
    $(".team-slider").owlCarousel({
                    navigation : false, // Show next and prev buttons
                    slideSpeed : 300,
                    paginationSpeed : 1000,
                    autoPlay: true,
                    pagination : false,
                    items : 4,
                    transitionStyle : "fade"
                });

    /*Form Validation*/
    $(document).on('keyup','.form-control', function (e) {
        if ($('.form-control').hasClass('error')) {
            $(this).parent().removeClass('valid');
            $(this).parent().addClass('error');
        }
    });
    $(document).on('keyup','.valid', function (e) {
        if ($('.form-control').hasClass('valid')) {
            $(this).parent().removeClass('error');
            $(this).parent().addClass('valid');
        }
    });
});

/*Smooth Scrolling*/
$('#nav a, .nav-menu a, .arrow-up a, .arrow-down a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 800);
    return false;
});