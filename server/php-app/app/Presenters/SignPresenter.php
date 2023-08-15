<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Services\Logger;
use App\Services;

/**
 * Zakladny presenter pre prihlásenie
 * 
 * Posledna zmena(last change): 02.08.2023
 *
 * @author Petr BROUZDA
 * @author Ing. Peter VOJTECH ml.
 * @version 1.0.1
 */
class SignPresenter extends BaseNotLogPresenter
{
	/** @persistent */
	public $username = '';

	/** @persistent */
	public $backlink = '';

	public function __construct(Services\Config $config)
	{
		$this->appName = $config->appName;
		$this->reg_enabled = $config->reg_enabled;
		$this->links = $config->links;
	}

	public function actionIn($username = NULL): void
	{
		$response = $this->getHttpResponse();
		$response->setHeader('Cache-Control', 'no-cache');
		$response->setExpiration('1 sec');

		$this->username = $username;
	}

	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->setTranslator($this->texty_presentera);
		$form->addText('username', 'SignInForm_username')
			->setRequired('SignInForm_username_sr')
			->addRule($form::MIN_LENGTH, 'SignInForm_username_min_lenght', 2)
			->setDefaultValue($this->username);

		$form->addPassword('password', 'SignInForm_password')
			->addRule($form::MIN_LENGTH, 'SignInForm_password_min_lenght', 5)
			->setRequired('SignInForm_password_req');

		$form->addSubmit('send', 'SignInForm_login')
			->setHtmlAttribute('class', 'btn btn-success')
			->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		$renderer = $form->getRenderer();
		$renderer->wrappers['error']['container'] = 'div class="row"';
		$renderer->wrappers['error']['item'] = 'div class="col-md-12 alert alert-danger"';
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
			$form->addError(sprintf($this->texty_presentera->translate('SignInForm_main_error'), $e->getMessage()));
		} catch (\App\Exceptions\UserNotEnrolledException $e) {
			$this->flashMessage($this->texty_presentera->translate('UserNotEnrolledException'));
			$this->redirect("Enroll:step2", $values->username);
		}
	}

	public function actionOut(): void
	{
		$response = $this->getHttpResponse();
		$response->setHeader('Cache-Control', 'no-cache');
		$response->setExpiration('1 sec');

		if ($this->getUser()->getIdentity()) {
			Logger::log(
				'audit',
				Logger::INFO,
				"[{$this->getHttpRequest()->getRemoteAddress()}] Logout: odhlasen {$this->getUser()->getIdentity()->username}"
			);
		}
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Sign:in');
	}
}
