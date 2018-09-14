<?php 

include("header_new.inc");
include("input_validate.php");

$keyval=valid_keyval($_GET["My_key"]);

?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><p>Network ID:<br><?php print($keyval);?></p></li>
</ul>
<ul class="navbar">
  <li><a href="javascript:void(0);"
    NAME="Network" title="Network" onClick=window.open("layout.php?My_key=<?php print($keyval);?>","_self");>Return to network</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>


</ul>


<!-- Main content -->
<div id="outer">
<h1>Cross-validation and predictions</h1>
<br>
<p align="justify">
<ul class="listbar">
<?php
  $loo_file1="./data/".$keyval."looCV_temp.txt";
  $loo_file2="./data/".$keyval."looCV.txt";
if(file_exists($loo_file1))
  {?>
<li>1: Leave-one-out cross-validation predictions are being calculated.
     <a href="cv_predictions.php?My_key=<?php print($keyval);?>">Refresh and update status</a>
<br>
</li>
  <?php
   } else if(file_exists($loo_file2)) {
   ?>   
<li>1: <a href="cross_valid.php?My_key=<?php print($keyval);?>" target='_self'>Leave-one-out cross-validation results are available.</a></li>
  <?php
    } else {
  ?>
  <li>1: <a href="cross_valid.php?My_key=<?php print($keyval);?>" target='_self'>Perform leave-one-out cross-validation</a></li>
  <?php
    }
   ?>
<?php
  $kfold_file1="./data/".$keyval."kfoldCV_temp.txt";
  $kfold_file2="./data/".$keyval."kfoldCV.txt";
if(file_exists($kfold_file1))
  {?>
<li>2: k-fold cross-validation predictions are being calculated.
     <a href="cv_predictions.php?My_key=<?php print($keyval);?>">Refresh and update status</a>
<br>
</li>
  <?php
   } else if(file_exists($kfold_file2)) {
   ?>   
<li>2: <a href="kfold_cv.php?My_key=<?php print($keyval);?>" target='_self'>k-fold cross-validation results are available</a></li>
  <?php
    } else {
  ?>
  <li>2: <a href="kfold_cv.php?My_key=<?php print($keyval);?>" target='_self'>Perform k-fold cross-validation</a></li>
  <?php
    }
   ?>
<?php
  $ts_file1="./data/".$keyval."ts_upload.txt";
  $ts_file2="./data/".$keyval."ts_output.txt";
if(file_exists($ts_file1))
  {?>
<li>3: Test set predictions are being calculated.
     <a href="cv_predictions.php?My_key=<?php print($keyval);?>">Refresh and update status</a>
<br>
</li>
  <?php
   } else if(file_exists($ts_file2)) {
   ?>   
<li>3: <a href="test_set_predictions.php?My_key=<?php print($keyval);?>" target='_self'>Test set predictions are available</a></li>
  <?php
    } else {
  ?>
  <li>3: <a href="test_set_predictions.php?My_key=<?php print($keyval);?>" target='_self'>Make predictions using a test data set</a></li>
  <?php
    }
   ?>
</ul>
</p>
</div>
</body>
</html>
