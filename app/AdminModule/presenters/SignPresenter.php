<?php

    namespace App\AdminModule\Presenters;

    use Nette;


    /**
     * Sign in/out presenters.
     */
    class SignPresenter extends BasePresenter
    {


        /**
         * Sign-in form factory.
         * @return Nette\Application\UI\Form
         */
        protected function createComponentSignInForm()
        {
            $form = new \CustomForm();
            $form->addText('username', 'Přihlašovací jméno:')
                ->setRequired('Prosím zadejte přihlašovací jméno.')
                ->setAttribute("placeholder","Přihlašovací jméno");

            $form->addPassword('password', 'Heslo:')
                ->setRequired('Prosím zadejte heslo.')
                ->setAttribute("placeholder","Heslo");

            $form->addCheckbox('remember', 'Zapamatovat přihlášení na delší dobu.');

            $form->addSubmit('send', 'Přihlásit');
            // call method signInFormSucceeded() on success
            $form->onSuccess[] = $this->signInFormSucceeded;
            $form->setCustomRenderer();

            return $form;
        }


        public function signInFormSucceeded($form, $values)
        {
            if ($values->remember) {
                $this->getUser()->setExpiration('14 days', FALSE);
            } else {
                $this->getUser()->setExpiration('20 minutes', TRUE);
            }

            try {
                $this->getUser()->login($values->username, $values->password);
                $this->redirect('Default:');

            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError($e->getMessage());
            }
        }

        public function actionOut()
        {
            $this->getUser()->logout();
            $this->flashMessage('You have been signed out.');
            $this->redirect('in');
        }

        public function actionIn()
        {
            // if the user is already logged in redirect to admin homepage
            if($this->getUser()->isLoggedIn()){
                $this->redirect("Default:");
            }
        }
    }
