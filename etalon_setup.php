<?
  require_once('lib4php.php');

  $ini=parse_ini_file('etalon.ini');

  print_r($ini);
  $dir4etalon=$ini['etalon'];
  $res=shell_exec("rmdir /S /Q $dir4etalon");
  echo $res;
  mkdir($dir4etalon,0777,true);

  for($i=1;$i<=$ini['maxSrc'];$i++)
    {
     $dir4src=$ini["place$i"];
     $dir4tmp=$dir4etalon.'\\'.$i;
     if($dir4src=='') continue;
     echo2log($ini['logfile'], "--- *** INIT DIR: $dir4src [$i] *** ---");
     $filez=evd_dirz($dir4src, '*', true, true);
     foreach($filez as $file_id=>$fn1)
       {
        $fn2=$dir4tmp.'\\'.substr($fn1,strlen($dir4src)+1);
        mkdir(evd_dir($fn2),0777,true);
        echo2log($ini['logfile'], "Copy new File: $fn1 --> $fn2");
        copy($fn1,$fn2);
       }
    }
?>