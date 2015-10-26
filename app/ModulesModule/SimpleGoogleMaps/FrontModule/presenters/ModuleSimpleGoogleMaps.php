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

class ModuleSimpleGoogleMapsPresenter extends ModuleBasePresenter
{
    private $address = "";

    protected function loadModuleData(){
        $moduleData = $this->oParentPresenter->context->moduleSimpleGoogleMapsModel->find(array('page_page_modules_id' => $this->module->id));
        if($moduleData){
            $this->address = $moduleData->address;
        }
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->address = $this->address;
    }
}