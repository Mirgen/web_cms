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

class ModuleFeaturingPresenter extends ModuleBasePresenter
{
    private $features = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->features = $this->oParentPresenter->context->moduleFeaturingModel->findBy( array('page_page_modules_id' => $this->module->id) )->order('id DESC');
        }
    }

    private function getImageDirectory($featureId){
        return $this->context->parameters['wwwDir'] . '/images/module_featuring/' . $featureId . "/";
    }

    private function uploadOneImage(){
        $files = $this->request->getFiles();
        $file = $files['img'];
        $file_name = NULL;

        if(isset($file) && $file->isImage() && $file->isOk()) {
            $file_ext = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
            $file_name = uniqid(rand(0,20), TRUE).$file_ext;
            $file->move($this->getImageDirectory($this->module->id) . $file_name);

            $image = \Nette\Image::fromFile($this->getImageDirectory($this->module->id) . $file_name);
            if($image->getWidth() > 600) {
              $image->resize(600, NULL);
            }
            $image->crop('50%', '50%', 350, 350);

            $image->sharpen();
            $image->save($this->getImageDirectory($this->module->id) . $file_name);
        }

        return $file_name;
    }

    public function renderSave($id){
        $post = $this->request->getPost();

        $data['imagename'] = $this->uploadOneImage();
        $data['page_page_modules_id'] = $id;
        $data['title'] = $post['title'];
        $data['text'] = $post['text'];
        $data['link'] = $post['link'];
        $data['enabled'] = 1;

        $this->context->moduleFeaturingModel->insert($data);

        $this->flashMessage('Položka byla přidána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDelete($id){
        $features = $this->context->moduleFeaturingModel->findBy(array('page_page_modules_id' => $id));

        foreach ($features as $feature){
            $this->deleteImageFile($feature->imagename);
        }

        $this->context->moduleFeaturingModel->deleteBy( array('page_page_modules_id' => $id) );
        if(file_exists ( $this->getImageDirectory($this->module->id) )){
            rmdir ( $this->getImageDirectory($this->module->id) );
        }
        parent::renderDelete($id);
    }

    protected function createComponentAkceForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModuleFeaturing:Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);
        $form->addUpload('img', 'Obrázek:')
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');

        $form->addText('title', 'Nadpis', 50)->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat nadpis.');
        $form->addTextArea ('text', 'Text');
        $form->addText('link', 'Odkaz (celá URL adresa)', 1000);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderEdit($id){
        $feature = $this->context->moduleFeaturingModel->findOneBy( array('id' => $id) );
        $this->template->moduleId = $feature->page_page_modules_id;
        $this->template->imageName =   $feature->imagename;
        $this->template->deleteImgUrl = $this->link('ModuleFeaturing:deleteImage', array('id' => $feature->id, 'parent_page_id' => $this->params['parent_page_id']));
        $this['editForm']->setDefaults($feature);
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();

        $form->addUpload('img', 'Obrázek:')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');

        $form->addText('title', 'Nadpis', 50)->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat nadpis.');
        $form->addTextArea ('text', 'Text')->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat text.');
        $form->addText('link', 'Odkaz (celá URL adresa)', 50);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();
        $form->onSuccess[] = array($this, 'editFormSucceeded');

        return $form;
    }

    public function editFormSucceeded(UI\Form $form, $values)
    {
        $feature = $this->context->moduleFeaturingModel->find($this->params['id']);
        $this->loadModuleFromDB($feature->page_page_modules_id);

        if(is_null($form->getHttpData($form::DATA_TEXT, 'cancel')))
        {
            if($values['img']->name != NULL){
                // delete old image:
                $this->deleteImageFile($feature->imagename);
                // upload new image:
                $data['imagename'] = $this->uploadOneImage();
            }
            $data['title'] = $values['title'];
            $data['text'] = $values['text'];
            $data['link'] = $values['link'];

            $feature->update($data);

            $this->flashMessage('Položka byla úspěšně editována.');
        }

        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderEnableDisableFeature($id){
        $enabled = 1;
        $feature = $this->context->moduleFeaturingModel->findOneBy( array('id' => $id) );

        if( 1 === $feature->enabled){
            $enabled = 0;
        }
        $feature->update( array('enabled' => $enabled) );

        $this->flashMessage('Zobrazování položky bylo pozastaveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeleteFeature($id){
        $feature = $this->context->moduleFeaturingModel->findOneBy( array('id' => $id) );
        $this->load($feature->page_page_modules_id);

        $this->context->moduleFeaturingModel->delete($id);
        if(isset($feature->imagename) && !empty($feature->imagename)){
            $this->deleteImageFile($feature->imagename);
        }

        $this->flashMessage('Položka byla smazána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeleteImage($id){
        $feature = $this->context->moduleFeaturingModel->findOneBy( array('id' => $id) );
        $this->loadModuleFromDB($feature->page_page_modules_id);

        if(isset($feature->imagename) && !empty($feature->imagename)){
            $this->deleteImageFile($feature->imagename);
        }

        $feature->update( array('imagename' => NULL) );
        $this->flashMessage('Obrázek byl smazán.');
        $this->redirect('ModuleFeaturing:edit', array('id' => $feature->id, 'parent_page_id' => $this->params['parent_page_id']));
    }

    private function deleteImageFile($fileName){
        if(true === file_exists ( $this->getImageDirectory($this->module->id) . $fileName )){
            return unlink($this->getImageDirectory($this->module->id) . $fileName);
        }
        return true;
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->features = $this->features;
        $this->moduleContentTemplate->addFeatureForm = $this->createComponentAkceForm();
        $this->moduleContentTemplate->parent_page_id = $this->oParentPresenter->getParameter('id');
    }
}