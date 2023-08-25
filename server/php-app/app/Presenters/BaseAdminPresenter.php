<?php

declare(strict_types=1);

namespace App\Presenters;

use	App\Services\Logger;
use Nette;
use Nette\Application\UI\Form;

class BaseAdminPresenter extends BasePresenter
{
	use Nette\SmartObject;

	// hodnoty z konfigurace
	/** @var string v config.neon hodnota title */
	public $appName;
	/** @var array */
	public $links;

	public function checkAcces(int $deviceUserId, string $type = "zařízení"): void
	{
		if ($this->getUser()->id != $deviceUserId) {
			Logger::log(
				'audit',
				Logger::ERROR,
				sprintf($this->texty_presentera->translate('log_base_not_allowed'), $this->getUser()->id, $this->getUser()->getIdentity()->email, $type, $deviceUserId)
			);
			$this->getUser()->logout(true);
			$this->flashRedirect('Sign:in', sprintf($this->texty_presentera->translate('base_not_rights'), $type), 'warning');
		}
	}

	public function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->appName = $this->appName;
		$this->template->links = $this->links;
	}

	public function populateTemplate($activeItem, $submenuAfterItem = FALSE, $submenu = NULL)
	{
		$this->template->path = "";

		$this->populateMenu($activeItem, $submenuAfterItem, $submenu);
	}

	/**
	 * Uprava formulare pro Boostrap4
	 */
	public function makeBootstrap4(Form $form): void
	{
		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = null;
		$renderer->wrappers['pair']['container'] = 'div class="form-group row"';
		$renderer->wrappers['pair']['.error'] = 'has-danger';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-10';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-2 col-form-label align-top text-right"';
		$renderer->wrappers['control']['description'] = 'span class="form-text font-italic"';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
		$renderer->wrappers['control']['.error'] = 'is-invalid';

		$renderer->wrappers['group']['container'] = null; // 'div class="col-sm-9 bg-light"';
		$renderer->wrappers['group']['label'] = 'span class="form-text font-weight-bold border-top p-1 mb-1"'; // bg-secondary text-white 

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
				$usedPrimary = true;
			} elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control-file');
			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->addClass('form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}
				$control->getControlPrototype()->addClass('form-check-input');
				$control->getSeparatorPrototype()->setName('div')->addClass('form-check');
			}
		}
	}
}
