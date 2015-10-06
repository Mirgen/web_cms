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

class ModuleGuestBookPresenter extends ModuleBasePresenter
{

    private $guestBookPosts = NULL;

    public function renderDelete($id){
        $this->context->moduleGuestBookModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    protected function loadModuleData(){
        $this->guestBookPosts = $this->oParentPresenter->context->moduleGuestBookModel->findBy(array('page_page_modules_id' => $this->module->id));
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->guestBookPosts = $this->guestBookPosts;
    }
}