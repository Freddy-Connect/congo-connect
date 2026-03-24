<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import ('BxDolTwigCalendar');

class BxArticlesCalendar extends BxDolTwigCalendar {

    function BxArticlesCalendar ($iYear, $iMonth, &$oDb, &$oConfig, &$oTemplate) {
        parent::BxDolTwigCalendar($iYear, $iMonth, $oDb, $oConfig);
    }

    function getEntriesNames () {
        return array(_t('_modzzz_articles_article_single'), _t('_modzzz_articles_article_plural'));
    }

    function _getWeekNames () {
        if(0 == $this->iWeekStart)
            $aWeek[] = array('name' => _t('_modzzz_articles_week_sun'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_mon'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_tue'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_wed'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_thu'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_fri'));
        $aWeek[] = array('name' => _t('_modzzz_articles_week_sat'));
        if(8 == $this->iWeekEnd)
            $aWeek[] = array('name' => _t('_modzzz_articles_week_sun'));
        return $aWeek;        
    } 


}
