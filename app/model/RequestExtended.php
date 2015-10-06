<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestExtended
 *
 * @author Jiri Kvapil
 */

namespace Nette\Application;

use Nette;

class RequestExtended extends Nette\Application\Request
{
    public function getParameter($sParametrName){
        $parameters = $this->request->getParameters();
        return $parameters[$sParametrName];
    }
}