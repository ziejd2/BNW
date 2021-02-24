<?php
function structure_change($keyval)
{
  //$dir="./data/";
$dir="/tmp/bnw/";
$matfile=$dir.$keyval."varname.txt";
$namelist=file_get_contents("$matfile");
$matfilestruct=$dir.$keyval."structure_input.txt";
$list=file_get_contents("$matfilestruct");
$ft=$dir.$keyval."structure_input_new.txt";
$fp = fopen("$ft","w");

$str_arrname=array();
$str_arrname=explode("\n",$namelist);
$dataname=array();
$dataname=explode("\t",$str_arrname[0]);

$arrname=array();
$arrname=explode("\n",$list); 


$cell_val=array();

$data_cell=array();

$name=explode("\t",$arrname[0]);



$i=0;
foreach($name as $name_cell)
{
 $cell_val[$i]=1;
 $name_cell=trim($name_cell);
  foreach($dataname as $list_name)
  { 
       $list_name=trim($list_name);
        if($name_cell==$list_name)
        {   
             //echo $name_cell; 
             $cell_val[$i]=0;
             
        }
   
  }
$i++;
}


$j=0;
for($l=0;$l<=$i;$l++)
{
  $line=$arrname[$l];

  if($j==0)
  {
    fwrite($fp,$line);
    
  }
  else
  {  
   
    $data_cell=explode("\t",$line);
  
    for($k=0;$k<$i;$k++)
    {
       $cell=$data_cell[$k]*$cell_val[$k];
       fprintf($fp,"%s\t",$cell);
    } 
  }
  fprintf($fp,"\n");
 
  $j++;
}

//return $matfilestruct;
}

?>