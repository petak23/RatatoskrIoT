<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Http\Url;

use App\Model;
use App\Services;

final class InventoryPresenter extends BaseAdminPresenter
{
	use Nette\SmartObject;

	/** @persistent */
	public $viewid = "";

	/** @var Services\InventoryDataSource */
	private $datasource;

	/** @var Nette\Security\Passwords */
	private $passwords;

	// --- DB Models ---
	/** @var Model\PV_Units @inject */
	public $units;
	/** @var Model\PV_Devices @inject */
	public $devices;

	public function __construct(
		Services\Config $config
	) {
		$this->links = $config->links;
		$this->appName = $config->appName;
	}

	public function injectPasswords(Nette\Security\Passwords $passwords)
	{
		$this->passwords = $passwords;
	}

	public function injectDatasource(Services\InventoryDataSource $datasource)
	{
		$this->datasource = $datasource;
	}


	// Seznam  Zařízení

	// http://lovecka.info/ra/inventory/home/
	public function renderHome()
	{
		$this->checkUserRole('user');
		$this->populateTemplate(1);
		$this->template->devices = $this->devices->getDevicesUser($this->getUser()->id);
	}

	public function renderUnits()
	{
		$this->checkUserRole('user');
		$this->populateTemplate(5);
		$this->template->units = $this->units->getUnits();
	}


	// http://lovecka.info/ra/inventory/user/
	public function renderUser()
	{
		$this->checkUserRole('user');
		$this->populateTemplate(3);
		$this->template->userData = $this->datasource->getUser($this->getUser()->id);

		if ($this->template->userData['prev_login_ip'] != NULL) {
			$this->template->prev_login_name = gethostbyaddr($this->template->userData['prev_login_ip']);
			if ($this->template->prev_login_name === $this->template->userData['prev_login_ip']) {
				$this->template->prev_login_name = NULL;
			}
		}
		if ($this->template->userData['last_error_ip'] != NULL) {
			$this->template->last_error_name = gethostbyaddr($this->template->userData['last_error_ip']);
			if ($this->template->last_error_name === $this->template->userData['last_error_ip']) {
				$this->template->last_error_name = NULL;
			}
		}


		$url = new Url($this->getHttpRequest()->getUrl()->getBaseUrl());
		$this->template->monitoringUrl = $url->getAbsoluteUrl() . "monitor/show/{$this->template->userData['monitoring_token']}/{$this->getUser()->id}/";
	}


	protected function createComponentUserForm(): Form
	{
		$form = new Form;
		$form->addProtection();

		$form->addEmail('email', 'E-mail adresa:')
			->setRequired()
			->setOption('description', 'Na tuto adresu se zasílají e-mail notifikace.')
			->setHtmlAttribute('size', 50);

		$form->addText('monitoring_token', 'Bezpečnostní token pro výstup monitoringu:')
			->setOption('description', 'Pokud není vyplněn, data nejsou bez autorizace dostupná.')
			->setHtmlAttribute('size', 50);

		$form->addSubmit('send', 'Uložit')
			->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

		$form->onSuccess[] = [$this, 'userFormSucceeded'];

		$this->makeBootstrap4($form);
		return $form;
	}


	public function userFormSucceeded(Form $form, array $values): void
	{
		$this->datasource->updateUser($this->getUser()->id, $values);
		$this->flashMessage('Změny provedeny.');
		$this->redirect("Inventory:user");
	}

	public function actionEdit(): void
	{
		$this->checkUserRole('user');
		$this->populateTemplate(3);

		$post = $this->datasource->getUser($this->getUser()->id);
		if (!$post) {
			$this->error('Uživatel nebyl nalezen');
		}
		$post = $post->toArray();

		$this['userForm']->setDefaults($post);
	}


	protected function createComponentPasswordForm(): Form
	{
		$form = new Form;
		$form->addProtection();

		$form->addPassword('oldPassword', 'Stávající heslo:')
			->setOption('description', 'Vyplňte své aktuálně platné heslo.')
			->setRequired();

		$form->addPassword('password', 'Nové heslo:')
			->setOption('description', 'Nové heslo, které chcete nastavit.')
			->setRequired();

		$form->addPassword('password2', 'Opakujte nové heslo:')
			->setOption('description', 'Zadejte nové heslo ještě jednou.')
			->addRule($form::EQUAL, 'Heslo musí být zadáno dvakrát stejně!', $form['password'])
			->setRequired();

		$form->addSubmit('send', 'Změnit heslo')
			->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

		$form->onSuccess[] = [$this, 'passwordFormSucceeded'];

		$this->makeBootstrap4($form);
		return $form;
	}


	public function passwordFormSucceeded(Form $form, array $values): void
	{
		$post = $this->datasource->getUser($this->getUser()->id);
		if (!$post) {
			$this->error('Uživatel nebyl nalezen');
		}
		if (!$this->passwords->verify($values['oldPassword'], $post->phash)) {
			$form['oldPassword']->addError('Neplatné heslo - zadejte správné stávající heslo.');
			return;
		}

		$hash = $this->passwords->hash($values['password']);
		$this->datasource->updateUserPassword($this->getUser()->id, $hash);

		$this->flashMessage('Změna hesla provedena.');
		$this->redirect("Inventory:user");
	}

	public function actionPassword(): void
	{
		$this->checkUserRole('user');
		$this->populateTemplate(3);
	}
}
