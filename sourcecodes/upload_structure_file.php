<meta charset="utf-8">
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php
  //Going to modify this so it just writes the uploaded data to a file.
  //Standardization and determining other factors will be performed in
  //  a Matlab/Octave script.

include("header_new.inc");
include("header_batchsearch.inc");
include("runtime_check.php");
include("input_validate.php");

$searchID="";
$UploadValue="NO";
$TextFile=$HTTP_POST_FILES["MyFile"]["name"];


/////////////Generate a random key/////////////////////
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;


if($_POST["My_key"]!="")
  $keyval=$_POST["My_key"];

$keyval=valid_keyval($keyval);

$sid=$keyval."continuous_input";
//$dir="./data/";
$dir="/tmp/bnw/";

$input_table_file="./data/".$keyval."input_table.txt";


$TextinFile=$dir.$sid."_orig.txt";


if(isset($HTTP_POST_VARS["searchkey"]))
{
   $searchID=$HTTP_POST_VARS["searchkey"];

}

if($searchID=="")
{
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a> 
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<?php
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
                 echo "<script type='text/javascript'> window.alert ('Sorry, error uploading $TextFile.')</script>";
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
<div id="outernew">
<h2><font>Upload data file</font></h2>
<FORM name="key_search" id="in_form" enctype="multipart/form-data" ACTION="upload_structure_file.php" METHOD=POST>
<INPUT style="background-color:#FFFFFF;" type="file" name="MyFile" id="MyFile_id" class="inputfile" onchange="upload_submit()" >
<label for="MyFile_id">Choose a file. . .</label>
  <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
<INPUT TYPE="hidden" name="MyUpload" value="NO">
  <textarea name="searchkey" style="display:none;"><?PHP print($searchID)?> </textarea>
<br><br>
  <INPUT TYPE="submit" class=button2 value="  Load example data  " onclick="return demo();">&nbsp&nbsp&nbsp
      <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
</FORM>
</div>
<script>
  function upload_submit() {
    Upload();
    document.getElementById("in_form").submit();
  }
</script>



<?php

if($searchID!="")
{
  if ($UploadValue=="NO")
  {
      $fpdata = fopen($dir.$keyval."continuous_input_orig.txt","w");
      fwrite($fpdata,$searchID);
  }  
  shell_exec('./run_scripts/run_prep_input '.$keyval);
?>
<ul class="navbar2">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a> 
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<div id="outernew_load">
<br>
<a class=button3 href="net_structure.php?My_key=<?php print($keyval);?>">Upload structure file</a>
<br>
<br>
<p><h3>The variables in the data file have the following properties:
<br></h3>
  <div class="d3_table" id="table_div1">
  <script type="text/javascript">
   d3.text("<?php print($input_table_file);?>", function(error, raw){
       var dsv = d3.dsvFormat("\t")
	 var data = dsv.parse(raw)
	 var caption_text = data.pop();
       if (error) throw error;
       tabulate_caption("#table_div1",data,caption_text.Variable);
     });
</script>
</div>
<br><br><br></h3>
</p>
<br>



<?php
}
?>

</body>
</html>


