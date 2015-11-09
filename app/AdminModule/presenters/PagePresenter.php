<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI;


/**
 * Homepage presenter.
 */
class PagePresenter extends BasePresenter
{
    private $page = NULL;

    private $modulesCount = 0;

    public function loadModules(){
        $modulesClasses = $this->context->pageModules->loadAdminModules($this->getParameter("id"));
        $modules = array();
        $this->modulesCount = $modulesClasses->getRowCount();
        foreach ($modulesClasses as $module){
            $moduleClass = $this->context->pageModules->loadClass($module->class_id, $this->context);
            $moduleClass->setModulesCount($this->modulesCount);
            $modules[] = html_entity_decode($moduleClass->load($module->id, $this));
        }
        return $modules;
    }

    public function renderDefault()
	{
        $this->template->pagesTable = $this->getPagesTableBody();
	}

    public function getPagesTableBody() {
        $sHtml = '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Název stránky</th>
                            <th>Titulek</th>
                            <th>SEO URL text</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>'
                        . $this->getPagesTableBodyRecursive() .
                    '</tbody>
                </table>';
        return $sHtml;
    }

    private function getPagesTableBodyRecursive($pageId = NULL, $depth = 0,$oPages = NULL){
        // Initialization:
        $aParameters['deleted'] = 0;
        if($oPages == NULL){
            $aParameters['id_parent'] = $pageId;
            $oPages = $this->context->page->findBy($aParameters);
        }

        $sHtml = '';
        foreach($oPages as $oPage) {
            $aParameters['id_parent'] = $oPage->id;
            $oSubPages = $this->context->page->findBy($aParameters);
            // if we have submenu:
            if( $oSubPages->count() > 0 ){
                $sHtml .=       '<tr>
                                    <td>' . $oPage->name . '</td>
                                    <td>' . $oPage->title . '</td>
                                    <td>' . $oPage->seo_url_text . '</td>
                                    <td><a href="' . $this->link('Page:edit',  $oPage->id)  . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>';
                                    if($oPage->id != 1){
                $sHtml .=               '<td><a href="' . $this->link('Page:delete',  $oPage->id) . '" class="confirmation"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
                                    }
                $sHtml .=       '</tr>
                                <tr>
                                    <td colspan="6">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Název stránky</th>
                                                    <th>Titulek</th>
                                                    <th>SEO URL text</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>'
                                                . $this->getPagesTableBodyRecursive($oPage->id, $depth+1, $oSubPages) .
                                            '</tbody>
                                        </table>
                                    </td>
                                </tr>';
            } else {
                $sHtml .=   '<tr>
                                <td>' . $oPage->name . '</td>
                                <td>' . $oPage->title . '</td>
                                <td>' . $oPage->seo_url_text . '</td>
                                <td><a href="' . $this->link('Page:edit',  $oPage->id) . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>';
                                    if($oPage->id != 1){
                $sHtml .=               '<td><a href="' . $this->link('Page:delete',  $oPage->id) . '" class="confirmation"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
                                    } else {
                $sHtml .=               '<td></td>';
                                    }
                $sHtml .=   '</tr>';
            }
        }
        return $sHtml;
    }

    protected function createComponentAddPageModuleToPageForm()
    {
        $aPageModules = $this->context->pageModules->getPageModulesForSelect();

        $form = new \CustomForm();
        $form->addSelect('page_module_id', 'Modul:', $aPageModules)->setPrompt('Zvolte modul');
        $form->addSubmit('addPage', 'Přidat modul');
        $form->onSuccess[] = array($this, 'addPageModuleToPageFormSucceeded');
        $form->setCustomRenderer();
        return $form;
    }

    public function addPageModuleToPageFormSucceeded(UI\Form $form, $values)
    {
        if($values['page_module_id'] == NULL) {
            $this->flashMessage('Musíte vybrat modul, který bude přidán do stránky.', 'danger');
            $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
        } else {

            $this->registerModule($this->getParameter('id'), $values['page_module_id']);

            $this->flashMessage('Modul byl úspěšně přidán.');
            $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
        }
    }


    protected function createComponentAddExistingModuleForm()
    {
        $aPageModules = $this->context->pageModules->getAllModulesForSelect();

        $form = new \CustomForm();
        $form->addSelect('module_id', 'Modul:', $aPageModules)->setPrompt('Zvolte modul');
        $form->addSubmit('addPage', 'Přidat modul');
        $form->onSuccess[] = array($this, 'addExistingModuleFormSucceeded');
        $form->setCustomRenderer();
        return $form;
    }

    public function addExistingModuleFormSucceeded(UI\Form $form, $values)
    {
        if($values['module_id'] == NULL) {
            $this->flashMessage('Musíte vybrat modul, který bude přidán do stránky.', 'danger');
        } else {
            $this->putModuleToPage($this->getParameter('id'), $values['module_id']);
            $this->flashMessage('Modul byl úspěšně přidán.');
        }
        $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
    }

    protected function registerModule($page_id, $page_module_id){

        /* First save the module itself - kind of instance of a module */
        $insertedModuleId = $this->createmoduleInstance($page_module_id);
        $this->putModuleToPage($page_id, $insertedModuleId);

        // initialize module
        $module = $this->context->pageModules->loadClass($page_module_id);
        $module->loadModule($insertedModuleId, $this);
        $module->initialize();
    }

    private function putModuleToPage($pageId, $moduleInstanceId){
        $data = array();
        $data['page_id'] = $pageId;
        $data['position'] = $this->context->pageModuleRegister->findBy(array('page_id' => $pageId))->count()+1;
        $data["page_module_instance_id"] = $moduleInstanceId;
        $data["enabled"] = 1;

        /* Save module data related to the concrete page, One (the same) module can be on many pages */
        $this->context->pageModuleRegister->insert($data);
    }

    private function createmoduleInstance($page_module_id){
        /* First save the module itself - kind of instance of a module */
        $insertedModule =  $this->context->pageModuleInstance->insert( array("module_id" => $page_module_id));
        return $insertedModule->id;
    }

    protected function createComponentAddPageForm()
    {
        $aPages = $this->context->page->getParentsForSelect();

        $form = new \CustomForm();
        $form->addText('name', 'Jméno stránky:')->setRequired('Zadejte prosím jméno stránky.');
        $form->addText('title', 'Titulek stránky:');
        $form->addText('seo_url_text', 'SEO URL text:');
        $form->addSelect('id_parent', 'Nadřazená stránka:', $aPages)->setPrompt('Zvolte nadřazenou stránku');
        $form->addSubmit('addPage', 'Vytvořit novou stránku');
        $form->setCustomRenderer();
        $form->onSuccess[] = array($this, 'addPageFormSucceeded');

        return $form;
    }

    public function addPageFormSucceeded(UI\Form $form, $values)
    {
        if(isset($values) && !empty($values) )
        {
            $data = array();
            $data['name'] = $values['name'];
            $data['title'] = $values['title'];
            $data['seo_url_text'] = $values['seo_url_text'];
            if(isset($values['id_parent']) && !empty($values['id_parent'])){
                $data['id_parent'] = $values['id_parent'];
            }

            $newPage = $this->context->page->insert($data);

            // calculate and save Nice URL text to a newly added page
            unset($data);
            $data['id'] = $newPage->id;
            $data['final_url_text'] = $this->context->page->getFinalUrlText($newPage->id);
            $this->context->page->update($data);

            $this->flashMessage('Stránka byla úspěšně přidána.');
        }

        $this->redirect('Page:default');
    }

	public function renderAdd()
	{

	}

	public function renderDelete($id)
	{
        $data['id'] = $id;
        $data['deleted'] = 1;

        $this->context->page->update($data);
        $this->flashMessage('Stránka byla úspěšně smazána.');
        $this->redirect('Page:default');
	}

    /** createComponentEditPageForm GET VALUES FROM  renderEdit, by line "$this['editPageForm']->setDefaults($this->page);" */
    protected function createComponentEditPageForm()
    {
        $aPages = $this->context->page->getParentsForSelect();

        // disable form submit when the page is homepage/index page of the web,
        // we dont want user to edit these homepage data
        $disabled = false;
        if($this->page){
            if($this->page->id === 0){
                $disabled = true;
            }
        }

        $form = new \CustomForm();
        $form->addText('name', 'Jméno stránky:')->setRequired('Zadejte prosím jméno stránky.');
        $form->addText('title', 'Titulek stránky:');
        $form->addText('seo_url_text', 'SEO URL text:');
        $form->addSelect('id_parent', 'Nadřazená stránka:', $aPages)->setPrompt('Zvolte nadřazenou stránku');
        $form->addCheckbox('online', 'Zobrazovat stránku?');
        $form->addSubmit('editPage', 'Editovat a uložit')->setDisabled($disabled);
        $form->onSuccess[] = array($this, 'editPageFormSucceeded');
        $form->setCustomRenderer();

        return $form;
    }

    // volá se po úspěšném odeslání formuláře
    public function editPageFormSucceeded(UI\Form $form, $values)
    {
        $data = array();
        $data['id'] = $this->getParameter('id');
        $data['name'] = $values['name'];
        $data['title'] = $values['title'];
        $data['seo_url_text'] = $values['seo_url_text'];
        $data['id_parent'] = isset($values['id_parent']) && !empty($values['id_parent']) ? $values['id_parent'] : NULL;
        $data['online'] = true === $values['online'] ? 1 : 0;

        $this->context->page->update($data);

        $data = array();
        $data['id'] = $this->getParameter('id');
        $data['final_url_text'] = $this->context->page->getFinalUrlText($data['id']);
        $this->context->page->update($data);

        $this->flashMessage('Stránka byla úspěšně upravena.');
        $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
    }

	public function renderEdit($id)
	{
        $this->page = $this->context->page->findOneBy(array('id' => $id));
        $this->setLatestPagesCookie();

        if (!$this->page) { // kontrola existence záznamu
            throw new BadRequestException;
        } else {
            $this->template->page = $this->page;
            $this->template->modules = $this->loadModules();
        }
        $this['editPageForm']->setDefaults($this->page);
	}

    private function setLatestPagesCookie(){
        if($this->page){
            $cookieExpiry = time()+60*60*24*7; // expires after 7 days
            $httpRequest = $this->getHttpRequest();
            $latestPagesCookie = $httpRequest->getCookie("latestPages");
            $latestPages[$this->page->id] = array("id" => $this->page->id, "name" => $this->page->name);

            if(NULL == $latestPagesCookie){
                setcookie("latestPages", serialize($latestPages), $cookieExpiry, "/");
            } else {
                setcookie("latestPages", "", time()-3600);
                $latestPagesArray = unserialize($latestPagesCookie);

                // put last visited page to the first place of the recent pages array:
                unset($latestPagesArray[$this->page->id]);
                $latestPagesArray[$this->page->id] = array("id" => $this->page->id, "name" => $this->page->name);

                // max 10 latest pages: 
                array_slice($latestPagesArray, 10);
                setcookie("latestPages", serialize($latestPagesArray), $cookieExpiry, "/");
            }
        }
    }
}
