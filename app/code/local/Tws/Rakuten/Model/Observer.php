<?php
class Tws_Rakuten_Model_Observer
{
    protected $product = null;
    protected $producte = null;
    protected $storeI = null;
    protected $entities = null;
    protected $productIds = null;
    protected $productId = null;
    protected static $currentlySavedOrderId = -1;
    protected static $isSameSavedOrder = false;
    protected static function setCurrentOrderId($orderId)
    {
        if(self::$currentlySavedOrderId === $orderId) {
            self::$isSameSavedOrder = true;
        } else {
            self::$isSameSavedOrder = false;
        }
        self::$currentlySavedOrderId = $orderId;
    }
    public function atributaftersave(Varien_Event_Observer $observer)
    {
     $entities = $observer->getEvent()->getData();
            $productIds = $entities['product_ids'];
     
            foreach($productIds as $productId){
            
       $this->productaftersave($observer,$productId);      
            } 
     
    }
    public function StockItemSaveAfter(Varien_Event_Observer $observer)
    {   
      $productId = $observer->getData('item')->getData('product_id');
      
         $this->productaftersave($observer,$productId);    
            
    
    }
	public function productaftersave(Varien_Event_Observer $observer, $productid=0)
	{       
     
             if($productid==0){$prodid=$observer->getData('product')->getId();}else{$prodid=$productid;}
            $product = Mage::getModel('catalog/product')->load($prodid);
            $store_id=$product->getStoreIds();
          
            if($store_id[0]==1){
            $storeIdactuell = Mage::app()->getStore()->getStoreId();
            
            Mage::app()->setCurrentStore($store_id[0]);
             
          
              $exportService  = Mage::getModel('rakuten/product_export');
              $result            = null;
             $helper            = Mage::helper('rakuten');
             $productHelper  = Mage::helper('rakuten/product');
             $errMsg            = '';
             
          if(isset($product['rakuten'])and $product['rakuten']!=0 and $product['status']==1){
          
         
          $result = $exportService->exportProduct($product); 
            
  
           
            }else{
            $result = $exportService->deleteProduct($product);           
 
   
            } 
            unset($result);  unset($product);
              Mage::app()->setCurrentStore($storeIdactuell);  
			}
            }
    public function updateproduct ($producte,$storeI)
    {
       
  
    
 /*       $produktpreis=round($row['11'],2); 
    
 //  $rakutensimple= unserialize(file_get_contents('http://webservice.rakuten.de/merchants/products/getProducts?key='.$rakutenkey.'&format=php&search='.$row['2'].'&search_field=product_art_no'));
   
   if ($row['14']=='simple'){      
     if($row['1']=='') : 
     // echo '<pre>'.print_r('Hauptprodukt', true).'</pre>';
   //   echo     'http://webservice.rakuten.de/merchants/products/editProduct?key='.$rakutenkey.'&product_art_no='.$row['2'].'&stock='.$row['6'].'&stock_policy='.$row['7'].'&available='.$row['7'].'&price='.$produktpreis.'&'.$price_reduced.'';
   //     echo     file_get_contents('http://webservice.rakuten.de/merchants/products/editProduct?key='.$rakutenkey.'&product_art_no='.$row['2'].'&stock='.$row['6'].'&stock_policy='.$row['7'].'&available='.$row['7'].'&price='.$produktpreis.'&'.$price_reduced.'');
      
      elseif  ($row['1']!='') :
   //   echo '<pre>'.print_r('Variante', true).'</pre>';
   //      echo  'http://webservice.rakuten.de/merchants/products/editProductVariant?key='.$rakutenkey.'&variant_art_no='.$row['2'].'&stock='.$row['6'].'&stock_policy='.$row['7'].'&available='.$row['7'].'&price='.$produktpreis.'&'.$price_reduced.'';
        $rakutensimple=  file_get_contents('http://webservice.rakuten.de/merchants/products/editProductVariant?key='.$rakutenkey.'&variant_art_no='.$row['2'].'&stock='.$row['6'].'&stock_policy='.$row['7'].'&available='.$row['7'].'&price='.$produktpreis.'&'.$price_reduced.'');
   //    echo '<pre>Produkt ID:'.print_r($rakutensimple, true).'</pre>';
     endif;
   //    echo 'unsere SKU' .$row['2'];
         }
   if ($row['14']=='configurable'){
    //    echo     'http://webservice.rakuten.de/merchants/products/editProduct?key='.$rakutenkey.'&product_art_no='.$row['2'].'&stock_policy='.$row['7'].'&visible=1';
   //     echo     file_get_contents('http://webservice.rakuten.de/merchants/products/editProduct?key='.$rakutenkey.'&product_art_no='.$row['2'].'&stock_policy='.$row['7'].'&visible=1'); 
       
   }     */
  
   }	
    public function salesOrderSaveAfter(Varien_Event_Observer $observer)
    {  
        $order = $observer->getData('order');
        self::setCurrentOrderId($order->getId());
        $this->_cancelRakutenOrder($order);
        return $this;
    }
    public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();
        $order = $track->getShipment()->getOrder();
        $RakutenOrderId=$this->bestellungexist1($order->getIncrementId());

        if(   
            strlen($RakutenOrderId['bestellnummer']) < 1 
        ) {
         return $this;  
        }
          $storeIdactuell = Mage::app()->getStore()->getStoreId();
          Mage::app()->setCurrentStore($RakutenOrderId['shop']);
          $shipmentExportService = Mage::getModel('rakuten/ShipmentExport_ExportService');
          $result = $shipmentExportService->exportShipment($RakutenOrderId['bestellnummer'],$track->getNumber(),$track->getTitle());
          Mage::app()->setCurrentStore($storeIdactuell);  
          return $this;
    }
    
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
 
        $shipment    = Mage::getModel('sales/order_shipment')->load($observer->getData('shipment')->getId());
        $order        = $shipment->getOrder();
        $RakutenOrderId=$this->bestellungexist1($order->getIncrementId());

        if(   
            strlen($RakutenOrderId[0]) < 1 
        ) {
         return $this;  
        }
      
         return $this;
    }
    protected function bestellungexist1($rakutenid){
         $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
  $sql = "SELECT
rakutenindex.bestellnummer,
rakutenindex.shop
FROM
rakutenindex
WHERE  ordernr='".$rakutenid."'";  

$result = $read->fetchAll($sql);

        
if($result):
return  $result[0];
else:
return;
endif;
}
    protected function _cancelRakutenOrder(Mage_Sales_Model_Order $order)
    {
       $RakutenOrderId=$this->bestellungexist1($order->getIncrementId());
           
        if(
             strlen($RakutenOrderId['bestellnummer'])> 1  &&  
            $order->getData('state') === 'canceled'
        ) {
            $order->RakutenOrderId=$RakutenOrderId['bestellnummer'];
            $col = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('relation_parent_id', $order->getId())
                ->load();
                  
            if($col->count() > 0) {
                return $this;
            }
            $storeIdactuell = Mage::app()->getStore()->getStoreId();
             Mage::app()->setCurrentStore($RakutenOrderId['shop']);
            $orderCancellationService = Mage::getModel('rakuten/OrderCancellation_CancellationService');
            $result = null;
            $result = $orderCancellationService->cancelOrder($order);
            
            Mage::app()->setCurrentStore($storeIdactuell);  
        }  
        return $this;
    }
}
?>