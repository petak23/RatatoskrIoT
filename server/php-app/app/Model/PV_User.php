<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table;

/**
 * Model, ktory sa stara o tabulku rausers
 * 
 * Posledna zmena 15.06.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class PV_User extends \App\Model\Table {

  /** @var string */
  protected $tableName = 'rausers';

  /**
   * Nájdenie všetkých užívateľov
   * @return Table\Selection */
  public function getUsers(): Table\Selection { 
    return $this->findAll()->order('username ASC');
  }

  /**
   * Nájdenie info o jednom užívateľovy
   * @param mixed $id primary key
   * @return Table\ActiveRow|null */
  public function getUser($id): ?Table\ActiveRow {
    return $this->find($id);
  }

  /** 
   * Vytvorenie užívateľa
   * @param iterable $data
   * @return ActiveRow|int|bool */
  public function createUser( $data ) {
    return $this->save(0, $data);
  }

  /** 
   * @param int $id Id uzivatela
   * @param string $phash Hash hesla 
   * @return ActiveRow|int|bool */
  public function updateUserPassword(int $id, string $phash ) {
    return $this->save( $id, ['phash' => $phash]);
  }
}