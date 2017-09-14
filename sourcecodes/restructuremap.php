<?php


function reversemap($id,$val,$keyval)
{

$dir="./data/";

$matfile=$dir."$keyval"."map.txt";


$matrix=file("$matfile");
$matrix1=file_get_contents("$matfile");             
$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$data_cell_canvas=array();

foreach($str_arrmat as $line)
{

$data_cell_canvas=explode("\t",$line);
$map_name=trim($data_cell_canvas[0]);
$map_type=$data_cell_canvas[1];
$map_std=$data_cell_canvas[2];
$map_mean=$data_cell_canvas[3];
if($id==$map_name)
{
 $mapval=($val-$map_mean)/$map_std;
  break;
}

}
return $mapval;
}



?>

