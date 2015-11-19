<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette\Utils\Image;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleFooterSocialPresenter extends ModuleBasePresenter
{
    public function renderGetImage($id){
        $image = Image::fromFile(__DIR__ . '/../../images/' . $id . ".png");
        $image->send( Image::PNG );
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->currentYear = date("Y");
    }
}