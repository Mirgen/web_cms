<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleSimpleEshop extends Base {
    /** @var string */
    protected $tableName = 'module_simpleeshop_products';

    /**
     * function getProductsByModuleID gets all product and its images for one 
     * e-shop module given by $module_id
     * 
     * @param int $module_id id of e-shop module 
     * @return array
     */
    public function getProductsByModuleID($module_id, $enabled = NULL){
        $productModel = new ModuleSimpleEshopProducts($this->database);
        return $productModel->getProductsByModuleID($module_id, $enabled);
    }

    /**
     * Get one product with all the data (images). 
     * 
     * @param int $product_id id of product 
     * @return array
     */
    public function getProduct($product_id, $enabled = NULL){
        $productModel = new ModuleSimpleEshopProducts($this->database);
        return $productModel->getProduct($product_id, $enabled);
    }

    /**
     * get one image given by image ID $image_id
     * 
     * @param int $image_id id of image
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function getImage($image_id){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        return $imageModel->find($image_id);
    }

    /**
     * function deleteProduct deletes one product given by parameter and deletes
     * also all images of this product
     * 
     * @param int $product_id id of product 
     * @return void
     */
    public function deleteProduct($product_id){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        $images = $imageModel->getImagesByProductID($product_id);
        foreach($images as $image){
            $image->delete();
        }
        $this->delete($product_id);
    }

    /**
     * Insert new image into database
     * 
     * @param array $data
     * @return \Nette\Database\Table\ActiveRow
     * */
    public function insertImage($data){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        return $imageModel->insert($data);
    }

    /**
     * Insert main image into database
     * 
     * @param array $data
     * @return \Nette\Database\Table\ActiveRow
     * */
    public function insertMainImage($data){
        $this->disableCurrentMainImage($data["product_id"]);
        $data["main"] = 1;
        return $this->insertImage($data);
    }

    /**
     * Set current main image as normal image. You have to set product id.
     * 
     * @param int $product_id
     * @return \Nette\Database\Table\ResultSet
     * */
    public function disableCurrentMainImage($product_id){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        return $imageModel->disableCurrentMainImage($product_id);
    }

    /**
     * Disables or neables product.
     * 
     * @param int $product_id 
     * @return Nette\Database\Context\ResultSet
     */
    public function enableDisable($product_id){
        return $this->query("UPDATE `" . $this->tableName . "` SET `enabled` = 1 + `enabled` * (-1) WHERE `id` = $product_id");
    }

    /**
     * Disables or neables image.
     * 
     * @param int $image_id 
     * @return Nette\Database\Context\ResultSet
     */
    public function enableDisableImage($image_id){
        $imageModel = new ModuleSimpleEshopImages($this->database);
        return $imageModel->enableDisableImage($image_id);
    }

     /**
     * Creates new order from given data.
     * @param array $data
     * @return \Nette\Database\Table\ActiveRow
     */
    public function createOrder($data){
        $orderModel = new ModuleSimpleEshopOrders($this->database);
        return $orderModel->insert($data);
    }

    /**
     * Returns all orders for one eshop given by eshop module id.
     * @param array $eshop_module_id
     * @return \Nette\Database\Table\Selection
     */
    public function getOrders($eshop_module_id){
        $orderModel = new ModuleSimpleEshopOrders($this->database);
        return $orderModel->getOrders($eshop_module_id);
    }

    /**
     * Delete order given by parameter.
     * @param array $id
     */
    public function deleteOrder($id){
        $orderModel = new ModuleSimpleEshopOrders($this->database);
        $orderModel->deleteOrder($id);
    }

    /**
     * Toggles order status. Processed or not processed.
     * @return Nette\Database\Context\ResultSet
     */
    public function toggleOrderStatus($id){
        $orderModel = new ModuleSimpleEshopOrders($this->database);
        return $orderModel->toggleOrderStatus($id);
    }

    /**
     * Deletes whole eshop module given by parameter.
     */
    public function deleteEshop($eshop_id){
        $orderModel = new ModuleSimpleEshopOrders($this->database);
        $productModel = new ModuleSimpleEshopProducts($this->database);

        // delete orders
        $orderModel->deleteBy(array("page_page_modules_id" => $eshop_id));
        // find all products
        $products = $productModel->findBy(array("page_page_modules_id" => $eshop_id));

        foreach($products as $product){
            $this->deleteProduct($product->id);
        }
    }
}