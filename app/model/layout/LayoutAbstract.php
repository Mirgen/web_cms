<?php

/**
 * Abstract class for layout objects. I reccoment to derive layout classes from this abstract class,
 * so you can just use existing model for layout.
 *
 * @author Jiri Kvapil
 */
namespace App\Model\Layout;

use Nette\Utils\Image;

abstract class LayoutAbstract implements ILayout
{
    private $title = "";

    private $subTitle = "";

    private $description = "";

    private $mainImage = "";

    private $images = array();

    private $segments = array();

    private $author = "";

    private $authorEmail = "";

    private $version = "";

    private $creationDate = "";

    private $name = "";

    private $path = "";

    private $lang = "cs-CZ";

    private $translations = array();

    public function __construct($layoutsDir, $lang = NULL){
        $this->setLanguage($lang);
        $this->setName();
        $this->setPath($layoutsDir);
        $this->setSegments();
        $this->loadSettings();
        $this->loadTranslations();
        $this->loadImages();
    }

    /* 
     * This methods loads images with previews and etc. of layout. After
     * this load it sets variables $this->mainImage and $this->images.
     * 
     * @return void
     */
    private function loadImages(){
        $imageDir = WWW_DIR . "/layouts/" . $this->name . "/images/";
        $i = 0;

        if (is_dir($imageDir)) {
            $dirs_or_files = array_diff(scandir($imageDir), array('.','..'));
            foreach ($dirs_or_files as $dir_or_file) {
                if(is_file($imageDir . "/" .$dir_or_file)){
                    $image = Image::fromFile($imageDir .$dir_or_file);

                    // ignore images bigger than XXX px
                    if($image->getWidth() <= 800){
                        // first image is main image
                        if($i === 0){
                            $this->mainImage = "../layouts/" . $this->name . "/images/" . $dir_or_file;
                        // other images
                        } else {
                            $this->images[] = "../layouts/" . $this->name . "/images/" . $dir_or_file;
                        }
                        $i++;
                    } else {
                        unset($image);
                    }
                }
            }
        } else {
            throw new Exception("'" . $this->path .  "' is not a directory!");
        }
    }

    public function translate($string)
    {
        if(isset($this->translations[$string])){
            return $this->translations[$string];
        }
        return  $string;
    }

    /* 
     * This method loads translations for language given by $lang class 
     * variable. It also set variables title, subtitle, description.
     * 
     * @return void
     */
    private function loadTranslations(){
        $path = $this->path . "/languages/" . $this->lang . ".php";
        if(file_exists($path)){
            $this->translations = include($path);

            $this->title = $this->translate("title");
            $this->subTitle = $this->translate("subTitle");
            $this->description = $this->translate("description");
        } else {
            throw new Exception("File 'setting.php' is not in expected path: '" . $path . "'.");
        }
    }

    /* 
     * This method loads data from setting.php file. Data such as author, 
     * creation date, version, email.
     * 
     * @return void
     */
    private function loadSettings(){
        $path = $this->path . "/setting.php";
        if(file_exists($path)){
            $settings = include($path);

            $this->author = $settings["author"];
            $this->creationDate = $settings["creation_date"];
            $this->version = $settings["version"];
            $this->authorEmail = $settings["author_email"];
        } else {
            throw new Exception("File 'setting.php' is not in expected path: '" . $path . "'.");
        }
    }

    /**
     * Set base path for layouts.
     * 
     * @param string $layoutsDir 
     * 
     * @return void
     */
    private function setPath($layoutsDir){
        $this->path = realpath($layoutsDir . $this->name);
        if(false === $this->path){
            throw new Exception("Layout '". $this->name ."' doesnt exists!");
        }
    }

    /* 
     * Get segments from segments.php file and save it to $this->segments variable.
     * 
     * @return void
     */
    private function setSegments(){
        if(file_exists($this->path . "/segments.php")){
            $this->segments = include($this->path . "/segments.php");
        } else {
            throw new Exception("File 'segments.php' is not in expected path: '" . $this->path . "/segments.php" . "'.");
        }
    }

    /*
     * Set the language to be used. Can be used public.
     * 
     * @return void
     */
    public function setLanguage($lang){
        if(NULL !== $lang){
            $this->lang = $lang;
        }
    }

    /**
     * Set the name of a layout. this method gets the name from name of the class. 
     *
     * @return void
     */
    private function setName(){
        $matches = array();
        preg_match('/^(.*)\\\\([a-zA-Z0-9]+)Layout$/', get_class($this), $matches);
        $this->name = $matches[2];
    }

    public function getTitle(){
        return $this->title;
    }

    public function getSubTitle(){
        return $this->subTitle;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getMainImage(){
        return $this->mainImage;
    }

    public function getImages(){
        return $this->images;
    }

    public function getSegments(){
        return $this->segments;
    }

    public function getAuthor(){
        return $this->author;
    }

    public function getAuthorEmail(){
        return $this->authorEmail;
    }

    public function getVersion(){
        return $this->version;
    }

    public function getCreationDate(){
        return $this->creationDate;
    }

    public function getName(){
        return $this->name;
    }
}