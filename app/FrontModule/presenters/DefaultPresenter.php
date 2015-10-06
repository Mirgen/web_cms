<?php

namespace App\FrontModule\Presenters;

/**
 * Homepage presenter.
 */
class DefaultPresenter extends BasePresenter
{
    /* function renderDefault() stands here for Homepage = 
     * index page of the whole web */
//	public function renderDefault()
//	{
//        //$this->template->pages = $this->cache->load('pages');
//        $this->template->modules = $this->loadModules();
//	}

    /* function renderPage() for other pages created in admin */
    public function renderPage()
	{
        $this->template->modules = $this->loadModules();
	}
}
