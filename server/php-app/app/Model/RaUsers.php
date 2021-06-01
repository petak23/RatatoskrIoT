<?php

namespace App\Model;

/**
 * Model, ktory sa stara o tabulku rausers
 * 
 * Posledna zmena 01.06.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class RaUsers extends Table {

  /** @var string */
  protected $tableName = 'rausers';

  public function getUsers() { 
    return $this->findAll()->order('username ASC');
  }

  public function getUser( $id ) {
    return $this->find($id);
  }
}