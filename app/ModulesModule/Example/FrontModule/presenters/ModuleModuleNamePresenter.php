<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleExamplePresenter extends ModuleBasePresenter
{
    private $articles = "";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->articles = array(
                array("title" => "Article one", "text" => "Lorem ipsum dolor sit amet."),
                array("title" => "Second article", "text" => "This is new article. Lorem ipsum dolor sit amet."),
                array("title" => "Third article", "text" => "Hello. Lorem ipsum dolor sit amet."),
            );
        }
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->articles = $this->articles;
    }
}