<?php

/**
 * List all attributes and values in CSV format unsorted
 * 
 * Author: Eshcole Peets
 * Site: TopSpot Internet Marketing
 * 
 */

require_once('app/Mage.php'); //Path to Magento
Mage::app();

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$f=fopen('siteAttributes.csv','w'); //create CSV
$csvHead=array();//switched header to match attribute label

// Base Magento attributes we don't need
$baseAttr=array(
    'name',
    'description',
    'short_description',
    'sku',
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'cost',
    'weight',
    'manufacturer',
    'meta_title',
    'meta_keyword',
    'meta_description',
    'image',
    'small_image',
    'thumbnail',
    'media_gallery',
    'old_id',
    'group_price',
    'tier_price',
    'color',
    'news_from_date',
    'news_to_date',
    'gallery',
    'status',
    'url_key',
    'url_path',
    'minimal_price',
    'is_recurring',
    'recurring_profile',
    'visibility',
    'custom_design',
    'custom_design_from',
    'custom_design_to',
    'custom_layout_update',
    'page_layout',
    'category_ids',
    'options_container',
    'required_options',
    'has_options',
    'image_label',
    'small_image_label',
    'thumbnail_label',
    'created_at',
    'updated_at',
    'country_of_manufacture',
    'msrp_enabled',
    'msrp_display_actual_price_type',
    'msrp',
    'tax_class_id',
    'gift_message_available',
    'price_type',
    'sku_type',
    'weight_type',
    'price_view',
    'shipment_type',
    'links_purchased_separately',
    'samples_title',
    'links_title',
    'links_exist',
    'allowed_to_quotemode',
    'group_allow_quotemode',
    'quotemode_conditions',
    'cost_tier_price'
);

$customAttr=array();

// Build CSV Header from Custom Attr labels only
// Client needs the heder in the following format for easier manipulation in excel
// --attr--|--attr_sort-- (with no dashes)

$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
    ->getItems();

foreach($attributes as $attr){
    if(!in_array($attr->getAttributecode(),$baseAttr)){
        $csvHead[]=$attr->getAttributecode();

        $attribute = Mage::getSingleton('eav/config')
        //change 'size' to the label of the attribute you need all the values for
        ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attr->getAttributecode());

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
            
            $l=array();
            foreach($options as $o){
                $l[]=$o['label'];
            }

            $i=0;
            $limit=count($l);
            while($i < $limit){
                $customAttr[$attr->getAttributecode()][$i]=$l[$i];
                $i++;
            }
        }
    }
}

fputcsv($f,$csvHead); //push array of attr labels to csv for CSV headers

$i=0; 

// TODO: code a dynamic way of finding the attribute with most values instead of hard coding it like this 
$max=count($customAttr['b_od']); 

// generate an array of just the custom attributes
$customAttrKeys=array_keys($customAttr); 

// If any of the custom attributes are found in the CSV header, put the value of the attribute in that row according to the index (i)
while($i < $max){
    $l=array();
    foreach($csvHead as $col){
        if(in_array($col,$customAttrKeys)){
            $l[$col] = $customAttr[$col][$i];
        }else{
            $l[$col] = '';
        } 
    }
    fputcsv($f,$l);
    $i++; 
}

fclose($f);
echo 'CSV created!'.PHP_EOL;
