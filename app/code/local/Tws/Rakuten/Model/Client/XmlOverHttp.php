<?php

class Tws_Rakuten_Model_Client_XmlOverHttp extends Varien_Object {
	
    protected $httpClient;
	protected $url_string='';
	public function __construct()
	{	
     $endpoint            = Mage::getStoreConfig('rakuten/endpoint/url');
	
	}
	
	public function sendRaw($xml,$urlcode)
	{	 $endpoint            =   'http://webservice.rakuten.de/merchants';
        
        $rakutenurl            = $endpoint.'/'.$urlcode;
       
       $url_string='';
        foreach($xml as $key=>$value) { $url_string .= $key.'='.$value.'&'; }
         
        $url_string = rtrim($url_string, '&');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $rakutenurl);
        curl_setopt($ch,CURLOPT_POST, count($xml));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $url_string);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
     
        try {
			$response	= curl_exec($ch);
		} catch(Zend_Http_Client_Adapter_Exception $e) {
		}
		curl_close($ch);
        return $response;
	}
}

?>