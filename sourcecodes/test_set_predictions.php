<?php 
include("header_new.inc");
include("header_batchsearch.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);
$searchID="";
$UploadValue="NO";
$TextFile=$HTTP_POST_FILES["MyFile"]["name"];
$TextinFile="./data/".$keyval."ts_upload.txt";
?>

<?php
if(isset($HTTP_POST_VARS["searchkey"]))
  {
    $searchID=$HTTP_POST_VARS["searchkey"];

  }

if(isset($HTTP_POST_VARS["MyUpload"]))
  {
    $UploadValue=$HTTP_POST_VARS["MyUpload"];
    if ($UploadValue=="YES")
      {
        if($TextFile!="")
	  {
            $sta=move_uploaded_file($HTTP_POST_FILES['MyFile']['tmp_name'],$TextinFile);
            if(!$sta)
	      {
                 echo "<script type='text/javascript'> window.alert ('Sorry, error uploading $TextFile.')</script>\
";
                 flush();
                 exit();
	      }
            else
	      {
                $searchID=file_get_contents("$TextinFile");
                //unlink($TextinFile);

	      }

	  }

       else
	 {
           echo "<script type='text/javascript'> window.alert ('Sorry, please select upload file.')</script>";
	 }
      }
  }


?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><p>Network ID:<br><?php print($keyval);?></p></li>
</ul>
<ul class="navbar">
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
  $filename1="./data/".$keyval."ts_upload.txt";
  $filename2="./data/".$keyval."ts_output.txt";
if(file_exists($filename1))
  {?>
<br>
Calculation submitted.
<br>
<a href="cv_predictions.php?My_key=<?php print($keyval);?>">Click to return to cross-validation and predictions menu</a>
<br>
<?php
  }
else if(file_exists($filename2))
  {?>
  <br>
  <h2> <a href=<?php $d="./data/".$keyval."ts_output.txt"; print($d);?>>View predictions</a></h2>
  <br>
<h3>Make a new set of predictions by submitting data file below</h3>
<FORM name="key_search" enctype="multipart/form-data" ACTION="test_set_predictions.php" METHOD=POST>
<table>
<tr>
<td>
<INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" size=45 >
<INPUT TYPE="submit" value="  Submit  " onclick="return Upload();">
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
<INPUT TYPE="hidden" name="MyUpload" value="NO">
</td>
</tr>
<tr>
<!---
<td align="left"><font color=#33339f><br>Content of uploaded data file:</font><br>
     <textarea name="searchkey" rows="8" cols="100"><?PHP print($searchID)?> </textarea>
</td>
---!>
</tr>
</table>
</FORM>
</div>
  <?php
   } else {
?>
<h2>Make predictions of test data file</h2>
  To make prediction for several test cases, upload a file containing the test cases.
<br>
<br>
    <FORM name="key_search" enctype="multipart/form-data" ACTION="test_set_predictions.php" METHOD=POST>
<table>
<tr>
<td>
<INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" size=45 >
<INPUT TYPE="submit" value="  Submit  " onclick="return Upload();">
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
<INPUT TYPE="hidden" name="MyUpload" value="NO">
</td>
</tr>
<tr>
<!---
<td align="left"><font color=#33339f><br>Content of uploaded data file:</font><br>
     <textarea name="searchkey" rows="8" cols="100"><?PHP print($searchID)?> </textarea>
</td>
---!>
</tr>
</table>
</FORM>
</div>
<br>

<?php
}

if($searchID!="")
{
  if($UploadValue=="NO")
    {
      $fpdata = fopen("./data/".$keyval."ts_input.txt","w");
      fwrite($fpdata,$searchID);
    }
  $command = './run_test_set '.$keyval;
  $output = shell_exec("$command > /dev/null 2 > /dev/null &");
  $pred_link='cv_predictions.php?My_key='.$keyval;
}
?>

</div>
</body>


