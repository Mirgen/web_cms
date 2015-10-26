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

class ModuleContactFormPresenter extends ModuleBasePresenter
{
    private $formData = NULL;

    public function renderSend($id){
        $isError = false;
        $parameters = $this->request->getParameters();
        $form_values = $this->request->getPost();
        $this->formData = $this->context->moduleContactFormModel->findOneBy( array('page_page_modules_id' => $id));

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
                ->addTo($this->formData->email)
                ->setSubject('Email z webu ' . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) )
                ->setHTMLBody("<b>Tento e-mail byl odeslan z webu " . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) . "</b><br /><br />"
                            . "<b>Odesílatel: </b>" . ( isset($form_values['email']) ? $form_values['email'] : "nezadáno" ) . "<br />"
                            . "<b>Telefon: </b>" . ( isset($form_values['phone_number']) ? $form_values['phone_number'] : "nezadán" ) . "<br />"
                            . "<b>Text zprávy: </b><br />" . ( isset($form_values['text']) ? $form_values['text'] : "nezadán" ) . "<br />");
            $mailer->send($mail);
            $this->flashMessage($this->formData->message_ok);
        }

        $this->redirect('Default:page', array('id' => $parameters['parent_page_id'], "seo_url_text" => $this->pages[$parameters['parent_page_id']]["final_url_text"]));
    }

    protected function setTemplateVariables(){
        $url = $this->oParentPresenter->link('ModuleContactForm:Send', array('id' => $this->iModuleId, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $this->moduleContentTemplate->url = $url;
    }
}