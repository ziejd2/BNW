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
	  $TextFile = valid_input($TextFile);
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
<h2><font color=#33339f>Upload data file for structure learning search</font></h2>
<FORM name="key_search" enctype="multipart/form-data" ACTION="bn_file_load_gom.php" METHOD=POST>

<table align="left" cellspacing="3" cellpadding="1" border="0"  width="60%">
<tr>
<td>
<INPUT style="background-color:#FFFFFF;color:#0000FF" type="file" name="MyFile" size=45 > 
<INPUT TYPE="submit" value="  Upload  " onclick="return Upload();">
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
<INPUT TYPE="hidden" name="MyUpload" value="NO"> 
</td>
</tr>
<tr>
<td align="left"><font color=#33339f><br>Content of uploaded data file:</font><br>
          <textarea name="searchkey" rows="8" cols="100"><?PHP print($searchID)?> </textarea>
</td>
</tr>
<tr>
<td>
<INPUT TYPE="submit" value="  Load example data of immune responses to infection with Chlamydia psittaci  " onclick="return demochl();">&nbsp&nbsp&nbsp
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
</td>
</tr>
<tr>
<td>
<INPUT TYPE="submit" value="  Load example data of a synthetic genetic network with 2 genotypes and 6 traits  " onclick="return demo8nodes();">&nbsp&nbsp&nbsp
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
</td>
</tr>
<tr>
<td>
<INPUT TYPE="submit" value="  Load example data of immune-related gene in the spleens of BXD mice  " onclick="return demospnl();">&nbsp&nbsp&nbsp
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
</td>
</tr>
<tr>
<td>
<INPUT TYPE="submit" value="  Load the ksl dataset from deal  " onclick="return demoksl();">&nbsp&nbsp&nbsp
<INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
</td>
</tr>
<tr>
<td>
<INPUT TYPE="submit" value="  Load the rat dataset from deal  " onclick="return demorat();">&nbsp&nbsp&nbsp
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
  $keyval = valid_keyval($keyval);
  shell_exec('./run_prep_input '.$keyval);
  $parent_number=4;
  $k_number=1;
  $runtime=exe_time($keyval,$parent_number,$k_number);
?>
<ul class="navbar2">
  <li><a href="executionprogress.php?My_key=<?php print($keyval);?>">Perform Bayesian network modeling using default settings</a>
  <li><a href="create_tiers_gom.php?My_key=<?php print($keyval);?>">Go to structure learning settings and the BNW structural constraint interface</a>  
  <li><a href="javascript:void(0);"
NAME="InputCheck" title="InputCheck"
    onClick=window.open("input_check.php?My_key=<?php print($keyval);?>","Rat//ting","width=950,height=270,0,status=0,");>View uploaded variables and data</a>
</ul>
<ul class="navbar">
<li><a href="help.php" target="_blank">Help</a>
<li><a href="home.php">Home</a>
</ul>
<div id="outernew">
<p><h3><?php 
print("Estimated run time for current dataset using default settings: $runtime seconds");
?>
<br><br><br><br></h3>
</p>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
<?php
}
?>

</body>
</html>


