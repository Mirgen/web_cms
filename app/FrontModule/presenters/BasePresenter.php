<?php

namespace App\FrontModule\Presenters;

use Nette,
	App\Model,
    Nette\Caching\Cache;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var array */
    private $settings;

    protected $page;

    protected $pages = array();

    protected $cache = NULL;

    protected function startup() {
            parent::startup();
            $this->setPaths();
            $this->cache = new Cache(new Nette\Caching\Storages\FileStorage('../temp'));

            $this->loadPage();

            $this->settings = $this->context->settings->getAll();
            $this->setLayoutVariale();

            if(isset($this->template)){
                $this->template->settings = $this->settings;
            }
            $this->cache->save('pages', $this->pages);
    }

    /*
     * This method check if we have default layout set in settings variable. It
     * is set in admin part in Website setting.
     * 
     * If it is not set we will create this setting with default layout. This 
     * setting is then available in templates. See /app/FrontModule/@layout.latte 
     * where $settings["layout"] is used. 
     * 
     * Basicaly in this latte file we are loading current layout file given by
     * $settings["layout"].
     */
    private function setLayoutVariale()
    {
        if(!isset($this->settings["layout"]) ||
           !file_exists(__DIR__ . "/../templates/layouts/" . $this->settings["layout"] . "/@layout.latte")
        ){
            $this->settings["layout"] = "Basic";
        }
    }

    public function loadPage(){
        if( NULL != $this->getParameter("id") && $this->presenter->name == "Front:Default" ){
            $page = $this->context->page->find($this->getParameter("id"));
            if(false === $page){
                throw new \Exception("This page does not exists.", "404");
            }
            $this->page = $this->context->page->find($this->getParameter("id"));
            $this->template->title = isset($this->page->title) && !empty($this->page->title) ? $this->page->title : $this->page->name;
        }
    }

    public function getSetting($name) {
        return $this->settings[$name];
    }

    public function getSettings(){
        return $this->settings;
    }

    public function loadModules(){
        $modulesClasses = $this->context->pageModules->loadFrontModules($this->getParameter("id"));
        $modules = array();
        foreach ($modulesClasses as $key => $module){
            $class = 'App\FrontModule\Presenters\\'.$module->class_name.'Presenter';
            $module_instance = new $class;
            $modules[] = $module_instance->load($module->id, $this);
        }
        return $modules;
    }

    private function setPaths(){
        define("WWW_DIR", realpath(__DIR__ . "/../../../www/"));
        define("APP_DIR", realpath(__DIR__ . "/../../"));
        define("IMG_DIR", realpath(WWW_DIR . "/images/"));
    }
}
