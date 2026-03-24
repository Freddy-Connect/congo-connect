<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

$aXmlTemplates = array (
    "message" => array (
        15 => '<message id="#1#" room="#3#" author="#4#" user="#5#" whisper="#6#" color="#7#" bold="#8#" underline="#9#" italic="#10#" size="#11#" font="#12#" smileset="#13#" date="#14#" count="#15#"><text><![CDATA[#2#]]></text></message>'
    ),

    "file" => array (
        4 => '<file user="#1#" file="#2#" count="#4#"><name><![CDATA[#3#]]></name></file>'
    ),

    "user" => array (
        2 => '<user id="#1#" status="#2#" />',
        3 => '<user id="#1#" status="#2#" type="#3#" />',
        4 => '<user id="#1#" status="#2#" type="#3#" online="#4#" />',
        8 => '<user id="#1#" sex="#3#" age="#4#" photo="#5#" profile="#6#" banned="#7#" type="#8#"><nick><![CDATA[#2#]]></nick></user>',
        10 => '<user id="#1#" status="#2#" sex="#4#" age="#5#" photo="#7#" profile="#8#" type="#9#" online="#10#"><nick><![CDATA[#3#]]></nick><desc><![CDATA[#6#]]></desc></user>',
        11 => '<user id="#1#" status="#2#" sex="#4#" age="#5#" photo="#7#" profile="#8#" type="#9#" online="#10#" time="#11#"><nick><![CDATA[#3#]]></nick><desc><![CDATA[#6#]]></desc></user>'
    ),

    "result" => array (
        1 => '<result value="#1#" />',
        2 => '<result value="#1#" status="#2#" />'
    ),

    "room" => array (
        //5 => '<room id="#1#" owner="#2#" password="#3#"><title><![CDATA[#4#]]></title><desc><![CDATA[#5#]]></desc></room>',
        7 => '<room id="#1#" in="#6#" inTime="#7#" owner="#2#" password="#3#"><title><![CDATA[#4#]]></title><desc><![CDATA[#5#]]></desc></room>',
    ),

    "smileset" => array (
        2 => '<properties current="#1#" url="#2#" />',
        3 => '<smileset folder="#1#" config="#2#"><![CDATA[#3#]]></smileset>'
    )
);
