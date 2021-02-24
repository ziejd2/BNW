<?php
  //Going to modify this so it just writes the uploaded data to a file.
  //Standardization and determining other factors will be performed in
  //  a Matlab/Octave script.

include("header_new.inc");
include("header_batchsearch.inc");
include("input_validate.php");

$searchID="";
$UploadValue="NO";
$TextFile=$_FILES["MyFile"]["name"];

if($_GET["My_key"]!="")
  $keyval=$_GET["My_key"];

if($_POST["My_key"]!="")
  $keyval=$_POST["My_key"];

$keyval=valid_keyval($keyval);

$sid="structure_input";
//$dir="./data/";
//$dir="/tmp/bnw/".$keyval;
$dir="/var/lib/genenet/bnw/";

$TextinFile=$dir.$keyval.$sid.".txt";

//$TextinFileFinal=$dir.$sid.".txt";

//$TextinFile=$dir.$sid."_temp.txt";

if(isset($_POST["searchkey"]))
{
   $searchID=$_POST["searchkey"];

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
<h2><font>Upload structure file</font></h2>
<FORM name="key_search" id="in_form" enctype="multipart/form-data" ACTION="net_structure.php" METHOD=POST>
<INPUT style="background-color:#FFFFFF;" type="file" name="MyFile" id="MyFile_id" class="inputfile" onchange="upload_submit()" >
<label for="MyFile_id">Choose a file. . .</label>
  <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
<INPUT TYPE="hidden" name="MyUpload" value="NO">
  <textarea name="searchkey" style="display:none;"><?PHP print($searchID)?> </textarea>
<br><br>
  <INPUT TYPE="submit" class=button2 value="  Load example structure  " onclick="return demo_str();">&nbsp&nbsp&nbsp
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
  $str_arr=array();
  $searchID=trim($searchID);
  $str_arr=explode("\n",$searchID);
  $data=array();

  $fpmain = fopen($TextinFile,"w");

  foreach($str_arr as $line)
  {
    $line=trim($line);
    fwrite($fpmain, "$line\n");

  }
      }
?>
<ul class="navbar2">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a> 
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<div id="outernew_load">
<br>
<a class=button3 href="graphviz_structure.php?My_key=<?php print($keyval);?>">Display uploaded network</a>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<?php
}
?>

</body>
</html>


