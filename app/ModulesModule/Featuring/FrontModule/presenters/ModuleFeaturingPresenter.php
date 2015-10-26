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

class ModuleFeaturingPresenter extends ModuleBasePresenter
{
    private $features = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->features = $this->oParentPresenter->context->moduleFeaturingModel->findBy( array('page_page_modules_id' => $this->iModuleId, 'enabled' => 1) )->order('id DESC');
        }
    }

    public function render(){
        $this->moduleTemplate->moduleId = $this->iModuleId;
        $this->moduleTemplate->features = $this->features;

        return (string) $this->moduleTemplate;
    }
}