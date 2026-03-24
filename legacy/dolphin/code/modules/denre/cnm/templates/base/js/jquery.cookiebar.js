/*
 * Copyright (C) 2012 PrimeBox (info@primebox.co.uk)
 * 
 * This work is licensed under the Creative Commons
 * Attribution 3.0 Unported License. To view a copy
 * of this license, visit
 * http://creativecommons.org/licenses/by/3.0/.
 * 
 * Documentation available at:
 * http://www.primebox.co.uk/projects/cookie-bar/
 * 
 * When using this software you use it at your own risk. We hold
 * no responsibility for any damage caused by using this plugin
 * or the documentation provided.
 */
(function($){
    $.cookieBar = function(options,val){
        if(options=='cookies'){
            var doReturn = 'cookies';
        }else if(options=='set'){
            var doReturn = 'set';
        }else{
            var doReturn = false;
        }
        var defaults = {
            message: 'We use cookies to track usage and preferences.',
            acceptButton: true,
            acceptText: 'I Understand',
            acceptFunction: function(cookieValue){if(cookieValue!='enabled' && cookieValue!='accepted') window.location = window.location.href;},
            declineButton: false,
            declineText: 'Disable Cookies',
            declineFunction: function(cookieValue){if(cookieValue=='enabled' || cookieValue=='accepted') window.location = window.location.href;},
            policyButton: false,
            policyText: 'Privacy Policy',
            policyURL: '/privacy-policy/',
            autoEnable: true,
            acceptOnContinue: false,
            acceptOnScroll: false,
            acceptAnyClick: false,
            expireDays: 365,
            renewOnVisit: false,
            forceShow: false,
            effect: 'slide',
            element: 'body',
            append: false,
            fixed: false,
            bottom: false,
            zindex: '',
            domain: String(window.location.hostname),
            referrer: String(document.referrer)
        };
        var options = $.extend(defaults,options);

        //Sets expiration date for cookie
        var expireDate = new Date();
        expireDate.setTime(expireDate.getTime()+(options.expireDays*86400000));
        expireDate = expireDate.toGMTString();
        
        var cookieEntry = 'cb-enabled={value}; expires='+expireDate+'; path=/';
        
        //Retrieves current cookie preference
        var i,cookieValue='',aCookie,aCookies=document.cookie.split('; ');
        for (i=0;i<aCookies.length;i++){
            aCookie = aCookies[i].split('=');
            if(aCookie[0]=='cb-enabled'){
                cookieValue = aCookie[1];
            }
        }
        //Sets up default cookie preference if not already set
        if(cookieValue=='' && doReturn!='cookies' && options.autoEnable){
            cookieValue = 'enabled';
            document.cookie = cookieEntry.replace('{value}','enabled');
        }else if((cookieValue=='accepted' || cookieValue=='declined') && doReturn!='cookies' && options.renewOnVisit){
            document.cookie = cookieEntry.replace('{value}',cookieValue);
        }
        if(options.acceptOnContinue){
            if(options.referrer.indexOf(options.domain)>=0 && String(window.location.href).indexOf(options.policyURL)==-1 && doReturn!='cookies' && doReturn!='set' && cookieValue!='accepted' && cookieValue!='declined'){
                doReturn = 'set';
                val = 'accepted';
            }
        }
        if(doReturn=='cookies'){
            //Returns true if cookies are enabled, false otherwise
            if(cookieValue=='enabled' || cookieValue=='accepted'){
                return true;
            }else{
                return false;
            }
        }else if(doReturn=='set' && (val=='accepted' || val=='declined')){
            //Sets value of cookie to 'accepted' or 'declined'
            document.cookie = cookieEntry.replace('{value}',val);
            if(val=='accepted'){
                return true;
            }else{
                return false;
            }
        }else{
            //Sets up enable/accept button if required
            var message = options.message.replace('{policy_url}',options.policyURL);
            
            if(options.acceptButton){
                var acceptButton = '<a href="" class="cb-enable">'+options.acceptText+'</a>';
            }else{
                var acceptButton = '';
            }
            //Sets up disable/decline button if required
            if(options.declineButton){
                var declineButton = '<a href="" class="cb-disable">'+options.declineText+'</a>';
            }else{
                var declineButton = '';
            }
            //Sets up privacy policy button if required
            if(options.policyButton){
                var policyButton = '<a href="'+options.policyURL+'" class="cb-policy">'+options.policyText+'</a>';
            }else{
                var policyButton = '';
            }
            //Whether to add "fixed" class to cookie bar
            if(options.fixed){
                if(options.bottom){
                    var fixed = ' class="fixed bottom"';
                }else{
                    var fixed = ' class="fixed"';
                }
            }else{
                var fixed = '';
            }
            if(options.zindex!=''){
                var zindex = ' style="z-index:'+options.zindex+';"';
            }else{
                var zindex = '';
            }

            //Stop clicking on page
            if(options.disableClick && (cookieValue=='enabled' || cookieValue=='')) {
                var div = document.createElement("div");
                div.id = "cookie-wrap";

                // Move the body's children into this wrapper
                while (document.body.firstChild)
                {
                    div.appendChild(document.body.firstChild);
                }

                // Append the wrapper to the body
                document.body.appendChild(div);
            }
 
            //Displays the cookie bar if arguments met
            if(options.forceShow || cookieValue=='enabled' || cookieValue==''){
                if(options.append){
                    $(options.element).append('<div id="cookie-bar"'+fixed+zindex+'><div id="cookie-box"><div id="cookie-txt">'+message+'</div><div id="cookie-btns">'+acceptButton+declineButton+policyButton+'</div></div></div>');
                }else{
                    $(options.element).prepend('<div id="cookie-bar"'+fixed+zindex+'><div id="cookie-box"><div id="cookie-txt">'+message+'</div><div id="cookie-btns">'+acceptButton+declineButton+policyButton+'</div></div></div>');
                }

                //Denre - Template Evo fix
                if(!options.bottom){
                    var ch = $('#cookie-bar').outerHeight(true);
                    if(options.disableClick) {
                        $('#cookie-wrap').css({'top': ch, 'position': 'relative'});
                    } else {
                        $('.sys_root').css({top: ch});
                    }
                }
            }

            var removeBar = function(func){
                if(options.acceptOnScroll) $(document).off('scroll');
                if(typeof(func)==='function') func(cookieValue);
                if(options.effect=='slide'){
                    $('#cookie-bar').slideUp(300,function(){$('#cookie-bar').remove();});
                }else if(options.effect=='fade'){
                    $('#cookie-bar').fadeOut(300,function(){$('#cookie-bar').remove();});
                }else{
                    $('#cookie-bar').hide(0,function(){$('#cookie-bar').remove();});
                }

                $(document).unbind('click',anyClick);

                //Denre - Template Evo fix remove top
                $('.sys_root_bg').css({top: 0});
            };
            var cookieAccept = function(){

                document.cookie = cookieEntry.replace('{value}','accepted');

                removeBar(options.acceptFunction);
            };
            var cookieDecline = function(){
                var deleteDate = new Date();
                deleteDate.setTime(deleteDate.getTime()-(864000000));
                deleteDate = deleteDate.toGMTString();
                aCookies=document.cookie.split('; ');
                for (i=0;i<aCookies.length;i++){
                    aCookie = aCookies[i].split('=');
                    if(aCookie[0].indexOf('_')>=0){
                        document.cookie = aCookie[0]+'=0; expires='+deleteDate+'; domain='+options.domain.replace('www','')+'; path=/';
                    }else{
                        document.cookie = aCookie[0]+'=0; expires='+deleteDate+'; path=/';
                    }
                }
                document.cookie = cookieEntry.replace('{value}','declined');
                removeBar(options.declineFunction);
            };
            var anyClick = function(e){
                if(!$(e.target).hasClass('cb-policy')) cookieAccept();
            };
            
            $('#cookie-bar .cb-enable').click(function(){cookieAccept();return false;});
            $('#cookie-bar .cb-disable').click(function(){cookieDecline();return false;});
            if(options.acceptOnScroll){
                var scrollStart = $(document).scrollTop(),scrollNew,scrollDiff;
                $(document).on('scroll',function(){
                    scrollNew = $(document).scrollTop();
                    if(scrollNew>scrollStart){
                        scrollDiff = scrollNew - scrollStart;
                    }else{
                        scrollDiff = scrollStart - scrollNew;
                    }
                    if(scrollDiff>=Math.round(options.acceptOnScroll)) cookieAccept();
                });
            }
            if(options.acceptAnyClick && !options.disableClick){
                $(document).bind('click',anyClick);
            }
        }
    };
})(jQuery);