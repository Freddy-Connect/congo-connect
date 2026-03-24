<?php

require_once( 'inc/header.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

bx_import('BxTemplCmtsView');
bx_import('BxDolPaginate');
bx_import('BxDolModule');

  $term = $_GET['q'];


    
    /*Module Checking*/
            $Photos_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Photos'" );
            $Ads_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Ads'" );
            $Blog_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Blog'" );
            $Article_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Articles'" );
            $Event_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Events'" );
            $File_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Files'" );
            $Group_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Groups'" );
            $News_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='News'" );
            $Site_mod = db_value( "SELECT `title` FROM `sys_modules` WHERE `title`='Sites'" );
			// freddy ajout Jobs - Business Listing - Classified - Formations
			 $Job_mod = db_value( "SELECT `class_prefix` FROM `sys_modules` WHERE `class_prefix`='BxJobs'" );
			 
			$Listing_mod = db_value( "SELECT `class_prefix` FROM `sys_modules` WHERE `class_prefix`='BxListing'" );
			
			
			$Classified_mod = db_value( "SELECT `class_prefix` FROM `sys_modules` WHERE `class_prefix`='BxClassified'" );
			
			
			$Formation_mod = db_value( "SELECT `class_prefix` FROM `sys_modules` WHERE `class_prefix`='BxFormations'" );
			
			
			// End freddy ajout Jobs - Business Listing - Classified - Formations
             

    /*Values Getting*/
            $profiles = "SELECT `NickName` FROM `Profiles`  WHERE `NickName` LIKE '".$term."%'";
            $profilesres = db_res($profiles);
			
			// Ajout Freddy FirstName - Lastname
			 $firstlastnames = "SELECT `NickName`,`FirstName` , `LastName` FROM `Profiles`  WHERE `FirstName` LIKE '".$term."%'  OR `LastName` LIKE '".$term."%'";
            $firstlastnamesres = db_res($firstlastnames);
			///////////////////

            if($Photos_mod == 'Photos'){
            $photos = "SELECT `Owner`,`Title`,`Uri` FROM `bx_photos_main`  WHERE `Title` LIKE '".$term."%'";
            $photosres = db_res($photos);
            } 
            if($Ads_mod == 'Ads'){   
            $ads = "SELECT `EntryUri` FROM `bx_ads_main`  WHERE `EntryUri` LIKE '".$term."%'";
            $adsres = db_res($ads);
            }
            if($Blog_mod == 'Blog'){
            $blogs = "SELECT `PostUri`,`PostCaption` FROM `bx_blogs_posts`  WHERE `PostUri` LIKE '".$term."%'";
            $blogsres = db_res($blogs);
            }
            if($Article_mod == 'Articles'){
            $articals = "SELECT `caption`,`uri` FROM `bx_arl_entries`  WHERE `caption` LIKE '".$term."%'";
            $articalsres = db_res($articals);
            }
            if($Event_mod == 'Events'){
            $events = "SELECT `Title`,`EntryUri` FROM `bx_events_main`  WHERE `Title` LIKE '".$term."%'";
            $eventsres = db_res($events);
            }
            if($File_mod == 'Files'){
            $files = "SELECT `Title`,`Uri` FROM `bx_files_main`  WHERE `Title` LIKE '".$term."%'";
            $filesres = db_res($files);
            }
            if($Group_mod == 'Groups'){ 
            $groups = "SELECT `Title`,`Uri` FROM `bx_groups_main`  WHERE `Title` LIKE '".$term."%'";
            $groupsres = db_res($groups);
            }
            if($News_mod == 'News'){ 
            $news = "SELECT `caption`,`Uri` FROM `bx_news_entries`  WHERE `caption` LIKE '".$term."%'";
            $newsres = db_res($news);
            }
            if($Site_mod == 'Sites'){ 
            $sites = "SELECT `title`,`entryUri` FROM `bx_sites_main`  WHERE `title` LIKE '".$term."%'";
            $sitesres = db_res($sites);
            }
			
			
			// freddy ajout Jobs - Business Listing - Classified - Formations
			
			 
			  if($Job_mod == 'BxJobs'){
            $jobs = "SELECT `title`,`uri` FROM `modzzz_jobs_main`  WHERE `title` LIKE '".$term."%'";
            $jobsres = db_res($jobs);
            }
			
			 if($Listing_mod == 'BxListing'){
            $listings = "SELECT `title`,`uri` FROM `modzzz_listing_main`  WHERE `title` LIKE '".$term."%'";
            $listingsres = db_res($listings);
            }
			
			
			 if($Classified_mod == 'BxClassified'){
            $classifieds = "SELECT `title`,`uri` FROM `modzzz_classified_main`  WHERE `title` LIKE '".$term."%'";
            $classifiedsres = db_res($classifieds);
            }
			
			 if($Formation_mod == 'BxFormations'){
            $formations = "SELECT `title`,`uri` FROM `modzzz_formations_main`  WHERE `title` LIKE '".$term."%'";
            $formationsres = db_res($formations);
            }
			
			
			// End freddy ajout Jobs - Business Listing - Classified - Formations

           /* $stack = array();
             $class = 'test';
             $title = 'title';
             $label = 'label';
             $value = 'value';
            $arr = array(
                            'class' => $class,  
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);*/
            

            $stack = array();

            $profile = 0;
            /*Profiles*/
            while(($profilesfin = mysql_fetch_assoc($profilesres)) !== false) {
                if($profile == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Membres';
                $label = $profilesfin['NickName'];
                $value = $profilesfin['NickName'];
                $arr = array(
                            'class' => $class,  
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $profile++;
            }
			
			
			// freddy ajout firstname  and lastname
			 $firstlastname = 0;
            /*Profiles*/
            while(($firstlastnamesfin = mysql_fetch_assoc($firstlastnamesres)) !== false) {
                if($firstlastname == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Profils';
                $label = $firstlastnamesfin['FirstName'].' '.$firstlastnamesfin['LastName'];
                $value = $firstlastnamesfin['NickName'];
                $arr = array(
                            'class' => $class,  
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $firstlastname++;
            }
			
			
			
			//////////////////////////////////////////
			

          /*  if($Photos_mod == 'Photos'){
            $photo = 0;
            //Photos
            while(($photosfin = mysql_fetch_assoc($photosres)) !== false) {
                if($photo == 0)
                    $class = 'show';
                else
                    $class = 'hide';

                $Owner_Id= $photosfin['Owner'];

                if($photosfin['Title'] == 'Avatar'){
                    $Owner_name = db_value( "SELECT `NickName` FROM `Profiles` WHERE `ID` = '" . $Owner_Id . "'" );
                    $fin_label = $Owner_name.' '.$photosfin['Title'];
                }
                else{
                    $fin_label = $photosfin['Title'];
                }

                $title = 'Photos';
                $label = $fin_label;
                $value = 'm/photos/view/'.$photosfin['Uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $photo++;
            }
            }
			*/
           
            if($Ads_mod == 'Ads'){
                $ads = 0; 
            /*Ads*/
            while(($adsfin = mysql_fetch_assoc($adsres)) !== false) {
                if($ads == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Ads';
                $label = $adsfin['EntryUri'];
                $value = 'ads/entry/'.$adsfin['EntryUri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $ads++;
            }
            }

            if($Blog_mod == 'Blog'){
                $blog = 0;
            /*Blogs*/
            while(($blogsfin = mysql_fetch_assoc($blogsres)) !== false) {
                if($blog == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Blogs';
                $label = $blogsfin['PostCaption'];
                $value = 'blogs/entry/'.$blogsfin['PostUri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $blog++;
            }
            }
            if($Article_mod == 'Articles'){
                $artical = 0;
            /*Articals*/
            while(($articalsfin = mysql_fetch_assoc($articalsres)) !== false) {
                if($artical == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Articals';
                $label = $articalsfin['caption'];
                $value = 'm/articles/view/'.$articalsfin['uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $artical++;
            }
            }

            if($Event_mod == 'Events'){
                $event = 0;
            /*Events*/
            while(($eventsfin = mysql_fetch_assoc($eventsres)) !== false) {
                if($event == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                // freddy modif 
				//$title = 'Events';
				$title = 'Evénements';
                $label = $eventsfin['Title'];
                $value = 'm/events/view/'.$eventsfin['EntryUri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $event++;
            }
            }
			
         /*   if($File_mod == 'Files'){
                $file = 0;
            //Files
            while(($filesfin = mysql_fetch_assoc($filesres)) !== false) {
                 if($file == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Files';
                $label = $filesfin['Title'];
                $value = 'm/files/view/'.$filesfin['EntryUri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $file++;
                }
            }
			*/
            if($Group_mod == 'Groups'){ 
                $group = 0;
            /*Groups*/
            while(($groupsfin = mysql_fetch_assoc($groupsres)) !== false) {
                if($group == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Groups';
                $label = $groupsfin['Title'];
                $value = 'm/groups/view/'.$groupsfin['Uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $group++;
            }
            } 
            if($News_mod == 'News'){  
                $news = 0;
            /*News*/
            while(($newsfin = mysql_fetch_assoc($newsres)) !== false) {
                if($news == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'News';
                $label = $newsfin['caption'];
                $value = 'm/news/view/'.$newsfin['Uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $news++;
            }
            }
            if($Site_mod == 'Sites'){  
                $Site = 0;
            /*Sites*/
            while(($sitesfin = mysql_fetch_assoc($sitesres)) !== false) {
                if($Site == 0)
                    $class = 'show';
                else
                    $class = 'hide';
                $title = 'Sites';
                $label = $sitesfin['title'];
                $value = 'm/sites/view/'.$sitesfin['entryUri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $Site++;
            }
            }
			
			
			// freddy ajout Jobs, Business listng, Classified, Formations
			
			
			 if($Job_mod == 'BxJobs'){
                $job = 0;
            /*Jobs*/
            while(($jobsfin = mysql_fetch_assoc($jobsres)) !== false) {
                if($job == 0)
                    $class = 'show';
                else
                    $class = 'hide';
               
				$title = 'Offres d\'emploi';
                $label = $jobsfin['title'];
                $value = 'm/jobs/view/'.$jobsfin['uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $job++;
            }
            }
			
			
			 if($Listing_mod == 'BxListing'){
                $job = 0;
            /*Business Listing*/
            while(($listingsfin = mysql_fetch_assoc($listingsres)) !== false) {
                if($listing == 0)
                    $class = 'show';
                else
                    $class = 'hide';
               
				
				$title = 'Entreprises';
                $label = $listingsfin['title'];
                $value = 'm/listing/view/'.$listingsfin['uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $listing++;
            }
            }
			
			 if($Classified_mod == 'BxClassified'){
                $classified = 0;
            /*Classified*/
            while(($classifiedsfin = mysql_fetch_assoc($classifiedsres)) !== false) {
                if($classified == 0)
                    $class = 'show';
                else
                    $class = 'hide';
               
				
				$title = 'Annonces';
                $label = $classifiedsfin['title'];
                $value = 'm/classified/view/'.$classifiedsfin['uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $classified++;
            }
            }
			
			
			 if($Formation_mod == 'BxFormations'){
                $formation = 0;
            /*Courses / Formations*/
            while(($formationsfin = mysql_fetch_assoc($formationsres)) !== false) {
                if($formation == 0)
                    $class = 'show';
                else
                    $class = 'hide';
               
				
				$title = 'Formations';
                $label = $formationsfin['title'];
                $value = 'm/formations/view/'.$formationsfin['uri'];
                $arr = array(
                            'class' => $class,
                            'title' => $title,
                            'label' => $label,
                            'value' => $value
                            );
                array_push($stack, $arr);
                $formation++;
            }
            }
			
			
			// END freddy ajout Jobs, Business listng, Classified, Formations
			
			
			
			
            echo json_encode($stack);
            exit;

       /* function actionAjax(){
          
         }
*/
