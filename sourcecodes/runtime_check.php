<?php
//This code calculates execution time of BNW for input parametrs
function exe_time($keyval,$parent_number,$k_number)
{

$dir="./data/";

$nrf = $dir.$keyval."nrows.txt";
$nrows=trim(file_get_contents("$nrf"));

fwrite($fprows,"$lc\n");

$nf=$dir.$keyval."nnode.txt";
$node=trim(file_get_contents("$nf"))-1;
$parent_number=$parent_number-1;
if($parent_number>$node)
{
  $parent_number=$node;
}     


$paent="maxparent_table.txt";
$max_parent=file_get_contents("$paent");


$max_parent_row=array();
$max_parent_row=explode("\n",$max_parent);

$max_parent_data=array();
$max_parent_data=explode("\t",$max_parent_row[$node]);
$local=$max_parent_data[$parent_number];


$k_n="k_only_table.txt";
$k_p=file_get_contents("$k_n");

$k_row=array();
$k_row=explode("\n",$k_p);

$k_data=array();
$k_data=explode("\t",$k_row[$node]);
if($k_number==1)
  $k_time=$k_data[0];
else if($k_number==10)
  $k_time=$k_data[1];
else if($k_number==50)
  $k_time=$k_data[2];
else if($k_number==100)
  $k_time=$k_data[3];
else if($k_number==500)
  $k_time=$k_data[4];
else if($k_number==1000)
  $k_time=$k_data[5];
else if($k_number>1000)
  $k_time=$k_data[5]*($k_number/1000);


$totaltime=$local*($nrows/100)+$k_time+12;

//return $max_parent_row[$node];
return $totaltime;

}