<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class ModuleBasePresenter extends  BasePresenter
{
    /** Nette\Database\Table\ActiveRow **/
    protected $module = NULL; 

    protected $oParentPresenter = NULL;

    protected $templateDirectory;

    protected $moduleTemplate;

    protected $moduleContentTemplate;

    protected $moduleTemplateDir = "DefaultTemplates";

    protected $modulesCount = 0;

    protected $templateFile = "default.latte";
    
    protected $moduleName = "";

    protected $db = null;

    public function beforeRender()
    {
        // set the same layout for all modules so they look the same like
        // other pages in administration
        $this->setLayout('../../../../../AdminModule/templates/@layout');
        parent::beforeRender();
    }

    public function setModulesCount($count){
        $this->modulesCount = $count;
    }

    private function loadTemplate()
    {
        if(isset($this->templateFile) && !empty($this->templateFile))
        {
            $classInfo = new \ReflectionClass($this);
            $this->templateDirectory = dirname($classInfo->getFileName()) . "/../templates/";

            /* create layout template for every module */
            $this->moduleTemplate = $this->oParentPresenter->createTemplate();
            $this->moduleTemplate->setFile(__DIR__ . "/../templates/Modules/layout.latte");

            /* Useful variables */
            $this->moduleTemplate->module = $this->module;
            $this->moduleTemplate->numberOfModules = $this->modulesCount;
            $this->moduleTemplate->settingsForm = $this->createComponentSettingsForm();

            $httpRequest = $this->oParentPresenter->getHttpRequest();
            $this->moduleTemplate->moduleMaximalized = $httpRequest->getCookie("module-" . $this->module->id) === NULL ? false : true;

            /* create template for content of a module itself */
            $this->moduleContentTemplate = $this->oParentPresenter->createTemplate();
            $this->moduleContentTemplate->setFile($this->templateDirectory . $this->moduleTemplateDir . "/" . $this->templateFile);

            /* Useful variables */
            $this->moduleContentTemplate->module = $this->module;
        }
    }

    protected function startup() {
        parent::startup();
        if(NULL !== $this->getParameter('moduleid')){
            $this->loadModule($this->getParameter('moduleid'));
        } else if(NULL !== $this->getParameter('id')) {
            $this->loadModule($this->getParameter('id'));
        }
    }

    public function load($moduleId, $oParentPresenter = NULL){
        $this->loadModule($moduleId, $oParentPresenter);
        return $this->loadModuleTemplate();
    }

    public function loadModuleTemplate(){
        if($this->oParentPresenter != NULL)
        {
            $this->loadTemplate();
            $this->loadContentTemplate();
            $this->moduleTemplate->content = $this->moduleContentTemplate;

            return (string) $this->moduleTemplate;
        }
    }

    public function loadModule($moduleId, $oParentPresenter = NULL){
        $this->oParentPresenter = $oParentPresenter;
        $matches = array();
        preg_match('/Module([a-zA-Z0-9]+)Presenter$/', get_class($this), $matches);
        $this->moduleName = $matches[1];

        // set DB variable
        $this->setDB();
        $this->loadModuleFromDB($moduleId);
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

    protected function loadModuleFromDB($moduleId){
            if($this->oParentPresenter){
            $this->module = $this->oParentPresenter->context->pageModuleRegister->getModule($moduleId);
            $this->module->settings = $this->oParentPresenter->context->modulesSettings->findBy(array("module_id" => $this->module->id));
            $this->loadModuleData();
        } else {
            $this->module = $this->context->pageModuleRegister->getModule($moduleId);
            if($this->module){
                $this->module->settings = $this->context->modulesSettings->findBy(array("module_id" => $this->module->id));
            }
            $this->template->module = $this->module;
        }
    }

    protected function loadModuleData(){
        return NULL;
    }

    /**
     * Function initialization() is executed after addition of this module to a page.
     * (Used only once after registration of the module to a page)
     * This function is used for example to add some new rows to database, to add 
     * default values, create some files, etc...
     */
    public function initialize(){
        return true;
    }

    private function recalculatePositions($page_id){
        $modules = $this->context->pageModuleRegister->findBy(array('page_id' => $page_id))->order('position');
        $i = 1;
        foreach ($modules as $key => $module){
            $module->update( array('position' => $i) );
            $i++;
        }
    }

    /**
      * Method renderDelete() deletes module given by $id.
      *
      * @param integer $id Id of module to be deleted.
      *
      * @return redirect to new page
      */
    public function renderDelete($id){
        /* 1. First delete occurence of the deleted module for the given page: */
        $this->context->pageModuleRegister->deleteBy(   array(
                                                            "page_id" => $this->module->page_id,
                                                            "page_module_instance_id" => $id
                                                        )
                                                    );
        $this->deleteWholeModule($id);
        $this->recalculatePositions($this->module->page_id);

        $this->flashMessage('Modul byl smazán.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    private function deleteWholeModule($instanceId){
        /* If the module is NOT on other page, we will delete also "module itself". So that nothing  will left from this module! */
        $this->context->pageModuleInstance->delete($instanceId);
        /* Make sure to delete all the setting related to deleted module */
        $this->context->modulesSettings->deleteBy(array("module_id" => $instanceId));
    }

    public function renderEnableDisable($id){
        $enabled = 1;

        if( 1 === $this->module->enabled){
            $enabled = 0;
            $this->flashMessage('Modul byl pozastaven.');
        } else {
            $this->flashMessage('Modul byl opět spuštěn.');
        }

        $this->context->pageModuleRegister->find($this->module->presence_id)->update( array('enabled' => $enabled) );
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderMoveModuleDown($id){
        $moduleToSwapPosition = $this->context->pageModuleRegister->findOneBy(array('page_id' => $this->module->page_id, 'position' => $this->module->position + 1));

        /* update position of BOTH modules: */
        $moduleToSwapPosition->update( array('position' => $moduleToSwapPosition->position - 1) );
        //$this->module->update( array('position' => $this->module->position + 1) );
        $this->context->pageModuleRegister->find($this->module->presence_id)->update( array('position' => $this->module->position + 1) );

        $this->flashMessage('Řazení bylo změněno.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderMoveModuleUp($id){
        $moduleToSwapPosition = $this->context->pageModuleRegister->findOneBy(array('page_id' => $this->module->page_id, 'position' => $this->module->position - 1));

        /* update position of BOTH modules: */
        $moduleToSwapPosition->update( array('position' => $moduleToSwapPosition->position + 1) );
        //$this->module->update( array('position' => $this->module->position - 1) );
        $this->context->pageModuleRegister->find($this->module->presence_id)->update( array('position' => $this->module->position - 1) );

        $this->flashMessage('Řazení bylo změněno.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    protected function addNewModuleSetting($name, $key, $value){
        $data = array(
                    "module_id" => $this->module->id,
                    "name" => $name,
                    "key" => $key,
                    "value" => $value
                );
        return $this->oParentPresenter->context->modulesSettings->insert($data);
    }

    protected function createComponentSettingsForm() {
        $form = new \CustomForm();

        $url_save = $this->oParentPresenter->link("Module".$this->moduleName .":SaveSettings", array('id' => $this->module->id, 'parent_page_id' => $this->module->page_id));
        $form->setAction($url_save);

        foreach($this->module->settings as $setting){
            $form->addText($setting->key, $setting->name)->setDefaultValue($setting->value);
        }

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderSaveSettings($id){
        $settings = $this->request->getPost();

        foreach($settings as $key => $value){
            $setting = $this->context->modulesSettings->findBy(array("module_id" => $id, "key"=>$key));
            if($setting){
                $data = array("value" => $value);
                $setting->update($data);
            }
        }

        $this->flashMessage('Nastavení bylo uloženo.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function getPathToImages(){
        return __DIR__ . "/../../../www/" . '/images/module' . $this->moduleName . '/';
    }
}
