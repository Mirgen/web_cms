<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette,
	App\Model, 
    Nette\Application\UI,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleContactFormPresenter extends BasePresenter
{
    private $oParentPresenter = NULL;

    private $iModuleId = NULL;

    private $oModuleDB = NULL;


    public function load($iModuleId, $oParentPresenter = NULL){
        $this->oParentPresenter = $oParentPresenter;
        $this->iModuleId = $iModuleId;
        
        $this->loadModuleFromDB();
        
        return $this->render();
    }

    public function loadModuleFromDB(){
        $this->oModuleDB = $this->oParentPresenter->context->moduleContactFormModel->findOneBy( array('page_page_modules_id' => $this->iModuleId) );
    }

    public function getEmail(){
        $email = "";

        if(NULL != $this->oModuleDB){
            $email = $this->oModuleDB->email;
        }

        return $email;
    }

    public function renderSend($id){
        $isError = false;
        $parameters = $this->request->getParameters();
        $form_values = $this->request->getPost();
        $this->oModuleDB = $this->context->moduleContactFormModel->findOneBy( array('page_page_modules_id' => $id));

        $mail = new Message;
        $mailer = new SendmailMailer;

        if(!isset($form_values['email']) || empty($form_values['email'])){
            $isError = true;
            $this->flashMessage("Musíte zadat vaši e-mailovou adresu.", "danger");
        }
        if(!isset($form_values['text']) || empty($form_values['text'])){
            $isError = true;
            $this->flashMessage("Musíte zadat text zprávy.", "danger");
        }

        if(!$isError) {
            $mail->setFrom($form_values['email'])
                ->addTo($this->oModuleDB->email)
                ->setSubject('Email z webu ' . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) )
                ->setHTMLBody("<b>Tento e-mail byl odeslan z webu " . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) . "</b><br /><br />"
                            . "<b>Odesílatel: </b>" . ( isset($form_values['email']) ? $form_values['email'] : "nezadáno" ) . "<br />"
                            . "<b>Telefon: </b>" . ( isset($form_values['phone_number']) ? $form_values['phone_number'] : "nezadán" ) . "<br />"
                            . "<b>Text zprávy: </b><br />" . ( isset($form_values['text']) ? $form_values['text'] : "nezadán" ) . "<br />");
            $mailer->send($mail);
            $this->flashMessage($this->oModuleDB->message_ok);
        }

        $this->redirect('Default:page', array('id' => $parameters['parent_page_id'], "seo_url_text" => $this->pages[$parameters['parent_page_id']]["final_url_text"]));
    }

    private function render(){
        $url = $this->oParentPresenter->link('ModuleContactForm:Send', array('id' => $this->iModuleId, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));

        $html = '
        <div class="container">
            <div class="module">
                <h3>Napište mi</h3>
                <form class="form-horizontal" method="post" action="' . $url . '">
                    <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label">Vaše jméno</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="text" name="name" placeholder="Vaše jméno">
                      </div>
                    </div>
                    <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label"><span class="mandatory-star">*</span>E-mail</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="text" name="email" placeholder="E-mail">
                      </div>
                    </div>
                    <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label">Telefon</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="text" name="phone_number" placeholder="Telefon">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label"><span class="mandatory-star">*</span>Text zprávy</label>
                      <div class="col-sm-10">
                        <textarea class="form-control" rows="10" name="text"></textarea>
                      </div>
                    </div>
                    <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label"></label>
                      <div class="col-sm-10">
                        <input class="btn btn-default" type="submit" value="Odeslat">
                      </div>
                    </div>
                </form>
            </div>
        </div>';
        return $html;
    }
}