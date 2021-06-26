<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database;

/**
 * Model, ktory sa stara o tabulku devices
 * 
 * Posledna zmena 15.06.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class PV_Devices extends Table {

  /** @var string */
  protected $tableName = 'devices';

  /** @var Database\Table\Selection */
	protected $sensors;

  /**
   * @param Database\Context $db */
  public function __construct(Database\Context $db)  {
    parent::__construct($db);
    $this->sensors = $this->connection->table("sensors");
	}

  public function getDevicesUser( $userId ) : VDevices {
    $rc = new VDevices();

    // nacteme zarizeni
    
    $result = $this->findBy(['user_id'=>$userId])->order('name ASC');

    foreach ($result as $row) {
      $dev = new VDevice( $row->toArray() );
      $dev->attrs['problem_mark'] = false;
      if( $dev->attrs['last_bad_login'] != NULL ) {
        if( $dev->attrs['last_login'] != NULL ) {
          $lastLoginTs = (DateTime::from( $dev->attrs['last_login']))->getTimestamp();
          $lastErrLoginTs = (DateTime::from(  $dev->attrs['last_bad_login']))->getTimestamp();
          if( $lastErrLoginTs >  $lastLoginTs ) {
            $dev->attrs['problem_mark'] = true;
          }
        } else {
          $dev->attrs['problem_mark'] = true;
        }
      }
      $rc->add( $dev );
    }
    
    // a k nim senzory

    /*$result = $this->database->query(  '
        select s.*, 

----> TODO dc.desc as dc_desc, 
     
        vt.unit
        from sensors s
        left outer join device_classes dc
        on s.device_class=dc.id
        left outer join value_types vt
        on s.value_type=vt.id
        left outer join devices d
        on s.device_id = d.id
        where d.user_id = ?
        order by s.name asc
    ', $userId );*/
    $result = $this->sensors->where(['device_id.user_id'=>$userId])->order('name ASC');

    foreach ($result as $row) {
      $r = $row->toArray();
      $device = $rc->get( $r['device_id'] );
      $r['warningIcon'] = 0;
      $r['dc_desc'] = $row->device_class->desc;
      $r['unit'] = $row->value_type->unit;
      dump($row->value_type->unit);
      dumpe($r);
      if( $r['last_data_time'] ) {
        $utime = (DateTime::from( $r['last_data_time'] ))->getTimestamp();
        if( time()-$utime > $r['msg_rate'] ) {
          $r['warningIcon'] = ( $device->attrs['monitoring']==1 ) ? 1 : 2;
        } 
      }
      
      if( isset($device) ) {
        $device->addSensor( $r );
      }
    }

    return $rc;
  }

} // End class PV_Devices

class VDevices {
  use \Nette\SmartObject;

  public $devices = [];
  
  public function add( VDevice $device )
  {
      $this->devices[ $device->attrs['id'] ] = $device;
  }

  public function get( $id ) : VDevice
  {
      return $this->devices[$id];
  }
}

class VDevice {
  use \Nette\SmartObject;

  /**
   * 	id	passphrase	name	desc	first_login	last_login
   */
  public $attrs;

  /**
   * Pole poli s indexy
   * id	device_id	channel_id	name	device_class	value_type	msg_rate	desc	display_nodata_interval	preprocess_data	preprocess_factor	dc_desc	unit
   */
  public $sensors = [];

  public function __construct( $attrs ) {
    $this->attrs = $attrs;
  }
  
  public function addSensor( $sensorAttrs ) {
      $this->sensors[ $sensorAttrs['id'] ] = $sensorAttrs;
  }
}