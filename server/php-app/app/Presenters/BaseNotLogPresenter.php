<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use PeterVojtech;
use Language_support;

/**
 * Zakladny presenter pre vsetky presentery pre prihlásenie a registráciu
 * 
 * Posledna zmena(last change): 15.08.2023
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright Copyright (c) 2023 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link      http://petak23.echo-msz.eu
 * @version 1.0.2
 */
abstract class BaseNotLogPresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;
	use PeterVojtech\MainLayout\Favicon\faviconTrait;
	//use PeterVojtech\MainLayout\GoogleAnalytics\googleAnalyticsTrait;

	protected $appName;
	protected $reg_enabled;
	protected $links;

	/** @var Language_support\LanguageMain @inject */
	public $texty_presentera;

	/** @var string Skratka aktualneho jazyka 
	 * @persistent */
	public $language = 'sk';

	protected function startup()
	{
		parent::startup();

		//Nastavenie textov podla jazyka 
		$this->texty_presentera->setLanguage($this->language);
	}

	public function beforeRender(): void
	{
		$this->template->appName = $this->appName;
		$this->template->reg_enabled = $this->reg_enabled;
		$this->template->links = $this->links;
		$this->template->setTranslator($this->texty_presentera);
	}
}
