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
        }
    }

    protected function startup() {
        parent::startup();
        if($this->getParameter('id')){
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

            $settings = $this->oParentPresenter->context->modulesSettings->findBy(array("module_id" => $this->module->id));
            foreach($settings as $setting){
                $this->module->settings->{$setting->key} = $setting->value;
            }
        }
    }

    protected function loadModuleFromDB(){
        if($this->oParentPresenter){
            $this->module = $this->oParentPresenter->context->pageModuleRegister->getModule($this->iModuleId);
            $this->loadModuleSettings();
            $this->loadModuleData();
        } else {
            $this->module = $this->context->pageModuleRegister->getModule($this->iModuleId);
            $this->loadModuleSettings();
        }
    }

    protected function loadModuleData(){
        return NULL;
    }
}
