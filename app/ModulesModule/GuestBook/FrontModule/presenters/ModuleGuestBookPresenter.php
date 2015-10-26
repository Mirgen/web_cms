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

class ModuleGuestBookPresenter extends ModuleBasePresenter
{
    private $guestBookPosts = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->guestBookPosts = $this->oParentPresenter->context->moduleGuestBookModel->findBy( array('page_page_modules_id' => $this->iModuleId) )->order('id DESC');
        }
    }

    public function renderAdd($id){
        $form_values = $this->request->getPost();

        $data = array();
        $data['name'] = $form_values['name'];
        $data['email'] = $form_values['email'];
        $data['text'] = $form_values['text'];
        $data['page_page_modules_id'] = $id;

        $this->context->moduleGuestBookModel->insert($data);
        $this->flashMessage("Zpráva byla úspěšně přidána.");
        $this->redirect('Default:page#guestBook' . $id, array('id' => $_GET['parent_page_id']));
    }

    public function render(){
        $this->moduleTemplate->addPostLink = $this->oParentPresenter->link('ModuleGuestBook:Add', array('id' => $this->iModuleId, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $this->moduleTemplate->guestBookPosts = $this->guestBookPosts;

        return (string) $this->moduleTemplate;
    }
}