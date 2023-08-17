<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms;
use App\Services;
use App\Services\Logger;
use Nette\Application\UI\Form;

/**
 * Základny presenter pre prihlásenie
 * 
 * Posledna zmena(last change): 17.08.2023
 *
 * @author Petr BROUZDA
 * @author Ing. Peter VOJTECH ml.
 * @version 1.0.2
 */
class SignPresenter extends BaseNotLogPresenter
{
	/** @persistent */
	public $username = '';

	/** @persistent */
	public $backlink = '';

	// -- Forms
	/** @var Forms\Sign\SignInFormFactory @inject*/
	public $signInForm;

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

	/** 
	 * Formular pre prihlasenie uzivatela. */
	protected function createComponentSignInForm(): Form
	{
		$form = $this->signInForm->create($this->language);
		$form->onSuccess[] = function () {
			// https://pla.nette.org/cs/jak-po-odeslani-formulare-zobrazit-stejnou-stranku
			$this->restoreRequest($this->backlink);
			$this->redirect('Inventory:user');
		};
		return $form;
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
				"[{$this->getHttpRequest()->getRemoteAddress()}] Logout with email: {$this->getUser()->getIdentity()->email}"
			);
		}
		$this->getUser()->logout();
		$this->flashMessage('base_log_out_mess');
		$this->redirect('Sign:in');
	}
}
