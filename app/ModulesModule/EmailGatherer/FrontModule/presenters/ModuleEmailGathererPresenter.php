<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use \Nette\Application\UI\Form;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleEmailGathererPresenter extends ModuleBasePresenter
{

    public function renderSave($id){
        $form_values = $this->request->getPost();
        $email = $this->context->moduleEmailGathererModel->findOneBy(array("email" => $form_values["email"]));
        if($email){
            $this->flashMessage("Uvedená e-mailová adresas již byla přihlášena k odběru novinek.", "danger");
        } else {
            $data = array();
            $data['email'] = $form_values['email'];
            $data['page_page_modules_id'] = $id;

            $this->context->moduleEmailGathererModel->insert($data);
            $this->flashMessage("Děkujeme, byl jste přihlášen k odběru novinek.");
        }

        $this->redirect('Default:page', array('id' => $_GET['parent_page_id']));
    }

    protected function createComponentNewsletterForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModuleEmailGatherer:Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);

        $form->addText('email', 'Email: ', 256)
                ->addRule(Form::EMAIL, 'Nezadali jste platnou e-mailovou adresu.')
                ->setRequired('Zadejte e-mailovou adresu');
        $form->addSubmit('create', 'Přihlásit se k odběru novinek!');

        $form->setCustomRenderer();

        return $form;
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->newsletterForm = $this->createComponentNewsletterForm();
    }
}