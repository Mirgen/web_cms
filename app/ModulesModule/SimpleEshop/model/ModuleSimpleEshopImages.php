<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleSimpleEshopImages extends Base {
    /** @var string */
    protected $tableName = 'module_simpleeshop_images';

    /**
     * Returns images given by parameters.
     * 
     * @param int $product_id ID of product
     * @param int $enabled flag if image is enabled or disabled
     * 
     * @return \Nette\Database\Table\Selection images
     */
    public function getImagesByProductID($product_id, $enabled = NULL){
        $params = array(
            "product_id" => $product_id,
        );

        if(NULL != $enabled){
            $params["enabled"] = $enabled;
        }

        return $this->findBy($params);
    }

    /**
     * Set current main image as normal image. You have to set product id.
     * 
     * @param int $product_id
     * @return \Nette\Database\Table\ResultSet
     * */
    public function disableCurrentMainImage($product_id){
        return $this->query("UPDATE `" . $this->tableName . "` SET `main` = 1 + `main` * (-1) WHERE `main` = 1 AND `product_id` = $product_id");
    }
    

    /**
     * Disables or neables image.
     * 
     * @param int $image_id 
     * @return Nette\Database\Context\ResultSet
     */
    public function enableDisableImage($image_id){
        $this->query("UPDATE `" . $this->tableName . "` SET `enabled` = 1 + `enabled` * (-1) WHERE `id` = $image_id");
        return $this->find($image_id);
    }

}