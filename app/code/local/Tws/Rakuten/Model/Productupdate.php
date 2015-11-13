<?php
require_once 'app/Mage.php';
Mage::app('default');
$resource = Mage::getSingleton('core/resource');
$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 

$write = Mage::getSingleton('core/resource')->getConnection('core_write'); 

$query = "SELECT rakutenindex FROM ".$resource->getTableName('rakutenindex')." WHERE bestellnummer='xxxx'";
$result = $read->fetchAll($query);
//$result = $read->query($query);


//$result =$write->query($insert); 

?>
