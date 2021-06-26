<?php

declare(strict_types=1);

namespace App\Services;

use App\Model;
use Nette;
use Nette\Utils\Strings;
use Nette\Utils\DateTime;
use Nette\Http\Request;
use App\Services\Logger;
use \App\Exceptions\UserNotEnrolledException;

/**
 * @last_edited petak23<petak23@gmail.com> 26.06.2021
 */
class RaAuthenticator implements Nette\Security\IAuthenticator
{
	//private $database;
  private $passwords;
  private $request;

  /** @var Model\PV_User */
  private $pv_user;

  const NAME = 'audit';

	public function __construct(Model\PV_User $pv_user,
                              /*Nette\Database\Context $database,*/
                              Nette\Security\Passwords $passwords, 
                              Nette\Http\Request $request )
	{
		//$this->database = $database;
    $this->pv_user = $pv_user;
    $this->passwords = $passwords;
    $this->request = $request;
  }
    
  //private function getUser( $username )
  //{
  //return $this->database->table('rausers')
  //                      ->where('username', $username)
  //                      ->fetch();
  //}

  private function badPasswordAction($id, $badPwdCount, $lockoutTime, $ip, $browser )
  {
    /*$count = $this->database->table('rausers')
      ->where('id', $id) 
      ->update([
        'id_rauser_state' => 91,
        'bad_pwds_count' => $badPwdCount,
        'locked_out_until' => $lockoutTime,
        'last_error_time' => new DateTime(),
        'last_error_ip' => $ip,
        'last_error_browser' => $browser,
      ]);*/
    $this->pv_user->save( $id, [
        'id_rauser_state' => 91,
        'bad_pwds_count' => $badPwdCount,
        'locked_out_until' => $lockoutTime,
        'last_error_time' => new DateTime(),
        'last_error_ip' => $ip,
        'last_error_browser' => $browser,
      ]);
  }

  private function loginOkAction( /*$id, */$userData, $ip, $browser )
  {
    /*$count = $this->database->table('rausers')
      ->where('id', $userData->id) 
      ->update([
        'id_rauser_state' => 10,
        'bad_pwds_count' => 0,
        'cur_login_time' => new DateTime(),
        'cur_login_ip' => $ip,
        'cur_login_browser' => $browser,
        'prev_login_time' => $userData->cur_login_time,
        'prev_login_ip' => $userData->cur_login_ip,
        'prev_login_browser' => $userData->cur_login_browser
      ]);*/
    $this->pv_user->save( $userData->id, [
        'id_rauser_state' => 10,
        'bad_pwds_count' => 0,
        'cur_login_time' => new DateTime(),
        'cur_login_ip' => $ip,
        'cur_login_browser' => $browser,
        'prev_login_time' => $userData->cur_login_time,
        'prev_login_ip' => $userData->cur_login_ip,
        'prev_login_browser' => $userData->cur_login_browser
      ]);
  }

  /*private function updatePwdHash($id, $phash )
  {
    $count = $this->database->table('rausers')
      ->where('id', $id) 
      ->update([
            'phash' => $phash
      ]);
  }*/

	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
    $ip = $this->request->getRemoteAddress();
    $ua = $this->request->getHeader('User-Agent') . ' / ' . $this->request->getHeader('Accept-Language');

		[$username, $password] = $credentials;

		$userData = $this->pv_user->findOneBy(['username' => $username]); //$this->getUser($username);

		if (!$userData) {
      Logger::log( self::NAME, Logger::ERROR , "[{$ip}] Login: nenalezen uzivatel {$username}, '{$ua}'" ); 
			throw new Nette\Security\AuthenticationException('Uživatel neexistuje.');
    }
        
    if( $userData->id_rauser_state == 1 ) {
      Logger::log( self::NAME, Logger::ERROR , "[{$ip}] Login: {$username} state=1 " ); 
      throw new \App\Exceptions\UserNotEnrolledException('Tento účet ještě není aktivní, zadejte kód z e-mailu.');
    }  else if( $userData->id_rauser_state == 90 ) {
      Logger::log( self::NAME, Logger::ERROR , "[{$ip}] Login: {$username} state=90, '{$ua}'" ); 
      throw new Nette\Security\AuthenticationException('Tento účet byl správcem systému uzamčen.');
    } else if( $userData->id_rauser_state == 91 ) {
      $lockoutTime = (DateTime::from( $userData->locked_out_until ))->getTimestamp();
      if( $lockoutTime > time() ) {
        $rest = $lockoutTime - time();
        Logger::log( self::NAME, Logger::ERROR , "[{$ip}] Login: {$username} state=91 for {$rest} sec; '{$ua}'" ); 
        throw new Nette\Security\AuthenticationException("Tento účet je dočasně uzamčen, zkuste to znovu za {$rest} sekund." );
      }
    } else if( $userData->id_rauser_state == 10 ) {
      // OK, korektni stav
    }

		if (!$this->passwords->verify($password, $userData->phash)) {
      $badPwdCount = $userData->bad_pwds_count + 1;
      Logger::log( self::NAME, Logger::ERROR , "[{$ip}] Login: spatne heslo pro {$username}, badPwdCount={$badPwdCount}, '{$ua}'" ); 

      $delay = pow( 2, $badPwdCount );
      $lockoutTime = (new DateTime())->setTimestamp( time() + $delay );
      $this->badPasswordAction($userData->id, $badPwdCount, $lockoutTime, $ip, $ua);

			throw new Nette\Security\AuthenticationException('Špatné heslo.');
    }

    // pokud heslo potrebuje rehash, rehashnout
    if ($this->passwords->needsRehash($userData->phash)) {
      //$hash = $this->passwords->hash($password);
      //$this->updatePwdHash($userData->id, $hash );
      $this->pv_user->save($userData->id, ['phash' => $this->passwords->hash($password)]);
    }

    $this->loginOkAction( /*$userData->id, */ $userData, $this->request->getRemoteAddress(), $ua);
    
    Logger::log( self::NAME, Logger::INFO , "[{$ip}] Login: prihlasen {$username} v roli '{$userData->role}', '{$ua}'" ); 

    $roles = Strings::split($userData->role, '~,\s*~');
		return new Nette\Security\Identity($userData->id, $roles, ['username' => $userData->username, 'prefix' => $userData->prefix ]);
	}
}