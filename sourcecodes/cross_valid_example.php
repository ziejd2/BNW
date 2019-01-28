<?php 
include("header_new.inc");
include("input_validate.php");
$keyval=$_GET["My_key"];
if($keyval!='') {
  $keyval = valid_keyval($keyval);
}
?>

<?php
$varName = "";
$varNameErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $keyval=$_POST["My_key"];
  if($keyval!='') {
    $keyval = valid_keyval($keyval);
  }
  if (empty($_POST["varName"])) {
    $varNameErr = "Entering a variable name is required";
  } else {
    //    $varName = test_input($_POST["varName"]);
    $varName = valid_input($_POST["varName"]);
    $varNameErr = "1";
    // check if name only contains letters and whitespace
    //if (!preg_match("/^[\w]*$/",$varName)) {
    //  $varNameErr = "The input string contains characters that are not allowed"; 
    //}
  }
}


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li><p>Network ID:<br><?php print($keyval);?></p></li>
</ul>
<ul class="navbar">
  <li><a href="javascript:void(0);"
  NAME="Network" title="Network" onClick=window.open("layout_example.php?My_key=<?php print($keyval);?>","_self");>Return to network</a>
  <li><a href="javascript:void(0);"
  NAME="CV_predictions" title="CV_pred" onClick=window.open("cv_predictions_example.php?My_key=<?php print($keyval);?>","_self");>Return to validation and predictions menu</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>


<!-- Main content -->
<div id="outer">

<?php
  $filename1="./data/".$keyval."looCV.txt";
  $filename2="./data/".$keyval."looCV_temp.txt";
if(file_exists($filename2))
 {?>
<br>
  <h2> Leave-one-out cross-validation results are being calculated</h2>
<br>
     <a href=<?php $ref="cv_predictions_example.php?".$keyval; print($ref);?>Refresh to update status</a>
<br>
<?php
 }
 else if(file_exists($filename1))
 {?>
<br>
    <h2> <a href=<?php $d="./data/".$keyval."looCV.txt"; print($d);?>>View cross-validation results</a></h2>
<br>
<h3>Perform leave-one-out cross-validation of another network variable<br></h3>
<p align="justify"> 
 Enter the name of another variable below to validate predictions of that variable.
<br>
<p><span class="error"></span></p>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Variable Name:<INPUT TYPE="text" name="varName" value="<?php echo $varName;?>" style="padding: 2px 5px; border: 2px solid; border-color: black black black black; font-family: Georgia, ..., serif; font-size: 18px;
display: block; height: 30px; width: 300px;"><span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
<input type='hidden' name='My_key' value='<?php print($keyval)?>'>
<br><input type="submit" name="submit" value="Submit" style="display: block; height: 30px; width:100px; font-family: Georgia, ..., serif; font-size: 16px;">
</FORM>
</p>
<br>
<?php
     } else {
?>
<h2>Perform leave-one-out cross-validation of network</h2>
<p align="justify"> 
  To perform cross-validation, enter the name of the variable that you want to test the predictions of below.
<br>
<p><span class="error"></span></p>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Variable Name:<INPUT TYPE="text" name="varName" value="<?php echo $varName;?>" style="padding: 2px 5px; border: 2px solid; border-color: black black black black; font-family: Georgia, ..., serif; font-size: 18px;
display: block; height: 30px; width: 300px;"><span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
<input type='hidden' name='My_key' value='<?php print($keyval)?>'>
<br><input type="submit" name="submit" value="Submit" style="display: block; height: 30px; width:100px; font-family: Georgia, ..., serif; font-size: 16px;">
</FORM>
</p>
<br>

<?php
}

if($varNameErr=="1")
{
  $command = './run_scripts/run_loo '.$keyval.' '.$varName;
  $output = shell_exec("$command > /dev/null 2 > /dev/null &");
  $pred_link='cv_predictions_example.php?My_key='.$keyval;
  sleep(1);
  echo "<br>";
  echo "Calculation submitted";
  echo "<br>";
  echo "<a href=$pred_link>Click to return to cross-validation and predictions menu</a>";
}
?>

</div>
</body>


