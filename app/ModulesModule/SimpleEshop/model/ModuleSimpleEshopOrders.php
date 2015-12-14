<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleSimpleEshopOrders extends Base {
    /** @var string */
    protected $tableName = 'module_simpleeshop_orders';

    /**
     * Creates new order from given data.
     * @param array $data
     * @return \Nette\Database\Table\ActiveRow
     */
    public function createOrder($data){
        return $this->insert($data);
    }

    /**
     * Returns all orders for one eshop given by eshop module id.
     * @param array $eshop_module_id
     * @return \Nette\Database\Table\Selection
     */
    public function getOrders($eshop_module_id){
        $params = array(
            "page_page_modules_id" => $eshop_module_id,
        );
        return $this->findBy($params)->order("id", "desc");
    }

    /**
     * Delete order given by parameter.
     * @param array $id
     */
    public function deleteOrder($id){
        $this->delete($id);
    }

    /**
     * Toggles order status. Processed or not processed.
     * @return Nette\Database\Context\ResultSet
     */
    public function toggleOrderStatus($id){
        return $this->query("UPDATE `" . $this->tableName . "` SET `processed` = 1 + `processed` * (-1) WHERE `id` = $id");
    }
}