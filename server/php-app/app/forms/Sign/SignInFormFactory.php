<?php

namespace App\Forms\Sign;

use App\Model;
use Language_support;
use Nette\Application\UI\Form;
use Nette\Security;
use stdClass;

/**
 * Prihlasovací formulár
 * Posledna zmena 16.08.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class SignInFormFactory
{
	/** @var Security\User */
	protected $user;
	/** @var Language_support\LanguageMain */
	private $texts;
	/** @var Model\PV_User_main */
	private $pv_user;

	public function __construct(
		Security\User $user,
		Language_support\LanguageMain $language_main,
		Model\PV_User_main $pv_user
	) {
		$this->user = $user;
		$this->texts = $language_main;
		$this->pv_user = $pv_user;
	}

	public function create(string $language): Form
	{
		$this->texts->setLanguage($language);

		$form = new Form();
		$form->setRenderer(new \App\Forms\User\UserFormRenderer);
		$form->addProtection();
		$form->setTranslator($this->texts);

		$form->addEmail('email', 'SignInForm_email')
			->setHtmlAttribute('size', 0)->setHtmlAttribute('maxlength', 100)
			->addRule(Form::EMAIL, 'SignInForm_email_ar')
			->setRequired('SignInForm_email_sr');

		$form->addPassword('password', 'SignInForm_password')
			->addRule($form::MIN_LENGTH, 'SignInForm_password_min_lenght', 5)
			->setRequired('SignInForm_password_req');

		$form->addCheckbox('remember', 'SignInForm_remember');

		$form->addSubmit('send', 'SignInForm_login')
			->setHtmlAttribute('class', 'btn btn-success')
			->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		$renderer = $form->getRenderer();
		$renderer->wrappers['error']['container'] = 'div class="row"';
		$renderer->wrappers['error']['item'] = 'div class="col-md-12 alert alert-danger"';
		return $form;
	}

	public function signInFormSucceeded(Form $form, stdClass $values): void
	{
		try {
			if (!$values->remember) {
				$this->user->setExpiration('30 minutes');
			} else {
				$this->user->setExpiration('14 days');
			}
			$this->user->login($values->email, $values->password);
		} catch (Security\AuthenticationException $e) {
			$form->addError($this->texts->translate("AuthenticationException_"/*"SignInForm_error_"*/ . $e->getCode()));
		} catch (\App\Exceptions\UserNotEnrolledException $e) {
			$form->addError($this->texts->translate('UserNotEnrolledException'));
		}
	}
}
