<?php

/**
 * Interface for segment classes. 
 * 
 * @author Jiri Kvapil
 */
namespace App\Model\Layout;

interface ISegment
{
    /**
     * Get ID of segment. 
     * @return string 
     */
    public function getId();

    /**
     * Get translated name of segment
     * @return string
     */
    public function getName();
}