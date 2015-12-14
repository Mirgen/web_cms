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

class ModuleSimpleEshopPresenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $products = NULL;

    public function initialize()
    {
        // add new setting available for the module
        $this->addNewModuleSetting("Nadpis", "title", "E-shop");
        $this->addNewModuleSetting("Měna", "currency", "Kč");
        $this->addNewModuleSetting("Měna za cenou?", "currency_position", 1);
        $this->addNewModuleSetting("Váš e-mail pro e-shop", "email", "info@example.com");
        $this->addNewModuleSetting("Hláška potvrzení objednávky", "order_confirmation", "Produkt byl zakoupen. Budeme se vaší objednávkou co nejdřív zabývat. Na e-mail vám bylo odesláno potvrzení.");
        parent::initialize();
    }

    protected function loadModuleData(){
        $this->products = $this->db->getProductsByModuleID($this->module->id);
    }

    public function renderDelete($id){
        $this->db->deleteEshop($id);
        \DirectoryHelper::deleteDirectory(parent::getPathToImages() . $this->module->id . "/");
        parent::renderDelete($id);
    }

    protected function createComponentAddForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link("Module" . $this->moduleName . ':Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));

        $form->setAction($url_save);
        $this->setFormItems($form);

        return $form;
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();
        $this->setFormItems($form);
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);

        if(!$this->request->getPost()){
            $url_save = $this->link("Module" . $this->moduleName . ':Edit', array('moduleid' => $this->module->id, 'id' => $this->getParameter('id'), 'parent_page_id' => $this->module->page_id));
            $form->setAction($url_save);
        }

        $form->setCustomRenderer();
        return $form;
    }

    protected function createComponentAddImagesForm() {
        $form = new \CustomForm();
        $url_save = $this->link('Module' . $this->moduleName . ':SaveImages', array('moduleid' => $this->module->id, 'id' => $this->params['id']));
        $form->setAction($url_save);
        $form->addUpload('img', 'Přidat obrázek:', true)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
                //->addRule(\Nette\Application\UI\Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 5 MB.', '50000')
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat image.');

        $form->addSubmit('create', 'Nahrát');
        $form->addSubmit('cancel', 'Zrušit')
                ->setValidationScope(FALSE);
        $form->setCustomRenderer();

        return $form;
    }

    public function renderSaveImages($id)
    {
        $post = $this->request->getPost();

        if(!isset($post['cancel'])){
            $imageUploader = new \ImageUploader($this->request->getFiles(), "module" .  $this->moduleName . "/" . $this->module->id . "/" . $id, true, true);
            $uploadedImages = $imageUploader->startUpload();

            foreach($uploadedImages as $image){
                if(true === isset($image["error"])){
                    $this->flashMessage("Error '" . $image["error"] . "' while uploading file '" . $image["original"] .  "'.");
                } else {
                    $this->createAdditionalThumbnail($image["name"], $image["extension"], $id);

                    $data["product_id"] = $id;
                    $data["filename"] = $image["name"];
                    $data["file_extension"] = $image["extension"];
                    $this->db->insertImage($data);
                }
            }
        }

        $this->redirect("Module" . $this->moduleName . ':Edit', array('id' => $id, 'moduleid' => $this->module->id));
    }

    private function setFormItems($form){
        $form->addUpload('main_image', 'Hlavní obrázek:')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');
        $form->addText('title', 'Název produktu', 128)
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat název.');
        $form->addTextArea ('description', 'Popis')
                ->setAttribute("class", "tinymce_text_editor")
                ->addRule(\Nette\Application\UI\Form::FILLED, 'Je nutné zadat text.');
        $form->addText('price', 'Cena')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::FLOAT, 'Cena musí být číslo');
        $form->addText('discount_percentage', 'Sleva v %')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::INTEGER, 'Sleva v % musí být číslo');
        $form->addText('discount_amount', 'Sleva částka')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                ->addRule(\Nette\Application\UI\Form::FLOAT, 'Sleva v % musí být číslo');

        $form->addSubmit('create', 'Uložit');
        $form->setCustomRenderer();
    }

    public function renderSave($id){
        $post = $this->request->getPost();
        $data = array();

        $data['page_page_modules_id'] = $id;
        $data['title'] = $post["title"];
        $data['description'] = $post["description"];
        $data['price'] = $post["price"];
        $data['discount_percentage'] = $post["discount_percentage"];
        $data['discount_amount'] = $post["discount_amount"];
        $ret = $this->db->insert($data);

        $data = array();
        // upload main image
        if($ret){
            $this->saveMainImage($ret->id);
        }

        $this->flashMessage('Produkt byl přidán.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    private function createAdditionalThumbnail($fileName, $fileExtension, $product_id)
    {
        return \ImageUploader::createThumbnail(
            WWW_DIR . "/images/" . "module" .  $this->moduleName . "/" . $this->module->id . "/" . $product_id . "/" . $fileName . $fileExtension, // source 
            WWW_DIR . "/images/" . "module" .  $this->moduleName . "/" . $this->module->id . "/" . $product_id . "/" . $fileName . "_th" . $fileExtension, //destination 
            400, // 400px width 
            NULL // max height 
        );
    }

    private function saveMainImage($product_id){
        $files = $this->request->getFiles();

        if($files["main_image"]){
            if(0 === $files["main_image"]->error){
                $imageUploader = new \ImageUploader($files, "module" .  $this->moduleName . "/" . $this->module->id . "/" . $product_id, true, true);
                $uploadedImages = $imageUploader->startUpload();
                $this->createAdditionalThumbnail($uploadedImages[0]["name"], $uploadedImages[0]["extension"], $product_id);

                if(true === isset($uploadedImages[0]["error"])){
                    $this->flashMessage("Error '" . $uploadedImages[0]["error"] . "' while uploading file '" .$uploadedImages[0]["original"] .  "'.");
                } else {
                    $data["product_id"] = $product_id;
                    $data["filename"] = $uploadedImages[0]["name"];
                    $data["file_extension"] = $uploadedImages[0]["extension"];
                    $this->db->insertMainImage($data);
                }
            }
        }
    }

    public function renderEnableDisableProduct($id){
        $this->db->enableDisable($id);

        $this->flashMessage('Zobrazování produktu bylo změněno.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderEnableDisableImage($id){
        $image = $this->db->enableDisableImage($id);

        $this->flashMessage('Zobrazování obrázku bylo změněno.');
        $this->redirect("Module" . $this->moduleName . ':Edit', array('id' => $image->product_id, 'moduleid' => $this->module->id));
    }

    public function renderEdit($id){
        $post = $this->request->getPost();
        if($post){
            $data = array();

            $data['id'] = $id;
            $data['title'] = $post["title"];
            $data['description'] = $post["description"];
            $data['price'] = $post["price"];
            $data['discount_percentage'] = $post["discount_percentage"];
            $data['discount_amount'] = $post["discount_amount"];

            $this->db->update($data);
            if($this->request->getFiles()){
                $this->saveMainImage($id);
            }

            $this->redirect("Module" . $this->moduleName . ':Edit', array('id' => $id, 'moduleid' => $this->module->id));
        } else {
            $product = $this->db->getProduct($id);
            $this->template->product = $product;
            $this->template->addImagesForm = $this->createComponentAddImagesForm();
            $this['editForm']->setDefaults($product);
        }
    }

    public function getPathToImagesByProduct($product_id){
        $basePath = parent::getPathToImages();
        return $basePath . $this->module->id . "/" . $product_id . "/";
    }

    public function renderDeleteProduct($id){
        // delete files 
        \DirectoryHelper::deleteDirectory($this->getPathToImagesByProduct($id));
        // delete all from database 
        $this->db->deleteProduct($id);
        $this->flashMessage('Product byl smazán.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderDeleteOrder($id){
        $this->db->deleteOrder($id);
        $this->flashMessage('Objednávka byla smazána.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderToggleOrderProcessed($id){
        $this->db->toggleOrderStatus($id);
        $this->flashMessage('Status objednávky byl změněn.');
        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderDeleteImage($id){
        $image = $this->db->getImage($id);
        $file = $this->getPathToImagesByProduct($image->product_id) . $image->filename . $image->file_extension;
        $thumb_file = $this->getPathToImagesByProduct($image->product_id) . $image->filename . "_t" . $image->file_extension;
        $thumb_file2 = $this->getPathToImagesByProduct($image->product_id) . $image->filename . "_th" . $image->file_extension;

        // delete image file 
        if(file_exists($file)){
            unlink($file);
        }
        // delete thumb image file 
        if(file_exists($thumb_file)){
            unlink($thumb_file);
        }
        // delete second thumb image file 
        if(file_exists($thumb_file2)){
            unlink($thumb_file2);
        }
        // delete all from database 
        $image->delete();

        $this->flashMessage('Obrázek byl smazán.');
        $this->redirect("Module" . $this->moduleName . ':Edit', array('id' => $image->product_id, 'moduleid' => $this->module->id));
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->imagePath = $this->getPathToImages();
        $this->moduleContentTemplate->orders = $this->db->getOrders($this->module->id);
        $this->moduleContentTemplate->products = $this->products;
        $this->moduleContentTemplate->addForm = $this->createComponentAddForm();
    }
}