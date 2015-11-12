<?php

class Tws_Rakuten_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
      $this->setTemplate('tws/rakuten/payment/info.phtml');
    }

    public function toPdf()
    {
     $this->setTemplate('tws/rakuten/payment/pdf.phtml');
       return $this->toHtml();
    }
}
