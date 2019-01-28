<?php 
  //include("restructuremap.php");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

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

$nm=$dir."$keyval"."mapdata.txt";
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




 $ft=$dir.$keyval."var.txt"; 
 $f1=fopen("$ft","w");  
 $ft=$dir.$keyval."vardata.txt";
 $f2=fopen("$ft","w");  
 $ft=$dir.$keyval."varname.txt";
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

include("mat_structure.php");
$keyval = valid_keyval($keyval);
structure_change($keyval);





//  $file1="./data/".$keyval."run_newintervention.sh";
//  $initiallines=file_get_contents("./data/temp_intervention_file");
//  $all_lines="$initiallines"."$keyval\nfi\nexit";

//  $fp = fopen($file1,"w"); 
//  fwrite($fp, "$all_lines\n");
//  fclose($fp);
  //execute shell script for matlab
 // $cmd="./runmat_inv.sh $keyval";
//  system($cmd);
shell_exec('./run_scripts/run_octave_inv '.$keyval);




$matfile=$dir.$keyval."structure_input_new.txt";

$fout=fopen($dir.$keyval."graphviz_new.txt","w");
$matrix1=file_get_contents("$matfile");           
$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$dataname=array();
$dataname=explode("\t",$str_arrmat[0]);
$n=count($dataname);

$initialstring="digraph G {\n"."size=\"10,10\";  ratio = fill;\n"."node [shape=square,width=1.5];\n";
$endstring="}";
fwrite($fout,"$initialstring");

for($i=1;$i<=$n;$i++)
{
         $ii=$i-1; 
         $row_name=trim($dataname[$ii]);  

 
         $col_arr=explode("\t",$str_arrmat[$i]);
        
        
	   for($j=0;$j<$n;$j++)
	   {
               $col_val=trim($col_arr[$j]);
               if($col_val==1)
               { 
                  $col_name=trim($dataname[$j]);
                  fprintf($fout,"%s -> %s;\n",$row_name,$col_name);  

                 
	        }
          }
          
     
} 
fwrite($fout,"$endstring");   

?>
<script>
window.open("network_layout_inv_2_example.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
