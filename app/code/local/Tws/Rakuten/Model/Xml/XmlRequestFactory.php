<?php

class Tws_Rakuten_Model_Xml_XmlRequestFactory extends Varien_Object {
    protected $username = '';
    protected $password = '';
	public function __construct() 
    {
       $storeId = Mage::app()->getStore()->getStoreId();
       $this->setAuthenticationParameters(
            Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
            Mage::getStoreConfig('tws_rakuten/config_rakuten/password',$storeId)
        );
     }
    public function setAuthenticationParameters($username, $password)
    {
     $this->username = trim((string) $username);
     $this->password = trim((string) $password);
    } 
	public function createOrderCancellationRequest(Mage_Sales_Model_Order $order, Mage_Sales_Model_Quote $quote)
    {   
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->setAuthenticationParameters(
            Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
            Mage::getStoreConfig('tws_rakuten/config_rakuten/password',$storeId)
            
        );
        if(!$this->areAuthenticationParamtersSet()) {
            throw new Tws_Rakuten_Model_Xml_XmlBuildException();
        }
        $rakutencancel=array(
       'key'=>$this->username,
       'format'=>'php',
       'order_no'=>$order->getRakutenOrderId()); 
        return $rakutencancel;
    }

    public function createShipment($orderid, $shipmentid,$shipmettitle)
    {   $storeId = Mage::app()->getStore()->getStoreId();
        $this->setAuthenticationParameters(
             Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
            Mage::getStoreConfig('tws_rakuten/config_rakuten/password',$storeId)
        );
        $rakutenshipment=array(
       'key'=>$this->username,
       'format'=>'php',
       'order_no'=>$orderid, 
       'carrier'=>$shipmettitle,
       'tracking_number'=>$shipmentid);
        return $rakutenshipment;
    }
	public function createOrderRequestXml($dateFrom, $dateTo)
     {
      $storeId = Mage::app()->getStore()->getStoreId();
        $this->setAuthenticationParameters(
              Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
            Mage::getStoreConfig('tws_rakuten/config_rakuten/password',$storeId)
        );
        if(!$this->areAuthenticationParamtersSet()) {
            throw new Tws_Rakuten_Model_Xml_XmlBuildException();
        }
          $rakutenorderimport=array(
       'key'=>$this->username,
       'format'=>'php',
       'per_page'=>'100',
       'from'=>$dateFrom,
       'to'=>$dateTo,
       'order'=>'created_asc');
       return $rakutenorderimport;
      
    }
 	public function areAuthenticationParamtersSet()
	{
		return (is_string($this->username) && is_string($this->password) && (strlen($this->username) > 0) && (strlen($this->password) > 0));
	}
	
 }

?>