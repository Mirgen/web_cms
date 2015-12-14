<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleSimpleEshopProducts extends Base {
    /** @var string */
    protected $tableName = 'module_simpleeshop_products';

    /**
     * Returns products given by parameters.
     * 
     * @param int $module_id ID of e-shop module
     * @param int $enabled flag if product is enabled or disabled
     * 
     * @return \Nette\Database\Table\Selection products
     */
    public function getProductsByModuleID($module_id, $enabled = NULL){
        $productsResult = array();
        $params = array(
                "page_page_modules_id" => $module_id
            );

        if(NULL != $enabled){
            $params["enabled"] = $enabled;
        }

        $products = $this->findBy($params);
        foreach($products as $product){
            $product = $product->toArray();
            $this->completeProduct($product, $enabled);
            $productsResult[] = $product;
        }

        return $productsResult;
    }

    /**
     * Get one product with all the data (images). 
     * 
     * @param int $product_id id of product 
     * @return array
     */
    public function getProduct($product_id, $enabled){
        $params = array(
                    "id" => $product_id
                );

        if(NULL != $enabled){
            $params["enabled"] = $enabled;
        }
        $product = $this->findOneBy($params)->toArray();
        $this->completeProduct($product, $enabled);

        return $product;
    }

    /**
     * Adds additional data to product. For example calculates prices, discount,
     * adds images, nice URL segment, ...
     * 
     * @param int $enabled if images should be enabled or disabled
     * @param array &$product
     */
    private function completeProduct(&$product, $enabled){
        $this->calculatePrice($product);
        $product["nice_url_segment"] = $this->getProductNiceURLSegment($product);
        $product = array_merge($product, $this->getProductImages($product["id"], $enabled));
    }

    /**
     * function getImages gets all images of one product given by parameter
     * $product_id
     * 
     * @param int $enabled if we want onlz enabled images
     * @param int $product_id id of product 
     * @return \Nette\Database\Table\Selection images
     */
    public function getImages($product_id, $enabled = NULL){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        return $imageModel->getImagesByProductID($product_id, $enabled);
    }

    /**
     * Receives product array and process its price and discounts. Result is 
     * ne array with calculated price and discount.
     * e-shop module given by $module_id
     * 
     * @param array $product Product array.
     */
    private function calculatePrice(&$product){

        $product["discount"] = 0;
        if(isset($product["discount_percentage"]) && $product["discount_percentage"] > 0){
            $product["discount"] = ($product["price"] / 100) * $product["discount_percentage"];
        } else if(isset($product["discount_amount"]) && $product["discount_amount"] > 0){
            $product["discount"] = $product["discount_amount"];
        }

        $product["final_price"] = $product["price"] - $product["discount"];
    }

    /**
     * Get array of images of product given by $product_id. 
     * 
     * @param int $product_id id of product 
     * @param bool $enabled if only enabled images should be loaded
     * @return array
     */
    private function getProductImages($product_id, $enabled){
        $images = $this->getImages($product_id, $enabled)->fetchAll();

        $productImages = array();

        foreach($images as $image){
            $image = $image->toArray();

            // set main image:
            if($image["main"] == 1){
                $productImages["main_image"] = $image;
            // other images goes to "images"
            } else {
                $productImages["images"][] = $image;
            }
        }
        return $productImages;
    }

    private function getProductNiceURLSegment($product){
        return \UrlExtended::friendly_url($product['id'].'-'.$product['title']);
    }
}