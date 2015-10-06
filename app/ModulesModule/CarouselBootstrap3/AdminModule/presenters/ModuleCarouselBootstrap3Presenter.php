<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

 /*
 * @author Jiri Kvapil
 */

class ModuleCarouselBootstrap3Presenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $slides = array();

    protected function initialize()
    {
        // add new setting available for the module
        $this->addNewModuleSetting("Který slide poběží první?", "active", 1);
        $this->addNewModuleSetting("Šířka prezentace", "width", "100%");
        $this->addNewModuleSetting("Výška prezentace", "height", "800px");
        parent::initialize();
    }

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->slides = $this->oParentPresenter->context->moduleCarouselBootstrap3Model->findBy(array('page_page_modules_id' => $this->module->id))->order('order');
        }
    }

    protected function createComponentAdminAddForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link("Module" . $this->moduleName . ':Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);
        $form->addUpload('img', 'Obrázek:')
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');
                //->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat obrázek.');

        $form->addText('title', 'Nadpis', 50, 255);
        $form->addText('subtitle', 'Podtitulek', 50, 255);
        $form->addTextArea ('text', 'Text');
        $form->addText('link', 'Odkaz (celá URL adresa)', 50);
        $form->addText('link_text', 'Viditelný text odkazu', 50, 32);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderSave($id){
        $post = $this->request->getPost();
        $this->loadModule($id);

        $imageUploader = new \ImageUploader($this->request->getFiles(), "module" .  $this->moduleName . "/" . $this->module->page_id , false);
        $uploadedImages = $imageUploader->startUpload();

        $data['image'] = $uploadedImages[0]["name"].$uploadedImages[0]["extension"];
        $data['page_page_modules_id'] = $id;
        $data['title'] = $post['title'];
        $data['subtitle'] = $post['subtitle'];
        $data['text'] = $post['text'];
        $data['link'] = $post['link'];
        $data['link_text'] = $post['link_text'];
        $data['enabled'] = 1;

        $this->context->moduleCarouselBootstrap3Model->insert($data);

        $this->flashMessage('Přidání proběhlo v pořádku.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderEdit($id){
        $item = $this->context->moduleCarouselBootstrap3Model->findOneBy( array('id' => $id) );
        $this->loadModule($item->page_page_modules_id);
        $this->template->image = $item->image;
        $this->template->module = $this->module;
        $this->template->deleteImgUrl = $this->link("Module" . $this->moduleName . ':deleteImage', array('id' => $item->id, 'parent_page_id' => $this->module->page_id));
        $this['editForm']->setDefaults($item);
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();
        $form->addUpload('img', 'Obrázek:')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');

        $form->addText('title', 'Nadpis', 50, 255);
        $form->addText('subtitle', 'Podtitulek', 50, 255);
        $form->addTextArea ('text', 'Text');
        $form->addText('link', 'Odkaz (celá URL adresa)', 50);
        $form->addText('link_text', 'Viditelný text odkazu', 50, 32);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();
        $form->onSuccess[] = array($this, 'editFormSucceeded');

        return $form;
    }

    public function editFormSucceeded(\Nette\Application\UI\Form $form, $values)
    {
        $item = $this->context->moduleCarouselBootstrap3Model->find($this->params['id']);
        $this->loadModule($item->page_page_modules_id);

        if(is_null($form->getHttpData($form::DATA_TEXT, 'cancel')))
        {
            if($values['img']->name != NULL){
                // upload new image:
                $imageUploader = new \ImageUploader($this->request->getFiles(), "module" .  $this->moduleName . "/" . $this->module->page_id);
                $uploadedImages = $imageUploader->startUpload();
                if(isset($uploadedImages[0]["name"])){
                    $data['image'] = $uploadedImages[0]["name"].$uploadedImages[0]["extension"];
                    // delete old image:
                    if(NULL !== $item->image){
                        $imageUploader->deleteImage($item->image);
                    }
                }
            }
            $data['title'] = $values['title'];
            $data['subtitle'] = $values['subtitle'];
            $data['text'] = $values['text'];
            $data['link'] = $values['link'];
            $data['link_text'] = $values['link_text'];

            $item->update($data);
            if(false === isset($uploadedImages[0]["error"])){
                $this->flashMessage('Úprava proběhla úspěšně.');
            } else {
                $this->flashMessage("Error '" . $uploadedImages[0]["error"] . "' while uploading file '" .$uploadedImages[0]["original"] .  "'.");
            }
        }

        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderDelete($id){
        // delete items of this module
        $this->context->moduleCarouselBootstrap3Model->deleteBy( array('page_page_modules_id' => $id) );

        // delete files for this module
        $this->loadModule($id);
        $imageUploader = new \ImageUploader(array(), "module" .  $this->moduleName . "/" . $this->module->page_id);
        $imageUploader->deleteDirectory();

        // delete rest of the module
        parent::renderDelete($id);
    }

    public function renderDeleteItem($id){
        $item = $this->context->moduleCarouselBootstrap3Model->find($id);
        $this->loadModule($item->page_page_modules_id);

        // delete image
        $imageUploader = new \ImageUploader(array(), "module" .  $this->moduleName . "/" . $this->module->page_id);
        $imageUploader->deleteImage($item->image);

        // delete item from db
        $this->context->moduleCarouselBootstrap3Model->delete($id);
        $this->flashMessage('Smazání proběhlo v pořádku.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeleteImage($id){
        $item = $this->context->moduleCarouselBootstrap3Model->find($id);
        $this->loadModule($item->page_page_modules_id);

        // delete image
        $imageUploader = new \ImageUploader(array(), "module" .  $this->moduleName . "/" . $this->module->page_id);
        $imageUploader->deleteImage($item->image);

        // delete item from db
        $item->update(array('image' => NULL));
        $this->flashMessage('Obrázek byl smazán.');
        $this->redirect($this->module->class_name . ':edit', array('id' => $item->id, 'parent_page_id' => $this->params['parent_page_id']));
    }

    public function renderUpdateOrder(){
        if ($this->isAjax()) {
            $images = $this->request->post;
            if (isset($images['items']) && !empty($images['items'])) {
                foreach($images['items'] as $imgOrder => $imgId) {
                    $this->context->moduleCarouselBootstrap3Model->find($imgId)->update(array("order" => $imgOrder + 1));
                }
                echo 1;
            } else {
              // No items provided for reordering.
                echo 0;
            }
        }
    }

    public function renderEnableDisableItem($id){
        $enabled = 1;
        $image = $this->context->moduleCarouselBootstrap3Model->findOneBy( array('id' => $id) );

        if( 1 === $image->enabled){
            $enabled = 0;
        }
        $image->update( array('enabled' => $enabled) );

        $this->flashMessage('Zobrazování obrázků bylo pozastaveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->slides = $this->slides;
        $this->moduleContentTemplate->addForm = $this->createComponentAdminAddForm();
        $this->moduleContentTemplate->updateOrderUrl = $this->oParentPresenter->link("Module" . $this->moduleName . ':UpdateOrder');
    }
}