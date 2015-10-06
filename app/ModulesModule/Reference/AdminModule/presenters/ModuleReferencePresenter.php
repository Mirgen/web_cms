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

class ModuleReferencePresenter extends ModuleBasePresenter
{
    private $references = NULL;

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->references = $this->oParentPresenter->context->moduleReferenceModel->findBy( array('page_page_modules_id' => $this->module->id) )->order('id DESC');
        }
    }

    private function getImageDirectory($referenceId){
        return $this->context->parameters['wwwDir'] . '/images/module_reference/' . $referenceId . "/";
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
            if($image->getWidth() > 1200) {
              $image->resize(1200, NULL);
            }

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
        $data['subtitle'] = $post['subtitle'];
        $data['text'] = $post['text'];
        $data['client'] = $post['client'];
        $data['link'] = $post['link'];
        $data['enabled'] = 1;

        $this->context->moduleReferenceModel->insert($data);

        $this->flashMessage('Reference byla přidána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDelete($id){
        $references = $this->context->moduleReferenceModel->findBy(array('page_page_modules_id' => $id));

        foreach ($references as $reference){
            $this->deleteImageFile($reference->imagename);
        }

        $this->context->moduleReferenceModel->deleteBy( array('page_page_modules_id' => $id) );
        if(file_exists ( $this->getImageDirectory($this->module->id) )){
            rmdir ( $this->getImageDirectory($this->module->id) );
        }
        parent::renderDelete($id);
    }

    protected function createComponentAkceForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link('ModuleReference:Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));
        $form->setAction($url_save);
        $form->addUpload('img', 'Obrázek:')
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');

        $form->addText('title', 'Nadpis', 50)->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat nadpis.');
        $form->addText('subtitle', 'Podtitulek', 50);
        $form->addTextArea ('text', 'Text')
                ->setAttribute("class", "tinymce_text_editor")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat text.');
        $form->addText('client', 'Zákazník (jméno, město, ...)', 50);
        $form->addText('link', 'Odkaz (celá URL adresa)', 50);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderEdit($id){
        $reference = $this->context->moduleReferenceModel->findOneBy( array('id' => $id) );
        $this->template->moduleId = $reference->page_page_modules_id;
        $this->template->imageName =   $reference->imagename;
        $this->template->deleteImgUrl = $this->link('ModuleReference:deleteImage', array('id' => $reference->id, 'parent_page_id' => $this->params['parent_page_id']));
        $this['editForm']->setDefaults($reference);
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();

        $form->addUpload('img', 'Obrázek:')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');

        $form->addText('title', 'Nadpis', 50)->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat nadpis.');
        $form->addText('subtitle', 'Podtitulek', 50);
        $form->addTextArea('text', 'Text')
                ->setAttribute("class", "tinymce_text_editor")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat text.');
        $form->addText('client', 'Zákazník (jméno, město, ...)', 50);
        $form->addText('link', 'Odkaz (celá URL adresa)', 50);

        $form->addSubmit('create', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        $form->setCustomRenderer();
        $form->onSuccess[] = array($this, 'editFormSucceeded');

        return $form;
    }

    public function editFormSucceeded(UI\Form $form, $values)
    {
        $reference = $this->context->moduleReferenceModel->find($this->params['id']);
        $this->load($reference->page_page_modules_id);

        if(is_null($form->getHttpData($form::DATA_TEXT, 'cancel')))
        {
            if($values['img']->name != NULL){
                // delete old image:
                $this->deleteImageFile($reference->imagename);
                // upload new image:
                $data['imagename'] = $this->uploadOneImage();
            }
            $data['title'] = $values['title'];
            $data['subtitle'] = $values['subtitle'];
            $data['text'] = $values['text'];
            $data['client'] = $values['client'];
            $data['link'] = $values['link'];

            $reference->update($data);

            $this->flashMessage('Reference byla úspěšně editována.');
        }

        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderEnableDisableReference($id){
        $enabled = 1;
        $reference = $this->context->moduleReferenceModel->findOneBy( array('id' => $id) );

        if( 1 === $reference->enabled){
            $enabled = 0;
        }
        $reference->update( array('enabled' => $enabled) );

        $this->flashMessage('Zobrazování reference bylo pozastaveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeleteReference($id){
        $reference = $this->context->moduleReferenceModel->findOneBy( array('id' => $id) );
        $this->loadModuleFromDB($reference->page_page_modules_id);

        $this->context->moduleReferenceModel->delete($id);
        if(isset($reference->imagename) && !empty($reference->imagename)){
            $this->deleteImageFile($reference->imagename);
        }

        $this->flashMessage('Reference byla smazána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderDeleteImage($id){
        $reference = $this->context->moduleReferenceModel->findOneBy( array('id' => $id) );
        $this->iModuleId = $reference->page_page_modules_id;

        if(isset($reference->imagename) && !empty($reference->imagename)){
            $this->deleteImageFile($reference->imagename);
        }

        $reference->update( array('imagename' => NULL) );
        $this->flashMessage('Obrázek byl smazán.');
        $this->redirect('ModuleReference:edit', array('id' => $reference->id, 'parent_page_id' => $this->params['parent_page_id']));
    }

    private function deleteImageFile($fileName){
        if(true === file_exists ( $this->getImageDirectory($this->module->id) . $fileName )){
            return unlink($this->getImageDirectory($this->module->id) . $fileName);
        }
        return true;
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->references = $this->references;
        $this->moduleContentTemplate->addReferenceForm = $this->createComponentAkceForm();
        $this->moduleContentTemplate->parent_page_id = $this->oParentPresenter->getParameter('id');
    }
}