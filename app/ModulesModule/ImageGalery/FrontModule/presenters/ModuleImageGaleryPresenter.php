<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette,
	App\Model,
    Nette\Forms\Controls,
    Nette\Application\UI;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleImageGaleryPresenter extends ModuleBasePresenter
{
    private $images = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->images = $this->oParentPresenter->context->moduleImageGaleryModel->findBy( array('page_page_modules_id' => $this->iModuleId, 'enabled' => 1) )->order('order');
        }
    }

    public function render(){
        $this->moduleTemplate->pageId = $this->oParentPresenter->getParameter('id');
        $this->moduleTemplate->images = $this->images;

        return (string) $this->moduleTemplate;
    }
}