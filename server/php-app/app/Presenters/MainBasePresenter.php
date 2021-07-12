<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Services\Logger;

/**
 * Zakladny presenter pre vsetky presentery
 * 
 * Posledna zmena(last change): 06.07.2021
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link      http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class MainBasePresenter extends Nette\Application\UI\Presenter {

  use Nette\SmartObject;

  protected function startup() {
    parent::startup();
    // Nacitanie uzivatela
    $user = $this->getUser(); 

    // Kontrola prihlasenia
    if ($user->isLoggedIn()) { //Prihlaseny uzivatel
      if (!$user->isAllowed($this->name, $this->action)) { //Kontrola ACL
        Logger::log( 'audit', Logger::ERROR , 
          "[{$this->getHttpRequest()->getRemoteAddress()}] ACCESS: Uzivatel #{$user->id} {$user->getIdentity()->username} zkusil pouzit funkciu vyzadujucu vyssiu rolu." );
        
        $response = $this->getHttpResponse();
        $response->setHeader('Cache-Control', 'no-cache');
        $response->setExpiration('1 sec'); 
    
        $this->getUser()->logout(true); // Odhlasenie spojene s odstranenim identity https://doc.nette.org/cs/3.1/access-control#toc-identita

        $this->flashRedirect('Sign:in', 'Na požadovanú akciu nemáte dostatočné oprávnenie!', 'danger');
      }
    } else { //Neprihlaseny uzivatel
      if (!$user->isAllowed($this->name, $this->action)) { //Kontrola ACL
        if ($user->getLogoutReason() === Nette\Security\UserStorage::LOGOUT_INACTIVITY) {
          Logger::log( 'webapp', Logger::ERROR , 
              "[{$this->getHttpRequest()->getRemoteAddress()}] ACCESS: Uzivatel je neprihlaseny, jdeme na login." ); 

          // https://pla.nette.org/cs/jak-po-odeslani-formulare-zobrazit-stejnou-stranku
          $this->flashRedirect(['Sign:in', ['backlink' => $this->storeRequest()]], 'Boli ste príliš dlho neaktívny a preto ste boli odhlásený! Prosím, prihláste sa znovu.', 'danger');
        } else {
          $this->flashRedirect('Sign:in', 'Nemáte dostatočné oprávnenie na danú operáciu!', 'danger');
        }
      }
    }
  }

  /** Funkcia pre zjednodusenie vypisu flash spravy a presmerovania
   * @param array|string $redirect Adresa presmerovania
   * @param string $text Text pre vypis hlasenia
   * @param string $druh - druh hlasenia */
  public function flashRedirect($redirect, $text = "", $druh = "info") {
		$this->flashMessage($text, $druh);
    if (is_array($redirect)) {
      if (count($redirect) > 1) {
        if (!$this->isAjax()) {
          $this->redirect($redirect[0], $redirect[1]);
        } else {
          $this->redrawControl();
        }
      } elseif (count($redirect) == 1) { $this->redirect($redirect[0]);}
    } else { 
      if (!$this->isAjax()) { 
        $this->redirect($redirect); 
      } else {
        $this->redrawControl();
      }
    }
	}

  /**
   * Funkcia pre zjednodusenie vypisu flash spravy a presmerovania aj pre chybovy stav
   * @param boolean $ok Podmienka
   * @param array|string $redirect Adresa presmerovania
   * @param string $textOk Text pre vypis hlasenia ak je podmienka splnena
   * @param string $textEr Text pre vypis hlasenia ak NIE je podmienka splnena  */
  public function flashOut($ok, $redirect, $textOk = "", $textEr = "") {
    if ($ok) {
      $this->flashRedirect($redirect, $textOk, "success");
    } else {
      $this->flashMessage($textEr, 'danger');
    }
  }

}