<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MenuItem
 *
 * @author Jiri Kvapil
 */
namespace App\Model;

class Page extends Base {
    /** @var string */
    protected $tableName = 'page';

    public function getAllActivePages() {
        $aParameters = array('online' => 1, 'deleted' => 0);
        $oPages = $this->findBy($aParameters);
        return $oPages;
    }

    public function getParentsForSelect() {
        $oPages = $this->getAllActivePages();
        $aPages = array();

        foreach($oPages as $oPage) {
            $aPages[$oPage->id] = $oPage->name;
        }
        return $aPages;
    }

    public function getUrl($id) {
        $page = $this->find($id);
        $nice_url_text = isset($page->seo_url_text) && !empty($page->seo_url_text) ? $page->seo_url_text : $page->name;
        return \UrlExtended::friendly_url( $id . "-" . $nice_url_text );
    }
}