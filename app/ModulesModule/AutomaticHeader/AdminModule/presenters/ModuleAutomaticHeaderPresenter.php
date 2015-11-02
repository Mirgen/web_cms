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

class ModuleAutomaticHeaderPresenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $menu = NULL;

    public function initialize()
    {
        // add new setting available for the module
        $this->addNewModuleSetting("Zobrazovat menu?", "menu_visible", 1);
        $this->addNewModuleSetting("Přichytit menu (při skrolování zůstane nahoře)?", "sticky", 1);
        $this->addNewModuleSetting("Zobrazovat logo?", "logo", 1);
        $this->addNewModuleSetting("Logo text", "logo_text", "Awsome web text");
        $this->addNewModuleSetting("Efekt postupného zobrazení?", "fade_effect", "1");
        parent::initialize();
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->menu = $this->oParentPresenter->context->moduleAutomaticHeaderModel->getMenu();
        } else {
            $this->menu = $this->context->moduleAutomaticHeaderModel->getMenu();
        }
    }

    public function renderDelete($id){
        parent::renderDelete($id);
    }

    protected function createComponentAdminAddForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link("Module" . $this->moduleName . ':Save', array('id' => $this->module->id));
        $form->setAction($url_save);
        $form->addUpload('img', 'Obrázek:')
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Musíte zadat obrázek.');
        $form->setCustomRenderer();

        return $form;
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->menu = $this->menu;
    }
}