<?
  require_once('lib4php.php');

  $ini=parse_ini_file('etalon.ini');

  print_r($ini);
  $alarmaOut='';
  $alarmaAdd='';
  $dir4etalon=$ini['etalon'];
  mkdir($dir4etalon,0777,true);

  for($i=1;$i<=$ini['maxSrc'];$i++)
    {
     $dir4src=$ini["place$i"];
     $dir4tmp=$dir4etalon.'\\'.$i;
     if($dir4src=='') continue;
     echo2log($ini['logfile'], "--- *** Scan dir: $dir4src [$i] *** ---");
     $filez=evd_dirz($dir4src, '*', true, true);
     foreach($filez as $file_id=>$fn1)
       {
        $fn2=$dir4tmp.'\\'.substr($fn1,strlen($dir4src)+1);
        echo2log($ini['logfile'], "Compare Files: $fn1 -- $fn2");
        if(file_exists($fn2))
          {
           if(md5_file($fn1)!=md5_file($fn2))
             {
              echo2log($ini['logfile'], "FN1 - $fn1: ".md5_file($fn1));
              echo2log($ini['logfile'], "FN2 - $fn2: ".md5_file($fn2));

              $diff=evd_file_diff($fn1,$fn2);
              
              $alarmaOut.=alarmaOut($ini['alarma'], "Есть исправления в ".basename($fn1)." ($fn1)");#.md5_file($fn1)
              $alarmaAdd.=alarmaOut($ini['alarma'], dos2utf($diff));

              echo2log($ini['logfile'], "Copy new File: $fn1 --> $fn2");
              copy($fn1,$fn2);
             }
          }
        else
          {
           $alarmaOut.=alarmaOut($ini['alarma'], "Новый файл - ".basename($fn1)." ($fn1)");
           mkdir(evd_dir($fn2),0777,true);
           echo2log($ini['logfile'], "Copy new File: $fn1 --> $fn2");
           copy($fn1,$fn2);
          }
       }
     //обратная проверка что файл в исходнике удален
     $filez=evd_dirz($dir4tmp, '*', true, true);
     foreach($filez as $file_id=>$fn2)
       {
        $fn1=$dir4src.'\\'.substr($fn2,strlen($dir4tmp)+1);
        echo2log($ini['logfile'], "BackCompare Files: $fn1 -- $fn2");
        if(!file_exists($fn1))
          {
           $alarmaOut.=alarmaOut($ini['alarma'], "Удален файл - ".basename($fn1)." ($fn1)");
           echo2log($ini['logfile'], "Unlink File: $fn2");
           unlink($fn2);
          }
       }

     echo2log($ini['logfile'], "--- *** End Scan dir: $dir4src [$i] *** ---\n\n\n");
    }

  if($alarmaAdd!='') $alarmaOut.="\n\n--- детали:\n\n".$alarmaAdd;
  echo2log($ini['etalonArh']."\\".date('Ymd-His').'.lo', $alarmaOut);
  if($alarmaOut!=='') sendEmail($ini['mailto'], utf2win("Эталонная копия - есть изменения..."), utf2win("<pre>$alarmaOut</pre>"));
                 else sendEmail($ini['mailto'], utf2win("Эталонная копия - без изменений..."), "");

  function alarmaOut($o,$s)
    {
     echo2log($o, $s);
     return $s."\n";
    }
?>