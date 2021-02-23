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
$keyval=$_GET["My_key"];
if($keyval!='') {
  $keyval = valid_keyval($keyval);
}
?>
</head>

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


<?php
$varName_file = "/tmp/bnw/".$keyval."name.txt";
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

<body>

<!-- Main content -->
<div id="outer">

<?php
  //  $filename1="./data/".$keyval."looCV.txt";
  //  $filename2="./data/".$keyval."looCV_temp.txt";
  $dir="/tmp/bnw/";
$filename1=$dir.$keyval."looCV.txt";
$filename2=$dir.$keyval."looCV_temp.txt";
   $plotly_file=$dir.$keyval."loo_plotly.html";
   $plotly_file_local = "./data/".$keyval."loo_plotly.html";
   $pred_file_local = "./data/".$keyval."looCV.txt";
if(file_exists($filename2))
 {?>
<br>
  <h2> Leave-one-out cross-validation results are being calculated</h2>
<br>
     <a href=<?php $ref="cv_predictions.php?".$keyval; print($ref);?>Refresh to update status</a>
<br>
<?php
 }
 else if(file_exists($filename1))
 {?>
     <div>
	  <object type="text/html" data=<?php print($plotly_file_local);?> width="800" height="500" >
         </object>
     </div>
     <div class="d3_table" id="table_div1">
     <script type="text/javascript">
	d3.text("<?php print($pred_file_local);?>", function(error,raw) {
	    var dsv=d3.dsvFormat("\t")
	    var data=dsv.parse(raw)
	      var caption_text=data.pop()
	      if (error) throw error;
	    tabulate_caption("#table_div1",data,caption_text.CaseRow);
	  });
</script>
</div>
<br>
    <a class=button2 href=<?php print($pred_file_local);?>>Download cross-validation results</a>
<br>
<br>
<h3>Perform leave-one-out cross-validation of another network variable<br></h3>
<p><span class="error"></span></p>
<FORM METHOD="post" id=form ACTION="<?php $location = htmlspecialchars($_SERVER["PHP_SELF"]); echo ''.$location.'#form';?>">
<p style='font-size:18px'>Select variable name:</p>
<select style='font-size:18px' Name="varName">
 <?php foreach($varNamesArr as $entry){
     echo "<option value='".$entry."'>$entry</option>";
   }?>
</select>
<input type='hidden' name='My_key' value='<?php print($keyval)?>'>
<input class=button2 type="submit" name="submit" value="Submit" style="font-size:18px">
<br><br>
</FORM>
</FORM>
</p>
<br>
<?php
     } else {
?>
<h2>Perform leave-one-out cross-validation of network</h2>
<p align="justify" style='font-size:18px'> 
  Select the variable that you want to examine the predictions of below.
<br>
<br>
<p><span class="error"></span></p>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p style='font-size:18px'> Select variable name:</p><br>
<select style='font-size:18px' Name="varName">
 <?php foreach($varNamesArr as $entry){
     echo "<option value='".$entry."'>$entry</option>";
   }?>
</select>
<input type='hidden' name='My_key' value='<?php print($keyval)?>'>
<input class=button2 type="submit" name="submit" value="Submit" style="font-size:18px">
</FORM>
</p>
<br>

<?php
}

if($varNameErr=="1")
{
  $command = './run_scripts/run_loo '.$keyval.' '.$varName;
  //  $command = 'sh plotly_loo.sh'
  $output = shell_exec("$command > /dev/null 2 > /dev/null &");
  $pred_link='cv_predictions.php?My_key='.$keyval;
  sleep(1);
  echo "<br>";
  echo "<a class=button3 href=$pred_link>Calculation submitted. Click here to return to cross-validation and predictions menu.</a><br><br>";
}
?>

</div>
</body>


