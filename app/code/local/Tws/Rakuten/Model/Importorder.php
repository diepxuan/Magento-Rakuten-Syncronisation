<?php
require_once 'app/Mage.php';
Mage::app('default');
$allStores = Mage::app()->getStores();
$start='2015-02-09 00:00:00';
$stop='2015-12-01 23:59:59';
$resource = Mage::getSingleton('core/resource');
function bestellungexist($rakutenid,$shop){
  $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
  $sql = "SELECT
rakutenindex
FROM
".$resource->getTableName('rakutenindex')."
WHERE
shop ='".$shop."' and bestellnummer='".$rakutenid."'";  
$result = $read->fetchAll($sql);
if($result[0]['rakutenindex']):
return  $result[0]['rakutenindex'];
else:
return;
endif;
}
function bestellungmage4($rakutenid,$shop){
  $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
  $sql = "SELECT
ordernr
FROM
".$resource->getTableName('rakutenindex')."
WHERE
shop ='".$shop."' and bestellnummer='".$rakutenid."'";  
$result = $read->fetchAll($sql); 
if($result[0]['ordernr']):     
return  $result[0]['ordernr'];
else:
return;
endif;
}
function bestellungins4($rakutenid,$shop,$orderId,$status){
  $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
 $insert="INSERT INTO ".$resource->getTableName('rakutenindex')." (bestellnummer, shop, ordernr, status1) values ('".$rakutenid."', '".$shop."', '".$orderId."', '".$status."')";
 $write->query($insert); 
  return ;
}
 function customerExists1($email, $websiteId =null)
{
    $customer = Mage::getModel('customer/customer');
    if ($websiteId) {
        $customer->setWebsiteId($websiteId);
      
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
        return true;
      } 
    return false;
}

 $skuarray='';
 function bestand1($productid,$neu=1,$orgbestand=0){
     global $skuarray  ;
      $storeIdactuell = Mage::app()->getStore()->getStoreId();
     Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 $productz = Mage::getModel('catalog/product')->load($productid);
 $stockItem = Mage::getModel('cataloginventory/stock_item')->assignProduct($productz);
 $bestand=$stockItem->getQty();

 if($neu==1){
if($bestand<=0){
    $skuarray[$productid]['Produktid']=$productid;
    $skuarray[$productid]['bestand']=$bestand;  

    $stockItem->setData('stock_id', 1);
    $stockItem->setData('store_id', 1);
    $stockItem->setData('is_in_stock', 1); 
    $stockItem->setData('backorders', 1);
    $stockItem->save();
    $productz->save();
} 
unset($stockItem);
unset($productz);      
 }elseif($neu==2){
  $stockItem->setData('stock_id', 1);
   $stockItem->setData('store_id', 1);
    $stockItem->setData('is_in_stock', 0); 
    $stockItem->setData('backorders', 0);
    $stockItem->save() ;
    $productz->save() ;
    unset($stockItem); 
unset($productz);    
 }
  Mage::app()->setCurrentStore($storeIdactuell);    
 } 
 
foreach ($allStores as $_storeId => $val) 
{
 $storeIdactuell = Mage::app()->getStore()->getStoreId();
 Mage::app()->setCurrentStore($_storeId);
 $userid=Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$_storeId);
$data=array('user'=>$userid,'store'=>$_storeId);
      
if($userid){
    unset($responseXml);
    unset($xml);
    unset($xmlRequestFactory);
    unset($client);
    unset($rawResult);
     $xmlRequestFactory    = Mage::getSingleton('rakuten/xml_xmlRequestFactory');
     $xml            = $xmlRequestFactory->createOrderRequestXml($start,$stop);
     $httpClient            = Mage::getModel('rakuten/client_xmlOverHttp');
      
     try {
     $rakutenorders= unserialize($httpClient->sendRaw($xml,'orders/getOrders'));
      
            } catch(Exception $e) {
             Mage::app()->setCurrentStore($storeIdactuell);
             return; 
            }
        
  if  ($rakutenorders['result']['success']!=1) { Mage::app()->setCurrentStore($storeIdactuell); return ;}
  
     foreach ($rakutenorders['result']['orders']['order'] as $i => $value){
     
         if (!bestellungexist($value['order_no'],$_storeId)){
         if ($value['status']=='editable'){
            
  
                 if($value['client']['gender']=='Herr'){
                 $gender='1';
                    }elseif($value['client']['gender']=='Frau'){
                 $gender='2';   
                 }
                 
       unset($user);
       $user=customerExists1($value['client']['email'],$_storeId);
       if (!$user) {
          $store = Mage::app()->getStore();
          $groupid=Mage::getStoreConfig('tws_rakuten/config_rakuten/gruppe',$_storeId);
          if(!$groupid){$groupid=0;}
          $customer = Mage::getModel('customer/customer');
          $customer ->setWebsiteId($_storeId)
            ->setStore($store)
            ->setGroupId($groupid)
            ->setPrefix($value['client']['gender'])
            ->setFirstname(utf8_encode($value['client']['first_name']))
            ->setLastname(utf8_encode($value['client']['last_name']))
            ->setEmail($value['client']['email'])
            ->setPassword($value['client']['email']);
           try{
    $customer->save();
}
catch (Exception $e) {
    Zend_Debug::dump($e->getMessage());
    
}
       }
       $customer = Mage::getModel('customer/customer');
    $customer->setWebsiteId($_storeId);
$customer->loadByEmail($value['client']['email']);   
    $transaction = Mage::getModel('core/resource_transaction');
     $order = Mage::getModel('sales/order')
        ->setIncrementId($value['order_no'])
        ->setStoreId($_storeId)
        ->setQuoteId(0) ;
        $order->setCustomer_email($customer->getEmail())
        ->setCustomerFirstname($customer->getFirstname())
        ->setCustomerLastname($customer->getLastname())
        ->setCustomerGroupId($customer->getGroupId())
        ->setCustomer_is_guest(0)
        ->setCustomer($customer);                     
if (!$value['client']['phone']){$arrAddressesbilling['telephone']="000";}; 
$billingAddress = Mage::getModel('sales/order_address')
->setStoreId($_storeId)
->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
->setCustomerId($customer->getId())
->setCustomerAddressId($customer->getDefaultBilling())  
->setPrefix($customer->getPrefix())
->setFirstname(utf8_encode($value['client']['first_name']))  
->setLastname(utf8_encode($value['client']['last_name'])  ) 
->setStreet( array(utf8_encode($value['client']['street']." ".$value['client']['street_no'])))
->setCity(utf8_encode($value['client']['city']) )
->setCountry_id($value['client']['country'])
->setRegion('Sachsen')
->setRegion_id('91')
->setPostcode($value['client']['zip_code'])
->setTelephone($value['client']['phone'])     
;       
$order->setBillingAddress($billingAddress);
if (!$value['client']['phone']){$arrAddressesshipping['telephone']="000";};
$shippingAddress = Mage::getModel('sales/order_address')
->setStoreId($_storeId)
->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
->setCustomerId($customer->getId())
->setCustomerAddressId($customer->getDefaultBilling())   
->setPrefix($customer->getPrefix())
->setFirstname(utf8_encode($value['delivery_address']['first_name']) )  
->setLastname(utf8_encode($value['delivery_address']['last_name'])   ) 
->setStreet( array(utf8_encode($value['delivery_address']['street']." ".$value['delivery_address']['street_no'])))
->setCity(utf8_encode($value['delivery_address']['city']) )
->setCountry_id($value['delivery_address']['country'])
->setRegion('Sachsen')
->setRegion_id('91')
->setPostcode($value['delivery_address']['zip_code'])
->setTelephone($value['client']['phone'])        
;   
 Tws_Rakuten_Model_Carrier_Rakutenshipping::unlock(); 
 Tws_Rakuten_Model_Carrier_Rakutenshipping::setDeliveryCosts($value['shipping']);   
    $order->setShippingAddress($shippingAddress)
    ->setShipping_method('rakutenshipping_methode') 
    ->setShippingAmount($value['shipping']) 
    ->setBaseShippingAmount($value['shipping']) 
     ->setShippingDescription('Pauschale Versandkosten');

  
   $payvalue='Rakuten';         
if($value['payment']=='PP'){$payvalue='Vorauskasse';}
if($value['payment']=='CC'){$payvalue='Kreditkarte';}
if($value['payment']=='ELV'){$payvalue='Lastschrift';}
if($value['payment']=='ELV-AT'){$payvalue='Lastschrift Österreich';}
if($value['payment']=='SUE'){$payvalue='Sofortüberweisung';}
if($value['payment']=='CB'){$payvalue='ClickAndBuy';}
if($value['payment']=='INV'){$payvalue='Rechnung';}
if($value['payment']=='INV-AT'){$payvalue='Rechnung Österreich';}
if($value['payment']=='PAL'){$payvalue='Paypal';}
if($value['payment']=='GP'){$payvalue='giropay';}
if($value['payment']=='KLA'){$payvalue='Klarna';}
if($value['payment']=='MPA'){$payvalue='mpass';}
if($value['payment']=='BAR'){$payvalue='Barzahlen';}
if($value['payment']=='YAP'){$payvalue='YAPITAL';} 
$orderPayment = Mage::getModel('sales/order_payment')
->setStoreId($_storeId)
->setCustomerPaymentId(0)
->setMethod('rakutenpayment')
->setPo_number($value['order_no'].' Rechnungsnummer: '.$value['invoice_no'])
->setPay_number('paypal')
->setAdditional_data($payvalue.'('.$value['payment'].') am: '.$value['created'].' Versenden bis: '.$value['max_shipping_date'])
;
$order->setPayment($orderPayment);    
$subTotal = 0;       
      foreach ($value['items']['item'] as $i => $value1){
           unset($_product);
           $_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$value1['product_art_no']); 
          $rate='19' ;
           $rowTotal = round(($value1['price']/119*100),2) * $value1['qty'];
           $tax = round((($rowTotal/100)*19),2);
           $productId=$_product->getId();
$orderItem = Mage::getModel('sales/order_item')
->setStoreId($_storeId)
->setQuoteItemId(0)
->setQuoteParentItemId(NULL)
->setProductId($productId)
->setProductType($_product->getTypeId())
->setQtyBackordered(NULL)
->setTotalQtyOrdered($value1['rqty'])
->setQtyOrdered($value1['qty'])
->setTaxPercent('19')
->setTaxAmount($tax)
->setName($_product->getName())
->setSku($_product->getSku())
->setPrice(round(($value1['price']/119*100),2))
->setBasePrice($_product->getPrice())
->setOriginalPrice($_product->getPrice())
->setRowTotal($rowTotal)
->setBaseRowTotal($rowTotal);
 
$subTotal += $rowTotal;
$order->addItem($orderItem);
           $totalqty = (int)Mage::getModel('cataloginventory/stock_item')
           ->loadByProduct($productId)
           ->getQty();     
$newqty = $totalqty-$value1['qty'];  
Mage::getModel('cataloginventory/stock_item')
       ->loadByProduct($productId)
       ->setQty($newqty)
       ->save();
           unset($_product);   
           unset($n);   
     }                        
$subTotal=$subTotal+round(($subTotal/100*19),2);
$grandTotal=$subTotal+$value['shipping']; 
$taxTotal= round(($grandTotal/119*19),2);
$order
->setSubtotal($subTotal-$taxTotal)
->setBaseSubtotal($subTotal-$taxTotal)
->setTaxAmount($taxTotal)
->setGrandTotal($grandTotal)
->setBaseGrandTotal($grandTotal);      
$transaction->addObject($order);
$transaction->addCommitCallback(array($order, 'place'));
$transaction->addCommitCallback(array($order, 'save'));
$transaction->save();
$res=bestellungins4($value['order_no'],$_storeId,$value['order_no'],$value['status']);

if(isset($skuarray)){
foreach($skuarray as $key => $item){
  
bestand1($item['Produktid'],2,$item['bestand']);
} 
}
         }
    }else{
     $order = Mage::getModel('sales/order')->loadByIncrementId(bestellungmage4($value['order_no'],$_storeId));
      $status=$order->getStatus();
    if($value['status']=='created' && $status=='pending' or $value['status']=='created' && $status=='processing' ){
      $order->setStatus("pending_payment");
      $order->save();  
      }
    if($value['status']=='editable' && $status=='pending' or $value['status']=='editable' && $status=='pending_payment'){
      $order->setStatus("processing");
      $order->save();  
     }
    if($value['status']=='editable' && $status=='complete'){
     $order->setStatus("swt");
     $order->save();    
    }
    if($value['status']=='cancelled' && $status=='processing'){
      $order->setStatus("canceled");
      $order->save();
     }
    if($value['status']=='payout' && $status=='waz'){
      $order->setStatus("complete");
      $order->save();
    }
    if($value['status']=='shipped' && $status='complete'){
      $order->setStatus("waz");
      $order->save();
    }
  
    }
  }  
 }
 Mage::app()->setCurrentStore($storeIdactuell);
}  
?>