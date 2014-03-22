<?php

class mail extends api
{
  protected function Secured_4534fdjiofu47rod()
  {
    $trans = db::Begin();
    //db::Query("LOCK mail.send_tasks IN ACCESS EXCLUSIVE MODE;");
    $res = db::Query("SELECT * FROM mail.send_tasks WHERE sended IS NULL ORDER BY id ASC LIMIT 1");
    foreach ($res as $task)
    {
      $headers = 
        "From: {$task['from']}" . "\r\n" .
        "Reply-To: {$task['reply']}" . "\r\n" .
        'X-Mailer: Digital812';    

      mail($task['to'], $task['subj'], $task['body'], $headers);
      db::Query("UPDATE mail.send_tasks SET sended=now() WHERE id=$1", array($task['id']));
    }
    $trans->Commit();
  }
}