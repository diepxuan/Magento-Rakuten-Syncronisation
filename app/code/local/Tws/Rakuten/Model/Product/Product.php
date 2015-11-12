<?php

class Tws_Rakuten_Model_Product_Product
//class Tws_Rakuten_Model_Product_Product implements Tws_Rakuten_Model_Product_Interface_Offer
{
	protected $_id = '';
	protected $_name = '';
	protected $_ean = '';
	protected $_description = '';
	protected $_shortDescription = '';
	protected $_images = null;
	protected $_marketplaceCategoryCode = '';
	protected $_manufacturer = '';
	protected $_price = null;
	protected $_taxGroup = '';
	protected $_availibility = 0;
	protected $_endDate = null;
	protected $_deliveryTime = null;
	protected $_variantInfo = null;
	protected $_isUnavailable = false;
	public function __construct()
	{
		$this->_images = Mage::getModel('rakuten/product_image_collection');
	}
	public function getId()
	{
		return $this->_id;
	}
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	public function getEan()
    {
        return $this->_ean;
    }
    public function getSku()
	{
		return $this->_sku;
	}
	public function setEan($ean)
    {
        $this->_ean = $ean;
        return $this;
    }
    public function setSku($sku)
	{
		$this->_sku = $sku;
		return $this;
	}
	public function getDescription()
	{
		return $this->_description;
	}
	public function setDescription($description)
	{
		$this->_description = $description;
		return $this;
	}
	public function getShortDescription()
	{
		return $this->_shortDescription;
	}
	public function setShortDescription($shortDescription)
	{
		$this->_shortDescription = $shortDescription;
		return $this;
	}
	public function addImage(Tws_Rakuten_Model_Product_Image $image)
	{
		$this->_images->append($image);
		return $this;
	}
	public function getImages()
	{
		return $this->_images;
	}
	public function getManufacturer()
	{
		return $this->_manufacturer;
	}
    public function setManufacturer($manufacturer)
	{
		$this->_manufacturer = $manufacturer;
		return $this;
	}
	public function getPrice()
	{
		return $this->_price;
	}
	public function setPrice($price)
	{
		$this->_price = $price;
		return $this;
	}
	public function getTaxGroup()
	{
		return $this->_taxGroup;
	}
	public function setTaxGroup($taxGroup)
	{
		$this->_taxGroup = $taxGroup;
		return $this;
	}
	public function getAvailibility()
	{
		return $this->_availibility;
	}
	public function setAvailibility($availibility)
	{
		$this->_availibility = $availibility;
		return $this;
	}
	public function getEndDate()
	{
		return $this->_endDate;
	}
	public function setEndDate(Zend_Date $endDate)
	{
		$this->_endDate = $endDate;
		return $this;
	}
	public function getDeliveryTime()
	{
		return $this->_deliveryTime;
	}
	public function setDeliveryTime($deliveryTime)
	{
		$this->_deliveryTime = $deliveryTime;
		return $this;
	}
	public function getVariantInfo()
	{
		return $this->_variantInfo;
	}
	public function setVariantInfo(Tws_Rakuten_Model_Product_Variant_Info $variantInfo = null)
	{
		$this->_variantInfo = $variantInfo;
		return $this;
	}
	public function setIsUnavailable($unavailable)
	{
		$this->_isUnavailable = (boolean) $unavailable;
		return $this;
	}
	public function getIsUnavailable()
	{
		return $this->_isUnavailable;
	}
}

?>