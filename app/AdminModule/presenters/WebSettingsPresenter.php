<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI;


/**
 * Homepage presenter.
 */
class WebSettingsPresenter extends BasePresenter
{
    /*
     * integer max width of logo - image of logo will be resized not to be bigger than this value
     */
    private $maxLogoWidth = 300;

    public function renderDefault()
    {

    }

    protected function createComponentSettings()
    {
        $form = new \CustomForm();
        $form->addUpload('logo', 'Logo')
                ->addCondition(\Nette\Application\UI\Form::FILLED)
                    ->addRule(\Nette\Application\UI\Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.');
        $form->addText('title', 'Titulek stránky', 64);
        $form->addText('description', 'Popis stránky', 256);
        $form->addText('keywords', 'Klíčová slova stránky', 512);
        $form->addSubmit('addPage', 'Uložit');

        $form->setDefaults($this->settings);
        $form->onSuccess[] = array($this, 'addPageModuleToPageFormSucceeded');
        $form->setCustomRenderer();
        return $form;
    }

    public function addPageModuleToPageFormSucceeded(UI\Form $form, $values)
    {
        $settings = array();

        $settings['title'] = $values['title'];
        $settings['keywords'] = $values['keywords'];
        $settings['description'] = $values['description'];

        if(0 == $values['logo']->error){
            $imageUploader = new \ImageUploader($this->request->getFiles());
            $imageUploader->setNewFileName("logo");
            $imageUploader->setMaxWidth($this->maxLogoWidth);
            $uploadedImages = $imageUploader->startUpload();

            if(true === isset($uploadedImages[0]["error"])){
                $this->flashMessage("Error '" . $uploadedImages[0]["error"] . "' while uploading file '" .$uploadedImages[0]["original"] .  "'.");
            } else {
                // delete old logo:
                if(isset($settings["logo"])){
                    $imageUploader->deleteImage($settings["logo"]);
                }
                $settings["logo"] = $uploadedImages[0]["name"].$uploadedImages[0]["extension"];
            }
        }

        if(false === empty($settings)){
            $this->saveSettings($settings);
        }
        $this->flashMessage("Nastavení bylo uloženo.");
        $this->redirect('WebSettings:Default');
    }

    private function saveSettings($settings){
        foreach($settings as $name => $value){
            if(false === $this->updateSetting($name, $value)){
                $this->context->settings->insert(array("name" => $name, "value" => $value));
            }
        }
    }

    public function renderDeleteLogo(){
        $imageUploader = new \ImageUploader(NULL);
        // delete logo from server filesystem
        $imageUploader->deleteImage($this->settings["logo"]);

        // delete logo from database
        $this->context->settings->deleteBy(array("name" => "logo"));

        $this->flashMessage("Logo bylo smazáno.");
        $this->redirect('WebSettings:Default');
    }

    private function updateSetting($name, $value){
        $setting = $this->context->settings->findOneBy(array("name" => $name));
        if($setting){
            $setting->update(array("value" => $value));
            return $setting;
        }
        return false;
    }
}
