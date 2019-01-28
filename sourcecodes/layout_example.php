<?php

include("header_new.inc");
include("input_validate.php");

$keyval=valid_keyval($_GET["My_key"]);

$dir="./data/";

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
<form  method="post" name="form"> 
<table width="100%" align="center" style="background-color:" bordercolor=white border=0.5 cellspacing="0" cellpadding="0">
 <tr valign=top>
      <td colspan=4><hr size=3></td>
  </tr>
<tr >
<th align=right> <h3>Prediction Mode:&nbsp</h3></th>
  <td align=left>
      <input name="Datatype" type="radio" value=<?php $radioval="Evidence"; echo $radioval; ?> checked <?php echo $S1; ?>" onClick="javascript:this.form.submit();">Evidence&nbsp;&nbsp;&nbsp;
      <input name="Datatype" type="radio" value=<?php $radioval="Intervention"; echo $radioval; ?> <?php echo $S2; ?>" onClick="javascript:this.form.submit();"> Intervention
      
  </td>
</tr>
  <tr valign=top>
      <td colspan=4><hr size=3></td>
  </tr>

</table>
</form>

<!-- Site navigation menu -->
<ul class="navbar2">
   <li><p>Selected mode:<br><?php print($radiovalue);?></p></li>
   <li><p>Network ID:<br><?php print($keyval);?></p></li>
</ul>

<ul class="navbar">
 <li><a href="clear_example.php?My_key=<?php print($keyval);?>" target='_blank'>Clear evidence</a>
<li><a href="cv_predictions_example.php?My_key=<?php print($keyval);?>";>Cross validation and predictions</a>
  <li><a href="javascript:void(0);"
NAME="Model Averaging Matrix" title="Model Averaging Matrix"
onClick=window.open("matrix.php?My_key=<?php print($keyval);?>","Ratting","width=950,height=270,0,status=0,");>Display structure matrix</a>  
  <li><a href="javascript:void(0);"
NAME="Parameters" title="Parameters"
onClick=window.open("parameter_display.php?My_key=<?php print($keyval);?>","Ratting","width=950,height=270,0,status=0,");>View parameters</a>  
 <li><a href="help.php" target='_blank'>Help</a> 
 <li><a href="home.php">Home</a>
</ul>

<?php
    //echo "Selected mode:";
    //echo $radiovalue;



if($radiovalue==$valcompare)
{
?>
<div  id="outernew">

<object type="text/html" data="network_layout_evd_example.php?My_key=<?php print($keyval);?>" style="width:3000; height:3000">
<p>Error: Try again</p>
</object>

</div>
<?php
}
else
{
?>
<div  id="outernew">
<object type="text/html" data="network_layout_inv_example.php?My_key=<?php print($keyval);?>" style="width:3000; height:3000">
<p>Error: Try again</p>
</object>

</div>
<?php
}
?>

