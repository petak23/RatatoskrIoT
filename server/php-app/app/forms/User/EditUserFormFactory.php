<?php

declare(strict_types=1);

namespace App\Forms\User;

use App\Exceptions;
use App\Model;
use App\Services\Logger;
use Nette\Application\UI\Form;
use Nette\Security;

/**
 * Tovarnicka pre formular na pridanie a editaciu užívateľa
 * Posledna zmena 01.07.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class EditUserFormFactory {
  /** @var Model\PV_User */
	private $pv_user;
  /** @var array */
  private $user_state;

  /** @var Security\Passwords */
	private $passwords;
  /** @var Security\User */
	private $user;

  /** @var array */
	private $urovneReg;

  private $remoteAddress;

	
  public function __construct(Model\PV_User $pv_user,
                              Model\PV_User_roles $pv_user_roles,
                              Model\PV_User_state $user_state,
                              Security\User $user, Security\Passwords $passwords) {
		$this->pv_user = $pv_user;
    $this->user_state = $user_state->getAllForForm();
    $this->passwords = $passwords;
    $this->urovneReg = $pv_user_roles->urovneReg(($user->isLoggedIn()) ? ($user->getIdentity()->id_user_roles != null ? $user->getIdentity()->id_user_roles : 0) : 0);
    $this->user = $user;
	}
  
  /**
   * Formular pre pridanie alebo editaciu kategorie
   * @return Form */
  public function create($remoteAddress): Form  {
    $this->remoteAddress = $remoteAddress;

    $form = new Form;
    $form->addProtection();
  
    $form->addHidden('id');
    
    $form->addText('username', 'Login name:')
        ->setRequired()
        ->setHtmlAttribute('size', 50);

    $form->addText('prefix', 'Prefix:')
        ->setRequired()
        ->setHtmlAttribute('size', 50);

    $form->addText('password', 'Heslo:')
        ->setOption('description', 'Pokud je vyplněno, bude nastaveno jako nové heslo; pokud se nechá prázdné, heslo se nemění.'  )
        ->setHtmlAttribute('size', 50);

    $form->addText('email', 'E-mail:')
        ->setOption('description', 'Adresa pro mailové notifikace.'  )
        ->setHtmlAttribute('size', 50);

    $form->addSelect('id_rauser_state', 'Stav účtu:', $this->user_state)
        ->setDefaultValue('10')
        ->setPrompt('- Zvolte stav -')
        ->setRequired();

    $form->addCheckbox('role_admin', 'Administrátor')
        ->setOption('description', 'Má právo spravovat uživatele.'  );
        
    $form->addCheckbox('role_user', 'Uživatel');

    $form->addSelect('id_user_roles', 'Rola užívateľa:', $this->urovneReg);

    $form->addText('measures_retention', 'Retence - přímá data:')
        ->setDefaultValue('60')
        ->setOption('description', 'Ve dnech. 0 = neomezeno.'  )
        ->addRule(Form::INTEGER, 'Musí být číslo')
        ->setRequired()
        ->setHtmlAttribute('size', 50);

    $form->addText('sumdata_retention', 'Retence - sumární data:')
        ->setDefaultValue('366')
        ->setOption('description', 'Ve dnech. 0 = neomezeno.'  )
        ->addRule(Form::INTEGER, 'Musí být číslo')
        ->setRequired()
        ->setHtmlAttribute('size', 50);

    $form->addText('blob_retention', 'Retence - soubory:')
        ->setDefaultValue('8')
        ->setOption('description', 'Ve dnech. 0 = neomezeno.'  )
        ->addRule(Form::INTEGER, 'Musí být číslo')
        ->setRequired()
        ->setHtmlAttribute('size', 50);

      $form->addSubmit('send', 'Uložit')
          ->setHtmlAttribute('class', 'btn btn-success')
          //->setHtmlAttribute('onclick', 'this.form.submit(); this.disabled=true; ') // if( Nette.validateForm(this.form) ) { this.form.submit(); this.disabled=true; }return false;
          ->onClick[] = [$this, 'userFormSucceeded'];

      $form->addSubmit('cancel', 'Späť bez zmeny')
          ->setHtmlAttribute('class', 'btn btn-outline-secondary')
          ->setValidationScope([]);

      return $form;
  }


    public function userFormSucceeded(\Nette\Forms\Controls\SubmitButton $button): void {
      if ($this->user->isInRole('admin')) {
        $values = $button->getForm()->getValues(TRUE); 	//Nacitanie hodnot formulara
    
        $id = $values['id'];
        $roles = [];
        if( $values['role_admin']==1 ) {
          $roles[] = "admin";
        }
        if( $values['role_user']==1 ) {
          $roles[] = "user";
        }
        $values['role'] = implode ( ',', $roles );

        if( strlen($values['password']) )  {
          $values['phash'] = $this->passwords->hash($values['password']);
        }
        unset($values['id'], $values['password'], $values['role_user'], $values['role_admin']);

        try {
          if( $id ) {
            // editace
            $user = $this->pv_user->getUser( $id );
            if (!$user) {
              Logger::log( 'audit', Logger::ERROR, "Uzivatel {$id} nenalezen" );
              $button->addError('Uživatel nenalezen');
            }
            $user->update( $values );
          } else {
            // zalozeni
            $this->pv_user->createUser( $values );
          }
        } catch (\Nette\Database\DriverException $e) {
          $button->getForm()->addError($e->getMessage());
        }
      } else {
        $button->getForm()->addError('Neoprávnený prístup. Editácia nemožná!');
        Logger::log( 'audit', Logger::ERROR , 
          "[{$this->remoteAddress}] ACCESS: Uzivatel #{$this->user->id} {$this->user->getIdentity()->username} zkusil pouzit funkci vyzadujici roli admin" ); 
      }
    }
}