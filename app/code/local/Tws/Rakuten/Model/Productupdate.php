<?php
require_once 'app/Mage.php';
Mage::app('default');

$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 

$write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
$table_prefix = Mage::getConfig()->getTablePrefix();
$query = "SELECT rakutenindex FROM rakutenindex WHERE bestellnummer='xxxx'";
$result = $read->fetchAll($query);
//$result = $read->query($query);


//$result =$write->query($insert); 

?>
