<?php
$design_header_font = getParam('giveme_design_header_font');  
$header_font = getParam('giveme_header_font'); 
$body_font = getParam('giveme_body_font'); 
$body_header_font = getParam('giveme_body_header_font'); 
$footer_font = getParam('giveme_footer_font'); 
$menu_font = getParam('giveme_menu_font'); 
$custom_color = getParam('giveme_custom_color');
//echo 'hi!';exit;

if($custom_color == "none")
	$custom_color = false;

?>

<?php if($body_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $body_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>
<?php if($body_header_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $body_header_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>
<?php if($header_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $header_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>
<?php if($menu_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $menu_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>
<?php if($footer_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $footer_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>
<?php if($design_header_font) { ?>
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $design_header_font); ?>:200,300,400,500,600,700,800' type='text/css' rel='stylesheet' />
<?php } ?>


<style type="text/css"> 

<?php if($header_font) { ?>
.sys-sm-profile span,.sys-service-menu div#sys_search input,.sys-service-menu-profile .profile_block a, .sys-sm-link span,body.memberin .author_theme.header div.sys-service-menu div.sys-sm-profile span{font-family:<?php echo "'".$header_font."', sans-serif;"; ?>;}
<?php } ?>


<?php if($design_header_font) { ?>
.disignBoxFirst .boxFirstHeader .dbTitle,.disignBoxFirst .boxFirstHeader,.rss_item_wrapper .bx-def-font-h2 a,.bx-twig-unit-info .bx-def-font-h2 a,.bx_files_title a,.giving-home_block h2, .giving-home_block .bx-def-font-large h2, .giving-home_block .bx-def-font-h2, .giving-home_block .bx-def-font-h2 a, .giving-home_block .unit_title.bx-def-font-h2, .giving-home_block .unit_title.bx-def-font-h2 a.unit_title.bx-def-font-h2,.giving-home_block .bx-def-bc-margin h2,.giving-home_block .giving-changes h2,.giving-home_block .giving-latest h2,.giving-home_block .giving-latest .latest-centerbg h3,.giving-home_block .giving-volunteer h3,.giving-home_block .giving-gallery .giving-gallery-title h3,.giving-home_block .latest-events h3,.giving-home_block .latest-events .latest_cont h3,.giving-home_block .newsletters .news_mail .news_left h3,h4.modal-title,/*.giving-home_block .newsletters  h3.title,*//*.giving-home_block .newsletters .right_side h3.title,*/.giving-home_block .newsletters  .addre .link,.footer-manage .dbTitle/*, .giving-home_block .footer-manage h3.title*/
{ font-family:<?php echo "'".$design_header_font."', sans-serif;"?>; }
<?php } ?>

<?php if($body_font) { ?>
.dbTopMenu .dbTmActive span.dbTmaTitle,.dbTopMenuPopupCnt .notActive  a,.dbTopMenuPopupCnt .active  span,.rss_item_desc,.rss_item_info span,.rss_read_more a.rss_read_more_link,.thumbnail_block .thumb_username a,.info_section  .view_all a,.sys_file_search_info .sys_file_search_title a,.sys_file_search_info .sys_file_search_from a,.colspan,.form_input_submit.bx-btn,.siteStatUnit,.siteStatUnit a,.daily_quotes i,.daily_quotes p,.wall-oii-description,.wall-oii-author-name a,.wall-oii-posted,.wall-comment-counter-holder i,.wall-voting-count i,.wall-load-more button.bx-btn.bx-btn-disabled,.wall-caption,.wall-caption span,.wall-caption a,.wall-divider .wall-divider-cnt,.wall-content,.blog_text,.wall-event-stats span,.wall-event-stats a,.wall-event-stats a span,.bx-twig-unit-line,.wall-description,.wall-title  a,div.wall-view div.wall-load-more .bx-btn ,.ordered_block span,.form_input_select,label,.per_page_block span,.caption_section,.linkSection a,.linkSection span,.form_advanced_table .bx-form-caption,.paginate_page span,.paginate_page a,.sys_cal_browse a,.sys_cal_table th,.viewAllMembers,.sys_cal_cell u,.sys_cal_cell a b,.sys_cal_cell a span,.giving-home_block .bx-btn,.bx-btn,.gsc-search-button,.bx-def-bc-margin p,.bx-def-bc-margin,.bx-def-bc-margin a.form_input_captcha,.lcont_top a,.lcont_other a,.msgbox_content,.blog_caption a,.clr3 ,.cls_res_info_nowidth div,.unit_date a,.unit_date span,.unit_comments,.bx_events_main_info_text,.bx_events_unit_participants b,.forum_stat,.forum_icon_title_desc span,.forum_table_column_stat,#loading,.forum_centered_msg,.bx-def-margin-bottom,.bx-def-bc-padding > form,.forum_file_attachment,.lp_txt,.lp_u,.lp_u a,date,.pas a,.bx_sys_file_upload_wrapper .form_input_file,#join_form .input_wrapper_submit .form_input_submit,#login_box_form .input_wrapper_submit .form_input_submit,.input_wrapper_custom a,.sys-form-login-join .ui-state-default.ui-corner-top.ui-tabs-active.ui-state-active a,.ui-state-default.ui-corner-top a,#sys-bm-switcher-template .dbTopMenu .dbTmActive span.dbTmaTitle,#sys-bm-switcher-template .disignBoxFirst .boxFirstHeader .dbTitle,#sys-bm-switcher-language .disignBoxFirst .boxFirstHeader .dbTitle,.view_all a,.sys-pct-nickname,.form_input_text,.form_input_date,.popup_confirm_text ,.bx-def-bc-padding,.bx-def-bc-padding  a,.login_ajax_wrap .popup_form_wrapper.trans_box .disignBoxFirst .boxFirstHeader,.bx_files_info,.bx_files_info a,.bx_files_info span,.sys-profile-cover-menu-cnt a,.sys_page_submenu_bottom a,.sys_page_submenu  a,.flexnav li ul li a, .flexnav ul li ul li, .giving-home_block .bx-def-font-large p, .blog_text.bx-def-font-large.bx-def-margin-sec-top, .bx-twig-unit-line.bx-twig-unit-desc, .bx-def-font-large, .forum_icon_title_desc > a, .forum_icon_title_desc > span, .giving-home_block .wall-view span, .forum_topic_ext_info, .forum_stat,.giving-home_block .sys_breadcrumb a, .giving-home_block .sys_breadcrumb span,.sys_breadcrumb .bc_unit,.form_input_captcha,.forum_icon_title_desc.forum_icon_medium a,td.notActive a.top_members_menu,.subMenuCnt .sys_page_header,.tp-caption.skewfromrightshort.fadeout.start h3,.tp-caption.skewfromrightshort.fadeout.start p ,.giving-home_block .giving-concept .first_con .concept-img .concept-textbg,.giving-home_block .giving-changes p,.giving-home_block .changes-color > a,.splashpage .giving-changes .donate-txt,.join-button a,.giving-home_block .giving-latest p,.giving-home_block .giving-latest .latest-centerbg p,.giving-home_block .giving-latest .latest-leftimg .right-text,.giving-home_block .giving-latest .latest-leftimg .left-text,.giving-home_block .giving-latest .giving-button > a,.giving-home_block .giving-latest .giving-buttons > a,.giving-home_block .giving-volunteer p,.giving-home_block .giving-volunteer .join-button > a,.giving-home_block .giving-gallery .giving-gallery-title p,.giving-home_block .giving-gallery .giving-menu ul li.filter > a,.giving-home_block .latest-events p,.giving-home_block .latest-events .latest_cont ul li,.giving-home_block .newsletters .news_mail .news_left p,.giving-home_block .newsletters .news_mail .news_form .bx-form-caption.bx-def-font-inputs-captions,.giving-home_block .latest-events .viwe_events > a,.sys_breadcrumb .bc_unit.bx-def-margin-sec-left > a,body.memberin.splashpage .btn.btn-info.btn-lg,.banner_button > a,div.login_ajax_wrap div.sys-form-login-join, div.login_ajax_wrap div.sys-form-login-join input, div.login_ajax_wrap div.sys-form-login-join select, div.login_ajax_wrap div.sys-form-login-join textarea, div.login_ajax_wrap div.sys-form-login-join button,div.wall-divider-cnt,.modal-body .btn.btn-color,.modal-body > span,div.extra_top_menu table.fixed_menu div.html_data a, div.extra_top_menu table.fixed_menu div.html_data a:link, div.extra_top_menu table.fixed_menu div.html_data a:visited, div.extra_top_menu table.fixed_menu div.html_data a:hover,b.menu_item_username,.part_name a.item_block,body.memberin.splashpage .banner_button .btn.btn-info.btn-lg,.head .control-label,.modal-body > span#donate_amt_error,.anet_pay_form label,.anet_pay_form .submit,.anet_pay_form input.text, select.text,.form_field_row .bx-btn,.banner_button .join-button a,#join_form .bx-form-caption.bx-def-font-inputs-captions, .sys-auth .bx-btn, #tabs-join .bx-form-block-header .bx-form-caption,.sys-form-login-join .ui-state-default.ui-corner-top.ui-tabs-active.ui-state-active a,.sys-form-login-join .ui-state-default.ui-corner-top a ,.form_advanced_table .bx-form-caption,/*.giving-home_block .newsletters .left_side .quick ul li a,.giving-home_block .newsletters .right_side p,.giving-home_block .newsletters .right_side button.submit,.giving-home_block .newsletters .left_side .addre p,.giving-home_block .newsletters .left_side .addre ul li,.giving-home_block .newsletters .left_side .addre .link span,.giving-home_block .newsletters .right_side input,.giving-home_block .newsletters .addre p,.giving-home_block .newsletters .addre ul li,.giving-home_block .newsletters .quick ul li a,.giving-home_block .newsletters .quick ul li a,.giving-home_block .newsletters .right_side .form_advanced_table .bx-form-caption,.giving-home_block .newsletters .right_side .bx-btn,#mail_box .left_section label,*/.sys_page_profile .sys-pct-nickname.bx-def-font-h1,.sys_page_profile .sys-profile-cover-actions a span.sys-pca-text,div.pmt-orders-field, div.pmt-orders-chb, div.pmt-orders-date, div.pmt-orders-client, div.pmt-orders-order, div.pmt-orders-amount, div.pmt-orders-license, div.pmt-orders-action  

{ font-family:<?php echo "'".$body_font."', sans-serif;"?>; }
<?php } ?>


<?php if($body_font) { ?>
.form_input_submit .bx-btn,.sys_ph_submenu_submenu_cnt .pas a.sublinks,.sys_ph_submenu_submenu_cnt .act  a.sublinks,.tp-caption.skewfromrightshort.fadeout.start p
{ font-family:<?php echo "'".$body_font."', sans-serif !important;"?> ; }
<?php } ?>

<?php if($body_header_font) { ?>
.boxFirstHeader, .dbTitle, div.sub_design_box_head .caption_section, .form_advanced_table th.block_header, .bx-def-font-h2, .bx-def-font-h2 a, .boxContent h2, .boxContent .bx-def-font-h2, .boxContent .forum_topic_title, .boxContent  div.wall-oii-title a,  .sys_file_search_unit .sys_file_search_title a, sys_album_info .sys_album_title a, div.bx_files_title a, .fileTitle {font-family:<?php echo "'".$body_header_font."', sans-serif;"; ?>;}
<?php } ?>


<?php if($footer_font) { ?>
.giving-home_block .newsletters .left_side .quick ul li a,.giving-home_block .newsletters .right_side p,.giving-home_block .newsletters .right_side button.submit,.giving-home_block .newsletters .left_side .addre p,.giving-home_block .newsletters .left_side .addre ul li,.giving-home_block .newsletters .left_side .addre .link span,.giving-home_block .newsletters .right_side input,.giving-home_block .newsletters .addre p,.giving-home_block .newsletters .addre ul li,.giving-home_block .newsletters .quick ul li a,.giving-home_block .newsletters .quick ul li a,.giving-home_block .newsletters .right_side .form_advanced_table .bx-form-caption,.giving-home_block .newsletters .right_side .bx-btn,#mail_box .left_section label,.giving-home_block .new_news a.bx-btn,.giving-home_block .new_news h3.title,.sub_footer .col-sm-3 > p ,.giving-home_block .newsletters .new_news h3.title,.sub_footer .col-sm-9 .bottom_links_block  {font-family:<?php echo "'".$footer_font."', sans-serif ;"?>;}
<?php } ?>


<?php if($menu_font) { ?>
.giving-home_block .flexnav li a span,.flexnav li ul li a, .flexnav ul li ul li,.giving-home_block .menu-button ,.mm-listview > li > a, .mm-listview > li > span,ul.mainmenu li.item-with-ul a span,.mm-navbar a,.flexnav.mainmenu.lg-screen li ul.submenu li a{ font-family:<?php echo "'".$menu_font."', sans-serif;"; ?>; }
<?php } ?>


/************************************************Customcolor*******************************************/

<?php if($custom_color) { ?>
body.home .wall-caption span, a:link, a:hover, a:active, a:visited, div.sys-form-login-join .sys-flj-content a, div.sys-form-login-join .sys-flj-content a:link, div.sys-form-login-join .sys-flj-content a:hover, div.sys-form-login-join .sys-flj-content a:active, div.subMenu a.sublinks, .giving-home_block div.subMenu a.sublinks:link, .sys_album_info .sys_album_title a, .sys_album_info .sys_album_from a, .lp_date, div.sys_ph_submenu_submenu a.sublinks, div.sys_ph_submenu_submenu a.sublinks:link, div.sys_ph_submenu_submenu a.sublinks:visited, #preloader i, div.paginate_btn a, div.paginate_btn span, div.paginate_page a, div.paginate_page span, .login_ajax_wrap .dbTopMenu .bx-popup-element-close, .searchrow_block_simple a, #back-top span i, .bx-form-input-emoji.bx-def-font-grayed i.sys-icon.smile-o, .siteStatUnit,.sys-form-login-join .form_advanced_table .input_wrapper.input_wrapper_custom a,div.sys-service-menu a.sys-sm-link span, div.sys-service-menu a.sys-sm-link i, div.profile_block a,.tp-caption.skewfromrightshort.fadeout.start h3 span,.part_img i, .extra_menu_subitem_container i.user,.g_hand i,.giving-home_block .flexnav li a.top_link:hover,.giving-home_block .new_icon a i,ul.flexnav li a:hover span,.dbTopMenuPopupCnt .active span,.giving-home_block .flexnav li.active a span,.sys_breadcrumb .bc_unit.bx-def-margin-sec-left > a,.icon-logo .path1::before,.mobile-menu a:hover i, .mobile-menu a:focus i,.modal-content .modal-title button.close,#divUnderCustomization .wall-caption span,.wall-event .wall-caption span ,.giving-home_block .newsletters .left_side .addre .link,div.dbPrivacy .sys-icon 
{ color: <?php echo $custom_color; ?>; }
<?php } ?>

<?php if($custom_color) { ?>
.g_hand i,.filter > a:hover,.giving-home_block .flexnav li a.top_link:hover
{ color: <?php echo $custom_color; ?> !important; }
<?php } ?>



<?php if($custom_color) { ?>
div.sub_design_box_head,.ui-tabs-active a,.wall-events .wall-load-more .bx-btn:hover,.giving-home_block .wall-outline-paginate .wall-load-more .bx-btn.bx-btn-disabled:hover,.form_advanced_table .bx-form-block-header, .form_advanced_table .bx-form-block-headers,.sys_cal_cell,.form_input_captcha,div.sys_ph_submenu_submenu div.act,.wall-event-cnt .wall-event-actions a.bx-btn:hover ,.member_menu ul  li.active > a,.giving-home_block .wall-comment a:hover,.login_ajax_wrap .sys-form-login-join .bx-btn, #join_form_table .bx-form-block-header .bx-form-caption,.wall-voting a.bx-btn:hover ,.giving-home_block .giving-latest .giving-button > a,.giving-home_block .giving-latest .giving-buttons > a,.giving-home_block .giving-volunteer .join-button > a:hover,div.wall-divider-cnt,.giving-home_block .latest-events .viwe_events > a,.giving-home_block .newsletters .news_mail,div.pollResultStatsRow,.giving-home_block #buttonArea .bx-btn,div.sub_design_box_head, a.bx-btn, .bx-btn, .ui-tabs-active a, .wall-events .wall-load-more .bx-btn:hover, .buddies .wall-outline-paginate .wall-load-more .bx-btn.bx-btn-disabled:hover, .form_advanced_table .bx-form-block-header, .form_advanced_table .bx-form-block-headers, .sys_cal_cell, .form_input_captcha, div.sys_ph_submenu_submenu div.act, .wall-event-cnt .wall-event-actions a.bx-btn:hover, .member_menu ul li.active > a,div.pollResultStatsRow,#bx-popup-confirm .bx-btn, #submitAction .bx-btn, .form_input_multiply_remove,.giving-home_block .newsletters .right_side button.submit,.giving-home_block .newsletters .left_side .addre .title a,.giving-home_block .newsletters  .addre .title ,.giving-home_block .newsletters .right_side .bx-btn{background-color: <?php echo $custom_color; ?>;}
<?php } ?> 

<?php if($custom_color) { ?>#loading,.wnd_title,div.pollResultStatsRow,.mm-panels .mainmenu li:hover,.submit.buy,.giving-home_block .b-progress__bar{background-color: <?php echo $custom_color ;?>!important;}<?php } ?> 

<?php if($custom_color) { ?>
.sys-profile-cover-actions.bx-def-padding a, .sys-profile-cover-actions.bx-def-padding a:hover,#slide_menu .nav.mm-listview li a:hover, #slide_menu .nav.mm-listview li:hover, #slide_menu .sub-menu.mm-listview li:hover, #slide_menu .last-menu.mm-listview li:hover,.flexnav li a:focus,.giving-home_block .bx-btn, #invite_friend .input_wrapper.input_wrapper_submit .form_input_submit.bx-btn, #sys_popup_ajax .input_wrapper.input_wrapper_submit .form_input_submit.bx-btn,.sys_file_search_info,div.wall-divider::before, div.wall-divider-today::before, div.wall-event::before,.main_settings div.sub_design_box_head, .quick_links_elink .lcont_top,.bx-form-block-header .bx-form-caption,.sys-form-login-join .ui-state-default.ui-corner-top a,.sys_page.sys_page_member .form_advanced_table .bx-form-caption,.giving-home_block  ul.flexnav li ul li a:hover,.giving-home_block .giving-gallery .giving-menu ul li.active,.nav > li > a:focus, .nav > li > a:hover, .mm-listview > li:hover{background:<?php echo $custom_color; ?>; }
<?php } ?>

<?php if($custom_color) { ?>
div.wall-oii-actions .bx-btn:hover, .latestgrp_container .left-s, .latestgrp_container .right-s,.bx-gallery-icon-selector, .giving-home_block .wall-divider .wall-divider-cnt, #login_div .bx-btn, .giving-home_block .sys-flj-content .sys-auth .bx-btn,#login_div .bx-form-element.bx-form-element-submit.bx-def-margin-top .form_input_submit.bx-btn, #login_div .form_advanced_table .bx-form-block-header.bx-def-margin-top-auto.bx-def-font-inputs, #login_div .ui-widget-header .ui-state-active,div.wall-oii-actions .bx-btn,div.wall-event-actions .bx-btn,.wall-event-cnt .wall-event-actions a.bx-btn:hover,div.sub_design_box_head ,.flexnav li.last ul.submenu .item-with-ul > ul,.tp-banner-container .banner-button a ,div.wall-event div.wall-event-owner div.thumbnail_block,.giving-home_block .giving-volunteer .join-button > a:hover,.banner_button > a{ border-color: <?php echo $custom_color; ?>; }
<?php } ?>

<?php if($custom_color) { ?>.loader .circle ,.loader .circle 1{  border-color:  <?php echo  $custom_color; ?>;}<?php } ?>


<?php if($custom_color) { ?>.bsetec_bloglist li:hover, .bsetec_bloglist li.active, .bsetec_forumlist li:hover, .bsetec_forumlist li.active, #slides div.featured_block_1:hover, #slides div.featured_block_1.active { border-bottom: 1px solid <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.mainmenu li:hover ul.submenu span.arrow,span.lines_lines{  border-bottom-color:<?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.boxFirstHeader,.head-line,.giving-home_block .giving-concept .first_con .concept-img .concept-textbg{  border-bottom-color:  <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>span.lines,.splash_line{  border-left-color:  <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>span.lines,.splash_line{  border-right-color:  <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.loader .circle,.loader .circle1{  border-top: 5px solid <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.loader .circle,.loader .circle1{  border-bottom: 5px solid <?php echo $custom_color; ?>;}<?php } ?>


<?php if($custom_color) { ?>.sys_file_search_info{  border-bottom: 3px solid  <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.flexnav.mainmenu.lg-screen li ul.submenu{ border-top-color:  <?php echo $custom_color; ?>;}<?php } ?>

<?php if($custom_color) { ?>.gsc-tabHeader.gsc-tabhActive{  border-top:2px solid  <?php echo $custom_color; ?> !important;}<?php } ?>

<?php if($custom_color) { ?>.banner_button .btn.btn-info.btn-lg,.banner_button .join-button a{  border:3px solid  <?php echo $custom_color; ?> !important;}<?php } ?>
<?php if($custom_color) { ?>.giving-home_block .tp-bullets.simplebullets.round.hovered .bullet.selected, .tp-bullets.simplebullets.round .bullet.selected {  border:2px solid  <?php echo $custom_color; ?> ;}<?php } ?>


<?php if($custom_color) { ?>.wnd_title {  border-bottom: 1px solid <?php echo $custom_color; ?> !important;}<?php } ?>
/**************************************************End**************************************************/

 
 </style>
 


