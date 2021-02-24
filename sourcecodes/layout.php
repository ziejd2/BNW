<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>

<?php

include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";

$vf1=$dir.$keyval."var.txt";
$vf2=$dir.$keyval."varname.txt";
$vf3=$dir.$keyval."vardata.txt";
unlink($vf1);
unlink($vf2);
unlink($vf3);


$radiovalue="Evidence";
$valcompare="Evidence";


  if(isset($_POST["Datatype"])) 
 {

 $radiovalue=$_POST["Datatype"];
 if($radiovalue=="Evidence") $S1="checked=\"checked\"";
 if($radiovalue=="Intervention") $S2="checked=\"checked\"";
 }
 else
 {
   $radiovalue="Evidence";
   $S1="checked=\"checked\"";
 }



?>
<script language="JavaScript">
<!--
function calcHeight()
{
  //find the height of the internal page
  var the_height=
    document.getElementById('the_iframe').contentWindow.
      document.body.scrollHeight;

  //change the height of the iframe
  document.getElementById('the_iframe').height=
      the_height;
}
//-->
</script>

<!-- Site navigation menu -->
<ul class="navbar2">
   <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
   <li><p style="display:inline">Prediction mode</p>
     <form  method="post" name="form"> 
    <label><input name="Datatype" type="radio" value=<?php $radioval="Evidence"; echo $radioval; ?> checked <?php echo $S1; ?>" onClick="javascript:this.form.submit();"><p style="display:inline">Evidence<br></p></label>
      <label><input name="Datatype" type="radio" value=<?php $radioval="Intervention"; echo $radioval; ?> <?php echo $S2; ?>" onClick="javascript:this.form.submit();"><p style="display:inline">Intervention</p></label>
   </form></li>
   <li><a href="clear.php?My_key=<?php print($keyval);?>" target='_blank'>Clear evidence</a>
</ul>
<ul class="navbar2">
   <li><a href="cv_predictions.php?My_key=<?php print($keyval);?>";>Cross validation and predictions</a>
</ul>
   <button class="accordion">More about network&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
<div class="panel">
<ul class="navbar_ac">
<li><a href="parameter_display.php?My_key=<?php print($keyval);?>" target='_blank'>View parameters</a>
<li><a href="violin.php?My_key=<?php print($keyval);?>" target='_blank'>View violin plots of distributions</a>
  <li><a href="matrix_new.php?My_key=<?php print($keyval);?>" target="_blank">View structure matrix</a>  
<?php
$filename1="/var/lib/genenet/bnw/".$keyval."slsettings.txt";
if(file_exists($filename1))  
  {?>
  <li><a href="review_settings.php?My_key=<?php print($keyval);?>" target="_blank">View structure learning settings</a>
<?php
}?>  
  <li><a href="input_check.php?My_key=<?php print($keyval);?>" target="_blank">View input data and descriptions</a>
 <li><a href="layout_svg_no.php?My_key=<?php print($keyval);?>">Return to simplified network structure</a>
</ul>
</div>
<button class="accordion">Modify network structure&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
<div class="panel">
<ul class="navbar_ac">
<li><a href="layout_cyto.php?My_key=<?php print($keyval);?>" target='_blank'>Add or remove network edges</a>
<li><a href="modify_structure_learning.php?My_key=<?php print($keyval);?>" target='_blank';>Modify structure learning settings</a>
<li><a href="remove_variables.php?My_key=<?php print($keyval);?>" target='_blank';>Select variables to remove and relearn structure</a>
</ul>
</div>
<ul class="navbar2">
 <li><a href="about_this_page.php#network_predictions" target='_blank'>About this page</a> 
 <li><a href="help.php" target='_blank'>Help</a> 
 <li><a href="../home.php">Home</a>
</ul>


<?php
    //echo "Selected mode:";
    //echo $radiovalue;



if($radiovalue==$valcompare)
{
?>
<div  id="outernew">
<object type="text/html" data="network_layout_evd.php?My_key=<?php print($keyval);?>" style="width:3000; height:3000">
<p>Error: Try again</p>
</object>
</div>
<?php
}
else
{
?>
<div  id="outernew">
<object type="text/html" data="network_layout_inv.php?My_key=<?php print($keyval);?>" style="width:3000; height:3000">
<p>Error: Try again</p>
</object>

</div>
<?php
}
?>

