<?php

namespace App\FrontModule\Presenters;

use Nette,
	Nette\Caching\Cache;


/**
 * Homepage presenter.
 */
class RedirectionsPresenter extends BasePresenter
{

	public function renderDefault($id)
	{
        // load pages from cache, we need it to get text for nice URLs:
        $pages = $this->cache->load('pages');
        // redirect to corret page with nice url:
        $this->redirect('Default:page', array('id' => $id, "seo_url_text" => $pages[$id]['final_url_text']));
	}
}
