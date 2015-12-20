<?php

/**
 * Interface for layout classes. Every layout must have these methods.
 * 
 * @author Jiri Kvapil
 */
namespace App\Model\Layout;

interface ILayout
{
    /**
     * Get title of layout 
     * @return string 
     */
    public function getTitle();

    /**
     * Get sub-title of layout
     * @return string
     */
    public function getSubTitle();

    /**
     * Get description of title
     * @return string
     */
    public function getDescription();

    /**
     * Get URL of main image
     * @return string
     */
    public function getMainImage();

    /**
     * Get array of other images
     * @return array
     */
    public function getImages();

    /**
     * Get array of segments of a layout
     * @return array
     */
    public function getSegments();

    /**
     * Get author of a layout
     * @return string
     */
    public function getAuthor();

    /**
     * Get author's email
     * @return string
     */
    public function getAuthorEmail();

    /**
     * Get version of layout
     * @return string
     */
    public function getVersion();

    /**
     * Get when a layout was created
     * @return string
     */
    public function getCreationDate();

    /**
     * Get name of a layout
     * @return string
     */
    public function getName();
}