<?php 
include("header_new.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);
?>

<?php
$varName = "";
$varNameErr = "";
$nm_folds="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nm_folds = (int)$_POST['nm_folds'];
  if (empty($_POST["varName"])) {
    $varNameErr = "Entering a variable name is required";
  } else {
    //    $varName = test_input($_POST["varName"]);
    $varName = valid_input($_POST["varName"]);
    $varNameErr = "1";
    // check if name only contains letters, numbers, and whitespace
    //   if (!preg_match('/^[-A-Za-z\d\ \t\r\n_\.]+$',$varName)) {
    //      $varNameErr = "The input string contains characters that are not allowed";
    //    }
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




<div id="outer">
<!-- Main content -->

<?php
  $filename1="./data/".$keyval."kfoldCV.txt";
  $filename2="./data/".$keyval."kfoldCV_temp.txt";
if(file_exists($filename2))
 {?>
<br>
  <h2> k-fold cross-validation results are being calculated</h2>
<br>
<?php
 }
 else if(file_exists($filename1))
 {?>
<br>
    <h2> <a href=<?php $d="./data/".$keyval."kfoldCV.txt"; print($d);?>>View cross-validation results</a></h2>
<br>
<h3>Perform k-fold cross-validation of another network variable</h3>
<p align="justify"> 
 Enter the name of variable and number of folds below to validate predictions of that variable.
<br>
</p>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Variable name:<INPUT TYPE="text" name="varName" value="<?php echo $varName;?>" style="padding: 2px 5px; border: 2px solid; border-color: black black black black; font-family: Georgia, ..., serif; font-size: 18px; display: block; height: 30px; width: 300px;">
<span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
<br>
    Number of folds:<br><SELECT NAME="nm_folds">
     <option value="2">2</option>
     <option value="3">3</option>
     <option value="4">4</option>
     <option value="5">5</option>
     <option value="6">6</option>
     <option value="7">7</option>
     <option value="8">8</option>
     <option value="9">9</option>
     <option value="10">10</option>
     </select>
     <input type='hidden' name='My_key' value='<?php print($keyval)?>'>
     <br><br><input type="submit" name="submit" value="Submit" style="display: block; height: 30px; width:100px; font-family: Georgia, ..., serif; font-size: 16px;">
</FORM>
<br>
<?php
     } else {
?>
<h2>Perform k-fold cross-validation of network</h2>
<p align="justify"> 
  To perform cross-validation, enter the name of the variable <br>that you want to test the predictions of and the number <br>of folds that the data should be separated into below.
<br>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Variable Name:<INPUT TYPE="text" name="varName" value="<?php echo $varName;?>" 
  style="padding: 2px 5px; border: 2px solid; border-color: black black black black; font-family: Georgia, ..., serif; font-size: 18px; display: block; height: 30px; width: 300px;">
  <span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
  <br>
     Number of folds:<br><SELECT NAME="nm_folds">
     <option value="2">2</option>
     <option value="3">3</option>
     <option value="4">4</option>
     <option value="5">5</option>
     <option value="6">6</option>
     <option value="7">7</option>
     <option value="8">8</option>
     <option value="9">9</option>
     <option value="10">10</option>
     </select>
     <input type='hidden' name='My_key' value='<?php print($keyval)?>'>
     <br><br><input type="submit" name="submit" value="Submit" style="display: block; height: 30px; width:100px; font-family: Georgia, ..., serif; font-size: 16px;">
</FORM>
</p>
<br>

<?php
}

if($nm_folds!="")
{
  $command = './run_kfold '.$keyval.' '.$varName.' '.$nm_folds;
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


