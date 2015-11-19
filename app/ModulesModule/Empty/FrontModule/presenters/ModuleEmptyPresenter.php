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

class ModuleEmptyPresenter extends ModuleBasePresenter
{
    //private $articles = "";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            // load data for module
        }
    }

    public function setTemplateVariables(){
        //$this->moduleContentTemplate->articles = $this->articles;
    }
}