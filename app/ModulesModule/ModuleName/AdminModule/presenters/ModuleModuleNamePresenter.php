<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

 /*
 * @author Jiri Kvapil
 */

class ModuleModuleNamePresenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $articles = NULL;

    protected function initialize()
    {
        // add new setting available for the module
        // $this->addNewModuleSetting("Title", "key", "value");
        parent::initialize();
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            // load artecles, e.g. from Database, file, etc.
            $this->articles = array(
                array("title" => "Article one", "text" => "Lorem ipsum dolor sit amet."),
                array("title" => "Second article", "text" => "This is new article. Lorem ipsum dolor sit amet."),
                array("title" => "Third article", "text" => "Hello. Lorem ipsum dolor sit amet."),
            );
        }
    }

    /**
     * add new methods, e.g. render methods, actions, components, ...
     */

    public function renderDelete($id){
        $this->context->moduleModuleNameModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->articles = $this->articles;
    }
}