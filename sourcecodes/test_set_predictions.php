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
$searchID="";
$UploadValue="NO";
$TextFile=$_FILES["MyFile"]["name"];
//$TextinFile="./data/".$keyval."ts_upload.txt";
$TextinFile="/var/lib/genenet/bnw/".$keyval."ts_upload.txt";

if(isset($_POST["searchkey"]))
  {
    $searchID=$_POST["searchkey"];

  }

if(isset($_POST["MyUpload"]))
  {
    $UploadValue=$_POST["MyUpload"];
    if ($UploadValue=="YES")
      {
        if($TextFile!="")
          {
            $sta=move_uploaded_file($_FILES['MyFile']['tmp_name'],$TextinFile);
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
  //  $filename1="./data/".$keyval."ts_upload.txt";
  //$filename2="./data/".$keyval."ts_output.txt";
  $dir="/var/lib/genenet/bnw/";
  $filename1=$keyval."ts_upload.txt";
  $filename2=$keyval."ts_output.txt";
$plotly_file=$keyval."ts_plotly.html";
//$plotly_file_local="./data/".$keyval."ts_plotly.html";
//$pred_file_local="./data/".$keyval."ts_output.txt";


if(file_exists($dir.$filename1))
  {?>
<br>
<a class=button3 href="cv_predictions.php?My_key=<?php print($keyval);?>">Calculation submitted. Click here to return to cross-validation and predictions menu.</a>
<br>
<?php
  }
else if(file_exists($dir.$plotly_file))
  {
  $table_text = json_encode(file("file://".$dir.$filename2));
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
  <a class=button2 href=<?php print("reroute.php?".$filename2);?>>Download predictions</a>
<br>
<br>

<h3>Make a new set of predictions by submitting data file below</h3>
<FORM name="key_search" id="in_form" enctype="multipart/form-data" ACTION="test_set_predictions.php" METHOD=POST>
<table>
<tr>
<td>
<INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" id="MyFile_id" class="inputfile" onchange="upload_submit()">
<label for="MyFile_id">Choose a file. . .</label>
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
<br>
<br>
</div>
<script>
 function upload_submit() {
       Upload();
       document.getElementById("in_form").submit();
     }
</script>
  <br>


  <?php
   } else {
?>
<h2>Make predictions of test data file</h2>
  To make prediction for several test cases, upload a file containing the test cases.
<br>
<br>
    <FORM name="key_search" id="in_form2" enctype="multipart/form-data" ACTION="test_set_predictions.php" METHOD=POST>
<INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" id="MyFile_id2" class="inputfile" onchange="upload_submit2()">
<label for="MyFile_id2">Choose a file . . .</label>
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
<script>
   function upload_submit2() {
      Upload();
      document.getElementById("in_form2").submit();
    }
</script>

<br>

<?php
}

if($searchID!="")
{
  if($UploadValue=="NO")
    {
      //      $fpdata = fopen("./data/".$keyval."ts_input.txt","w");
      $fpdata = fopen($dir.$keyval."ts_upload.txt","w");
      fwrite($fpdata,$searchID);
    }
  $command = './run_scripts/run_test_set '.$keyval;
  $output = shell_exec("$command > /dev/null 2 > /dev/null &");
  $pred_link='cv_predictions.php?My_key='.$keyval;
}
?>

</div>
</body>


