<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Services\Logger;

/**
 * @last_edited petak23<petak23@gmail.com> 06.07.2021 - sign out
 */
class SignPresenter extends MainBasePresenter
{
    /** @persistent */
    public $username = '';

    /** @persistent */
	public $backlink = '';

    public $links;

    public function __construct(\App\Services\Config $config )
    {
        $this->links = $config->links;
    }

    public function actionIn( $username=NULL ): void
    {
        $response = $this->getHttpResponse();
        $response->setHeader('Cache-Control', 'no-cache');
        $response->setExpiration('1 sec'); 

        $this->username = $username;
    }

    public function renderIn(): void
    {
        $this->template->links = $this->links;
    }
    
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.')
            ->setDefaultValue( $this->username );

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit')
            ->setHtmlAttribute('class', 'btn btn-outline-success')
            ->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        
		return $form;
    }

    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->setExpiration('30 hour');
            $this->getUser()->login($values->username, $values->password);

            // https://pla.nette.org/cs/jak-po-odeslani-formulare-zobrazit-stejnou-stranku
            $this->restoreRequest($this->backlink);

            $this->redirect('Inventory:user');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError("Přihlášení se nepodařilo: {$e->getMessage()}");
        } catch ( \App\Exceptions\UserNotEnrolledException $e ) {
            $this->flashMessage("Nejprve aktivujte účet zadáním kódu z e-mailu.");
            $this->redirect("Enroll:step2", $values->username );
        }
    }

    public function actionOut(): void
    {
        $response = $this->getHttpResponse();
        $response->setHeader('Cache-Control', 'no-cache');
        $response->setExpiration('1 sec'); 

        if( $this->getUser()->getIdentity() ) {
            Logger::log( 'audit', Logger::INFO , 
                "[{$this->getHttpRequest()->getRemoteAddress()}] Logout: odhlasen {$this->getUser()->getIdentity()->username}" ); 

        }
        $this->getUser()->logout(true); // Vymaže aj identitu
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Sign:in');
    }
}
