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

class ModuleFooterSocialPresenter extends ModuleBasePresenter
{

    public function initialize()
    {
        // add new setting available for the module
        $this->addNewModuleSetting("Odkaz na váš Facebook", "facebook", "");
        $this->addNewModuleSetting("Odkaz na váš Twitter", "twitter", "");
        $this->addNewModuleSetting("Odkaz na váš Instagram", "instagram", "");
        $this->addNewModuleSetting("Váše e-mailová adresa", "email", "");
        $this->addNewModuleSetting("Copyright od (rok)", "copyrightfrom", "");
        $this->addNewModuleSetting("Copyright text", "copyrighttext", "");
        parent::initialize();
    }

    public function renderDelete($id){
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        //$this->moduleContentTemplate->articles = $this->articles;
    }
}