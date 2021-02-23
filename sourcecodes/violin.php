<?php 
include("header_new.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);

$plotly_file_data="./data/".$keyval."violin_plotly.html";
$plotly_ev_file_data="./data/".$keyval."violin_plotly_evidence.html";
$plotly_file_tmp="/tmp/bnw/".$keyval."violin_plotly.html";
$plotly_ev_file_tmp="/tmp/bnw/".$keyval."violin_plotly_evidence.html";


?>



<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>


</ul>




<div id="violin_div">
<!-- Main content -->
<?php
  if(file_exists($plotly_file_tmp))
    {?>
 <object type="text/html" data=<?php print($plotly_file_data);?> width="100%" height="500">
 </object>
<?php
    }
  if(file_exists($plotly_ev_file_tmp))
    {?>
 <object type="text/html" data=<?php print($plotly_ev_file_data);?> width="100%" height="500">
 </object>
<?php
    }
?>

</div>
</body>


