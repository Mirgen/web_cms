<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageModules
 *
 * @author Jiri Kvapil
 */
namespace App\Model;

class PageModuleRegister extends Base {
    /** @var string */
    protected $tableName = 'page_modules_presence';

    public function getModule($id){
        $query =  " SELECT pm.name, pm.class_name, pmi.id as id, pmp.position, pmp.enabled, pmp.page_id, pmp.id as presence_id, pm.id as class_id "
                . " FROM page_modules_instance pmi "
                . " LEFT JOIN page_modules_presence pmp ON (pmi.id = pmp.page_module_instance_id) "
                . " LEFT JOIN page_modules pm ON (pm.id = pmi.module_id) "
                . " WHERE pmi.id = $id ";
        $module = $this->query($query);
        return $module->fetch();
    }
}