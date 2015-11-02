<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;


class ModuleMenu extends Base {
    /** @var string */
    protected $tableName = 'module_menu';

    public function getMenu($page_page_modules_id, $parent_id = NULL, $enabled = NULL){
        $menu = array();
        $query =  " SELECT mm.*, p.final_url_text, p.name as page_text "
                . " FROM module_menu mm "
                . " LEFT JOIN page p ON (p.id = mm.page_id) "
                . " WHERE page_page_modules_id = $page_page_modules_id ";
                if(NULL === $parent_id){
                    $query .= " AND parent_id is NULL ";
                } else {
                    $query .= " AND parent_id = $parent_id ";
                }
                if($enabled !== NULL){
                    $query .= " AND enabled = $enabled ";
                }
                $query .= " ORDER BY `order` ASC ";

        $result = $this->query($query);
        if($result){
            foreach($this->query($query)->fetchAll() as $menuItem){
                $menuItem->sub_menu = $this->getMenu($page_page_modules_id, $menuItem->id, $enabled);
                $menu[] = $menuItem;
            }
        }
        return $menu;
    }
}