<?php

namespace App\Presenters;

use Nette,
	App\Model, 
    Nette\Application\UI;


/**
 * Homepage presenter.
 */
class PagePresenter extends BasePresenter
{
    private $page = NULL;

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
                            <th>SEO text</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>'
                        . $this->getPagesTableBodyRecursive() .
                    '</tbody>
                </table>
                </div>';
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
                                    <td>' . $oPage->seo_text . '</td>
                                    <td><a href="' . $this->link('Page:edit',  $oPage->id)  . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                    <td><span class="glyphicon glyphicon-play" aria-hidden="true"></span></td>
                                    <td><a href="' . $this->link('Page:delete',  $oPage->id) . '" class="confirmation"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Název stránky</th>
                                                    <th>Titulek</th>
                                                    <th>SEO text</th>
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
                                <td>' . $oPage->seo_text . '</td>
                                <td><a href="' . $this->link('Page:edit',  $oPage->id) . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td><span class="glyphicon glyphicon-play" aria-hidden="true"></span></td>
                                <td><a href="' . $this->link('Page:delete',  $oPage->id) . '" class="confirmation"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                            </tr>';
            }
        }
        return $sHtml;
    }

    protected function createComponentAddPageModuleToPageForm()
    {
        $aPageModules = $this->context->pageModules->getPageModulesForSelect();

        $form = new UI\Form;
        $form->addSelect('page_module_id', 'Modul:', $aPageModules)->setPrompt('Zvolte modul');
        $form->addSubmit('addPage', 'Přidat modul');
        $form->onSuccess[] = array($this, 'addPageModuleToPageFormSucceeded');
        return $form;
    }

    public function addPageModuleToPageFormSucceeded(UI\Form $form, $values)
    {
        if($values['page_module_id'] == NULL) {
            $this->flashMessage('Musíte vybrat modul, který bude přidán do stránky.', 'error');
            $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
        } else {
            $data['page_id'] = $this->getParameter('id');
            $data['page_module_id'] = $values['page_module_id'];

            $this->context->pageModuleRegister->insert($data);

            $this->flashMessage('Modul byl úspěšně přidán.');
            $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
        }
    }

    protected function createComponentAddPageForm()
    {
        $aPages = $this->context->page->getParentsForSelect();

        $form = new UI\Form;
        $form->addText('name', 'Jméno stránky:')->setRequired('Zadejte prosím jméno stránky.');
        $form->addText('title', 'Titulek stránky:');
        $form->addText('seo_text', 'SEO text:');
        $form->addSelect('id_parent', 'Nadřazená stránka:', $aPages)->setPrompt('Zvolte nadřazenou stránku');
        $form->addSubmit('addPage', 'Vytvořit novou stránku');
        $form->onSuccess[] = array($this, 'addPageFormSucceeded');
        return $form;
    }

    // volá se po úspěšném odeslání formuláře
    public function addPageFormSucceeded(UI\Form $form, $values)
    {
        $post = $this->request->getPost();

        if(isset($post) && !empty($post) ){

            $data['name'] = $post['name'];
            $data['title'] = $post['title'];
            $data['seo_text'] = $post['seo_text'];
            if(isset($post['id_parent']) && !empty($post['id_parent'])){
                $data['id_parent'] = $post['id_parent'];
            }

            $this->context->page->insert($data);
        }

        $this->flashMessage('Stránka byla úspěšně přidána.');
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

        $form = new UI\Form;
        $form->addText('name', 'Jméno stránky:')->setRequired('Zadejte prosím jméno stránky.');
        $form->addText('title', 'Titulek stránky:');
        $form->addText('seo_text', 'SEO text:');
        $form->addSelect('id_parent', 'Nadřazená stránka:', $aPages)->setPrompt('Zvolte nadřazenou stránku');
        $form->addCheckbox('online', 'Zobrazovat stránku?');
        $form->addSubmit('editPage', 'Editovat a uložit');
        $form->onSuccess[] = array($this, 'editPageFormSucceeded');

        return $form;
    }

    // volá se po úspěšném odeslání formuláře
    public function editPageFormSucceeded(UI\Form $form, $values)
    {
        $data['id'] = $this->getParameter('id');
        $data['name'] = $values['name'];
        $data['title'] = $values['title'];
        $data['seo_text'] = $values['seo_text'];
        $data['id_parent'] = isset($values['id_parent']) && !empty($values['id_parent']) ? $values['id_parent'] : NULL;
        $data['online'] = isset($values['online']) ? 1 : 0;

        $this->context->page->update($data);
        $this->flashMessage('Stránka byla úspěšně přidána.');
        $this->redirect('Page:edit', array('id' => $this->getParameter('id')));
    }

	public function renderEdit($id)
	{
        $this->page = $this->context->page->findOneBy(array('id' => $id));

        if (!$this->page) { // kontrola existence záznamu
            throw new BadRequestException;
        } else {
            $this->template->page = $this->page;
            $this->template->modules = $this->loadModules();
        }

        $this['editPageForm']->setDefaults($this->page);
	}

}
