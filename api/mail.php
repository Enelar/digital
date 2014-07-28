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
      $headers = ["From: {$task['from']}"];
      if ($task['reply'] != 'NULL')
        array_push($headers, "Reply-To: {$task['reply']}");

      $ret = mail($task['to'], $task['subj'], $task['body'], implode("\r\n", $headers));
      if ($ret)
        db::Query("UPDATE mail.send_tasks SET sended=now() WHERE id=$1", array($task['id']));
    }
    $trans->Commit();
    echo '<META HTTP-EQUIV="REFRESH" CONTENT="10">';
    echo time();
    exit();
  }
}