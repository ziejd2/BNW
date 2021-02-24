<head>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php
include("header_new.inc");
include("header_batchsearch.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);

?>
</head>

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
    // check if name only contains letters and whitespace
    //    if (!preg_match("/^[\w]*$/",$varName)) {
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

<?php
$varName_file = "/var/lib/genenet/bnw/".$keyval."name.txt";
$varName_line = file_get_contents($varName_file);
$varNamesArr = explode("\t",$varName_line);
?>



<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="javascript:void(0);"
  NAME="Network" title="Network" onClick=window.open("layout.php?My_key=<?php print($keyval);?>","_self");>Return to network</a>
  <li><a href="javascript:void(0);"
  NAME="CV_predictions" title="CV_pred" onClick=window.open("cv_predictions.php?My_key=<?php print($keyval);?>","_self");>Return to validation and predictions menu</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>


</ul>




<div id="outer">
<!-- Main content -->

<?php
  //  $filename1="./data/".$keyval."kfoldCV.txt";
  //  $filename2="./data/".$keyval."kfoldCV_temp.txt";
  $dir="/var/lib/genenet/bnw/";
  $filename1=$keyval."kfoldCV.txt";
  $filename2=$keyval."kfoldCV_temp.txt";
$plotly_file=$keyval."kfold_plotly.html";
//$plotly_file_local="./data/".$keyval."kfold_plotly.html";
//$pred_file_local="./data/".$keyval."kfoldCV.txt";

if(file_exists($dir.$filename2))
 {?>
<br>
  <h2> k-fold cross-validation results are being calculated</h2>
<br>
<?php
 }
 else if(file_exists($dir.$plotly_file))
 {
 $table_text = json_encode(file("file://".$dir.$filename1));
 //$plotly_data=file_get_contents("file://".$dir.$plotly_file);
?>
 <div>
         <object width="800" height="500" data=<?php include($dir.$plotly_file)?> 
         </object>
     </div>
     <div class="d3_table" id="table_div1">
     <script type="text/javascript">
        d3.text("", function(error,raw) {
        var dsv=d3.dsvFormat("\t")
	var data1 = <?php echo $table_text;?>;
        var data2 = data1.join("");
        var data=dsv.parse(data2)
        var caption_text=data.pop()
        if (error) throw error;
           tabulate_caption("#table_div1",data,caption_text.CaseRow);
        });
</script>
</div>
<br>
    <a class=button2 href=<?php print("reroute.php?".$filename1);?>>Download cross-validation results</a>
<br>
<br>
<h3>Perform k-fold cross-validation of another network variable</h3><br>
<FORM METHOD="post" id=form ACTION="<?php $location = htmlspecialchars($_SERVER["PHP_SELF"]); echo ''.$location.'#form';?>">
<p style="font-size:18px">Select variable name:</p>
<select style='font-size:18px' Name="varName">
   <?php foreach($varNamesArr as $entry){
     echo "<option value='".$entry."'>$entry</option>";
   }?>
</select>
<span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
<br><br>
    <p style="font-size:18px">Number of folds:</p><SELECT style='font-size:20px' NAME="nm_folds">
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
     <br><br><input class=button2 type="submit" name="submit" value="Submit" style="font-size: 18px">
</FORM>
<br>

<?php
     } else {
?>
<h2>Perform k-fold cross-validation of network</h2>
<p align="justify">
  To perform cross-validation, select the name of the variable that you want to test <br>the predictions of and the number of folds below. <br><br>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
     <p style='font-size:18px'>Select variable name:</p>
<select style='font-size:18px' Name="varName">
   <?php foreach($varNamesArr as $entry){
     echo "<option value='".$entry."'>$entry</option>";
   }?>
</select>
  <span class="error"> <?php if($varNameErr!="1") {echo $varNameErr;};?></span>
  <br><br>
     <p style="font-size:18px">Number of folds:</p>
   <SELECT style='font-size:18px' NAME="nm_folds">
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
     <br><br><input class=button2 type="submit" name="submit" value="Submit" style="font-size: 18px;">
</FORM>
</p>
<br>

<?php
}

if($nm_folds!="")
{
  $command = './run_scripts/run_kfold '.$keyval.' '.$varName.' '.$nm_folds;
  $output = shell_exec("$command > /dev/null 2 > /dev/null &");
  $pred_link='cv_predictions.php?My_key='.$keyval;
  sleep(1);
  echo "<br>";
  echo "<a class=button3 href=$pred_link>Calculation submitted. Click here to return to cross-validation and predictions menu</a><br><br>";
}
?>

</div>
</body>


