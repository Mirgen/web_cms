<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleReferencePresenter extends ModuleBasePresenter
{
    private $references = NULL;

    protected $moduleTemplateDir = "Reference";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->references = $this->oParentPresenter->context->moduleReferenceModel->findBy( array('page_page_modules_id' => $this->iModuleId, 'enabled' => 1) )->order('id DESC');
        }
    }

    public function render(){
        $this->moduleTemplate->moduleId = $this->iModuleId;
        $this->moduleTemplate->references = $this->references;

        return (string) $this->moduleTemplate;
    }
}