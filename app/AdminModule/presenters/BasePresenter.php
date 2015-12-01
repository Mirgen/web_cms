<?php

namespace App\AdminModule\Presenters;

use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var array */
    protected $settings;

    protected function startup() {
        parent::startup();

        $this->setPaths();

        $settings = $this->context->settings->getAll();
        $this->template->title = "Administrace";

        $this->settings = $settings;
        $this->template->settings = $settings;

        $this->template->latestPages = $this->getLatestPages();

        /* If user is not logged in, force him to sign in first!! */
        if(!$this->getUser()->loggedIn && $this->presenter->name != "Admin:Sign"){
            $this->redirect('Sign:In');
        } else {
            $this->template->user = $this->getUser();
        }
    }

    private function setPaths(){
        define("WWW_DIR", realpath(__DIR__ . "/../../../www/"));
        define("APP_DIR", realpath(__DIR__ . "/../../"));
    }

    private function getLatestPages(){
        $latestPages = array();
        $latestPagesCookie = $this->getHttpRequest()->getCookie("latestPages");

        if(NULL !== $latestPagesCookie){
            $latestPages = array_reverse (unserialize($latestPagesCookie));
        }

        return $latestPages;
    }

    public function getSetting($name) {
        return $this->settings[$name];
    }
}
