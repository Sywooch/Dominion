<?php
/**
 * User: Rus
 * Date: 24.08.13
 * Time: 21:48
 */

require_once __DIR__ . "/../application/configs/config.php";


$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
//
//$registry = Zend_Registry::getInstance();

$export = new BrainPriceImport_Connect(
    new Buzz\Client\Curl(), new Buzz\Message\Request(), new Buzz\Message\Response(),
    'adlabs',
    'Ru$LAN'
);


$export->setHost('http://api.brain.com.ua');

$sid = $export->getAuthSID();


$grabber = new BrainPriceImport_PriceDataGrabber($export);

$categories = $grabber->getCategories();


$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><price></price>');

$categoryRoot = $xml->addChild('categories');


$vendors = $grabber->getVendors();


foreach ($grabber->getCategories() as $value) {


    if (1 == $value->categoryID) {
        continue;
    }

    echo "Add category {$value->name}\r\n";

    $categoryXmlElement = $categoryRoot->addChild('category');
    $categoryXmlElement->addAttribute('id', $value->categoryID);
    $categoryXmlElement->addAttribute('name', $value->name);

    $products = $grabber->getProducts($value->categoryID);

    echo "adding " . count($products) . " items for {$value->name}...\r\n";

    foreach ($products as $productItem) {
        $productXmlElement = $categoryXmlElement->addChild('product');

        foreach ($vendors as $vendor) {
            if ($vendor['vendorID'] == $productItem->vendorID  && $vendor['categoryID'] == $productItem->categoryID) {
                $vendorName = $vendor['name'];
                break;
            }
        }

        $productXmlElement->addChild('name', str_replace("&", "&amp;", $productItem->name));

        $stocks = implode(',', $productItem->stocks);

        $productXmlElement->addChild('stocks', str_replace("&", "&amp;", $stocks));
        $productXmlElement->addChild('product_code', str_replace("&", "&amp;", $productItem->product_code));
        $productXmlElement->addChild('warranty', str_replace("&", "&amp;", $productItem->warranty));
        $productXmlElement->addChild('is_archive', str_replace("&", "&amp;", (int) $productItem->is_archive));
        $productXmlElement->addChild('vendor', str_replace("&", "&amp;", $vendorName));
        $productXmlElement->addChild('articul', str_replace("&", "&amp;", $productItem->articul));
        $productXmlElement->addChild('volume', str_replace("&", "&amp;", $productItem->volume));
        $productXmlElement->addChild('is_new', str_replace("&", "&amp;", (int) $productItem->is_new));
        $productXmlElement->addChild('categoryID', str_replace("&", "&amp;", $productItem->categoryID));
        $productXmlElement->addChild('price', str_replace("&", "&amp;", $productItem->price));

    }

    echo "finished\r\n";

}

$xmlString = $xml->saveXML('test.xml');