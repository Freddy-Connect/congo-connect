<?php
$conditions=" (lang_key IN ('_ibdw_pagecover_update','_ibdw_pagecover_update_male','_ibdw_pagecover_update_female','_ibdw_groupcover_update','_ibdw_groupcover_update_male','_ibdw_groupcover_update_female','_ibdw_profilecover_update','_ibdw_profilecover_update_male','_ibdw_profilecover_update_female', '_bx_spy_profile_has_joined', '_bx_spy_profile_has_rated', '_bx_spy_profile_has_edited', '_bx_spy_profile_has_commented', '_bx_spy_profile_friend_accept', '_ibdw_evowall_bx_evowall_message','_ibdw_evowall_bx_evowall_messageseitu', '_ibdw_evowall_bx_url_share','_ibdw_evowall_bx_url_add'";
if ($spyprofileview=="on") $conditions=$conditions.",'_bx_spy_profile_has_viewed'";
if ($photo=="on") $conditions=$conditions.", '_bx_photos_spy_added', '_bx_photos_spy_comment_posted', '_bx_photos_spy_rated', '_bx_photoalbumshare', 'bx_photo_deluxe_commentofoto', 'bx_photo_deluxe_tag', 'bx_photo_deluxe_commentoalbum', '_ibdw_evowall_bx_photo_add_condivisione', '_bx_photo_add_condivisione','_ibdw_photodeluxe_likeadd'";
if ($video=="on") $conditions=$conditions.", '_bx_videos_spy_added', '_bx_videos_spy_rated', '_bx_videos_spy_comment_posted', '_ibdw_evowall_bx_video_add_condivisione'";
if ($event=="on") 
{
 if ($eventmodule=="Boonex") $conditions=$conditions.", '_bx_events_spy_post', '_bx_events_spy_join', '_bx_events_spy_rate', '_bx_events_spy_comment', '_bx_events_spy_post_change', '_ibdw_evowall_bx_event_add_condivisione','_ibdw_eventcover_update','_ibdw_eventcover_update_male','_ibdw_eventcover_update_female'";
 else $conditions=$conditions.", '_ue30_event_spy_post', '_ue30_event_spy_join', '_ue30_event_spy_rate', '_ue30_event_spy_comment', '_ue30_event_spy_post_change', '_ue30_event_add_condivisione','_ibdw_eventcover_update','_ibdw_eventcover_update_male','_ibdw_eventcover_update_female'";
}
if ($displayMessageStatus=="on") $conditions=$conditions.",'_bx_spy_profile_has_edited_status_message'";
if ($group=="on") $conditions=$conditions.", '_bx_groups_spy_post', '_bx_groups_spy_post_change', '_bx_groups_spy_join', '_bx_groups_spy_rate', '_bx_groups_spy_comment', '_ibdw_evowall_bx_gruppo_add_condivisione'";
if ($bxpage=="on") $conditions=$conditions.", '_bx_pages_spy_post', '_bx_pages_spy_post_change', '_bx_pages_spy_join', '_bx_pages_spy_rate', '_bx_pages_spy_comment', '_ibdw_evowall_bx_pagina_add_condivisione'";
if ($bxsite=="on") $conditions=$conditions.", '_bx_sites_poll_add', '_bx_sites_poll_rate', '_bx_sites_poll_commentPost', '_bx_sites_poll_change', '_ibdw_evowall_bx_site_add_condivisione'";
if ($poll=="on") $conditions=$conditions.", '_bx_poll_added', '_bx_poll_answered', '_bx_poll_rated', '_bx_poll_commented', '_ibdw_evowall_bx_poll_add_condivisione'";
if ($ads=="on") $conditions=$conditions.", '_ibdw_evowall_bx_ads_add_condivisione', '_bx_ads_added_spy', '_bx_ads_rated_spy', '_bx_ads_commented_spy'";
if ($blogs=="on") $conditions=$conditions.", '_ibdw_blogcover_update','_ibdw_blogcover_update_male','_ibdw_blogcover_update_female', '_ibdw_evowall_bx_blogs_add_condivisione', '_bx_blog_added_spy', '_bx_blog_rated_spy', '_bx_blog_commented_spy'";
if ($sounds=="on") $conditions=$conditions.", '_ibdw_evowall_bx_sounds_add_condivisione', '_bx_sounds_spy_added', '_bx_sounds_spy_comment_posted', '_bx_sounds_spy_rated'";
if ($modzzzproperty=="on") $conditions=$conditions.", '_modzzz_property_spy_post', '_modzzz_property_spy_post_change', '_modzzz_property_spy_join', '_modzzz_property_spy_rate', '_modzzz_property_spy_comment','_ibdw_evowall_modzzz_property_share'";
if ($ue30locations=="on") $conditions=$conditions.", '_ue30_location_spy_post', '_ue30_location_spy_post_change', '_ue30_location_spy_join', '_ue30_location_spy_rate', '_ue30_location_spy_comment','_ibdw_evowall_ue30_locations_add_condivisione'";
if ($modzzzclubs=="on") $conditions=$conditions.", '_modzzz_club_spy_post', '_modzzz_club_spy_post_change', '_modzzz_club_spy_join', '_modzzz_club_spy_rate', '_modzzz_club_spy_comment', '_ibdw_evowall_bx_club_add_condivisione'";
if ($modzzzpetitions=="on") $conditions=$conditions.", '_modzzz_petitions_spy_post', '_modzzz_petitions_spy_post_change', '_modzzz_petitions_spy_join', '_modzzz_petitions_spy_rate', '_modzzz_petitions_spy_comment', '_ibdw_evowall_bx_petition_add_condivisione'";
if ($modzzzpets=="on") $conditions=$conditions.", '_modzzz_pets_spy_post', '_modzzz_pets_spy_post_change', '_modzzz_pets_spy_rate', '_modzzz_pets_spy_comment', '_ibdw_evowall_bx_pet_add_condivisione'";
if ($modzzzbands=="on") $conditions=$conditions.", '_modzzz_bands_spy_post', '_modzzz_bands_spy_post_change', '_modzzz_bands_spy_join', '_modzzz_bands_spy_rate', '_modzzz_bands_spy_comment', '_ibdw_evowall_bx_band_add_condivisione'";
if ($modzzzschools=="on") $conditions=$conditions.", '_modzzz_schools_spy_post', '_modzzz_schools_spy_post_change', '_modzzz_schools_spy_join', '_modzzz_schools_spy_rate', '_modzzz_schools_spy_comment', '_ibdw_evowall_bx_school_add_condivisione'";
if ($modzzznotices=="on") $conditions=$conditions.", '_modzzz_notices_spy_post', '_modzzz_notices_spy_post_change', '_modzzz_notices_spy_rate', '_modzzz_notices_spy_comment', '_ibdw_evowall_bx_notice_add_condivisione'";
if ($modzzzclassified=="on") $conditions=$conditions.", '_modzzz_classified_spy_post', '_modzzz_classified_spy_post_change', '_modzzz_classified_spy_rate', '_modzzz_classified_spy_comment', '_ibdw_evowall_bx_classified_add_condivisione'";
if ($modzzznews=="on") $conditions=$conditions.", '_modzzz_news_spy_post', '_modzzz_news_spy_post_change', '_modzzz_news_spy_rate', '_modzzz_news_spy_comment', '_ibdw_evowall_bx_news_add_condivisione'";
if ($modzzzjobs=="on") $conditions=$conditions.", '_modzzz_jobs_spy_join', '_modzzz_jobs_spy_post', '_modzzz_jobs_spy_post_change', '_modzzz_jobs_spy_rate', '_modzzz_jobs_spy_comment','_ibdw_evowall_bx_job_add_condivisione'";


if ($modzzzarticles=="on") $conditions=$conditions.", '_modzzz_articles_spy_join', '_modzzz_articles_spy_post', '_modzzz_articles_spy_post_change', '_modzzz_articles_spy_rate', '_modzzz_articles_spy_comment','_ibdw_evowall_bx_article_add_condivisione'";


if ($modzzzformations=="on") $conditions=$conditions.", '_modzzz_formations_spy_join', '_modzzz_formations_spy_post', '_modzzz_formations_spy_post_change', '_modzzz_formations_spy_rate', '_modzzz_formations_spy_comment','_ibdw_evowall_bx_formation_add_condivisione'";


if ($modzzzinvestment=="on") $conditions=$conditions.", '_modzzz_investment_spy_join', '_modzzz_investment_spy_post', '_modzzz_investment_spy_post_change', '_modzzz_investment_spy_rate', '_modzzz_investment_spy_comment','_ibdw_evowall_bx_investment_add_condivisione'";

if ($modzzzlist=="on") $conditions=$conditions.", '_modzzz_listing_spy_join', '_modzzz_listing_spy_post', '_modzzz_listing_spy_post_change', '_modzzz_listing_spy_rate', '_modzzz_listing_spy_comment','_ibdw_evowall_bx_listing_add_condivisione'";
if ($modzzzfamily=="on") $conditions=$conditions.", '_modzzz_family_spy_post'";
if ($modzzzrelation=="on") $conditions=$conditions.", '_modzzz_relation_spy_post', '_modzzz_relation_spy_post_remove'";
if ($modzzzpolls=="on") $conditions=$conditions.", '_modzzz_polls_spy_post', '_modzzz_polls_spy_post_change', '_modzzz_polls_spy_rate', '_modzzz_polls_spy_comment', '_ibdw_evowall_modzzz_poll_add_condivisione'";
if ($modzzzdeal=="on") $conditions=$conditions.", '_modzzz_deals_spy_post', '_modzzz_deals_spy_post_change', '_modzzz_deals_spy_rate', '_modzzz_deals_spy_comment', '_ibdw_evowall_bx_deal_add_condivisione'";
if ($modzzzprovider=="on") $conditions=$conditions.", '_modzzz_provider_spy_join', '_modzzz_provider_spy_post', '_modzzz_provider_spy_post_change', '_modzzz_provider_spy_rate', '_modzzz_provider_spy_comment', '_ibdw_evowall_bx_provider_add_condivisione'";
if ($modzzzresume=="on") $conditions=$conditions.", '_modzzz_resume_spy_join', '_modzzz_resume_spy_post', '_modzzz_resume_spy_post_change', '_modzzz_resume_spy_rate', '_modzzz_resume_spy_comment', '_ibdw_evowall_bx_resume_add_condivisione'";
if ($andrewpcars=="on") $conditions=$conditions.", '_aca_spy_create_re_post', '_aca_spy_edit_re_post', '_ibdw_evowall_bx_aca_add_condivisione'";
if ($andrewpjob=="on") $conditions=$conditions.", '_ajb_wall_add_job_vacancy_spy', '_ibdw_evowall_bx_ajb_add_condivisione'";

if ($andrewpbuslist=="on") $conditions=$conditions.", '_abl_spy_create_bl_post', '_abl_spy_edit_bl_post', '_ibdw_evowall_bx_abl_add_condivisione'";
if ($andrewprealestate=="on") $conditions=$conditions.", '_are_spy_create_re_post', '_are_spy_edit_re_post', '_ibdw_evowall_bx_areal_add_condivisione'";
if ($kolimarfeyplaces=="on") $conditions=$conditions.", '_Places spy add', '_Places spy change', '_Places spy add_photo', '_Places spy add_video', '_Places spy comment', '_Places spy rate', '_Places spy add_kml', '_ibdw_evowall_bx_kplace_add_condivisione'";
if ($rayzlive=="on") $conditions=$conditions.", '_rz_live_spy_post', '_rz_live_spy_post_change', '_rz_live_spy_join', '_rz_live_spy_rate', '_rz_live_spy_comment','_ibdw_evowall_bx_rzlive_add_condivisione'";
$conditions=$conditions.")) AND bx_spy_data.sender_id NOT IN (SELECT PROFILE FROM sys_block_list WHERE ID=".$accountid.")";
?>