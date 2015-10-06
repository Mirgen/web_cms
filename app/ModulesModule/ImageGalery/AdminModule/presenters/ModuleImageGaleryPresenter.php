<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\AdminModule\Presenters;

use Nette,
	App\Model,
    Nette\Forms\Controls,
    Nette\Application\UI;

/**
 * Description of TextEditor
 *
 * @author Jiri Kvapil
 */

class ModuleImageGaleryPresenter extends ModuleBasePresenter
{
    private $images = NULL;

    private $main_img_max_width = 1200;
    private $main_img_max_height = 900;

    private $thumb_img_max_width = 400;
    private $thumb_img_max_height = 300;

    private $module_directory = "module_image_galery";

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->images = $this->oParentPresenter->context->moduleImageGaleryModel->findBy( array('page_page_modules_id' => $this->module->id) )->order('order');
        }
    }

    private function getImageDirectory($pageId){
        return $this->context->parameters['wwwDir'] . '/images/' . $this->module_directory . '/' . $pageId . "/";
    }

    public function renderSave($id){
        $files = $this->request->getFiles();
        $post = $this->request->getPost();

        foreach ($files['img'] as $file) {
            if($file->isImage() && $file->isOk()) {
                $file_ext = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
                $file_name = uniqid(rand(0,20), TRUE);
                $whole_path = $this->getImageDirectory($this->params['parent_page_id']) . $file_name.$file_ext;
                $file->move($whole_path);

                // main image:
                $image = \Nette\Image::fromFile($whole_path);
                if($image->getWidth() > $this->main_img_max_width) {
                  $image->resize($this->main_img_max_width, $this->main_img_max_height);
                }
                $image->sharpen();
                $image->save($whole_path);

                // thumb image:
                $image_thumb = \Nette\Image::fromFile($whole_path);
                // resize image to be smaller:
                if($image_thumb->getWidth() > $this->thumb_img_max_width) {
                    $image_thumb->resize($this->thumb_img_max_width, NULL);
                }
                // resize to square:
                if($image_thumb->getHeight() > $this->thumb_img_max_height){
                    // calculate TOP coordinate to fit square to the middle of the image
                    $top = ($image_thumb->getHeight() - $this->thumb_img_max_height)/2;
                    $image_thumb->crop(0, $top, $this->thumb_img_max_width, $this->thumb_img_max_height);
                }
                $image_thumb->sharpen();
                $image_thumb->save($this->getImageDirectory($this->params['parent_page_id']) . $file_name . "_t" .$file_ext);

                $data['page_page_modules_id'] = $id;
                $data['name'] = $file_name;
                $data['extension'] = ltrim($file_ext, ".");
                $data['description'] = $post['description'];
                $data['enabled'] = 1;
                $data['order'] = $this->context->moduleImageGaleryModel->findBy(array("page_page_modules_id" => $id))->count() + 1;

                $this->context->moduleImageGaleryModel->insert($data);
                $this->flashMessage('Soubor \'' . $file->name . '\' byl nahrán.');
            } else {
                $this->flashMessage('Soubor \'' . $file->name . '\' se nepodařilo nahrát, error: ' . $file->error, 'danger');
            }
        }

        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDelete($id){
        $images = $this->context->moduleImageGaleryModel->findBy(array('page_page_modules_id' => $id));
        foreach ($images as $image){
            // delete file from filesystem:
            $this->deleteImageFile($image->name . "." . $image->extension);
            $this->deleteImageFile($image->name . "_t." . $image->extension);
        }
        $this->context->moduleGuestBookModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    protected function createComponentImageGaleryForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModuleImageGalery:Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);
        $form->addUpload('img', 'Obrázek:', true)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
                //->addRule(\Nette\Application\UI\Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '50000')
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat image.');

        $form->addText('description', 'Popis: ', 50);

        $form->addSubmit('create', 'Nahrát');
        $form->addSubmit('cancel', 'Zrušit')
                ->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderDeleteImage($id){
        $image = $this->context->moduleImageGaleryModel->findOneBy( array('id' => $id) );

        // delete from database
        $this->context->moduleImageGaleryModel->delete($id);
        // delete file from filesystem:
        $this->deleteImageFile($image->name . "." . $image->extension);
        $this->deleteImageFile($image->name . "_t." . $image->extension);

        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    private function deleteImageFile($filePath){
        if(true === file_exists ( $this->getImageDirectory($this->params['parent_page_id']) . $filePath )){
            return unlink($this->getImageDirectory($this->params['parent_page_id']) . $filePath);
        }
        return true;
    }

    public function renderEnableDisableImage($id){
        $enabled = 1;
        $image = $this->context->moduleImageGaleryModel->findOneBy( array('id' => $id) );

        if( 1 === $image->enabled){
            $enabled = 0;
        }
        $image->update( array('enabled' => $enabled) );

        $this->flashMessage('Zobrazování obrázků bylo pozastaveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderUpdateOrder(){
        if ($this->isAjax()) {
            $images = $this->request->post;
            if (isset($images['img']) && !empty($images['img'])) {
                foreach($images['img'] as $imgOrder => $imgId) {
                    $this->context->moduleImageGaleryModel->find($imgId)->update(array("order" => $imgOrder + 1));
                }
                echo 1;
            } else {
              // No items provided for reordering.
                echo 0;
            }
        }
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->images = $this->images;
        $this->moduleContentTemplate->moduleDirectory = $this->module_directory;
        $this->moduleContentTemplate->imageGaleryForm = $this->createComponentImageGaleryForm();
        $this->moduleContentTemplate->updateOrderUrl = $this->oParentPresenter->link("Module" . $this->moduleName . ':UpdateOrder');
    }
}