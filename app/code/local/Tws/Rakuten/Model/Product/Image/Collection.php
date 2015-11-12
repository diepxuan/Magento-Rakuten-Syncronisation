<?php

class Tws_Rakuten_Model_Product_Image_Collection extends SplObjectStorage
{
	public function append($element)
	{
		parent::attach($element);
	}
}

?>