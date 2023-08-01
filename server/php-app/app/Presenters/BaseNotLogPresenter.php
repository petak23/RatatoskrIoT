<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use PeterVojtech;

/**
 * Zakladny presenter pre vsetky presentery pre prihlásenie a registráciu
 * 
 * Posledna zmena(last change): 01.08.2023
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright Copyright (c) 2023 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link      http://petak23.echo-msz.eu
 * @version 1.0.0
 */
abstract class BaseNotLogPresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;
	use PeterVojtech\MainLayout\Favicon\faviconTrait;
	use PeterVojtech\MainLayout\GoogleAnalytics\googleAnalyticsTrait;

	protected $appName;
	protected $reg_enabled;

	public function beforeRender(): void
	{
		$this->template->appName = $this->appName;
		$this->template->reg_enabled = $this->reg_enabled;
	}
}
