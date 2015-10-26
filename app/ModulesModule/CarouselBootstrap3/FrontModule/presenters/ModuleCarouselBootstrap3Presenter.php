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

class ModuleCarouselBootstrap3Presenter extends ModuleBasePresenter
{
    private $items = "";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->items = $this->oParentPresenter->context->moduleCarouselBootstrap3Model->findBy(array('page_page_modules_id' => $this->module->id))->order('order');
        }
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->items = $this->items;
    }
}