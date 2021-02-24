<?php 
include("header_new.inc");
include("input_validate.php");
include("reroute_image.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
//if($_POST["My_key"]!="")
//  $keyval=valid_keyval($_POST["My_key"]);

$dir="/var/lib/genenet/bnw/";
$plotly_file=$keyval."violin_plotly.html";
$plotly_ev_file=$keyval."violin_plotly_evidence.html";
//$plotly_file_tmp="/tmp/bnw/".$keyval."violin_plotly.html";
//$plotly_ev_file_tmp="/tmp/bnw/".$keyval."violin_plotly_evidence.html";

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
  if(file_exists($dir.$plotly_file))
    {
   $file_data=file_get_contents($dir.$plotly_file); 
?>

 <object width="100%" height="500" data=<?=$file_data?> 
 </object>
<?php
    } else {
?>
   <div id="outernew">
   <h3> No violin plot data file found. </h3>
<?php
}
  if(file_exists($dir.$plotly_ev_file))
    $ev_file_data=file_get_contents($dir.$plotly_ev_file);
    {?>
 <object width="100%" height="500" data=<?=$ev_file_data?>
 </object>
<?php
    }
?>

</div>
</body>


