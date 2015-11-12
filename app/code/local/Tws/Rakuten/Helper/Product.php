<?php

class Tws_Rakuten_Helper_Product extends Mage_Core_Helper_Abstract
{   
	const TAX_CLASS_STANDARD	= 'Standard';
	
	const TAX_CLASS_REDUCED		= 'Reduced';
	
	const TAX_CLASS_FREE		= 'Free';
	
    protected $_eanValidator = null;

	public function __construct()
	{
		//$this->_eanValidator = Mage::getSingleton('rakuten/Helper_Ean')->isValidEan();
	}
	
	public function hasValidEan(Mage_Catalog_Model_Product $product)
	{
		return $this->_eanValidator->isValid($this->getEan($product));
	}
	
	public function getEan(Mage_Catalog_Model_Product $product)
	{
		return $product->getEan();
	}
    public function getSku(Mage_Catalog_Model_Product $product)
    {
        return $product->getSku();
    }
	
	public function getImages(Mage_Catalog_Model_Product $product)
	{
		$images        = array();
		$galleryImages = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages();
		$imageUrl      = '';
		$imageCaption  = '';

		if(is_object($galleryImages) && $galleryImages->count() > 0) {
			foreach($galleryImages as $image) {
				$images[] = array(
					'url'		=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$image['file'],
					'caption'	=> $image['label']
				);
			}
		}
		
		return $images;
	}
	
	
	
	public function getPriceConsideringTaxes(Mage_Catalog_Model_Product $product)
	{ 
		$taxHelper			= Mage::helper('tax');
		$priceIncludesTax	= (bool)Mage::getStoreConfig('tax/calculation/price_includes_tax')? true : null;
    
		return $taxHelper->getPrice(
			$product,
			$product->getPrice(),
			$priceIncludesTax
		);
	}
	

	public function getRakutenStock(Mage_Catalog_Model_Product $product)
	{
		
		$defaultStock				= 0;
		$stock						= 0;
		$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
		
		if(!$stockItem->getIsInStock()) {
			return 0;
		}
		$stock = (integer) $stockItem->getQty();
				return $stock;
	}
	
	public function getRakutentEndDate(Mage_Catalog_Model_Product $product)
	{
		$endDate = new Zend_Date();
		
		if(
			$product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED
			
		) {
			$endDate->setTimestamp(time() - 86400);
		} else {
			$endDate->setTimestamp(time() + 31536000);
		}
		
		return $endDate;
	}
	
	
	public function getRakutenDeliveryTime(Mage_Catalog_Model_Product $product)
	{
		$attributeId  = Mage::getStoreConfig('rakuten/product_attributes/delivery_time');
		$deliveryTime = Mage::getStoreConfig('rakuten/product_attributes/default_delivery_time');
		
		if(
			strlen($attributeId) > 0 && 
			$product->hasData($attributeId) && 
			@preg_match("/^[0-9]+$/", $product->getData($attributeId))
		) {
			$deliveryTime = $product->getData($attributeId);
		}
		
		return $deliveryTime;
	}
	
	public function getParentConfigurable(Mage_Catalog_Model_Product $simpleProduct)
	{
		$parentConfigurable = null;
		
		if($simpleProduct->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
			$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($simpleProduct->getId());
			if(isset($parentIds[0])) {
				$parentConfigurable = Mage::getModel('catalog/product')->load($parentIds[0]);
			}
		}
		
		return $parentConfigurable;
	}
	
	public function getErrorDescription($errorType, $errorCode = '')
	{
		$description = '';
		
		switch($errorType) {
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_FIELD_IS_INVALID :
				$description = $this->__('Invalid value for field').' "<i><b>'.$this->__($this->getLabelForFieldName($errorCode)).'</b></i>".';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_REQUIRED_FIELD_IS_EMPTY :
				$description = $this->__('Missing value for field').' "<i><b>'.$this->__($this->getLabelForFieldName($errorCode)).'</b></i>".';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_PRODUCT_NOT_EXISTS_IN_RAKUTEN :
				$description = $this->__('Product is unknown in Rakutenmarketplace').'.';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_PRODUCT_NEGATIVE_STOCK :
				$description = $this->__('Product stock is lower than zero.');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_RAKUTEN_SERVER_ERROR :
				$description = $this->__('Internal error on Rakuten server').'.';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_NOT_AUTHORIZED :
				$description = $this->__('You are not authorized to execute the requested functionality on Rakuten');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_INVALID_DATA :
				$description = $this->__('The provided data was incorrect');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_INVALID_MODIFICATION :
				$description = $this->__('Ivalid modification of element');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_NO_CATEGORIZATION :
				$description = $this->__('The product is not mapped to neither a marketplace nor a shop category');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_PRODUCT_NOT_SELLABLE :
				$description = $this->__('The referenced product cannot be sold');
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_MISSING_VALUE_FOR_ATTRIBUTE :
				$description = $this->__('Missing value mapping for attribute').' "'.$errorCode.'".';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_VARIANT_GROUP_NOT_EXISTS :
				$description = $this->__('Variant group does not exist on Rakuten').' "'.$errorCode.'".';
				break;
			case Tws_Rakuten_Model_Validation_ValidationInterface::ERROR_UNDEFINED :
			default :
				$description = $this->__('Undefined Error');
		}
		
		return $description;
	}
	
	public function getLabelForFieldName($fieldName)
	{	
		$productAttributeModel	= Mage::getModel('eav/config')->getAttribute('catalog_product', $fieldName);
		$categoryAttributeModel	= Mage::getModel('eav/config')->getAttribute('catalog_category', $fieldName);

		$label = $productAttributeModel->getFrontendLabel();
		
		if (!(bool)$label)
			$label = $categoryAttributeModel->getFrontendLabel();
		
		if (!(bool)$label)
			$label = $fieldName;
			
		return $label;
	}
}

?>