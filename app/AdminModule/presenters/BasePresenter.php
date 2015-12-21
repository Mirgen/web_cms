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

    public function __construct()
    {
        parent::__construct();
        $this->setPaths();
    }

    protected function startup() {
        parent::startup();

        $this->loadSettings();
        $this->template->title = "Administrace";
        $this->template->latestPages = $this->getLatestPages();

        /* If user is not logged in, force him to sign in first!! */
        if(!$this->getUser()->loggedIn && $this->presenter->name != "Admin:Sign"){
            $this->redirect('Sign:In');
        } else {
            $this->template->user = $this->getUser();
        }
    }

    /**
     * Load global settings of site, like description, keyword, etc.
     */
    private function loadSettings(){
        $this->settings = $this->context->settings->getAll();
        $this->template->settings = $this->settings;
    }

    private function setPaths(){
        define("WWW_DIR", realpath(__DIR__ . "/../../../www/"));
        define("APP_DIR", realpath(__DIR__ . "/../../"));
        define("IMG_DIR", realpath(WWW_DIR . "/images/"));
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
