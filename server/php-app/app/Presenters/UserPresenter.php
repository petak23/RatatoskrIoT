<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

use Nette\Application\UI\Form;

use App\Exceptions;
use App\Forms;
use App\Model;

use App\Services\Logger;


/**
 * @last_edited petak23<petak23@gmail.com> 01.07.2021
 */
final class UserPresenter extends BaseAdminPresenter
{
  use Nette\SmartObject;

	// Database tables	
  /** @var Model\PV_Devices @inject */
	public $devices;

  /** @var Model\PV_User_state @inject */
	public $user_state;

	// Forms
	/** @var Forms\User\EditUserFormFactory @inject*/
	public $editUserForm;


	/** @var \App\Services\InventoryDataSource */
	private $datasource;

	/** @var \App\Services\Config */
	private $config;

	/** @var Nette\Security\Passwords */
	private $passwords;

	
	public function __construct($parameters, \App\Services\InventoryDataSource $datasource, Nette\Security\Passwords $passwords )
	{
		$this->datasource = $datasource;
		$this->links = $parameters['links'];
		$this->appName = $parameters['title'];
		$this->passwords = $passwords;
	}

	/*
	id	username	phash	role	email	prefix	id_user_state	bad_pwds_count	locked_out_until	
	measures_retention	sumdata_retention	blob_retention	self_enroll	self_enroll_code	self_enroll_error_count
			cur_login_time	cur_login_ip	cur_login_browser	prev_login_time	prev_login_ip	prev_login_browser
				last_error_time	last_error_ip	last_error_browser	monitoring_token	desc
	*/
	public function renderList(): void
	{
			$this->checkUserRole( 'admin' );
			$this->populateTemplate( 6 );
			$this->template->users = $this->userInfo->getUsers();
	}

	public function renderShow( $id ): void
	{
			$this->checkUserRole( 'admin' );
			$this->populateTemplate( 6 );
			$this->template->userData = $this->userInfo->getUser( $id ); 
			$this->template->devices = $this->devices->getDevicesUser( $id ); 
	}


	public function renderCreate()
	{
			$this->checkUserRole( 'admin' );
			$this->populateTemplate( 6 );
	}

	public function actionEdit(int $id): void
	{
			$this->checkUserRole( 'admin' );
			$this->populateTemplate( 6 );
			$this->template->id = $id;

			$post = $this->userInfo->getUser( $id );
			if (!$post) {
					Logger::log( 'audit', Logger::ERROR ,
							"Uzivatel {$id} nenalezen" );
					$this->error('Uživatel nenalezen');
			}
			$post = $post->toArray();
			$post['role_admin'] = strpos($post['role'], 'admin')!== false;
			$post['role_user'] = strpos($post['role'], 'user')!== false;
			$this->template->name = $post['username'];

			$this['userForm']->setDefaults($post);
	}

	/**
   * Edit user form component factory. Tovarnicka na formular pre editaciu užívateľa.
   */
	protected function createComponentUserForm(): Form {
		$form = $this->editUserForm->create($this->getHttpRequest()->getRemoteAddress());
		$form['send']->onClick[] = function ($button) { 
			$id = $button->getForm()->getValues()->id;
			$this->flashOut(!count($button->getForm()->errors), $id ? ['User:show', $id] : "User:list", 'Údaje boli uložené!', 'Došlo k chybe a údaje sa neuložili. Skúste neskôr znovu...');
		};
    $form['cancel']->onClick[] = function () {
			$this->redirect('User:show', ['id'=> $this->template->id]);
		};
		return $this->makeBootstrap4( $form );
	}

	/** @todo na konci  */
	public function actionDelete( int $id ): void
	{
		$this->checkUserRole( 'admin' );
		$this->populateTemplate( 6 );
		$this->template->appName = $this->appName;
		$this->template->links = $this->links;
		$this->template->path = '../';
		$this->template->id = $id;

		$user = $this->userInfo->getUser( $id );
		if (!$user) {
			Logger::log( 'audit', Logger::ERROR , "Uzivatel {$id} nenalezen" );
			$this->error('Uživatel nenalezen');
		}

		$this->template->userData = $user;
		/** @todo zmeň datasource na userInfo */
		$this->template->devices = $this->datasource->getDevicesUser( $id );
	}

    protected function createComponentDeleteForm(): Form
    {
			$form = new Form;
			$form->addProtection();

			$form->addCheckbox('potvrdit', 'Potvrdit smazání')
					->setOption('description', 'Zaškrtnutím potvrďte, že skutečně chcete smazat uživatele a všechna jeho zařízení, data a grafy.'  )
					->setRequired();

			$form->addSubmit('delete', 'Smazat')
					->setHtmlAttribute('onclick', 'if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; } return false;');

			$form->onSuccess[] = [$this, 'deleteFormSucceeded'];

			$this->makeBootstrap4( $form );
			return $form;
    }

		/** @todo zmeň datasource na userInfo */
    public function deleteFormSucceeded(Form $form, array $values): void
    {
			$this->checkUserRole( 'admin' );
			$id = $this->getParameter('id');

			if( $id ) {
				// overeni prav
				$post = $this->userInfo->getUser( $id );
				if (!$post) {
						Logger::log( 'audit', Logger::ERROR ,
								"Uzivatel {$id} nenalezen" );
						$this->error('Uživatel nenalezen');
				}
				Logger::log( 'audit', Logger::INFO , "[{$this->getHttpRequest()->getRemoteAddress()}, {$this->getUser()->getIdentity()->username}] Mazu uzivatele {$id}" ); 

				$this->datasource->deleteViewsForUser( $id );            
				$devices = $this->datasource->getDevicesUser( $id );
				foreach( $devices->devices as $device ) {
						
						$this->datasource->deleteDevice( $device->attrs['id'] );
				}
				$this->datasource->deleteUser( $id );
			} 

			$this->flashMessage("Uživatel smazán.", 'success');
			$this->redirect('User:list' );
    }

   
    
}


