$(function() {

  $('.statusselect').switchy();

 
  $('.statusselect').on('change', function(){





    
   /* // Animate Switchy Bar background color
    var bgColor = '#ccb3dc';

    if ($(this).val() == 'Present'){
      bgColor = '#006400';
    } else if ($(this).val() == 'Absent'){
      bgColor = '#FF0000';
    }

    $('.switchy-bar').animate({
      backgroundColor: bgColor
    });*/

    // Display action in console
    var log =  'Selected value is "'+$(this).val()+'"';
    $('#console').html(log).hide().fadeIn();
  });
});