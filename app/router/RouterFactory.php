<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{
        public $submodule;

        private $sPresenter = 'Default';

        private $sAction = 'page';

        /**
         * @return \Nette\Application\IRouter
         */
        public function createRouter() {
            $router = new RouteList;
            $router[] = new \RedirectionsRoute('<nice_url_segment [0-9A-Za-z-/]+>', array(
                'module' => 'front',
                'presenter' => 'Default',
                'action' => 'default',
                'id' => 0
            ), Route::ONE_WAY);

            $router[] =  new Route('admin/module/<moduleid>/<presenter>[/<action>[/<id>]]', array(
                'module' => 'Admin',
                'presenter' => $this->sPresenter,
                'action' => $this->sAction,
                'id' => NULL
            ));
            $router[] = new Route('admin[/<presenter>[/<action>[/<id>]]]', array(
                'module' => 'Admin',
                'presenter' => $this->sPresenter,
                'action' => $this->sAction,
                'id' => NULL,
            ));
            $router[] = new Route('<id [0-9]+>-<seo_url_text [0-9A-Za-z-/]+>', array(
                'module' => 'front',
                'presenter' => $this->sPresenter,
                'action' => $this->sAction,
                'id' => NULL,
            ));
            $router[] = new Route('[<presenter>[/<action>]/]<id>', array(
                'module' => 'front',
                'presenter' => $this->sPresenter,
                'action' => $this->sAction,
                'id' => 0,
            ));

            return $router;
        }

        public function submoduleFilter($submodule) {
          $this->submodule = ($submodule == 'admin' ? 'Admin' : 'Front');
        }

//        public function moduleFilterIn($module) {
//            return ucfirst($module).':'.$this->submodule;
//        }
//        public function moduleFilterOut($module) {
//            list($module) = $this->submodule.':'.$module[0];
//            return strtolower($module);
//        }

        public function moduleFilterIn($module) {
            //\Tracy\Debugger::dump($module);
            return ucfirst($module);
        }

        public function moduleFilterOut($module) {
          //list($module) = /*$this->submodule.':'.*/$module[0];
          //\Tracy\Debugger::dump($module);
          return strtolower($module);
        }
}
