<?php

/**
 * Usefull and needfull methods for layout functionality.
 *
 * @author Jiri Kvapil
 */
namespace App\Model\Layout;

class LayoutHelper
{
    /**
     * @var string Path to layouts. 
     */
    private $layoutsDirectory = "/FrontModule/templates/layouts/";

    public function __construct($layoutsDirectory = NULL)
    {
        if(NULL !== $layoutsDirectory){
            $this->setLayoutsDirectory($layoutsDirectory);
        }
    }

    /**
     * Get array of objects. Array of available layouts. 
     * 
     * @return array 
     */
    public function getLayouts(){
        $layouts = array();
        // scan dir and find all subdirs
        if (is_dir($this->getLayoutPath())) {
            $dirs_or_files = array_diff(scandir($this->getLayoutPath()), array('.','..'));
            foreach ($dirs_or_files as $item) {
                if(is_dir($this->getLayoutPath() . "/" .$item)){
                    $layouts[] = $this->loadLayout($item);
                }
            }
        } else {
            throw new Exception("Not a directory, wrong path to layout folder.");
        }
        return $layouts;
    }

    /**
     * For given layout name it loads layout class. 
     * 
     * @param string $name Name of a layout class we want to be loaded.
     * @return ILayout Returns object of class, which implements ILayout.
     */
    public function loadLayout($name){
        $className = "\App\Model\Layout\\" . $name . "Layout";
        $class = new $className($this->getLayoutPath());

        if($class instanceof ILayout){
            return $class;
        } else {
            throw new Exception("Class '" . $className . "' must be instance of ILayout.");
        }
    }

    /**
     * Set the directory of layouts.
     * 
     * @param string $layoutsDirectory Directory of layouts.
     * @return void
     */
    public function setLayoutsDirectory($layoutsDirectory){
        $this->layoutsDirectory = $layoutsDirectory;
    }

    /**
     * Get the path of folder with layouts.
     * 
     * @return string path
     */
    public function getLayoutPath(){
        return APP_DIR . $this->layoutsDirectory;
    }
}