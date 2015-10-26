<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleMenuPresenter extends ModuleBasePresenter
{
    private $menu = "";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->menu = $this->oParentPresenter->context->moduleMenuModel->getMenu($this->module->id, NULL, 1);
        }
    }

    public function render(){
        $this->moduleTemplate->menu = $this->menu;

        return (string) $this->moduleTemplate;
    }
}