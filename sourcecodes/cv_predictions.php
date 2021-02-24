<?php 

include("header_new.inc");
include("input_validate.php");

$keyval=valid_keyval($_GET["My_key"]);

?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="javascript:void(0);"
    NAME="Network" title="Network" onClick=window.open("layout.php?My_key=<?php print($keyval);?>","_self");>Return to network</a></li>
  <li><a href="javascript:void(0);"
    NAME="Clear predictions" title="Clear predictions" onClick=window.open("clear_cv_pred.php?My_key=<?php print($keyval);?>","_self");>Clear all prediction and validation files</a></li>
</ul>
    <ul class="navbar2">
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>


</ul>


<!-- Main content -->
<div id="outer">
<h1>Cross-validation and predictions</h1>
<br>
<p align="justify">
<ul class="listbar">
<li>This feature has recently been added to BNW and is still being tested. Please inform us of any issues.<br>
  Its use is briefly described <a href="help.php#crossvalid" target="_blank">here</a>.<br><br></li>
<?php
//  $dir="/tmp/bnw/";
  $dir="/var/lib/genenet/bnw/";
  $loo_file1=$dir.$keyval."looCV_temp.txt";
  $loo_file2=$dir.$keyval."looCV.txt";
if(file_exists($loo_file1))
  {?>
<li> <a class=button3 href="cv_predictions.php?My_key=<?php print($keyval);?>">Leave-one-out cross-validation predictions are being calculated.<br> Click here to update status.</a>
<br>
</li>
  <?php
   } else if(file_exists($loo_file2)) {
   ?>   
<li><a class=button3 href="cross_valid.php?My_key=<?php print($keyval);?>" target='_self'>Leave-one-out cross-validation results are available.</a></li>
  <?php
    } else {
  ?>
  <li><a class=button3 href="cross_valid.php?My_key=<?php print($keyval);?>" target='_self'>Perform leave-one-out cross-validation</a></li>
  <?php
    }
   ?>
<?php
  $kfold_file1=$dir.$keyval."kfoldCV_temp.txt";
  $kfold_file2=$dir.$keyval."kfold_plotly.html";
if(file_exists($kfold_file1))
  {?>
<li> <a class=button3 href="cv_predictions.php?My_key=<?php print($keyval);?>">k-fold cross-validation predictions are being calculated.<br>Click here to update status.</a>
<br>
</li>
  <?php
   } else if(file_exists($kfold_file2)) {
   ?>   
<li><a class=button3 href="kfold_cv.php?My_key=<?php print($keyval);?>" target='_self'>k-fold cross-validation results are available</a></li>
  <?php
    } else {
  ?>
  <li><a class=button3 href="kfold_cv.php?My_key=<?php print($keyval);?>" target='_self'>Perform k-fold cross-validation</a></li>
  <?php
    }
   ?>
<?php
  $ts_file1=$dir.$keyval."ts_upload.txt";
  $ts_file2=$dir.$keyval."ts_output.txt";
if(file_exists($ts_file1))
  {?>
<li><a class=button3 href="cv_predictions.php?My_key=<?php print($keyval);?>">Test set predictions are being calculated.<br> Click here to update status.</a>
<br>
</li>
  <?php
   } else if(file_exists($ts_file2)) {
   ?>   
<li><a class=button3 href="test_set_predictions.php?My_key=<?php print($keyval);?>" target='_self'>Test set predictions are available</a></li>
  <?php
    } else {
  ?>
  <li><a class=button3 href="test_set_predictions.php?My_key=<?php print($keyval);?>" target='_self'>Make predictions using a test data set</a></li>
  <?php
    }
   ?>
</ul>
</p>
</div>
</body>
</html>
