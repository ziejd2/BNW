<?php 

///////This code will allow users to group variables in tier. getcombineDescription() function combined all data and take you to "tier_description_processing_gom.php" for preparation of ban and whitelist //////////

include("header_new.inc");
include("runtime_check.php");
include("input_validate.php");
$keyval=$_GET["My_key"];

//$dir="./data/";
$dir="/tmp/bnw/";

$type_n=array();

//Get number of tier data and key value for changes in number of tier

if(isset($_POST["nm_tier"]))
{
   $type_n=explode("|",$_POST["nm_tier"]);
}
else if(isset($_POST["nm_parent"]))
{
   $type_n=explode("|",$_POST["nm_parent"]);
}
else if(isset($_POST["nm_k"]))
{
   $type_n=explode("|",$_POST["nm_k"]);
}
else if(isset($_POST["nm_thr"]))
{
    $type_n=explode("|",$_POST["nm_thr"]);
}

$parent_number=trim($type_n[0]);   
$k_number=trim($type_n[1]);   
$tier_number=trim($type_n[2]);   
$structure_thr=trim($type_n[3]);   


if($keyval=="")
  $keyval=$type_n[4];


if($parent_number=="")
{
   $parent_number=4;
}

if($k_number=="")
{
   $k_number=1;
}

if($tier_number=="")
{
  $tier_number=3;
}
if($structure_thr=="")
{
  $structure_thr=0.5;
}



$nf=$dir.$keyval."nnode.txt";
$node=trim(file_get_contents("$nf"));
$maxplist=$node-1;

//print default number of parents
$pfile=$dir.$keyval."parent.txt";
$parentf=fopen($pfile,"w");
fwrite($parentf,"$parent_number\n");

//print default number of k for model averaging
$kfile=$dir.$keyval."k.txt";
$kf=fopen($kfile,"w");
fwrite($kf,"$k_number\n");

//print model averaging threshold
$thrfile=$dir.$keyval."thr.txt";
$kf=fopen($thrfile,"w");
fwrite($kf,"$structure_thr\n");

//////////////////Check execution time//////////////////////////////////////////////
$keyval=valid_keyval($keyval);
$runtime=exe_time($keyval,$parent_number,$k_number);

//print("Runtime is $runtime");

?>
<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="input_check.php?My_key=<?php print($keyval);?>" target="_blank";>View uploaded variables and data</a>
  <li><a href="help.php#constraint_interface" target="_blank">About this page</a>
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<div id="outernew_sci">
<br>
<p><h3>Set structure learning settings:<br></h3>
</p>
<table align="center">
<tr>
<td align="left">
Maximum number of parents for any node:
</td>
<td align="left"> 
     <form method="post" action="create_tiers_gom_part1.php" name="form">
  <SELECT style='font-size:16px' NAME="nm_parent"  onchange="form.submit();">
      <?php if($node<=5) 
            {
               for($i=1;$i<=4;$i++)
              {
            ?>
              <option value=<?php $combined=$i."|".$k_number."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($parent_number == $i) {print "selected";}?>><?php print($i)?></option>
            <?php
              }
            }
            else if($node<=10) 
            {
               for($i=1;$i<$node;$i++)
              {
            ?>
              <option value=<?php $combined=$i."|".$k_number."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($parent_number == $i) {print "selected";}?>><?php print($i)?></option>
            <?php
              }
            }
            else
            {
               for($i=1;$i<=9;$i++)
               {
   
                ?>
                 <option value=<?php $combined=$i."|".$k_number."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($parent_number == $i) {print "selected";}?>><?php print($i)?></option>
               <?php
               }
               ?>
              <option value=<?php $combined=$maxplist."|".$k_number."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($parent_number == $maxplist) {print "selected";}?>>All</option>
           <?php

            }
            
     ?>  
 
      </SELECT>
   </form>
</td>
</tr>
<tr>
<td align="left">
Number of networks to include in model averaging:
</td>
<td align="left"> 
     <form method="post" action="create_tiers_gom_part1.php" name="form">
      <SELECT style='font-size:16px' NAME="nm_k"  onchange="form.submit();">
           <option selected value=<?php $combined=$parent_number."|"."1"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 1) {print "selected";}?>><?php print("1")?></option>
           <option value=<?php $combined=$parent_number."|"."10"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 10) {print "selected";}?>><?php print("10")?></option>
          <option value=<?php $combined=$parent_number."|"."50"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 50) {print "selected";}?>><?php print("50")?></option>
          <option value=<?php $combined=$parent_number."|"."100"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 100) {print "selected";}?>><?php print("100")?></option>
          <option value=<?php $combined=$parent_number."|"."500"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 500) {print "selected";}?>><?php print("500")?></option>
          <option value=<?php $combined=$parent_number."|"."1000"."|".$tier_number."|".$structure_thr."|".$keyval; print($combined)?> <?php if($k_number == 1000) {print "selected";}?>><?php print("1000")?></option>
      </SELECT>
   </form>
</td>
</tr>
<tr>
<td align="left">
Model averaging edge selection threshold:
</td>
<td align="left"> 
     <form method="post" action="create_tiers_gom_part1.php" name="form">
      <SELECT style='font-size:16px' NAME="nm_thr"  onchange="form.submit();">
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."0.5"."|".$keyval; print($combined)?> <?php if($structure_thr == 0.5) {print "selected";}?>>0.5</option>
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."0.6"."|".$keyval; print($combined)?> <?php if($structure_thr == 0.6) {print "selected";}?>>0.6</option>
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."0.7"."|".$keyval; print($combined)?> <?php if($structure_thr == 0.7) {print "selected";}?>>0.7</option>
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."0.8"."|".$keyval; print($combined)?> <?php if($structure_thr == 0.8) {print "selected";}?>>0.8</option>
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."0.9"."|".$keyval; print($combined)?> <?php if($structure_thr == 0.9) {print "selected";}?>>0.9</option>
     <option value=<?php $combined=$parent_number."|".$k_number."|".$tier_number."|"."1.0"."|".$keyval; print($combined)?> <?php if($structure_thr == 1.0) {print "selected";}?>>1.0</option>
    
      </SELECT>
   </form>
</td>
</tr>
</table>
</br>

<p><h3><?php 
print("Estimated run time for the current parameters: $runtime seconds");
?>
<br></h3>
</p>
<br>
<a class=button3 href="create_tiers_gom_part2.php?My_key=<?php print($keyval);?>">Continue to assign variables to tiers</a>
<br>