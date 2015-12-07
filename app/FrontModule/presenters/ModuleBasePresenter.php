<?php

namespace App\FrontModule\Presenters;

use Nette,
    App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class ModuleBasePresenter extends  BasePresenter
{
    protected $module = NULL;

    protected $oParentPresenter = NULL;

    protected $iModuleId = NULL;

    protected $templateDirectory;

    protected $moduleTemplate;

    protected $moduleContentTemplate;

    protected $moduleTemplateDir = NULL;

    protected $templateFile = "default.latte";

    protected $moduleName = "";

    protected $db = NULL;

    private function loadTemplate()
    {
        if(isset($this->templateFile) && !empty($this->templateFile))
        {
            $classInfo = new \ReflectionClass($this);
            $this->templateDirectory = dirname($classInfo->getFileName()) . "/../templates/";

            // get the name of the main template directory
            preg_match('/Module([a-zA-Z0-9]+)Presenter$/', get_class($this), $matches);
            if($this->moduleTemplateDir === NULL){
                $this->moduleTemplateDir = $matches[1];
            }

            /* create layout template for every module */
            $this->moduleTemplate = $this->oParentPresenter->createTemplate();
            $this->moduleTemplate->setFile(__DIR__ . "/../templates/Modules/layout.latte");
            $this->moduleTemplate->module = $this->module;

            $this->moduleContentTemplate = $this->oParentPresenter->createTemplate();
            $this->moduleContentTemplate->setFile($this->templateDirectory . $this->moduleTemplateDir . "/" . $this->templateFile);

            /* Useful variables */
            $this->moduleContentTemplate->basePath = preg_replace('#https?://[^/]+#A', '', $this->oParentPresenter->template->baseUrl);
            $this->moduleContentTemplate->moduleId = $this->iModuleId;
            $this->moduleContentTemplate->module = $this->module;
            $this->moduleContentTemplate->settings = $this->oParentPresenter->context->settings->getAll();
        }
    }

    protected function startup() {
        parent::startup();

        if($this->getParameter('moduleid')){
            $this->iModuleId = $this->getParameter('moduleid');
            $this->loadModuleFromDB();
            $this->template->module = $this->module;
        } else if($this->getParameter('id')){
            $this->load($this->getParameter('id'));
        }
    }

    public function load($iModuleId, $oParentPresenter = NULL){
        $this->oParentPresenter = $oParentPresenter;
        if($this->oParentPresenter != NULL)
        {
            $this->iModuleId = $iModuleId;
            $this->loadModuleFromDB();
            $this->loadTemplate();
            $this->setTemplateVariables();

            $this->moduleTemplate->content = $this->moduleContentTemplate;
            return (string) $this->moduleTemplate;
        }
    }

    private function loadModuleSettings(){
        if($this->module){
            $this->module->settings = new \stdClass();

            if(isset($this->oParentPresenter)){
                $settings = $this->oParentPresenter->context->modulesSettings->findBy(array("module_id" => $this->module->id));
            } else {
                $settings = $this->context->modulesSettings->findBy(array("module_id" => $this->module->id));
            }
            foreach($settings as $setting){
                $this->module->settings->{$setting->key} = $setting->value;
            }
        }
    }

    protected function loadModuleFromDB(){
        if($this->oParentPresenter){
            $this->module = $this->oParentPresenter->context->pageModuleRegister->getModule($this->iModuleId);
        } else {
            $this->module = $this->context->pageModuleRegister->getModule($this->iModuleId);
        }

        $this->setModuleName();
        $this->loadModuleSettings();
        $this->setDB();

        if($this->oParentPresenter){
            $this->loadModuleData();
        }
    }

    /*
     * Set DB variable. variable for DB operations.
     * 
     * @return void
*      */
    private function setDB(){
        // set DB variable
        $context = NULL;
        if($this->oParentPresenter){
            $context = $this->oParentPresenter->context;
        } else {
            $context = $this->context;
        }

        $DBModel = "module" . $this->moduleName . "Model";

        if(isset($context->{$DBModel})){
            $this->db = $context->{$DBModel};
        }
    }


    /*
     * This function sets the name of this module into class variable "moduleName". 
     * 
     * @return string moduleName
*      */
    private function setModuleName(){
        $matches = array();
        preg_match('/Module([a-zA-Z0-9]+)Presenter$/', get_class($this), $matches);
        return $this->moduleName = $matches[1];
    }

    protected function loadModuleData(){
        return NULL;
    }
}
