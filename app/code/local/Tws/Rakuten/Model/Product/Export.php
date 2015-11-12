<?php

class Tws_Rakuten_Model_Product_Export
{    
    const SELECTION_MODE_MARKED_ONLY = 1;
    const SELECTION_MODE_ALL = 2;
    protected $_selectionMode = self::SELECTION_MODE_MARKED_ONLY;
    protected $_productSelectService = null;
    protected $_processedSimpleProductIds = null;
    protected $_result = null;
    protected $_processableProducts = null;
    protected $_productHelper = null;
    protected $_variantGroupsExportStates = null;
    protected $_variantGroups = null;
    protected $_rawVariantGroups = null;
    public function __construct()
    {
        $this->_variantGroupsExportStates    = array();
        $this->_processedSimpleProductIds    = array();
        $this->_processableProducts          = array();
        $this->_productHelper                = Mage::helper('rakuten/product');
        $this->_result                       = Mage::getModel('rakuten/product_result');
        $this->_rawVariantGroups             = array();
     
    }
    public function deleteProduct(Mage_Catalog_Model_Product $product)
    {
        $parentConfigurable = null;
        $referenceProduct   = null;
        $exportConfigurable = false;
          $httpClient            = Mage::getModel('rakuten/Client_XmlOverHttp');
        
        if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $parentConfigurable = $this->_productHelper->getParentConfigurable($product);
            if($parentConfigurable === null) {
                $postdataimage=array(
                'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
                'product_art_no'=>$product->getSku()
     );
      $rakutenprodimage= unserialize( $httpClient->sendRaw($postdataimage,' products/deleteProduct'));
            } else {
                $postdataimage=array(
                'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
                'variant_art_no'=>$product->getSku()
     );
      $rakutenprodimage= unserialize( $httpClient->sendRaw($postdataimage,' products/deleteProductVariant'));
            }
        } elseif($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
             $postdataimage=array(
                'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
                'product_art_no'=>$product->getSku()
     );
      $rakutenprodimage= unserialize( $httpClient->sendRaw($postdataimage,' products/deleteProduct'));
       
        }
         unset($referenceProduct);
         unset($exportConfigurable);
         unset($httpClient);
         unset($product);
         unset($rakutenprodimage);
    }
    public function exportProduct(Mage_Catalog_Model_Product $product)
    {
        $parentConfigurable = null;
        $referenceProduct   = null;
        $exportConfigurable = false;

        if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $parentConfigurable = $this->_productHelper->getParentConfigurable($product);
            if($parentConfigurable === null) {
                $referenceProduct = $product;
            } else {
             // $referenceProduct = $product;  //Prüfen ob Hauptprodukt Existiert 
             return;
            } 
        } elseif($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
          //  $referenceProduct   = $product;
           $exportConfigurable = false;
           return;
        }
        if(isset($referenceProduct)){
         $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('entity_id', array('eq' => $referenceProduct->getId()));  
          }  
        if($productCollection->count() < 1) {
            return $this->_result;
        }
        
        if(!$exportConfigurable) {
         $this->_processSimpleProducts($productCollection);
        } else {
       
        }
        unset($referenceProduct);
         unset($exportConfigurable);
         unset($httpClient);
         unset($product);
         unset($rakutenprodimage);
        return;
    }
    protected function _processSimpleProducts(Mage_Catalog_Model_Resource_Product_Collection $simpleProducts)
    {
        if($simpleProducts->count() < 1) {
            return;
        }
        
        foreach($simpleProducts as $simpleProduct) {
            $this->_createExportableProduct(
                        $simpleProduct, 
                        null
                    );
                    unset($referenceProduct);
         unset($exportConfigurable);
         unset($httpClient);
         unset($product);
         unset($rakutenprodimage);
        }
    }
    protected function _createExportableProduct(Mage_Catalog_Model_Product $simpleProduct, Mage_Catalog_Model_Product $configurableProduct = null)
    {
        $simpleProduct = Mage::getModel('catalog/product')->load($simpleProduct->getId());
        $httpClient            = Mage::getModel('rakuten/Client_XmlOverHttp');
        $useParent     = is_object($configurableProduct);
        $storeId = Mage::app()->getStore()->getStoreId();
        if($useParent) {
            $configurableProduct = Mage::getModel('catalog/product')->load($configurableProduct->getId());
        }
      $existrakuten=array(
      'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
      'format'=>'php',
      'search_field'=>'product_art_no',
      'search'=>$simpleProduct->getSku());
      $delivery=3;
      if ($simpleProduct->getDelivery_time()=='2-7 Werktage'){$delivery=3;};
      if ($simpleProduct->getDelivery_time()=='4-5 Tage'){$delivery=3;}; 
      $stock=$this->_productHelper->getRakutenStock($simpleProduct);
      if($stock>0){
          $available=1;
          $visible=1;
      }else{
       $available=0;
       $visible=0;    
      }
      if($simpleProduct->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED){
          $stock=0;
          $available=0;
       $visible=0;   
      }
    //$shopcatp=$simpleProduct->getRakutenkat();
    $catID=''; 
    $catID=$simpleProduct->getCategoryIds();
    $categoryId='';
    $categoryId = (isset($catID[0]) ? $catID[0] : null);
    $shopcat=Mage::getModel('catalog/category')->load($categoryId)->getData('rakutencat');
    
     if(!$shopcat and !$shopcatp){
   //  return;    
     }elseif(!$shopcat and $shopcatp!=''){
     // $shopcat=$shopcatp;   
     }
     $scatd=Mage::getModel('catalog/category')->load($categoryId);
   
     $pcatnamed=  $scatd->getName();
     $rakutenpath=43221;
     if(strpos($pcatnamed,'Akkus')!==false){$rakutenpath=37541;} 
if(strpos($pcatnamed,'Externe Akkus')!==false){$rakutenpath=37541;} 
if(strpos($pcatnamed,'Ersatzteile')!==false){$rakutenpath=79165;}
if(strpos($pcatnamed,'Multimedia')!==false){$rakutenpath=43221;}
if(strpos($pcatnamed,'Datenkabel')!==false){$rakutenpath=37466;}
if(strpos($pcatnamed,'Handy-Halter')!==false){$rakutenpath=6532;}
if(strpos($pcatnamed,'Eingabehilfe')!==false){$rakutenpath=79165;}
if(strpos($pcatnamed,'Bluetooth Headsets')!==false){$rakutenpath=33301;}
if(strpos($pcatnamed,'Kabel Headsets')!==false){$rakutenpath=33401;}
if(strpos($pcatnamed,'Flip- und Bookcases')!==false){$rakutenpath=76945;}
if(strpos($pcatnamed,'Taschen')!==false){$rakutenpath=18661;}
if(strpos($pcatnamed,'Schutzcover')!==false){$rakutenpath=76965;}
if(strpos($pcatnamed,'Displayschutzfolien')!==false){$rakutenpath=78341;}
if(strpos($pcatnamed,'Schutzfolien')!==false){$rakutenpath=78341;}
if(strpos($pcatnamed,'Tischlader und Syncstationen')!==false){$rakutenpath=37456;}
if(strpos($pcatnamed,'KFZ-Ladezubehör')!==false){$rakutenpath=37461;}
if(strpos($pcatnamed,'Ladegeräte')!==false){$rakutenpath=37451;}
if(strpos($pcatnamed,'Speicher')!==false){$rakutenpath=37476;}

if(strpos($pcatnamed,'Adapter & Verbinder')!==false){$rakutenpath=43221;}
if(strpos($pcatnamed,'Antennen')!==false){$rakutenpath=43221;}
if(strpos($pcatnamed,'Sonstiges')!==false){$rakutenpath=43221;}

if(strpos($pcatnamed,'Werkzeuge')!==false){$rakutenpath=43221;} 
       unset($scatd);
 
     $tax=1;
     $percent='19';
     $tax_helper = Mage::getSingleton('tax/calculation');
     $tax_request = $tax_helper->getRateOriginRequest();
     $tax_request->setProductClassId($simpleProduct->getTaxClassId());
     $taxid = (string) $tax_helper->getRate($tax_request);
   
     if($taxid=='10') {$tax='10';}  
     if($taxid=='19') {$tax='1';}  
     if($taxid=='7'){$tax=2;}   
     if($percent=='0') {$tax='3';}   
     if($percent=='10.7'){$tax='4';}   
     if($percent=='12') {$tax='11';} 
     if($percent=='20'){ $tax='12';} 
    
          
      $versandgruppe=$simpleProduct->getAttributeText('rakutenshipgroup');
      if(!$versandgruppe){
        $versandgruppe=1;  
      }
     $price=round(($this->_productHelper->getPriceConsideringTaxes($simpleProduct)/100*Mage::getStoreConfig('tws_rakuten/config_rakuten/price')+$this->_productHelper->getPriceConsideringTaxes($simpleProduct)),2);
     $price_reduced='0.00';
     $postdatasimple=array();   
     $postdatasimple =  array(
                  'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
                  'format'=>'php',
                  'ean'=>$simpleProduct->getEan(),
                  'name'=>$simpleProduct->getName(),
                  'producer'=>$simpleProduct->getAttributeText('manufacturer'),
                  'price'=>$price,
                  'delivery'=>$delivery,
                  'stock'=>$stock,
                  'shipping_group'=>$versandgruppe,
                  'description'=>$simpleProduct->getDescription(),
                  'shop_category_id'=>$shopcat,
                  'rakuten_category_id'=>$rakutenpath,
                  'price_reduced'=>$price_reduced,
                  'price_reduced_type'=>'VK',
                  'available'=>$available,
                  'tax'=>$tax,
                  'connect'=>'1'
    
                );
     
   $unit = false;
   try {
     $unit=strtolower($simpleProduct->getBase_price_base_unit());  
   } catch(Exception $e) {
           //  Mage::app()->setCurrentStore($storeIdactuell);
             return; 
            }
   
   if($unit=='pcs'){
   $unit='kg';    
   }
  /* if($unit=='ml' or $unit=='l' or $unit=='g'or $unit=='kg'or $unit=='cm'or $unit=='m'){
      $baseinci=$simpleProduct->getBase_price_amount();
      $baseamount=$simpleProduct->getBase_price_base_amount();
      if(!$baseinci){
       $baseinci=0;   
       $baseamount=0;   
      }
      
      $postdatasimplebaseprice =  array(
                  'baseprice_unit'=>$unit,
                  'baseprice_volume'=>$baseamount.'.00',
                  'inci'=>$baseinci.'.00'
                 // 'baseprice_volume'=>$simpleProduct->base_price_base_unit()
                 );
      $postdatasimple=array_merge($postdatasimple,$postdatasimplebaseprice)  ;  
   
       }   */
      
      $postdataconfig=array();          
      $responseXml = unserialize($httpClient->sendRaw($existrakuten,'products/getProducts'));
      
     if($responseXml['result']['products']['paging'][0]['total']==0){
          if($useParent) {
          $postdataconfig=array(
           'product_art_no'=>$configurableProduct, 
           'variant_art_no'=>$simpleProduct->getSku()); 
           }else{
        $postdataconfig=array(
         'product_art_no'=>$simpleProduct->getSku(),
         'visstock_policy'=>$visible,
         'stock_policy'=>1
        );   
      }
      $postdata='';
      $postdata=array_merge($postdatasimple,$postdataconfig)  ;  
        $responseXml2 = unserialize($httpClient->sendRaw($postdata,'products/addProduct'));
         
     }elseif($responseXml['result']['products']['paging'][0]['total']==1){ 
          if($useParent) {
          $postdataconfig=array(
           'variant_art_no'=>$simpleProduct->getSku()); 
      }else{
        $postdataconfig=array(
         'product_art_no'=>$simpleProduct->getSku(),
         'visible'=>$visible,
           'stock_policy'=>1
        );   
      }
         $postdata=array_merge($postdatasimple,$postdataconfig)  ;  
       $responseXml2 = unserialize($httpClient->sendRaw($postdata,'products/editProduct'));  
       
     } 
    
    $postdataimage='';
     $postdataimage=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'format'=>'php',
     'product_art_no'=>$simpleProduct->getSku()
     );
      $rakutenprodimage= unserialize($httpClient->sendRaw($postdataimage,' products/getProductThumbnails'));
      if($rakutenprodimage['result']['success']!=0){
      foreach ($rakutenprodimage['result']['thumbnails']['thumbnail'] as $e => $produktimageorg){
     $postdataimage='';
     $postdataimage=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'image_id'=>$produktimageorg['image_id']
     );
      $rakutenprodimage= $httpClient->sendRaw($postdataimage,' products/deleteProductImage');
    }}
      $images = $this->_productHelper->getImages($simpleProduct);
        if(sizeof($images) > 0) {
            foreach($images as $image) {
               $postdataimage='';
     $postdataimage=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'format'=>'php',
     'product_art_no'=>$simpleProduct->getSku(),
     'url'=> $image['url'],
     'auto_visible'=>1 
     );
      $rakutenprodimage= unserialize( $httpClient->sendRaw($postdataimage,' products/addProductImage')); 
      
            }
        }
     $postdatalink='';
     $postdatalink=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'format'=>'php',
     'product_art_no'=>$simpleProduct->getSku()
     );
      $rakutenprodlink= unserialize($httpClient->sendRaw($postdatalink,' products/getProductLinks'));
 
      if($rakutenprodlink['result']['success']!=0){
      foreach ($rakutenprodlink['result']['links']['link'] as $e => $produktlinkorg){
     $postdatalink='';
     $postdatalink=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'link_id'=>$produktlinkorg['link_id']
     );
     
        
      $rakutenprodlink= $httpClient->sendRaw($postdatalink,' products/deleteProductLink');
    }}
   
     $postdataimage=array(
     'key'=>Mage::getStoreConfig('tws_rakuten/config_rakuten/username',$storeId),
     'format'=>'php',
     'product_art_no'=>$simpleProduct->getSku(),
     'name'=> 'Homepage',
     'url'=> $simpleProduct->getProductUrl(),
     'auto_visible'=>1 
     );  
                
     $rakutenprodimage= $httpClient->sendRaw($postdataimage,' products/addProductLink');     
     unset($storeId);
     unset($product);
     unset($useParent);
     unset($existrakuten);
     unset($simpleProduct);
     unset($shopcat);
     unset($postdatasimple);
     unset($postdataconfig);
     unset($responseXml2);
     unset($postdatalink);
     unset($httpClient);
     unset($rakutenprodlink);
     unset($rakutenprodimage);
        return ;
     
    }  
}   

?>