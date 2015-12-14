<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\FrontModule\Presenters;

use Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleSimpleEshopPresenter extends ModuleBasePresenter
{
    /**
     * @var array Array of products.
     */
    private $products = array();

    protected function loadModuleData(){
        $this->products = $this->db->getProductsByModuleID($this->module->id, 1);
    }

    /**
     * @param int $id ID of product.
     * @return void
     */
    public function renderProduct($id){
        $product = $this->context->moduleSimpleEshopModel->getProduct($id, 1);
        // TODO: load $this->module if it is not set! Do it after it will have a better method than now!
        $this->template->product = $product;
        $this->registerFilters($this->template);
    }

    protected function registerFilters($template){
        // filter for adding currency before or behind price
        $template->addFilter('addCurrencyToPrice', function ($price, $moduleSettings) {
            if($moduleSettings->currency_position == 0){
                $price = $moduleSettings->currency . " " . $price;
            } else {
                $price .= " " . $moduleSettings->currency;
            }
            return $price;
        });
    }

    protected function createComponentBuyForm()
    {
        $form = new \CustomForm();
        $form->addText("name", "Vaše jméno")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím vaše jméno.');
        $form->addText("surname", "Vaše přijmení")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím vaše přijmení.');
        $form->addText("email", "Váš email")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím váš e-mail.');
        $form->addText("city", "Město")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím město.');
        $form->addText("street", "Ulice a číslo popisné")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím ulici a číslo popisné.');
        $form->addText("zip_code", "PSČ")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Zadejte prosím PSČ.');

        $form->addSubmit('addPage', 'Zakoupit');
        $form->addSubmit('close', 'Zavřít')
                ->setAttribute('data-dismiss', 'modal');

        $form->onSuccess[] = array($this, 'buyFormSucceeded');
        $form->setCustomRenderer();
        return $form;
    }

    public function buyFormSucceeded(\CustomForm $form, $values)
    {
        $product = $this->context->moduleSimpleEshopModel->getProduct($this->getParameter("id"), 1);

        $this->iModuleId = $product["page_page_modules_id"];
        $this->loadModuleFromDB();

        if($form->isValid()){
            $values["product_id"] = $product["id"];
            $values["product_title"] = $product["title"];
            $values["page_page_modules_id"] = $product["page_page_modules_id"];

            $order = $this->db->createOrder($values);
            if(!$order){
                $this->flashMessage('Jejda. :( Něco je špatně. Zkuste znovu nebo se s námi spojte přímo. Děkujeme za pochopení.');
            } else {
                $mail = new Message;
                $mailer = new SendmailMailer;
                $emailHTML = $this->getEmailHTML($order, $product);

                // send confirmation email to customer
                $mail   ->setFrom($this->module->settings->email)
                        ->addTo($order->email)
                        ->setSubject('Potvrzení o koupi.')
                        ->setHTMLBody($emailHTML);
                $mailer->send($mail);

                // send confirmation email to owner of the eshop
                $mail   ->setFrom($this->module->settings->email)
                        ->addTo($this->module->settings->email)
                        ->setSubject('Potvrzení o koupi.')
                        ->setHTMLBody($emailHTML);
                $mailer->send($mail);

                $this->flashMessage($this->module->settings->order_confirmation, 'success');
            }
        } else {
            $this->flashMessage('Doplňte prosím všechny povinné položky.');
        }

        $this->redirect(":".$this->getName().':Product', array('id' => $product['nice_url_segment'], 'moduleid' => $product['page_page_modules_id']));
    }

    /**
     * Create HTML email for customer and Eshop owner. For now we have the same email for both.
     * 
     * @param $order Info about customer and order.
     * @param $product Info about product.
     * @return string Complete email in HTML.
     */
    private function getEmailHTML($order, $product){
        $latte = new \Latte\Engine;
        $this->registerFilters($latte);

        $parameters['order'] = $order;
        $parameters['module'] = $this->module;
        $parameters['www'] = $this->template->baseUrl;
        $parameters['web_setting'] = $this->settings;
        $parameters['product'] = $product;
        $parameters['product']['link'] = isset($_SERVER['HTTPS']) ? "https://" : "http://" .( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) . $this->link($this->module->class_name . ":Product", array('id' => $product['nice_url_segment'], 'moduleid' => $this->module->id));
        $parameters['product']['img_link'] = $this->template->baseUrl . "/images/" . $this->module->class_name . "/" . $this->module->id . "/" . $product["id"] . "/" . $product["main_image"]["filename"].$product["main_image"]["file_extension"];

        return $latte->renderToString(APP_DIR . "/ModulesModule/" . $this->moduleName ."/FrontModule/templates/". 'email.latte', $parameters);
    }

    public function setTemplateVariables(){
        $this->moduleContentTemplate->products = $this->products;
        $this->registerFilters($this->moduleContentTemplate);
    }
}