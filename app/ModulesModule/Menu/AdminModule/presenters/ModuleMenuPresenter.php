<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

 /*
 * @author Jiri Kvapil
 */

class ModuleMenuPresenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $menu = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->menu = $this->oParentPresenter->context->moduleMenuModel->findBy(array('page_page_modules_id' => $this->module->id) )->order('order DESC');
        }
    }

    /**
     * add new methods, e.g. render methods, actions, components, ...
     */

    public function renderDelete($id){
        $this->context->moduleMenuModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->menu = $this->menu;
    }
}