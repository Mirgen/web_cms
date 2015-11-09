<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

use Nette,
	App\Model, 
    Nette\Application\UI;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleContactFormPresenter extends ModuleBasePresenter
{
    private $moduleData = NULL;

    public function initialize(){
        $this->addNewModuleSetting("Nadpis", "title", "Napište mi");
        $data['page_page_modules_id'] = $this->module->id;
        $this->oParentPresenter->context->moduleContactFormModel->insert($data);
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->moduleData = $this->oParentPresenter->context->moduleContactFormModel->findOneBy(array('page_page_modules_id' => $this->module->id));
        }
    }

    public function renderSave($id){
        $form_values = $this->request->getPost();
        $data['email'] = $form_values['email'];
        $data['message_ok'] = $form_values['message_ok'];
        $oModule = $this->context->moduleContactFormModel->findOneBy( array('page_page_modules_id' => $id) );
        if($oModule){
            $oModule->update($data);
        } else {
            $data['page_page_modules_id'] = $id;
            $this->context->moduleContactFormModel->insert($data);
        }
        $this->flashMessage('Změny byly uloženy.');
        $this->redirect('Page:edit', array('id' => $_GET['parent_page_id']));
    }

    public function renderDelete($id){
        $this->context->moduleContactFormModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->moduleData = $this->moduleData;
    }
}