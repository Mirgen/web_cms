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


class PageModules extends Base {
    /** @var string */
    protected $tableName = 'page_modules';

    public function getAllModules($online = 1){
        $query =  " SELECT pmp.page_id, pmp.position, pmp.enabled, pmi.id as id, p.name as page_name, pmp.id as occurence_id, p.final_url_text as page_url_text, pm.name as module_name"
                . " FROM page_modules_presence pmp "
                . " LEFT JOIN page p ON (p.id = pmp.page_id) "
                . " LEFT JOIN page_modules_instance pmi ON (pmp.page_module_instance_id = pmi.id) "
                . " LEFT JOIN page_modules pm ON (pmi.module_id = pm.id) "
                . " WHERE p.online = $online "
                . " AND p.deleted = 0 ";

        return $this->query($query);
    }

    public function getAllModulesForSelect($online = "1 OR 0"){
        $modules = $this->getAllModules($online);
        $modulesArray = array();

        foreach($modules as $module){
            $modulesArray[$module->id] = $module->module_name . " na stránce " . $module->page_name;
        }
        return $modulesArray;
    }

    public function getAllActivePageModules() {
        $aParameters = array('enabled' => 1);
        $oPageModules = $this->findBy($aParameters);
        return $oPageModules;
    }

    public function getPageModulesForSelect() {
        $oPageModules = $this->getAllActivePageModules();
        $aPageModules = array();

        foreach($oPageModules as $oPageModule) {
            $aPageModules[$oPageModule->id] = $oPageModule->name;
        }
        return $aPageModules;
    }

    public function getModule($iModuleId){
        return $this->findOneBy(array('id' => $iModuleId));
    }

    public function loadClass($iModuleId){
        $module = $this->getModule($iModuleId);
        $class = 'App\AdminModule\Presenters\\' . $module->class_name.'Presenter';
        return new $class;
    }

    public function loadAdminModules($iPageId){
        $query =  " SELECT pm.name, pm.class_name, pmi.id as id, pmp.position, pmp.enabled, pmp.page_id, pmp.id as occurence_id, pm.id as class_id "
                . " FROM page_modules_presence pmp "
                . " LEFT JOIN page_modules_instance pmi ON (pmp.page_module_instance_id = pmi.id) "
                . " LEFT JOIN page_modules pm ON (pm.id = pmi.module_id) "
                . " WHERE pmp.page_id = $iPageId "
                . " ORDER BY position ASC ";
        $modules = $this->query($query);

        return $modules;
    }

    public function loadFrontModules($iPageId){
        $query =  " SELECT pm.class_name, pmi.id as id, pmp.position, pmp.enabled, pmp.page_id, pmp.id as occurence_id, pm.id as class_id "
                . " FROM page_modules_presence pmp "
                . " LEFT JOIN page_modules_instance pmi ON (pmp.page_module_instance_id = pmi.id) "
                . " LEFT JOIN page_modules pm ON (pm.id = pmi.module_id) " 
                . " WHERE pmp.page_id = $iPageId "
                . " AND pmp.enabled = 1 "
                . " AND pm.enabled = 1 "
                . " ORDER BY position ASC ";
        $modules = $this->query($query);
        return $modules;
    }
}