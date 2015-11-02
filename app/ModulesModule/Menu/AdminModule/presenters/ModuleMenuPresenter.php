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

class ModuleMenuPresenter extends ModuleBasePresenter
{
    // module private variables, e.g. articles for Articles module
    private $menu = NULL;

    private $pages = NULL;

    private $modules = array();

    protected function loadModuleData(){
        if($this->oParentPresenter){
            $this->menu = $this->oParentPresenter->context->moduleMenuModel->getMenu($this->module->id);
            $this->pages = $this->oParentPresenter->context->page->findAll()->order('order DESC')->fetchPairs("id", "name");
            $modules = $this->oParentPresenter->context->pageModules->getAllModules();
        } else {
            $this->menu = $this->context->moduleMenuModel->getMenu($this->module->id);
            $this->pages = $this->context->page->findBy(array('online' => 1) )->order('order DESC')->fetchPairs("id", "name");
            $modules = $this->context->pageModules->getAllModules();
        }

        array_unshift($this->pages, "--- Vyberte stránku ---");
        $this->modules[0] = "--- Vyberte modul ---";
        foreach($modules as $module){
            $this->modules[$module->id] = $module->module_name . " na stránce " . $module->page_name;
        }
    }

    protected function createComponentAddForm() {
        $form = new \CustomForm();
        $url_save = $this->oParentPresenter->link("Module" . $this->moduleName . ':Save', array('id' => $this->module->id, 'parent_page_id' => $this->oParentPresenter->getParameter('id')));

        $form->setAction($url_save);
        $this->setFormItems($form);

        return $form;
    }

    private function setFormItems($form){
        $form->addSelect('module_id', 'Modul (kapitola/sekce stránky)', $this->modules);

        $menuItems[0] = "--- Vyberte nadřazenou stránku ---";
        if(isset($this->menu)){
            foreach($this->menu as $item){
                $menuItems[$item->id] = (NULL !== $item->link_text) ? $item->link_text : $item->page_text;
            }
        }
        $form->addSelect('page_id', 'Stránka', $this->pages);
        $form->addSelect('parent_id', 'Nadřazená stránka', $menuItems);
        $form->addText('link', 'Odkaz (celá URL adresa)', 1000);
        $form->addText('link_text', 'Text odkazu', 128);
        $form->addCheckbox("enabled", "Hned zobrazovat");

        $form->addSubmit('create', 'Uložit');
        $form->setCustomRenderer();
    }

    public function renderSave($id){
        $data = $this->getDataForDB();
        $data['page_page_modules_id'] = $id;
        $this->context->moduleMenuModel->insert($data);

        $this->flashMessage('Položka menu byla přidána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    private function getDataForDB(){
        $post = $this->request->getPost();

        $data = array();
        if($post["page_id"] != 0){
            $data['page_id'] = $post["page_id"];
        }
        else if ($post["module_id"] != 0){
            $data['module_id'] = $post['module_id'];
            $data['page_id'] = $this->context->pageModuleRegister->find($data['module_id'])->page_id;
        }
        else if (isset($post['link'])){
            $data['link'] = $post['link'];
        }
        if($post["parent_id"] != 0){
            $data['parent_id'] = $post["parent_id"];
        } else {
            $data['parent_id'] = NULL;
        }
        $data['link_text'] = $post['link_text'];
        $data['enabled'] = isset($post['enabled']) ? 1 : 0;

        return $data;
    }

    public function renderEdit($id){
        $item = $this->context->moduleMenuModel->findOneBy( array('id' => $id) );

        if($_POST){
            $post = $this->request->getPost();

            if(isset($post["create"])){
                $item->update($this->getDataForDB());
                $this->flashMessage('Položka menu byla úspěšně upravena.');
            }

            $this->redirect('Page:edit', array('id' => $this->module->page_id));
        } else {
            $this->loadModuleData();
            $this['editForm']->setDefaults($item);
        }
    }

    protected function createComponentEditForm()
    {
        $form = new \CustomForm();
        $this->setFormItems($form);
        $form->addSubmit('cancel', 'Zrušit')->setValidationScope(FALSE);
        if(!$_POST){
            $url_save = $this->link("Module" . $this->moduleName . ':Edit', array('moduleid' => $this->module->id, 'id' => $this->getParameter('id'), 'parent_page_id' => $this->module->page_id));
            $form->setAction($url_save);
        }

        $form->setCustomRenderer();
        return $form;
    }

    public function editFormSucceeded(\CustomForm $form, $values)
    {
        $item = $this->context->moduleMenuModel->find($this->params['id']);
        $post = $this->request->getPost();

        if(isset($post["create"])){
            $item->update($this->getDataForDB());
            $this->flashMessage('Položka menu byla úspěšně upravena.');
        }

        $this->redirect('Page:edit', array('id' => $this->module->page_id));
    }

    public function renderUpdateOrder(){
        if ($this->isAjax()) {
            $post = $this->request->post;
            if (isset($post['item']) && !empty($post['item'])) {
                foreach($post['item'] as $itemOrder => $itemId) {
                    $this->context->moduleMenuModel->find($itemId)->update(array("order" => $itemOrder + 1));
                }
                echo 1;
            } 
            else if (isset($post['subitem']) && !empty($post['subitem'])) {
                foreach($post['subitem'] as $itemOrder => $itemId) {
                    $this->context->moduleMenuModel->find($itemId)->update(array("order" => $itemOrder + 1));
                }
                echo 1;
            } 
            else {
              // No items provided for reordering.
                echo 0;
            }
        }
    }

    public function renderDelete($id){
        $this->context->moduleMenuModel->deleteBy( array('page_page_modules_id' => $id) );
        parent::renderDelete($id);
    }

    public function renderDeleteItem($id){
        // delete all its submenus
        $this->context->moduleMenuModel->deleteBy(array("parent_id" => $id));
        // delete the menu item itself
        $this->context->moduleMenuModel->delete($id);

        $this->flashMessage('Položka menu byla smazána.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function renderEnableDisableItem($id){
        $this->context->moduleMenuModel->query("UPDATE `module_menu` SET `enabled` = 1 + `enabled` * (-1) WHERE `id` = $id");
        $this->flashMessage('Zobrazování bylo upraveno.');
        $this->redirect('Page:edit', array('id' => $this->params['parent_page_id']));
    }

    public function loadContentTemplate(){
        $this->moduleContentTemplate->menu = $this->menu;
        $this->moduleContentTemplate->addForm = $this->createComponentAddForm();
        $this->moduleContentTemplate->updateOrderUrl = $this->oParentPresenter->link("Module" . $this->moduleName . ':UpdateOrder');
    }
}