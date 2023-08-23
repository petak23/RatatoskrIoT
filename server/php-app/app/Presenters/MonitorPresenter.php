<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services;
use Nette;

final class MonitorPresenter extends BaseAdminPresenter
{
	use Nette\SmartObject;

	/** @var Services\InventoryDataSource */
	private $datasource;


	public function __construct(Services\InventoryDataSource $datasource)
	{
		$this->datasource = $datasource;
	}


	// http://lovecka.info/ra/monitor/show/aaabbb/2/
	public function renderShow(int $id, String $token): void
	{
		$userData = $this->datasource->getUser($id);
		if (!$userData) {
			$this->error('Zařízení nebylo nalezeno');
		}
		if (!$token || ($userData['monitoring_token'] !== $token)) {
			$this->error('Token nesouhlasí.');
		}
		$this->template->devices = $this->datasource->getDevicesUser($id);

		$response = $this->getHttpResponse();
		$response->setHeader('Cache-Control', 'no-cache');
		$response->setExpiration('1 sec');
	}
}
