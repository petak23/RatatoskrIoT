<?php

namespace App\ApiModule\Model;

use Nette;

/**
 * Model, ktory sa stara o tabulku value_types
 * 
 * Posledna zmena 13.09.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class Units
{

	const TABLE = 'value_types';

	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	private function dbtable()
	{
		return $this->database->table(self::TABLE);
	}

	public function getUnits(): array
	{
		return $this->dbtable()->order('id ASC')->fetchPairs("id", "unit");
	}
}
