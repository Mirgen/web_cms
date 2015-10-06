<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

use Nette,
	App\Model,
    Nette\Forms\Controls,
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
        if($this->oParentPresenter){
            $moduleData = $this->oParentPresenter->context->moduleSimpleGoogleMapsModel->find(array('page_page_modules_id' => $this->module->id));
            if($moduleData){
                $this->address = $moduleData->address;
            }
        }
    }

    public function renderDelete($id){
        $this->context->moduleSimpleGoogleMapsModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function renderSave($id){ // $id is module ID
        $form_values = $this->request->getPost();
        $this->loadModuleFromDB($id);

        $data['address'] = $form_values['address'];

        $module = $this->context->moduleSimpleGoogleMapsModel->findOneBy(array('page_page_modules_id' => $id));
        if($module){
            $module->update($data);
        } else {
            $data['page_page_modules_id'] = $id;
            $this->context->moduleSimpleGoogleMapsModel->insert($data);
        }

        $this->flashMessage('Změny byly uloženy.');
        $this->redirect('Page:edit', array('id' => $_GET['parent_page_id']));
    }

    protected function createComponentSimpleGoogleMapsForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModuleSimpleGoogleMaps:Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);

        $form->addText('address', 'Změnit adresu: ', 255)->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat adresu.');

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')
                ->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->address = $this->address;
        $this->moduleContentTemplate->form = $this->createComponentSimpleGoogleMapsForm();
    }
}