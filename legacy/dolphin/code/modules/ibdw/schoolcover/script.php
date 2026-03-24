<script type="text/javascript">
var stopmn=0;
var status=0;


//convertion of mouse events to touch events
function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
    switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;
        case "touchmove":  type = "mousemove"; break;        
        case "touchend":   type = "mouseup";   break;
        default:           return;
    }

    // initMouseEvent(type, canBubble, cancelable, view, clickCount, 
    //                screenX, screenY, clientX, clientY, ctrlKey, 
    //                altKey, shiftKey, metaKey, button, relatedTarget);

    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1, 
                                  first.screenX, first.screenY, 
                                  first.clientX, first.clientY, false, 
                                  false, false, false, 0/*left*/, null);

    first.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}

function init() {
    document.getElementById('schoolcover_main').addEventListener("touchstart", touchHandler, true);
    document.getElementById('schoolcover_main').addEventListener("touchmove", touchHandler, true);
    document.getElementById('schoolcover_main').addEventListener("touchend", touchHandler, true);
    document.getElementById('schoolcover_main').addEventListener("touchcancel", touchHandler, true);
}

//simulate double tap event
(function($){

  $.event.special.doubletap = {
    bindType: 'touchend',
    delegateType: 'touchend',

    handle: function(event) {
      var handleObj   = event.handleObj,
          targetData  = jQuery.data(event.target),
          now         = new Date().getTime(),
          delta       = targetData.lastTouch ? now - targetData.lastTouch : 0,
          delay       = delay == null ? 300 : delay;

      if (delta < delay && delta > 30) {
        targetData.lastTouch = null;
        event.type = handleObj.origType;
        ['clientX', 'clientY', 'pageX', 'pageY'].forEach(function(property) {
          event[property] = event.originalEvent.changedTouches[0][property];
        })

        // let jQuery handle the triggering of "doubletap" event handlers
        handleObj.handler.apply(this, arguments);
      } else {
        targetData.lastTouch = now;
      }
    }
  };

})(jQuery);

//End conversion
function displaymenu()
{
 $("#covermainmenu").css('display','block');
}

function openmenu()
{
  if (status==0) {status=1;}
  else {status=0;}
  if (status==1) 
  {
   $("#mainb").addClass('btnselected');
   $("#submenus").css('display','block');
  }
  else 
  {
   $("#submenus").css('display','none');
   $("#mainb").removeClass('btnselected');
  }
}
function closemenu()
{
 $("#covermainmenu").css('display','none');
}

function moveimagey(owneris,hashis,xpos,ypos,SchoolID)
{
 init();
 stopmn=1;
 closemenu();
 $("#loading_div").css("display","none");  
 $("#avtarea").css("display","none");
 var $bg = $('#schoolcover_main'),
     origin = {x: 0, y: 0},
     start = {x: xpos, y: ypos},
     movecontinue = false,
		 owner=owneris,
		 currenthash=hashis;
	
 //get div container height and width	
 var divheight=$('#schoolcover_main').height();	
 var divwidth=$('#schoolcover_main').width();
 //get image height
 var imageis=$('#schoolcover_main').css('background-image').replace(/url\(|\)$/ig, "");
 imageurl=imageis.replace(/"/g,"");
 var image = new Image();
 image.src = imageurl;
 //get image height adapted for the div container
 var adaptedheight=parseInt(image.height*divwidth/image.width);
 //movimento massimo possibile in alto
 var maxupimage=adaptedheight-divheight;
 
 $bg.css('opacity', '0.7');
 $bg.css('cursor', 'move');
 $('#infopoint').css('display','block');
 
    
 function move (e)
 {
   var moveby = 
   {
    x: origin.x - e.clientX,
    y: origin.y - e.clientY
   };
	 if (movecontinue === true) 
   {
	  start.x = 0;
    start.y = start.y - moveby.y;
		//set x to 0 to block the oriz position
		if(start.y>0) {start.y=0;}
		if(start.y<-maxupimage) {start.y=-maxupimage;}
    $(this).css('background-position', 0 + 'px ' + start.y + 'px');
	 }
   origin.x = e.clientX;
   origin.y = e.clientY;
   e.stopPropagation();
   return false;
 }

 function handle (e)
 {
   movecontinue = false;
   $bg.unbind('mousemove', move);
   if (e.type == 'mousedown') 
   {
    origin.x = e.clientX;
    origin.y = e.clientY;
    movecontinue = true;
    $bg.bind('mousemove', move);
   } 
   else 
   {
    $(document.body).focus();
   }
   e.stopPropagation();
   return false;
 }

 function reset ()
 {
   start = {x: 0, y: 0};
   $(this).css('backgroundPosition', '0 0');
 }
 //moving end on double click event
 function finish (e)
 {
   stopmn=0;
   var w=document.getElementById('pfblockconteiner').offsetWidth;
   $bg.unbind('mousedown mouseup mouseleave', handle);
	 $bg.css('opacity', '1.0');
	 $bg.css('cursor', 'default');
	 $('#infopoint').css('display','none');
	 $('#suggestions').css('display','none');
	 jQuery.ajax({
        type: "POST",
        url: "modules/ibdw/schoolcover/updateposition.php",
        data: 'owner='+owner+'&currenthash='+currenthash+'&PositionY='+start.y+'&PositionX=0&boxwidth='+w+'&SchoolID='+SchoolID,
        cache: false
   });
	 $("#posX").val(0);
	 $("#posY").val(start.y);
   $("#avtarea").css("display","block");
 }
 
  
 $bg.bind('mousedown mouseup mouseleave', handle);
 $bg.bind('dblclick', finish);
 $bg.bind('doubletap', finish);
}

function openexplainations()
{
 $('#infopoint').css('display','none');
 $('#suggestions').css('display','block');
}
function closetext()
{
 $('#infopoint').css('display','block');
 $('#suggestions').css('display','none');
}
function ibdw_cover_remove(owner,currenthash,baseurlis,id)
{
 jQuery.ajax({
        type: "POST",
        url: "modules/ibdw/schoolcover/removeimage.php",
        data: 'owner='+owner+'&currenthash='+currenthash+'&id='+id,
        cache: false
       });
 $('#schoolcover_main').css('background','url("'+baseurlis+'modules/ibdw/schoolcover/templates/base/images/default.jpg") no-repeat scroll transparent');
 $('#removemenu').css('display','none');
 $('#movemenu').css('display','none');
}

function update_schoolcore_main(SchoolID){
  $.ajax({
      type: "POST",
      data: "ajax=1"+"&SchoolID="+SchoolID,
      url: "modules/ibdw/schoolcover/core.php",
      success: function(data) {
        $("#pfblockconteiner").html(data);
      }
  });
}

function openchange_album() 
{
 $("#alignator").fadeIn();
}

function closechange_album()
{
 $("#alignator").css("display","none");
}

function change_album(hashe,id)
{
 $.ajax({
      type: "POST",
      data: "hashe=" + hashe+"&id="+id,
      url: "modules/ibdw/schoolcover/change_album.php",
      success: function(data) {
        update_schoolcore_main(id);
      }
 });
}
function closeuploader()
{
  $("#modificaalbums").css("display","none");
  $("#modificaalbums").html("");
}

function ibdw_cover_frompc(id_album_predef,user,SchoolID)
 {
  $("#loading_div").css("display","block");
  $.ajax({
      type: "POST",
      data: "id_album_predef=" + id_album_predef + "&user=" + user + "&SchoolID="+ SchoolID,
      url: "modules/ibdw/schoolcover/fragment.php",
      success: function(data) 
      {
        $("#modificaalbums").html(data);
        $("#modificaalbums").fadeIn();
        $("#loading_div").css("display","none");
      }
     });
}
</script>