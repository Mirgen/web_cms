<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var array */
    private $settings;

    protected function startup() {
            parent::startup();

            $settings = $this->context->settings->getAll();
            $this->template->title = "Title";

            $this->settings = $settings;
            $this->template->settings = $settings;
            $this->template->menu = $this->context->menu->getMenu();
    }

    public function getSettings($name) {
            return $this->settings[$name];
    }

    public function loadModules(){
        $modulesClasses = $this->context->pageModules->loadModules($this->getParameter("id"));
        $modules = array();
        foreach ($modulesClasses as $key => $module){
            $class = 'App\Presenters\\' . $module->class_name.'ModulePresenter';
            $module_instance = new $class;
            $modules[] = $module_instance->load($module->id, $this);
        }
        return $modules;
    }
}
