<?php
include("header_new.inc");
include("header_batchsearch.inc");
include("input_validate.php");
////////////////continuous///////////////
$searchID="";
$UploadValue="NO";
$TextFile=$HTTP_POST_FILES["MyFile"]["name"];

if($_POST["My_key"]!="")
  $keyval=$_POST["My_key"];

if($_GET["My_key"]!="")
  $keyval=$_GET["My_key"];

$keyval=valid_keyval($keyval);

$sid="structure_input";
$dir="./data/$keyval";

$TextinFileFinal=$dir.$sid.".txt";

$TextinFile=$dir.$sid."_temp.txt";

$TextinFilenamelist=$dir."name.txt";

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
                 echo "<script type='text/javascript'> window.alert ('Sorry, error uploading $TextFile.')</script>";
                 flush();
                 exit();
            }
            else
            {
                 $searchID=file_get_contents("$TextinFile");
		 //fclose($fh);
		  unlink($TextinFile);

            }

        }

       else
       {
           echo "<script type='text/javascript'> window.alert ('Sorry, please select upload file.')</script>";
       }
   }
}

if($searchID!="")
{
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="graphviz_structure.php?My_key=<?php print($keyval);?>">Display structure</a>
</ul>
<ul class="navbar">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a>
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<?php
}
else
{

?>
<!-- Site navigation menu -->
<ul class="navbar">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a>
  <li><a href="help.php" target='_blank'>Help</a>
  <li><a href="home.php">Home</a>
</ul>
<?php
}
?>

<div id="outernew">

<h2>Upload network structure</h2>
<FORM name="key_search" enctype="multipart/form-data" ACTION="net_structure.php" METHOD=POST>

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
   <td><font color=#3735CA><br>Content of uploaded structure file:</font><br>
          <textarea name="searchkey" rows="10" cols="100"><?PHP print($searchID)?> </textarea>
  </td>
</tr>
<tr>
    <td>
       <INPUT TYPE="submit" value="  Load example structure  " onclick="return demo_str();">&nbsp&nbsp&nbsp
      <INPUT TYPE="hidden" NAME="My_key" value=<?php print($keyval) ?> >
    </td>
</tr>

</table>
</FORM>

</div>
<?php
  $str_arr=array();
  $searchID=trim($searchID);
  $str_arr=explode("\n",$searchID);
  $data=array();
    
  $fpmain = fopen($TextinFileFinal,"w");
  
  foreach($str_arr as $line)
  {
       $line=trim($line);
	fwrite($fpmain, "$line\n");
      
  }


?>
