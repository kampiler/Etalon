<?
  error_reporting( E_ERROR );

  setlocale(LC_ALL,"ru_RU.UTF-8");
  #setlocale(LC_ALL,"RUS");
  #define('Encoding','WINDOWS-1251');
  date_default_timezone_set("Asia/Baghdad");
  setlocale(LC_CTYPE,"ru_RU.UTF-8");
  setlocale(LC_NUMERIC, 'ru');
  setlocale(LC_MONETARY, 'Russian'); 
  #mb_internal_encoding("UTF-8");

  //Заголовок XML-документа
  define('XMLHead',"<?xml version='1.0' encoding='".Encoding."'?>");


  function echo2log($fn, $subj='', $text='')
    {
     $r=true;

     if($text=='-')    $t='\n\n\n';
     elseif($text=='') $t=curtime("$subj");
                  else $t=curtime("$subj:\n$text");
     echo $t;
     mkdir(evd_dir($fn,0777,true));
     file_put_contents($fn, $t, FILE_APPEND | LOCK_EX);

     return $r;
    }

  function curtime($s)
    {
     if($s!='') return(date('Y-m-d H:i:s :: ').$s."\n");
           else return(date('Y-m-d H:i:s')."\n");
    }


  function clearDir($dir='')
    {
     if(dir!='')
       {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file)
          {
           (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
          }
       }
     #return rmdir($dir);
    }

  function evd_dir($fn)//вернуть название директории
    {
     return pathinfo($fn,PATHINFO_DIRNAME);
    }
  function evd_ext($fn)//вернуть расширение файла
    {
     $pinfo=pathinfo($fn);
     return $pinfo['extension'];
    }
  function evd_fname($fn)//только имя файла
    {
     $ext=evd_ext($fn);
     $fbase=substr($fn,0,strlen($fn)-strlen($ext)-1);
     $fbase=basename($fbase);
     return($fbase);
    }

  //
  // возвращает список файлов с полными именами файлов
  //
  function evd_dirz($dir=".", $ext='*', $is_full=true, $is_recursion=false)
    {
     $r=array();
     $d=@opendir($dir);
     if($d)
       while(($f=readdir($d))!==false)
         {
          #echo is_dir($dir."\\".$f)." - $f<br>\n";
          if(($f==".")or($f=="..")) continue;
          if($is_full) $fn=$dir."\\".$f; else $fn=$f;
          if(!is_dir($fn))
            {
             $e=strtoupper(evd_ext($fn));
             if(($ext=='*')or(strtoupper($ext)==$e)) $r[]=$fn;
            }
          else
            {
             if($is_recursion) $r=array_merge($r,evd_dirz($fn,$ext,$is_full,$is_recursion));
            }
         }
     return($r);
    }
 
  function dir2arr($dir)
    { 
     $r=array(); 

     $cdir=scandir($dir); 
     foreach($cdir as $key=>$value) 
       { 
        if(!in_array($value,array(".",".."))) 
          { 
           if(is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
             { 
             } 
           else 
             { 
              $r[]=strtoupper($dir.DIRECTORY_SEPARATOR.$value);
             } 
          } 
       } 
     
     return $r;
    } 

  
  function utf2win($str)
    {
     if($str!='') return @iconv("UTF-8", "CP1251", $str);
     else return '';
    }
  function win2utf($str)
    {
     if($str!='') return @iconv("CP1251", "UTF-8", $str);
     else return '';
    }
  
  function dos2win($str)
    {
     if($str!='') return @iconv("866", "CP1251", $str);
     else return '';
    }
  function win2dos($str)
    {
     if($str!='') return @iconv("CP1251", "866", $str);
     else return '';
    }

  function utf2lat($s)
    {
     $s=strtr($s, "абвгдеёзийклмнопрстуфхыэ",
                  "abvgdeeziyklmnoprstufhie");
     $s=strtr($s, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЫЭ",
                  "ABVGDEEZIYKLMNOPRSTUFHIE");
     $s=strtr($s,array(
                       "ж"=>"zh",  "ц"=>"ts", "ч"=>"ch", "ш"=>"sh",
                       "щ"=>"shch","ь"=>"",  "ъ"=>"", "ю"=>"yu", "я"=>"ya",
                       "Ж"=>"ZH",  "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH",
                       "Щ"=>"SHCH","Ь"=>"", "Ъ"=>"",  "Ю"=>"YU", "Я"=>"YA"
                      ));
     return $s;
    }

  function subj4form($dt, $form_id, $form_period='')
    {
     $r='';
     $r=preg_replace('/^0409(\d\d\d)/i', "F$1", $form_id);

     if($r!='')
       {
        if($form_period=='нерегулярная') $r.='D';
        if($form_period=='декадная')
          {
           if($form_id=='0409664') $r='D664';
          }
        $r="IES you have mail - $r//$dt";
       }

     return($r);
    }


  function dir4form($dt, $form_id, $form_period='')
    {
     global $dir4arhform;
     $r='';
     $r=preg_replace('/^0409(\d\d\d)/i', "F$1", $form_id);

     if($r==$form_id) $r='';//не поняли что за отчетность
     if($r!='')
       {
        if($form_period=='нерегулярная') $r.='D';
        if($form_period=='декадная')
          {
           if($form_id=='0409664') $r='D664';
          }

        $dt=str_replace('-','\\',$dt);
        $r=utf2win("$dir4arhform\\$dt\\ies");//архив
       }

     return($r);
    }

  function email4form($form_id)
    {
     global $ini;
     $r=$ini['mailto'];
     if(preg_match('/^0409(\d\d\d)/i',$form_id,$m))
       {
        #echo "\$m[1]=$m[1]\n";
        $e=$ini["f$m[1]"];
        if($e!='') $r.=",$e";
        #if($m[1]=='135') $r.=',f135@bank.ru';
       }
     return($r);
    }

  function sendEmail($to, $subj, $text='')
    {
     global $ini;
     if($ini['mailtest']=='test')
       {
        echo "!!! SENDEMAIL TEST !!!\nsubject=$subj\ntext=$text\n\n";
       }
     else
       {
        $objEmail = new COM("CDO.Message") or die("Cannot init CDO.Message");

        $objEmail->Configuration->Fields->Item("http://schemas.microsoft.com/cdo/configuration/sendusing")->Value = 2;
        $objEmail->Configuration->Fields->Item("http://schemas.microsoft.com/cdo/configuration/smtpserver")->Value     = $ini['mailserver'];//
        $objEmail->Configuration->Fields->Item("http://schemas.microsoft.com/cdo/configuration/smtpserverport")->Value = $ini['mailport'];
        $objEmail->Configuration->Fields->Update();

        $objEmail->From    = $ini['mailfrom'];
        $objEmail->To      = $to;
        $objEmail->Subject = $ini['mailsubj'].$subj;
    
        $objEmail->BodyPart->Charset = $ini['mailcharset'];
        $objEmail->HTMLBody  = $text;
    
        return $objEmail->Send();
       }
    }

  function evd_lz($str, $len=0, $ch='0')
    {
     $str=trim($str);
     if(mb_strlen($str)>=$len) return $str;
                       else return (str_repeat($ch, $len-mb_strlen($str)).$str);
    }

  function arr2str($arr)
    {
     $r='';
     foreach($arr as $s)
       {
        $r.=$s;
       }
     return $r;
    }

  function dos2utf($str)
    {
     if($str!='') return @iconv("866", "UTF-8", $str);
     else return '';
    }
  function utf2dos($str)
    {
     if($str!='') return @iconv("UTF-8", "866", $str);
     else return '';
    }

  function Commas($number=0, $dig=2, $delim="`", $lz=0, $lz_ch=' ')
    {
     #floatval($number);
     $number=str_replace(',','.',$number);
     @$number*=1;
     $number=sprintf("%.${dig}f",$number);
     $number=strrev(preg_replace("#(\d{3})(?=\d)#","\\1${delim}", strrev($number)));
     //после преобразования в binary забивает точку \x00
     $number=str_replace("\x00","\x2E",$number);
     $number=str_replace(',','.',$number);
     if($lz>0) $number=evd_lz($number, $lz, $lz_ch);
     return $number;
    }

  function xml100($s)
    {
     $r="$s";
     return($r/100);
    }

  function evd_file_diff($f1,$f2)
    {
     /*
     #$diff=shell_exec("chcp 866|fc $f1 $f2");
     $arr1=file($f1);
     $arr2=file($f2);
     $diff=implode("\n",array_diff($arr1,$arr2));
     */
     $diff='BAD';
     $f3='diff.cmp';
     $res=shell_exec("fcbat.cmd $f1 $f2 $f3");
     #echo $res;
     #if($res=='ok')
     $diff=file_get_contents($f3);
     unlink($f3);
     return $diff;
    }

?>