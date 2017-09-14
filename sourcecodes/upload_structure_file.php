<?php
include("header_new.inc");
include("header_batchsearch.inc");

////////////////continuous///////////////
$searchID="";
$UploadValue="NO";
$TextFile=$HTTP_POST_FILES["MyFile"]["name"];

/////////////Generate a key value for current use///////////////////////////////////////////////
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;


if($_POST["My_key"]!="")
  $keyval=$_POST["My_key"];


$sid="continuous_input";
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
  <li><a href="net_structure.php?My_key=<?php print($keyval);?>">Upload structure file</a>
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
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>
<?php
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
       <INPUT font-weight:bold" TYPE="submit" value="  Load example data  " onclick="return demo();">&nbsp&nbsp&nbsp
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
 
   
  $fph = fopen($TextinFilenamelist,"w");
  $fpmain = fopen($TextinFileFinal,"w");
  
 $cont_arr=array();
  $cont_arr_tmp=array();


  $data_type=array();
  $level_arr=array();

//Who is discrete? Who is continuous?
  $i=0;
  $lc=0;
  foreach($str_arr as $line)
  {
    $line=trim($line); 
    if($lc==0)
      {
       fwrite($fph, "$line\n");
	fprintf($fpmain, "$line\n");
       $name_t=$line;

	fclose($fph); 
       $data=explode("\t",$line);
	$j=0;
       foreach($data as $d_c)
	  {
           $d_c=trim($d_c);
           $level_arr[$j][0]=$d_c;  //variable name y for 
            
	    $data_type[$j]=0;
            $j++;
          }
          $lcc=$j;  //$lcc=number of column in the input file 
      }        
    else
      { 
        
        $strline=""; 
        $data=explode("\t",$line);
	 $j=0;
        foreach($data as $d_c) 
        {
            $d_c=trim($d_c);
	     $cont_arr[$i][$j]=$d_c; 
            $cont_arr_tmp[$i][$j]=$d_c; 

            $mystring = $d_c;
	     $findme   = '.';
	     $pos = strpos($mystring, $findme);
            $strline.="1\t";
  
            if ($pos != false)
            {
                $data_type[$j]=1;
            }
            $j++;
         }
	 $i++; 
    
      }
     $lc++;
     
  }
$lc--; //$lc=number of rows in the input file
//Now count number of labels for discrete variables and populate the $data_type[$j] 
$data_count=0;
$l_type="";
for($j=0;$j<$lcc;$j++)
{

       if($data_type[$j]!=1)
	{
	  $data_count=0;
	  for($i=0;$i<$lc;$i++)
	  {
              $vi=$i+1;
	       for($ii=$vi;$ii<$lc;$ii++)
	       {
                 if($cont_arr[$i][$j]==$cont_arr[$ii][$j] && $cont_arr[$i][$j]!=-9999)
		   {
		       $cont_arr[$ii][$j]=-9999; 
               
		   } 
	       }
               
              
         }
	  for($i=0;$i<$lc;$i++)
	     {
	       if($cont_arr[$i][$j]!=-9999 && $cont_arr[$i][$j]!="")
		{
                  // $data_count_l=$data_count+2;
                  $data_count++;
                  $level_arr[$j][$data_count]=$cont_arr[$i][$j];
              }
	     }
	  $data_type[$j]=$data_count;
	    
	   
	}
       if($j==0)
         $l_type.=$data_type[$j];
       else
	 $l_type.="\t".$data_type[$j];  
      
    }

////////////////////////////////////
$fptype = fopen($dir."type.txt","w");
$fpnode= fopen($dir."nnode.txt","w");
fwrite($fpnode,"$lcc\n");

$flevel = fopen($dir."nlevels.txt","w");
//print in level mapping file for discrete variables
for($j=0;$j<$lcc;$j++)
{
  if($data_type[$j]>1)
  {  
      fprintf($flevel,"%s",$level_arr[$j][0]);
      $m=$data_type[$j];
      for($jj=1;$jj<=$m;$jj++)
      {
         fprintf($flevel,"\t%s",$level_arr[$j][$jj]);
      }

      fprintf($flevel,"\n");  
  }   
}

fwrite($fptype,"$name_t\n");
for($j=0;$j<$lcc;$j++)
{
  fprintf($fptype,"$data_type[$j]\t");

}
fwrite($fptype,"\n");
////////////////////////////////////

  
fwrite($fpmain, "$l_type\n");
$l_c=0;
//perform discrete level replacement 
for($m=0;$m<$lcc;$m++)  //column
{
  if($data_type[$m]>1)
  {
     $r=$data_type[$m];
     for($j=1;$j<=$r;$j++)
     {  
         $search=$level_arr[$m][$j];
         $replace=$j."#";
         for($n=0;$n<$lc;$n++)  //row
         { 
           $val=$cont_arr_tmp[$n][$m];
           if($val==$search)
               $cont_arr_tmp[$n][$m]=$replace;  //alter levels 
         }
     }
     for($n=0;$n<$lc;$n++)  //row
          $cont_arr_tmp[$n][$m]=str_replace('#','',$cont_arr_tmp[$n][$m]);
  }
}

//write data to output file
for($j=0;$j<$lc;$j++)
{
  fprintf($fpmain,"%s",$cont_arr_tmp[$j][0]);
  for($jj=1;$jj<$lcc;$jj++)
    fprintf($fpmain,"\t%s",$cont_arr_tmp[$j][$jj]);
  fprintf($fpmain, "\n");
}

fclose($fpmain);



?>


