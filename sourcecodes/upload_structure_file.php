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
$dir="./data/";

$TextinFile=$dir.$sid."_orig.txt";


if(isset($HTTP_POST_VARS["searchkey"]))
{
   $searchID=$HTTP_POST_VARS["searchkey"];

}

if($searchID=="")
{
?>

<!-- Site navigation menu -->
<ul class="navbar">
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
<FORM name="key_search" enctype="multipart/form-data" ACTION="upload_structure_file.php" METHOD=POST>

<table align="left" cellspacing="3" cellpadding="1" border="0"  width="90%">
<tr>
   <td>
       <INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" size=45 > 
       <INPUT TYPE="submit" value="  Upload  " onclick="return Upload();">
      <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
        <INPUT TYPE="hidden" name="MyUpload" value="NO"> 
   </td>
</tr>
<tr>
   <td><font color=#3735CA><br>Content of uploaded data file:</font><br>
          <textarea name="searchkey" rows="10" cols="100"><?PHP print($searchID)?> </textarea>
  </td>
</tr>
<tr>
    <td>
       <INPUT TYPE="submit" value="  Load example data  " onclick="return demo();">&nbsp&nbsp&nbsp
      <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
    </td>
</tr>

</table>
</FORM>
</div>
<?php

if($searchID!="")
{
  if ($UploadValue=="NO")
  {
      $fpdata = fopen($dir.$keyval."continuous_input_orig.txt","w");
      fwrite($fpdata,$searchID);
  }  
  shell_exec('./run_prep_input '.$keyval);
?>
<ul class="navbar2">
  <li><a href="net_structure.php?My_key=<?php print($keyval);?>">Upload structure file</a>
  <li><a href="javascript:void(0);"
NAME="InputCheck" title="InputCheck"
    onClick=window.open("input_check.php?My_key=<?php print($keyval);?>","Rat//ting","width=950,height=270,0,status=0,");>View uploaded variables and data</a>
</ul>
<ul class="navbar">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a> 
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<?php
}
?>

</body>
</html>


