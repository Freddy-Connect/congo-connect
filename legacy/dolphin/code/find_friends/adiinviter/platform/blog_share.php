<?php

/**
* Share Dolphin Blog Post Content
*/
class Adi_Campaign_blog_share extends Adi_Campaigns_Prototype
{
	function get_category_id($content_id = 0)
	{
		$this->category_id = explode(';', $this->category_id);
		return $this->category_id;
	}
}


?>