<?php

class login extends api
{
  protected function Form()
  {
    return array(
      "design" => "login/form",
      "script" => "main/login"
    );
  }
  
  protected function Profile()
  {
    return ['data' => db::Query("SELECT email, phone, surname, delivery FROM users.logins WHERE id=$1", [$this->UID()], true)];
  }
  
  protected function Submit()
  {
    $voice = $this->CheckVoiceAccess();
    try
    {
      global $_POST;
      
      phoxy_protected_assert($voice, array("error" => "Something went wrong"));
      if ($this->IsUserExsist($_POST['email']))
      {
        $uid = $this->TryLogin($_POST['email'], $_POST['password']);
        if ($uid)
        {
          $_SESSION['uid'] = $uid;
          return ["data" => ["uid" => $uid], "routeline" => "LoginSoftReset"];
        }

        if (isset($_POST['password_repeat']) && strlen($_POST['password_repeat']) > 0)
          throw new phoxy_protected_call_error(["error" => "Пользователь уже существует, но пароль неверный"]);
      }
      if (!isset($_POST['password_repeat']) || strlen($_POST['password_repeat']) == 0)
        throw new phoxy_protected_call_error(["error" => "Логин или пароль не верный"]);
      return $this->Register();
    } catch (phoxy_protected_call_error $e)
    {
      $ret = array(
        "routeline" => "ShowLoginError",
        "data" => 
          array(
            "success" => true, 
            "error" => $e->result['error']
          )
        );
      throw new phoxy_protected_call_error($ret);
    }
  }
  
  public function CheckVoiceAccess( )
  {
    global $_POST;
    global $_SESSION;
    if (!isset($_SESSION)) session_start();

    $fallback_return_instructions = 
      array(
        "script" => array("main/login", "md5"),
        "routeline" => "GenerateLoginVoice",
      );
    $constructed_fallback = new phoxy_protected_call_error($fallback_return_instructions);

    try
    {
      if (!isset($_POST['voice_ask']))
        throw $constructed_fallback;
      if (!isset($_POST['voice_answer']))
        throw $constructed_fallback;
      if ($_POST['voice_ask'] != $_SESSION['voice_ask'])
        throw $constructed_fallback;
      if (!$this->CheckAnswer($_POST['voice_ask'], $_POST['voice_answer'], $_SESSION['voice_answer']))
        throw $constructed_fallback;
    } catch (phoxy_protected_call_error $e)
    {
      $pair = $this->GenVoiceAccessPair();
      $_SESSION['voice_ask'] = $pair['ask'];
      $_SESSION['voice_answer'] = $pair['answer'];
      
      $e->result['data']['voice']['ask'] = $pair['ask'];
      $e->result['data']['voice']['answer'] = $pair['answer'];
      throw $e;
    }
    $_SESSION['voice_answer'] = 0;
    return true;
  }
  
  private function GenVoiceAccessPair()
  {
    $ask = $this->GenRandString(32);
    $answer = 1E+100;
    return array('ask' => $ask, 'answer' => $answer);
  }
  
  private function CheckAnswer( $ask, $answer, $max_answer )
  {    
    $numstr = $this->bchexdec(md5($ask.$answer));
    return (float)$numstr < $max_answer;
  }
  
  private function bchexdec($hex)
  {
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
    }
    return $dec;
  }
  
  private function GenRandString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
  {
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
  }
  
  public function IsUserExsist( $login )
  {
    $res = db::Query("SELECT id FROM users.logins WHERE email=$1", array($login), true);
    return count($res) > 0;
  }
  
  private function TryLogin( $login, $password )
  {
    $user = db::Query("SELECT * FROM users.logins WHERE email=$1", array($login), true);
    if (!count($user))
      return false;
    if (!password_verify($password, $user['password']))
      return false;
    
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT))
    {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      if ($hash != false)
        db::Query("UPDATE users.logins SET password=$2 WHERE id=$1", array($user['id'], $hash));
    }
    return $this->MakeLogin($user['id']);
  }
  
  private function MakeLogin( $uid )
  {
    global $_SESSION;
    if (!isset($_SESSION)) session_start();
    $_SESSION['uid'] = $uid;
    return $this->UID();
  }
  
  public function UID()
  {
    global $_SESSION;    
    if (!isset($_SESSION)) session_start();
    phoxy_protected_assert($_SESSION['uid'] > 0, ["error" => "Необходима регистрация"]);
    return $_SESSION['uid'];  
  }
  
  private function Register()
  {
    global $_POST;

    $email = $_POST['email'];
    phoxy_protected_assert(strlen($email), ["error" => "Почта не может быть пустой"]);
    $email_hash = password_hash($email, PASSWORD_DEFAULT);
    $email_validation_hash = password_hash($email_hash, PASSWORD_DEFAULT);
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    phoxy_protected_assert($hash, ["error" => "Не удалось зашифровать пароль."]);
    $surname = $_POST['creditals'];
    $delivery = $_POST['delivery_address'];
    $phone = $_POST['phone'];

    $found = count(db::Query("SELECT * FROM users.logins WHERE email=$1 OR phone=$2", [$email, $phone]));
    phoxy_protected_assert(!$found, ["error" => "Пользователь уже существует"]);
    
    $arg = [$email_hash, $hash, $surname, $delivery];
    if (strlen($phone) > 5)
      array_push($arg, $phone);
    $row = db::Query("
    INSERT INTO 
      users.logins
      (email, password, surname, delivery, phone)
    VALUES
      ($1, $2, $3, $4, ".(strlen($phone) > 5 ? '$5' : "NULL").")
      RETURNING id",
      $arg, true);
    phoxy_protected_assert(count($row), ["error" => "Не удалось внести запись в таблицу"]);

    $accept_url = phoxy_conf()['site']."#login/ValidateEmail?id={$row['id']}&email={$email}&back={$_POST['back_url']}&hash={$email_validation_hash}";
    $decline_url = phoxy_conf()['site']."#login/CancelRegistration?id={$row['id']}&email={$email}&back={$_POST['back_url']}&hash={$email_validation_hash}";
    global $_SERVER;
    $site = $_SERVER['HTTP_HOST'];
    
    db::Query("INSERT INTO mail.send_tasks
      (\"to\", \"from\", \"reply\", subj, body)
      VALUES
      ($1, $2, $3, $4, $5)",
      [$email, "Бот Регистрации <regbot@{$site}>", "Поддержка Digital812 <support@{$site}>", 'Регистрация на Digital812', "
Спасибо за регистрацию в нашем магазине!

Для активации вашего профиля, пожалуйста пройдите по следующей ссылке:
{$accept_url}
Если вы не посылали запрос, то вы можете отменить привязку вашей почты:
{$decline_url}

При возникновении вопросов смело отвечайте на это письмо.
Ваш D812.
    "]);
    
    return array(
      "before" => "RegistrationSuccessfull",
      "data" => ["reset" => $this->DirectToMail($email)]
    );
  }
  
  private function ExtractSafeAddrFromMail( $email )
  {
    $parts = explode($email, "@");
    if (count($parts) != 2)
      return false;
    return $parts[1];
  }
  
  private function DirectToMail( $email )
  {
    $domain = $this->ExtractSafeAddrFromMail($email);
    if ($domain == 'gmail.com')
      return 'https://mail.google.com/mail/u/0/#search/digital812';
    return $domain;
  }
  
  protected function CancelRegistration( $id, $email, $back_url, $hash )
  {
    $ret_error = ["error" => "Почта уже подтверждена, или еще чего. Но запись не найдена", "reset" => true];
    $row = db::Query("SELECT * FROM users.logins WHERE id=$1", [$id], true);
    if (!count($row))
      return $ret_error;
    $email_hash = $row['email'];
    if (!password_verify($email_hash, $hash))
      return $ret_error;
    db::Query("DELETE FROM users.logins WHERE id=$1", [$id]);
    return ["error" => "Отмена регистрации успешна", "reset" => '/#'];
  }
  
  protected function ValidateEmail( $id, $email, $back_url, $hash )
  {
    $ret_error = ["error" => "Почта уже подтверждена, или еще чего. Но запись не найдена", "reset" => '/#'];
    $row = db::Query("SELECT * FROM users.logins WHERE id=$1", [$id], true);
    if (!count($row))
      return $ret_error;
    $email_hash = $row['email'];
    if (!password_verify($email_hash, $hash))
      return $ret_error;
    db::Query("UPDATE users.logins SET email=$2 WHERE id=$1", [$id, $email]);
    
    $this->MakeLogin($id);
    
    return [
      "reset" => "#".$back_url, 
    ];
  }
}