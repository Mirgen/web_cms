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
            $this->cache = new Cache(new Nette\Caching\Storages\FileStorage('../temp'));

            $this->loadPage();

            $this->settings = $this->context->settings->getAll();

            if(isset($this->template)){
                $this->template->settings = $this->settings;
                $this->template->menu = $this->getMenu();
            }
            $this->cache->save('pages', $this->pages);
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

    public function getSettings($name) {
            return $this->settings[$name];
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

    public function getMenu() {
        $sHtml = '
                    <ul class="nav navbar-nav">
                        <li><a href="' . $this->link('Default:page') . '">Úvodní stránka</a></li>'
                        . $this->getMenuRecursive() .
                    '</ul>';
        return $sHtml;
    }

    private function getMenuRecursive($pageId = NULL, $depth = 0,$oPages = NULL){
        // Initialization:
        // if we did not get submenu (which comes in $oPages), we have to load menu items (basicaly this is only at the begining)
        if($oPages == NULL){
            $aParameters = array('online' => '1', 'deleted' => 0, 'id_parent' => $pageId);
            $oPages = $this->context->page->findBy($aParameters);
        }

        $sHtml = '';
        foreach($oPages as $oPage) {

            // skip page with ID = 0, because it is index/homepage of the web
            if($oPage->id == 1){
                continue;
            }

            $aParameters['id_parent'] = $oPage->id;
            $oSubPages = $this->context->page->findBy($aParameters);
            $url = $this->link('Default:page', array('id' => $oPage->id, "seo_url_text" => $oPage->final_url_text));

            // prepare pages for cache, store pages data to $this->pages variable
            $this->pages[$oPage->id] = $oPage->toArray();

            if( $oSubPages->count() > 0 ){
                $sHtml .=  '<li class="dropdown">
                                <a href="' . $url . '" class="dropdown-toggle" data-toggle="dropdown">' . $oPage->name . ' <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    ' . $this->getMenuRecursive($oPage->id, $depth+1, $oSubPages) . '
                                </ul>
                            </li>';
            } else {
                $sHtml .= '<li><a href="' . $url . '">' . $oPage->name . '</a></li>';
            }
        }
        return $sHtml;
    }
}
