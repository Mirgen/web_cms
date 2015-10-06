<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author Jiri Kvapil
 */
namespace App\Model;

class Settings extends Base {
    /** @var string */
    protected $tableName = 'settings';

    public function getAll() {
        $rows = $this->findAll();
        $settings = array();
        foreach($rows as $row) {
                $settings[$row->name] = $row->value;
        }
        return $settings;
    }
}