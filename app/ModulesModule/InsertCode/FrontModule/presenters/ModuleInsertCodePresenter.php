<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette,
	App\Model, 
    Nette\Application\UI;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleInsertCodePresenter extends ModuleBasePresenter
{
    private $code = "";

    protected $moduleTemplateDir = "InsertCode";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $moduleData = $this->oParentPresenter->context->moduleInsertCodeModel->findOneBy(array('page_page_modules_id' => $this->iModuleId));
            if($moduleData){
                $this->code = $moduleData->code;
            }
        }
    }

    public function render(){
        $this->moduleTemplate->code = $this->code;

        return (string) $this->moduleTemplate;
    }
}