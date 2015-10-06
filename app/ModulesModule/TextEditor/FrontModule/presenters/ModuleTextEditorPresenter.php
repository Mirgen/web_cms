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

class ModuleTextEditorPresenter extends ModuleBasePresenter
{
    private $text = "";

    protected $moduleTemplateDir = "TextEditor";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->text = $this->oParentPresenter->context->moduleTextEditorModel->findOneBy( array('page_page_modules_id' => $this->iModuleId) )->text;
        }
    }

    public function render(){
        $this->moduleTemplate->text = $this->text;

        return (string) $this->moduleTemplate;
    }
}