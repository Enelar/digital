<?php

class grabber extends api
{
  private function ScoreProxy( $addr, $delta )
  {
    global $pg;    
    $res = $pg->Query("UPDATE proxy SET score=score+$2 WHERE addr=$1 RETURNING score", [$addr, $delta], true);
  }

  private function GetProxyAddr()
  {
    global $pg;
    $res = $pg->Query("
      UPDATE proxy
        SET last_use=now()
        WHERE addr=
        (
          SELECT addr 
            FROM proxy
            WHERE 
              now()-last_use>'30 sec'::interval
                AND score > -2
            ORDER BY score DESC, last_use ASC
            LIMIT 1
        ) RETURNING *", [], true);
    phoxy_protected_assert($res, ["error" => "No proxy available"]);
    return $res['addr'];
  }

  public function Grab( $id )
  {
    return $this->SyncReturn($id);
    $count = 10;
    $threads = $this->StartThreads($id, $count);
    $this->GetResults($threads);
    echo "FINISHED";
    $ret = false;
    $stat = [];
    foreach ($threads as $t)
    {
      $res = $t->res;
      if (!isset($stat[$res]))
        $stat[$res] = 0;
      $stat[$res]++;
      if ($res == 'timeout')
        $this->ScoreProxy($t->proxy, -20);
      else if ($res == 'KILL')
        $this->ScoreProxy($t->proxy, -1);
      else if ($res == 'LOAD_FAIL')
        $this->ScoreProxy($t->proxy, -40);
      else if ($res == '')
        $this->ScoreProxy($t->proxy, -10);
      else
      {
        $ret = $res;
        $this->ScoreProxy($t->proxy, 1);
        var_dump("SUCCESS: $t->proxy");
      }
    }
    var_dump($stat);
    return $ret;
  }

  private function SyncReturn($ymid)
  {
    include('worker.php');
    $t = new work(0, $ymid, "socks5");
    $t->run();
    return $t->res;
  }

  private function StartThreads( $ymid, $count )
  {
    include('worker.php');
    $ret = [];
    for ($i = 0; $i < $count; $i++)
    {
      $proxy = $this->GetProxyAddr();
      $t = new work($proxy, $ymid, "socks5");
      if ($t->start())
        $ret[] = $t;
    }
    return $ret;
  }

  private function GetResults( $res )
  {
    $start_time = microtime(true);
    foreach ($res as $t)
    {      
      while ($t->isRunning())
      {
        usleep(200);
        if (microtime(true) - $start_time > 20)
        {
          $t->kill();
          $t->res = "KILL";
          break;
        }
      }
      $t->join();
    }
    echo "COMPLETED ".(microtime(true) - $start_time);
        
    $ret = [];
    foreach ($res as $t)
      $ret[] = $t->res;
    return $ret;
  }
}