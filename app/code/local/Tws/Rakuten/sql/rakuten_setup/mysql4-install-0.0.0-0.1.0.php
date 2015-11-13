<?php
$installer = $this;
$installer->startSetup();
$resource = Mage::getSingleton('core/resource');
$installer->run("
CREATE TABLE IF NOT EXISTS rakutenindex(
  rakutenindex int(11) NOT NULL AUTO_INCREMENT,
  bestellnummer varchar(255) CHARACTER SET latin1 COLLATE latin1_german2_ci DEFAULT NULL,
  shop varchar(255) CHARACTER SET latin1 COLLATE latin1_german2_ci DEFAULT NULL,
  ordernr varchar(255) CHARACTER SET latin1 COLLATE latin1_german2_ci DEFAULT NULL,
  status1 varchar(255) CHARACTER SET latin1 COLLATE latin1_german2_ci DEFAULT NULL,
  PRIMARY KEY (rakutenindex)
) 
		
");


$installer->endSetup();
	 