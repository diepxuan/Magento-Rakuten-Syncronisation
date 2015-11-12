<?php  
    class Tws_Rakuten_Model_Carrier_Rakutenshipping     
		extends Mage_Shipping_Model_Carrier_Abstract
		implements Mage_Shipping_Model_Carrier_Interface
	{  
        protected $_code = 'rakutenshipping';
        static protected $_deliveryCosts = 0.0;
        static protected $_isLocked = true;
        public static function lock()
    {
        self::$_isLocked = true;
    }
    public static function unlock()
    {
        self::$_isLocked = false;
    }  
    public function getAllowedMethods()
    {
        return array('methode'=>$this->getConfigData('name'));
    }    
       
        public function collectRates(Mage_Shipping_Model_Rate_Request $request){
        if(self::$_isLocked) {
            return false;
        } 
            $result = Mage::getModel('shipping/rate_result');  
            $method = Mage::getModel('shipping/rate_result_method');  
            $method->setCarrier($this->_code);  
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('methode');  
            $method->setMethodTitle($this->getConfigData('name'));
		    $method->setPrice(self::$_deliveryCosts);
			$result->append($method);  
            return $result;  
        }  
       
        public static function setDeliveryCosts($deliveryCosts)
    {
        self::$_deliveryCosts = (float) $deliveryCosts;
    }
    }  
?>