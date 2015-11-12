<?php
class Tws_Rakuten_Model_Cron{	
	public function Order_update(){
     Mage::getSingleton('rakuten/importorder');
    } 
    public function Product_update(){
	 Mage::getSingleton('rakuten/productupdate');
	} 
}
?>