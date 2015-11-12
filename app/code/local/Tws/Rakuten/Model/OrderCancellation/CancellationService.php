<?php

/**
 * Service class which cancels an order.
  */
class Tws_Rakuten_Model_OrderCancellation_CancellationService extends Varien_Object
{
	
	public function cancelOrder(Mage_Sales_Model_Order $order)
	{	
			
		$xmlRequestFactory	= Mage::getModel('rakuten/Xml_XmlRequestFactory');
		
		$httpClient			= Mage::getModel('rakuten/Client_XmlOverHttp');
		
		$quote				= Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('entity_id', array('eq' => $order->getQuoteId()))->load()->getFirstItem();
      
        $quote->RakutenOrderId=$order->RakutenOrderId; 
		$requestXml			= '';
		$responseXml		= '';
		        
		try {
			$requestXml = $xmlRequestFactory->createOrderCancellationRequest($order, $quote);
             
			$responseXml = $httpClient->sendRaw($requestXml,'orders/setOrderCancelled');
          
		} catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}

		return $result;
	}
	
}

?>