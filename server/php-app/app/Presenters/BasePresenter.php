<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\Logger;
use Language_support;
use Nette;
use PeterVojtech;
use Tracy\Debugger;

class BasePresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;
	use PeterVojtech\MainLayout\Favicon\faviconTrait;
	use PeterVojtech\MainLayout\GoogleAnalytics\googleAnalyticsTrait;

	/** @var Language_support\LanguageMain */
	public $texty_presentera;

	/** @var string Skratka aktualneho jazyka 
	 * @persistent */
	public $language = 'sk';

	public function injectTexty_presentera(Language_support\LanguageMain $texty_presentera)
	{
		$this->texty_presentera = $texty_presentera;
	}

	protected function startup()
	{
		parent::startup();

		//Nastavenie textov podla jazyka 
		$this->texty_presentera->setLanguage($this->language);
	}

	public function beforeRender(): void
	{
		$this->template->setTranslator($this->texty_presentera);
	}

	public function checkUserRole($reqRole)
	{
		if (!$this->getUser()->loggedIn) {
			Logger::log(
				'webapp',
				Logger::ERROR,
				"[{$this->getHttpRequest()->getRemoteAddress()}] ACCESS: " . $this->texty_presentera->translate('log_base_not_logged_in')
			);

			if ($this->getUser()->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('base_long_inactivity');
			} else {
				$this->flashMessage('base_not_logged_now');
			}

			$response = $this->getHttpResponse();
			$response->setHeader('Cache-Control', 'no-cache');
			$response->setExpiration('1 sec');

			// https://pla.nette.org/cs/jak-po-odeslani-formulare-zobrazit-stejnou-stranku
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		if (!$this->getUser()->isInRole($reqRole)) {
			Logger::log(
				'audit',
				Logger::ERROR,
				"[{$this->getHttpRequest()->getRemoteAddress()}] ACCESS: Uzivatel #{$this->getUser()->id} {$this->getUser()->getIdentity()->email} zkusil pouzit funkci vyzadujici roli {$reqRole}"
			);

			$response = $this->getHttpResponse();
			$response->setHeader('Cache-Control', 'no-cache');
			$response->setExpiration('1 sec');

			$this->getUser()->logout();
			$this->flashMessage('Vaše úroveň oprávnění nestačí k použití této funkce!');
			$this->redirect('Sign:in');
		}
	}

	private function addMenuItem($vals, $submenuAfterItem = FALSE, $submenu = NULL)
	{
		$this->template->menu[] = $vals;
		if ($vals['id'] == $submenuAfterItem) {
			foreach ($submenu as $item) {
				$this->template->menu[] = $item;
			}
		}
	}

	public function populateMenu($activeItem, $submenuAfterItem = FALSE, $submenu = NULL)
	{
		$this->template->menu = array();

		$this->addMenuItem(
			['id' => '3', 'link' => 'inventory/user', 'name' => 'Můj účet'],
			$submenuAfterItem,
			$submenu
		);

		if ($this->getUser()->isInRole('admin')) {
			$this->addMenuItem(
				['id' => '6', 'link' => 'user/list', 'name' => 'Uživatelé'],
				$submenuAfterItem,
				$submenu
			);
		}

		$this->addMenuItem(
			['id' => '1', 'link' => 'inventory/home', 'name' => 'Zařízení'],
			$submenuAfterItem,
			$submenu
		);
		$this->addMenuItem(
			['id' => '2', 'link' => 'view/views', 'name' => 'Grafy'],
			$submenuAfterItem,
			$submenu
		);
		$this->addMenuItem(
			['id' => '5', 'link' => 'inventory/units', 'name' => 'Kódy jednotek'],
			$submenuAfterItem,
			$submenu
		);

		$this->template->menuId = $activeItem;

		$response = $this->getHttpResponse();
		$response->setHeader('Cache-Control', 'no-cache');
		$response->setExpiration('1 sec');
	}
}
