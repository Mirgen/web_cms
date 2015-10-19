<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

use Nette,
    App\Model,
    Nette\Application\UI;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModulePictogramsPresenter extends ModuleBasePresenter
{
    private $pictograms = NULL;
    
    private $icons = array(
                "glyphicon glyphicon-asterisk", 
                "glyphicon glyphicon-plus", 
                "glyphicon glyphicon-euro", 
                "glyphicon glyphicon-eur", 
                "glyphicon glyphicon-minus", 
                "glyphicon glyphicon-cloud", 
                "glyphicon glyphicon-envelope", 
                "glyphicon glyphicon-pencil", 
                "glyphicon glyphicon-glass", 
                "glyphicon glyphicon-music", 
                "glyphicon glyphicon-search", 
                "glyphicon glyphicon-heart", 
                "glyphicon glyphicon-star", 
                "glyphicon glyphicon-star-empty", 
                "glyphicon glyphicon-user", 
                "glyphicon glyphicon-film", 
                "glyphicon glyphicon-th-large", 
                "glyphicon glyphicon-th", 
                "glyphicon glyphicon-th-list", 
                "glyphicon glyphicon-ok", 
                "glyphicon glyphicon-remove", 
                "glyphicon glyphicon-zoom-in", 
                "glyphicon glyphicon-zoom-out", 
                "glyphicon glyphicon-off", 
                "glyphicon glyphicon-signal", 
                "glyphicon glyphicon-cog", 
                "glyphicon glyphicon-trash", 
                "glyphicon glyphicon-home", 
                "glyphicon glyphicon-file", 
                "glyphicon glyphicon-time", 
                "glyphicon glyphicon-road", 
                "glyphicon glyphicon-download-alt", 
                "glyphicon glyphicon-download", 
                "glyphicon glyphicon-upload", 
                "glyphicon glyphicon-inbox", 
                "glyphicon glyphicon-play-circle", 
                "glyphicon glyphicon-repeat", 
                "glyphicon glyphicon-refresh", 
                "glyphicon glyphicon-list-alt", 
                "glyphicon glyphicon-lock", 
                "glyphicon glyphicon-flag", 
                "glyphicon glyphicon-headphones", 
                "glyphicon glyphicon-volume-off", 
                "glyphicon glyphicon-volume-down", 
                "glyphicon glyphicon-volume-up", 
                "glyphicon glyphicon-qrcode", 
                "glyphicon glyphicon-barcode", 
                "glyphicon glyphicon-tag", 
                "glyphicon glyphicon-tags", 
                "glyphicon glyphicon-book", 
                "glyphicon glyphicon-bookmark", 
                "glyphicon glyphicon-print", 
                "glyphicon glyphicon-camera", 
                "glyphicon glyphicon-font", 
                "glyphicon glyphicon-bold", 
                "glyphicon glyphicon-italic", 
                "glyphicon glyphicon-text-height", 
                "glyphicon glyphicon-text-width", 
                "glyphicon glyphicon-align-left", 
                "glyphicon glyphicon-align-center", 
                "glyphicon glyphicon-align-right", 
                "glyphicon glyphicon-align-justify", 
                "glyphicon glyphicon-list", 
                "glyphicon glyphicon-indent-left", 
                "glyphicon glyphicon-indent-right", 
                "glyphicon glyphicon-facetime-video", 
                "glyphicon glyphicon-picture", 
                "glyphicon glyphicon-map-markver", 
                "glyphicon glyphicon-adjust", 
                "glyphicon glyphicon-tint", 
                "glyphicon glyphicon-edit", 
                "glyphicon glyphicon-share", 
                "glyphicon glyphicon-check", 
                "glyphicon glyphicon-move", 
                "glyphicon glyphicon-step-backward", 
                "glyphicon glyphicon-fast-backward", 
                "glyphicon glyphicon-backward", 
                "glyphicon glyphicon-play", 
                "glyphicon glyphicon-pause", 
                "glyphicon glyphicon-stop", 
                "glyphicon glyphicon-forward", 
                "glyphicon glyphicon-fast-forward", 
                "glyphicon glyphicon-step-forward", 
                "glyphicon glyphicon-eject", 
                "glyphicon glyphicon-chevron-left", 
                "glyphicon glyphicon-chevron-right", 
                "glyphicon glyphicon-plus-sign", 
                "glyphicon glyphicon-minus-sign", 
                "glyphicon glyphicon-remove-sign", 
                "glyphicon glyphicon-ok-sign", 
                "glyphicon glyphicon-question-sign", 
                "glyphicon glyphicon-info-sign", 
                "glyphicon glyphicon-screenshot", 
                "glyphicon glyphicon-remove-circle", 
                "glyphicon glyphicon-ok-circle", 
                "glyphicon glyphicon-ban-circle", 
                "glyphicon glyphicon-arrow-left", 
                "glyphicon glyphicon-arrow-right", 
                "glyphicon glyphicon-arrow-up", 
                "glyphicon glyphicon-arrow-down", 
                "glyphicon glyphicon-share-alt", 
                "glyphicon glyphicon-resize-full", 
                "glyphicon glyphicon-resize-small", 
                "glyphicon glyphicon-exclamation-sign", 
                "glyphicon glyphicon-gift", 
                "glyphicon glyphicon-leaf", 
                "glyphicon glyphicon-fire", 
                "glyphicon glyphicon-eye-open", 
                "glyphicon glyphicon-eye-close", 
                "glyphicon glyphicon-warning-sign", 
                "glyphicon glyphicon-plane", 
                "glyphicon glyphicon-calendar", 
                "glyphicon glyphicon-random", 
                "glyphicon glyphicon-comment", 
                "glyphicon glyphicon-magnet", 
                "glyphicon glyphicon-chevron-up", 
                "glyphicon glyphicon-chevron-down", 
                "glyphicon glyphicon-retweet", 
                "glyphicon glyphicon-shopping-cart", 
                "glyphicon glyphicon-folder-close", 
                "glyphicon glyphicon-folder-open", 
                "glyphicon glyphicon-resize-vertical", 
                "glyphicon glyphicon-resize-horizontal", 
                "glyphicon glyphicon-hdd", 
                "glyphicon glyphicon-bullhorn", 
                "glyphicon glyphicon-bell", 
                "glyphicon glyphicon-certificate", 
                "glyphicon glyphicon-thumbs-up", 
                "glyphicon glyphicon-thumbs-down", 
                "glyphicon glyphicon-hand-right", 
                "glyphicon glyphicon-hand-left", 
                "glyphicon glyphicon-hand-up", 
                "glyphicon glyphicon-hand-down", 
                "glyphicon glyphicon-circle-arrow-right", 
                "glyphicon glyphicon-circle-arrow-left", 
                "glyphicon glyphicon-circle-arrow-up", 
                "glyphicon glyphicon-circle-arrow-down", 
                "glyphicon glyphicon-globe", 
                "glyphicon glyphicon-wrench", 
                "glyphicon glyphicon-tasks", 
                "glyphicon glyphicon-filter", 
                "glyphicon glyphicon-briefcase", 
                "glyphicon glyphicon-fullscreen", 
                "glyphicon glyphicon-dashboard", 
                "glyphicon glyphicon-paperclip", 
                "glyphicon glyphicon-heart-empty", 
                "glyphicon glyphicon-link", 
                "glyphicon glyphicon-phone", 
                "glyphicon glyphicon-pushpin", 
                "glyphicon glyphicon-usd", 
                "glyphicon glyphicon-gbp", 
                "glyphicon glyphicon-sort", 
                "glyphicon glyphicon-sort-by-alphabet", 
                "glyphicon glyphicon-sort-by-alphabet-alt", 
                "glyphicon glyphicon-sort-by-order", 
                "glyphicon glyphicon-sort-by-order-alt", 
                "glyphicon glyphicon-sort-by-attributes", 
                "glyphicon glyphicon-sort-by-attributes-alt", 
                "glyphicon glyphicon-unchecked", 
                "glyphicon glyphicon-expand", 
                "glyphicon glyphicon-collapse-down", 
                "glyphicon glyphicon-collapse-up", 
                "glyphicon glyphicon-log-in", 
                "glyphicon glyphicon-flash", 
                "glyphicon glyphicon-log-out", 
                "glyphicon glyphicon-new-window", 
                "glyphicon glyphicon-record", 
                "glyphicon glyphicon-save", 
                "glyphicon glyphicon-open", 
                "glyphicon glyphicon-saved", 
                "glyphicon glyphicon-import", 
                "glyphicon glyphicon-export", 
                "glyphicon glyphicon-send", 
                "glyphicon glyphicon-floppy-disk", 
                "glyphicon glyphicon-floppy-saved", 
                "glyphicon glyphicon-floppy-remove", 
                "glyphicon glyphicon-floppy-save", 
                "glyphicon glyphicon-floppy-open", 
                "glyphicon glyphicon-credit-card", 
                "glyphicon glyphicon-transfer", 
                "glyphicon glyphicon-cutlery", 
                "glyphicon glyphicon-header", 
                "glyphicon glyphicon-compressed", 
                "glyphicon glyphicon-earphone", 
                "glyphicon glyphicon-phone-alt", 
                "glyphicon glyphicon-tower", 
                "glyphicon glyphicon-stats", 
                "glyphicon glyphicon-sd-video", 
                "glyphicon glyphicon-hd-video", 
                "glyphicon glyphicon-subtitles", 
                "glyphicon glyphicon-sound-stereo", 
                "glyphicon glyphicon-sound-dolby", 
                "glyphicon glyphicon-sound-5-1", 
                "glyphicon glyphicon-sound-6-1", 
                "glyphicon glyphicon-sound-7-1", 
                "glyphicon glyphicon-copyright-mark", 
                "glyphicon glyphicon-registration-mark", 
                "glyphicon glyphicon-cloud-download", 
                "glyphicon glyphicon-cloud-upload", 
                "glyphicon glyphicon-tree-conifer", 
                "glyphicon glyphicon-tree-deciduous", 
                "glyphicon glyphicon-cd", 
                "glyphicon glyphicon-save-file", 
                "glyphicon glyphicon-open-file", 
                "glyphicon glyphicon-level-up", 
                "glyphicon glyphicon-copy", 
                "glyphicon glyphicon-paste", 
                "glyphicon glyphicon-alert", 
                "glyphicon glyphicon-equalizer", 
                "glyphicon glyphicon-king", 
                "glyphicon glyphicon-queen", 
                "glyphicon glyphicon-pawn", 
                "glyphicon glyphicon-bishop", 
                "glyphicon glyphicon-knight", 
                "glyphicon glyphicon-baby-formula", 
                "glyphicon glyphicon-tent", 
                "glyphicon glyphicon-blackboard", 
                "glyphicon glyphicon-bed", 
                "glyphicon glyphicon-apple", 
                "glyphicon glyphicon-erase", 
                "glyphicon glyphicon-hourglass", 
                "glyphicon glyphicon-lamp", 
                "glyphicon glyphicon-duplicate", 
                "glyphicon glyphicon-piggy-bank", 
                "glyphicon glyphicon-scissors", 
                "glyphicon glyphicon-bitcoin", 
                "glyphicon glyphicon-btc", 
                "glyphicon glyphicon-xbt", 
                "glyphicon glyphicon-yen", 
                "glyphicon glyphicon-jpy", 
                "glyphicon glyphicon-ruble", 
                "glyphicon glyphicon-rub", 
                "glyphicon glyphicon-scale", 
                "glyphicon glyphicon-ice-lolly", 
                "glyphicon glyphicon-ice-lolly-tasted", 
                "glyphicon glyphicon-education", 
                "glyphicon glyphicon-option-horizontal", 
                "glyphicon glyphicon-option-vertical", 
                "glyphicon glyphicon-menu-hamburger", 
                "glyphicon glyphicon-modal-window", 
                "glyphicon glyphicon-oil", 
                "glyphicon glyphicon-grain", 
                "glyphicon glyphicon-sunglasses", 
                "glyphicon glyphicon-text-size", 
                "glyphicon glyphicon-text-color", 
                "glyphicon glyphicon-text-background", 
                "glyphicon glyphicon-object-align-top", 
                "glyphicon glyphicon-object-align-bottom", 
                "glyphicon glyphicon-object-align-horizontal", 
                "glyphicon glyphicon-object-align-left", 
                "glyphicon glyphicon-object-align-vertical", 
                "glyphicon glyphicon-object-align-right", 
                "glyphicon glyphicon-triangle-right", 
                "glyphicon glyphicon-triangle-left", 
                "glyphicon glyphicon-triangle-bottom", 
                "glyphicon glyphicon-triangle-top", 
                "glyphicon glyphicon-console", 
                "glyphicon glyphicon-superscript", 
                "glyphicon glyphicon-subscript", 
                "glyphicon glyphicon-menu-left", 
                "glyphicon glyphicon-menu-right", 
                "glyphicon glyphicon-menu-down", 
                "glyphicon glyphicon-menu-up", 
            );

    public function initialize()
    {
        $this->addNewModuleSetting("Nadpis", "title", "Please change this title");
        parent::initialize();
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->pictograms = $this->oParentPresenter->context->modulePictogramsModel->findBy( array('page_page_modules_id' => $this->module->id) )->order('id DESC');
        }
    }

    public function renderSave($id){
        $post = $this->request->getPost();

        if(isset($post['icon'])){
            $data['page_page_modules_id'] = $id;
            $data['text'] = $post['text'];
            $data['link'] = $post['link'];
            $data['icon'] = $this->icons[$post['icon']];
            $data['enabled'] = 1;

            $this->context->modulePictogramsModel->insert($data);
            $this->flashMessage('Položka byla přidána.');
        } else {
            $this->flashMessage('Musíte vybrat ikonku.', 'error');
        }

        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    protected function createComponentAddForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModulePictograms:Save', array('id' => $this->module->id, 'parent_page_id' => $this->module->page_id));
        $form->setAction($url_save);

        $form->addRadioList('icon', 'Ikonka', $this->icons);
        $form->addTextArea ('text', 'Text');
        $form->addText('link', 'Odkaz (celá URL adresa)', 1000);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderEdit($id){
        $pictogram = $this->context->modulePictogramsModel->findOneBy( array('id' => $id) )->toArray();
        $pictogram["icon"] = array_search($pictogram["icon"], $this->icons);

        $this['editForm']->setDefaults($pictogram);
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();

        $form->addRadioList('icon', 'Ikonka', $this->icons);
        $form->addTextArea ('text', 'Text');
        $form->addText('link', 'Odkaz (celá URL adresa)', 1000);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();
        $form->onSuccess[] = array($this, 'editFormSucceeded');

        return $form;
    }

    public function editFormSucceeded(UI\Form $form, $values)
    {
        $pictogram = $this->context->modulePictogramsModel->find($this->params['id']);
        $this->loadModuleFromDB($pictogram->page_page_modules_id);

        if(isset($values['icon'])){
            $data['text'] = $values['text'];
            $data['link'] = $values['link'];
            $data['icon'] = $this->icons[$values['icon']];

            $pictogram->update($data);
            $this->flashMessage('Položka byla upravena.');
        } else {
            $this->flashMessage('Musíte vybrat ikonku.', 'error');
        }

        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderEnableDisablePictogram($id){
        $enabled = 1;
        $pictogram = $this->context->modulePictogramsModel->findOneBy( array('id' => $id) );

        if( 1 === $pictogram->enabled){
            $enabled = 0;
        }
        $pictogram->update( array('enabled' => $enabled) );

        $this->flashMessage('Zobrazování položky bylo pozastaveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeletePictogram($id){
        $this->context->modulePictogramsModel->delete($id);

        $this->flashMessage('Položka byla smazána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDelete($id){
        $this->context->modulePictogramsModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->pictograms = $this->pictograms;
        $this->moduleContentTemplate->addForm = $this->createComponentAddForm();
    }
}