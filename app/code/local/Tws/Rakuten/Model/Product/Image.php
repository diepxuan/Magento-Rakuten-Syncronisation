<?php

class Tws_Rakuten_Model_Product_Image
{
	protected $_url = '';
	protected $_caption = '';
	public function getUrl()
	{
		return $this->_url;
	}
	public function setUrl($url)
	{
		$this->_url = $url;
		return $this;
	}
	public function getCaption()
	{
		return $this->_caption;
	}
	
	public function setCaption($caption)
	{
		$this->_caption = $caption;
		return $this;
	}
}

?>