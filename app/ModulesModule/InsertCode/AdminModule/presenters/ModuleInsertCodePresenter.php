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

class ModuleInsertCodePresenter extends ModuleBasePresenter
{
    private $code = "";

    protected function initialize()
    {
        $this->addNewModuleSetting("Nadpis", "title", "Zadejte nadpis.");
        parent::initialize();
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $moduleData = $this->oParentPresenter->context->moduleInsertCodeModel->findOneBy(['page_page_modules_id' => $this->module->id]);
            if($moduleData){
                $this->code = $moduleData->code;
            }
        }
    }

    public function renderSave($id){
        $form_values = $this->request->getPost();
        $data['code'] = $form_values['code'];
        $oModule = $this->context->moduleInsertCodeModel->findOneBy( array('page_page_modules_id' => $id) );
        if($oModule){
            $oModule->update($data);
        } else {
            $data['page_page_modules_id'] = $id;
            $this->context->moduleInsertCodeModel->insert($data);
        }
        $this->flashMessage('Změny byly uloženy.');
        $this->redirect('Page:edit', array('id' => $_GET['parent_page_id']));
    }

    public function renderDelete($id){
        $this->context->moduleInsertCodeModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->code = $this->code;
    }
}