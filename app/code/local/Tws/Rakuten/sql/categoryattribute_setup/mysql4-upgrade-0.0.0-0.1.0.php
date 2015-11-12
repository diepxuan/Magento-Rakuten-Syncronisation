<?php
$installer = $this;
$installer->startSetup();

 
$installer->addAttribute("catalog_category", "rakutencat",  array(
    "type"     => "varchar",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Rakuten Kategorie",
    "input"    => "text",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "0",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	 
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));

  $installer->addAttribute( Mage_Catalog_Model_Product::ENTITY, 'rakuten', array(
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Rakuten Verkauf',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => '',
            'position' => 98,
            
        ) );
 $installer->addAttribute( Mage_Catalog_Model_Product::ENTITY, 'rakutenshipgroup', array(
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Rakuten Shippinggroup',
            'input' => 'select',
            'source' => 'eav/entity_attribute_source_table',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 1,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => '',
            'position' => 99,
            'option' => 
  array (
    'values' => 
    array (
      1 => '1',
      2 => '2',
      3 => '3',
      4 => '4',
      5 => '5',
      6 => '6',
      7 => '7',
      8 => '8',
      9 => '9',
      10 => '10',
      11 => '11',
      12 => '12',
      13 => '13',
      14 => '14',
      15 => '15',
      16 => '16',
      17 => '17',
      18 => '18',
      19 => '19',
      20 => '20',
    ),
  ),
            
        ) );
$installer->endSetup();
	 