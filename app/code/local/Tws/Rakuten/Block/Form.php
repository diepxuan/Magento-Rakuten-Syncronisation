<?php

class Tws_Rakuten_Block_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
    parent::_construct();
     $this->setTemplate('tws/rakuten/payment/form.phtml');
    }

}
?>