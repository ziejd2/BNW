<?php 
include("input_validate.php");
$keyval=valid_keyval(trim($_GET['My_key']));

//include("restructuremap.php");


function discretemap($textdata,$sym,$dmapdata)
{
$textdata=trim($textdata);
$leve_l=array();
$leve_l=explode("\n",$dmapdata);
$leve_d=array();

foreach($leve_l as $l)
{ 
  $l=trim($l);
  $leve_d=explode("\t",$l);
  $inxname=trim($leve_d[0]);
  $name=trim($sym);
  if($inxname==$name)
  {    $i=0;
       foreach($leve_d as $d)
       { 
         $d=trim($d);
         if($d==$textdata)
             return $i;
         $i++;
       }
  } 
}

}





function mapid($name,$keyval)
{

$dir="./data/";
$nm=$dir.$keyval."mapdata.txt";
$namelist=file_get_contents("$nm");

$str_arrname=array();
$str_arrname=explode("\n",$namelist);
$dataname=array();
$dataname=explode("\t",$str_arrname[0]);
$i=1;

 foreach($dataname as $cell)
 {
   $cell=trim($cell);
   if($cell==$name)
   {
    $val=$i;
    break;      
   }
    $i++;
 }

return $val;

}


$dir="./data/";

$lfile=$dir.$keyval."nlevels.txt";
$dmapdata=file_get_contents($lfile);   


$sym=trim($_GET['name']);

$textdata=$_GET['evidence'];
$ft=$dir.$keyval."var.txt";
$fpvar = file_get_contents("$ft");
$ft=$dir.$keyval."vardata.txt";
$fpdata  = file_get_contents("$ft");
$ft=$dir.$keyval."varname.txt";
$fpvarname  = file_get_contents("$ft");

$vriable=mapid($sym,$keyval);

$ft=$dir.$keyval."type.txt";
$type = file_get_contents("$ft");
$type_r=array();
$type_r=explode("\n",$type);
$type_n=array();
$type_n=explode("\t",$type_r[0]);
$type_d=array();
$type_d=explode("\t",$type_r[1]);

$nn=count($type_n);

for($j=0;$j<$nn;$j++)
{   
   $dn=$type_n[$j];
   $sym=trim($sym);
   $dn=trim($dn); 
   if($dn==$sym)
     $s=$j;
} 

$dt=$type_d[$s];

if($dt!=1)
   $textdata=discretemap($textdata,$sym,$dmapdata);

 $ft=$dir."$keyval"."var.txt"; 
 $f1=fopen("$ft","w");  
 $ft=$dir."$keyval"."vardata.txt";
 $f2=fopen("$ft","w");  
 $ft=$dir."$keyval"."varname.txt";
 $f3=fopen("$ft","w");


$pos = strpos($fpvarname, $sym);

if($fpvarname=="")
{
  fwrite($f1,$vriable);
  fwrite($f2,$textdata);
  fwrite($f3,$sym);

}
else 
{
  if ($pos === false)// append at buttom as new records 
  {
  
     $fpvar=$fpvar."\t".$vriable;
     fwrite($f1,$fpvar);

     $fpdata=$fpdata."\t".$textdata;
     fwrite($f2,$fpdata);

     $fpvarname=$fpvarname."\t".$sym;
     fwrite($f3,$fpvarname);

  }
  else 
  {
     $cld=explode("\t",$fpvarname);
     $j=0;
     $inx=0;
     foreach($cld as $cc)
     {
        $cc=trim($cc);
        if($cc==$sym)
        {
              $inx=$j;
        } 
        $j++;
    
     }

 
     fwrite($f1,$fpvar);
     fwrite($f3,$fpvarname);

     $cld1=explode("\t",$fpdata);
     $j=0;
     $dataw="";
     foreach($cld1 as $cc)
     {
        $cc=trim($cc);
        if($j==$inx)
        {
              $inx=$j;
              fprintf($f2,"%s\t",$textdata);
        }
        else 
        {          
              fprintf($f2,"%s\t",$cc);
        }
        
        $j++;
    
     }

   }

}

 
  //execute shell script for matlab
  $keyval = valid_keyval($keyval);
  shell_exec('./run_octave_evd '.$keyval);

?>
<script>
window.open("network_layout_evd_2_example.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
