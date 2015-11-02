<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleAutomaticHeader extends Base {
    /** @var string */
    protected $tableName = 'page';

    public function getMenu($parent_id = NULL, $enabled = NULL){
        $menu = array();
        $query =  " SELECT * "
                . " FROM page "
                . " WHERE deleted  = 0 ";
                if(NULL === $parent_id){
                    $query .= " AND id_parent is NULL ";
                } else {
                    $query .= " AND id_parent = $parent_id ";
                }
                if($enabled !== NULL){
                    $query .= " AND online = $enabled ";
                }
                $query .= " ORDER BY `order` ASC ";

        $result = $this->query($query);
        if($result){
            foreach($this->query($query)->fetchAll() as $menuItem){
                $menuItem->sub_menu = $this->getMenu($menuItem->id, $enabled);
                $menu[] = $menuItem;
            }
        }
        return $menu;
    }
}