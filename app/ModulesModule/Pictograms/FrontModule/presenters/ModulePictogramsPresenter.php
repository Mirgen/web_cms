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

class ModulePictogramsPresenter extends ModuleBasePresenter
{
    private $pictograms = NULL;

    protected $moduleTemplateDir = "Pictograms";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->pictograms = $this->oParentPresenter->context->modulePictogramsModel->findBy( array('page_page_modules_id' => $this->iModuleId, 'enabled' => 1) )->order('id DESC');
        }
    }

    public function render(){
        $this->moduleTemplate->pictograms = $this->pictograms;

        return (string) $this->moduleTemplate;
    }
}