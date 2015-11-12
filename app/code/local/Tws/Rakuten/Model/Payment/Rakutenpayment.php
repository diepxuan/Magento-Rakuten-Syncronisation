<?php
class Tws_Rakuten_Model_Payment_Rakutenpayment extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'rakutenpayment';
    protected $_canUseCheckout = false;
    protected $_canUseInternal = true;
    protected $_formBlockType = 'rakutenpayment/form';
    protected $_infoBlockType = 'rakutenpayment/info';
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
         
      $this->getInfoInstance()->setPoNumber($data->getPoNumber());
      $this->getInfoInstance()->setAdditionalData($data->getAdditionalData());
      return $this;
    } 
} 
?>