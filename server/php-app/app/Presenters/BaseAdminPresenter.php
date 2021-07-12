<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
//use Tracy\Debugger;
use App\Model;
use App\Services\Logger;
use Nette\Application\UI\Form;

/**
 * @last_edited petak23<petak23@gmail.com> 06.07.2021
 */
class BaseAdminPresenter extends BasePresenter
{
    use Nette\SmartObject;

    /** @var Model\PV_User @inject */
	public $userInfo;

    public function checkAcces(int $deviceUserId, string $type="zařízení" )
    {
        if( $this->getUser()->id != $deviceUserId ) {
            Logger::log( 'audit', Logger::ERROR , 
                "Uzivatel #{$this->getUser()->id} {$this->getUser()->getIdentity()->username} zkusil editovat {$type} patrici uzivateli #{$deviceUserId}" );
            $this->getUser()->logout();
            $this->flashMessage("K tomuto {$type} nemáte práva!");
            $this->redirect('Sign:in');
        }
    }

    // hodnoty z konfigurace
    public $appName;
    public $links;

    public function populateTemplate( $activeItem, $submenuAfterItem = FALSE, $submenu = NULL )
    {
        $this->template->appName = $this->appName;
        $this->template->links = $this->links;
        $this->template->path = "";

        $this->populateMenu( $activeItem, $submenuAfterItem, $submenu );
    }

    public function beforeRender() {
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $this->template->userInfo = $this->userInfo->getUser($user->id);
        }
    }


    /**
     * Uprava formulare pro Boostrap4
     */
    public function makeBootstrap4(Form $form): Form
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
            if ($type === 'button' && !(isset($control->getControlPrototype()->attrs['class']) && strlen($control->getControlPrototype()->attrs['class']))) {
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
        return $form;
    }
}