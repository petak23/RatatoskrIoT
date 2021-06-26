<?php

declare(strict_types=1);

namespace App\Forms\User;

use App\Model;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * Tovarnicka pre formular na pridanie a editaciu užívateľa
 * Posledna zmena 23.06.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class EditUserFormFactory {
  /** @var Model\PV_User */
	private $pv_user;
  /** @var array */
  private $user_state;

  /** @var array */
	//private $urovneReg;
	
  public function __construct(Model\PV_User $pv_user, Model\PV_User_state $user_state, User $user) {
		$this->pv_user = $pv_user;
    $this->user_state = $user_state->getAllForForm();
    //$this->urovneReg = $user_roles->urovneReg(($user->isLoggedIn()) ? $user->getIdentity()->id_user_roles : 0);
	}
  
  /**
   * Formular pre pridanie alebo editaciu kategorie
   * @return Form */
  public function create(): Form  {
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
          //->onClick[] = [$this, 'userFormSucceeded']
          ;

      $form->addSubmit('cancel', 'Späť bez zmeny')
          ->setHtmlAttribute('class', 'btn btn-outline-secondary')
          ->setValidationScope([]);

      return $form;
  }


    /*public function userFormSucceeded(Form $form, array $values): void
    {
        $this->checkUserRole( 'admin' );

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
        unset($values['password']);
        unset($values['role_user']);
        unset($values['role_admin']);

        $id = $this->getParameter('id');
        if( $id ) {
            // editace
            //$user = $this->datasource->getUser( $id );
            $user = $this->userInfo->getUser( $id );
            if (!$user) {
                Logger::log( 'audit', Logger::ERROR ,
                    "Uzivatel {$id} nenalezen" );
                $this->error('Uživatel nenalezen');
            }
            $user->update( $values );
        } else {
            // zalozeni
            $this->datasource->createUser( $values );
        }

        $this->flashMessage("Změny provedeny.", 'success');
        if( $id ) {
            $this->redirect("User:show", $id );
        } else {
            $this->redirect("User:list" );
        }
    }*/
}