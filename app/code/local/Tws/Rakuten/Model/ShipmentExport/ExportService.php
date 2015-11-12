<?php

class Tws_Rakuten_Model_ShipmentExport_ExportService
{
	
	public function exportShipment($orderID, $shipmentID,$shipmetTitle)
	{    
		$xmlRequestFactory	= Mage::getSingleton('rakuten/Xml_XmlRequestFactory');
		$httpClient			= Mage::getModel('rakuten/Client_XmlOverHttp');
		$requestXml			= '';
		$responseXml		= '';
		 		
		try {
			$requestXml = $xmlRequestFactory->createShipment($orderID, $shipmentID,$shipmetTitle); 
		} catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}

		try {
			$responseXml = $httpClient->sendRaw($requestXml,'orders/setOrderShipped'); 
		} catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}

		return $result;
	}
	
}

?>