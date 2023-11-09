<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class Device
{
	use Nette\SmartObject;

	/**
	 * 	id	passphrase	name	desc	first_login	last_login
	 */
	public $attrs;

	/**
	 * @var array Pole poli s indexy
	 * id	device_id	channel_id	name	device_class	id_value_types	msg_rate	desc	display_nodata_interval	preprocess_data	preprocess_factor	dc_desc	unit
	 */
	public $sensors = [];

	/** @var bool Príznak problému */
	public $problem_mark = false;

	public function __construct($attrs)
	{
		$this->attrs = $attrs;
	}

	public function addSensor(array $sensorAttrs): void
	{
		$this->sensors[$sensorAttrs['id']] = $sensorAttrs;
	}
}
