<?php 
$keyval=$_GET["My_key"];
$dir="./data/";

$matfile=$dir.$keyval."structure_input_temp.txt";
$fpw=fopen($dir.$keyval."Model_Averaging_Scores.txt","w");
$matrix1=file_get_contents("$matfile");              //file("$matfile");
fwrite($fpw,$matrix1);
fclose($fpw);
$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$dataname=array();
$dataname=explode("\t",$str_arrmat[0]);
$n=count($dataname);





//echo $m_line;


if($matrix1!="")
{?>
 <h2> Model Averaging Scores </h2>
 <br><table width="90%" align="center" style="background-color:" bordercolor="" border=1 cellspacing="0" cellpadding="0">
  
<tr>
<th>
Name
</th>
<?php


foreach($dataname as $cell1)
{
  $cell1=trim($cell1);
    ?>
<th> <?php print($cell1); ?> </th>
<?php

}
?>
</tr>
<?php
   
   for($i=0;$i<$n;$i++)
   {
       $ii=$i+1;
       $datamat=explode("\t",$str_arrmat[$ii]);
        
       ?>
   <tr>
   <th>
      <?php print trim($dataname[$i]); ?>
   </th>
   <?php
     $dpr=trim($dataname[$i]);
    
 
	foreach($datamat as $cell)
	{
         $cell=trim($cell);
         if($cell!="")
          {  
          ?>
          <td>
            <?php  print $cell; ?>
         </td>   
     <?php
         
	     }
        }
      ?> 
    <tr>
   <?php
       
       }
     ?>
 </table>

<a href=<?php $d="./data/".$keyval."Model_Averaging_Scores.txt"; print($d);?>>download</a>

<?php 

}

/////////////////////////////////Second//////////////////////////////////////////////////////////


$matfile=$dir.$keyval."structure_input.txt";
//echo $matfile;

$fpw=fopen($dir.$keyval."Structure_matrix.txt","w");

$matrix1=file_get_contents("$matfile");              //file("$matfile");

fwrite($fpw,$matrix1);
fclose($fpw);




$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$dataname=array();
$dataname=explode("\t",$str_arrmat[0]);
$n=count($dataname);





//echo $m_line;


if($matrix1!="")
{?>
 <h2> Structure Matrix </h2>
 <br><table width="90%" align="center" style="background-color:" bordercolor="" border=1 cellspacing="0" cellpadding="0">
  
<tr>
<th>
Name
</th>
<?php


foreach($dataname as $cell1)
{
  $cell1=trim($cell1);
    ?>
<th> <?php print($cell1); ?> </th>
<?php

}
?>
</tr>
<?php
   
   for($i=0;$i<$n;$i++)
   {
       $ii=$i+1;
       $datamat=explode("\t",$str_arrmat[$ii]);
        
       ?>
   <tr>
   <th>
      <?php print trim($dataname[$i]); ?>
   </th>
   <?php
     $dpr=trim($dataname[$i]);
    
 
	foreach($datamat as $cell)
	{
         $cell=trim($cell);
         if($cell!="")
          {  
          ?>
          <td>
            <?php  print $cell; ?>
         </td>   
     <?php
         
	     }
        }
      ?> 
    <tr>
   <?php
       
       }
     ?>
 </table>
<?php 

}
?>

<a href=<?php $d="./data/".$keyval."Structure_matrix.txt"; print($d);?>>download<br></a>
<input type=button onClick="self.close();" value="Close this window">