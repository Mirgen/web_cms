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

class ModuleTextEditorPresenter extends ModuleBasePresenter
{
    private $moduleData = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->moduleData = $this->oParentPresenter->context->moduleTextEditorModel->findOneBy(array('page_page_modules_id' => $this->module->id));
        }
    }

    public function initialize(){
        $data['page_page_modules_id'] = $this->module->id;
        return $this->oParentPresenter->context->moduleTextEditorModel->insert($data);
    }

    public function renderSave($id){
        $form_values = $this->request->getPost();
        $data['text'] = $form_values['text'];
        $oModule = $this->context->moduleTextEditorModel->findOneBy( array('page_page_modules_id' => $id) );
        if($oModule){
            $oModule->update($data);
        } else {
            $data['page_page_modules_id'] = $id;
            $this->context->moduleTextEditorModel->insert($data);
        }
        $this->flashMessage('Změny byly uloženy.');
        $this->redirect('Page:edit', array('id' => $_GET['parent_page_id']));
    }

    public function renderDelete($id){
        $this->context->moduleTextEditorModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->moduleData = $this->moduleData;
    }
}