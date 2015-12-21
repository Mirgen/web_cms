<?php

/**
 * Class for creating segments aas objects. We will have each segment with Id 
 * and translated name.
 *
 * @author Jiri Kvapil
 */
namespace App\Model\Layout;

class Segment implements ISegment
{
    private $id = "";

    private $name = "";

    public function __construct($id, $name){
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }
}