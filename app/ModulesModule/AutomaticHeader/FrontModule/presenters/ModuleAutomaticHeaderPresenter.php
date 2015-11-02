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

class ModuleAutomaticHeaderPresenter extends ModuleBasePresenter
{
    private $menu = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->menu = $this->oParentPresenter->context->moduleAutomaticHeaderModel->getMenu();
        }
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->menu = $this->menu;
    }
}