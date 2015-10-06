<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleEmailGathererPresenter extends ModuleBasePresenter
{
    private $emails = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->emails = $this->oParentPresenter->context->moduleEmailGathererModel->findBy(array('page_page_modules_id' => $this->module->id));
        }
    }

    public function renderDelete($id){
        $this->context->moduleEmailGathererModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function renderDeleteEmail($id){
        $email = $this->context->moduleEmailGathererModel->findOneBy( array('id' => $id) );
        $this->load($email->page_page_modules_id);

        $this->context->moduleEmailGathererModel->delete($id);

        $this->flashMessage('Email byl smazán.');
        $this->redirect('Page:edit' . "#module-" . $this->module->id, array('id' => $this->module->page_id));
    }

    public function renderUnsubscribeEmail($id){
        $email = $this->context->moduleEmailGathererModel->find($id);
        $this->load($email->page_page_modules_id);

        $email->update( array("unsubscribed" => $email->unsubscribed *(-1)+1) ); // changes from 1 to 0 or from 0 to 1

        $this->flashMessage('Email byl odhlášen z odběru novinek.');
        $this->redirect('Page:edit' . "#module-" . $this->module->id, array('id' => $this->module->page_id));
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->emails = $this->emails;
    }
}