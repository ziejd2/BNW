<?php
////////////////This code load a data file and creates input file for continuous global optimal search (our modification). execute_bn_gom.php link is for execution of structure learning and create_tiers_gom.php is for adding restrictions///////////////////////////////////

include("header_new.inc");
include("runtime_check.php");
include("input_validate.php");

$keyval=valid_keyval($_GET["My_key"]);
//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";

//number of parents
$pfile=$dir.$keyval."parent.txt";
$parent_number=file_get_contents("$pfile");


//number of k for model averaging
$kfile=$dir.$keyval."k.txt";
$k_number=file_get_contents("$kfile");


$parent_number=trim($parent_number);
$k_number=trim($k_number);

//////////////////Check execution time///////////////////////////////////////////////
$runtime=exe_time($keyval,$parent_number,$k_number);
shell_exec('./run_scripts/run_settings '.$keyval);
?>

<div id="execute_div">
<br>
<p><h3><?php 
print("Network ID: $keyval<br/>");
print("Estimated run time: $runtime seconds");
?>
<br></h3>
<table align="center"><tr><td><b></br></br></br>Computing ....</b>
<div style="font-size:20pt;padding:6px;border:solid black 4px">
<span id="progress1">&nbsp; &nbsp;</span>
<span id="progress2">&nbsp; &nbsp;</span>
<span id="progress3">&nbsp; &nbsp;</span>
<span id="progress4">&nbsp; &nbsp;</span>
<span id="progress5">&nbsp; &nbsp;</span>
<span id="progress6">&nbsp; &nbsp;</span>
<span id="progress7">&nbsp; &nbsp;</span>
<span id="progress8">&nbsp; &nbsp;</span>
<span id="progress9">&nbsp; &nbsp;</span>
<span id="progress10">&nbsp; &nbsp;</span>
</p>
</div>
</td></tr></table>

<script language="javascript">
<!-- Begin Script
var progressEnd = 10;	// set to number of progress <span>'s.
var progressColor = 'blue';	// set to progress bar color
var progressInterval = 1000;	// set to time between updates (milli-seconds)

var progressAt = progressEnd;
var progressTimer;
function progress_clear() {
for (var i = 1; i <= progressEnd; i++) document.getElementById('progress'+i).style.backgroundColor = 'transparent';
progressAt = 0;
}
function progress_update() {
progressAt++;
if (progressAt > progressEnd) progress_clear();
else document.getElementById('progress'+progressAt).style.backgroundColor = progressColor;
progressTimer = setTimeout('progress_update()',progressInterval);
}
// End -->

progress_update();	
</script>
</div>
<script>
window.open("execute_bn_gom.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
