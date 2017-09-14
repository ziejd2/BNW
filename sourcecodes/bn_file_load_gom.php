<?php
////////////////This code load a data file and creates input file for continuous global optimal search (our modification). execute_bn_gom.php link is for execution of structure learning and create_tiers_gom.php is for adding restrictions///////////////////////////////////

include("header_new.inc");
include("header_batchsearch.inc");
include("runtime_check.php");
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


////////////////////max parent/////////////////////////


$type_n=array();

//Get number of parent data and key value for changes in number of parent
$type_n=explode("|",$_POST["nm_parent"]);
if($keyval=="")
   $keyval=$type_n[0];

$sid=$keyval."continuous_input";
$dir="./data/";

$TextinFileFinal=$dir.$sid.".txt";
$TextinFile=$dir.$sid."_temp.txt";
$TextinFilenamelist=$dir.$keyval."name.txt";

//print default number of parents
$parent_number=4;
$pfile=$dir.$keyval."parent.txt";
$parentf=fopen($pfile,"w");
fwrite($parentf,"$parent_number\n");

//print default number of k for model averaging
$k_number=1;
$kfile=$dir.$keyval."k.txt";
$kf=fopen($kfile,"w");
fwrite($kf,"$k_number\n");

//print default threshold for model averaging
$thrfile=$dir.$keyval."thr.txt";
$kf=fopen($thrfile,"w");
fwrite($kf,"0.5\n");

if(isset($HTTP_POST_VARS["searchkey"]))
{
   $searchID=$HTTP_POST_VARS["searchkey"];

}

if($searchID!="")
{
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="executionprogress.php?My_key=<?php print($keyval);?>">Perform Bayesian network modeling using default settings</a>
  <li><a href="create_tiers_gom.php?My_key=<?php print($keyval);?>">Go to structure learning settings and the BNW structural constraint interface</a>  
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
		  unlink($TextinFile);

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
          <textarea name="searchkey" rows="10" cols="100"><?PHP print($searchID)?> </textarea>
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
//////////////////Format data for Structure learning "C" code////////

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

///////////////////Write data to files/////////////////
$fptype = fopen($dir.$keyval."type.txt","w");
$fpnode= fopen($dir.$keyval."nnode.txt","w");
fwrite($fpnode,"$lcc\n");
$fprows = fopen($dir.$keyval."nrows.txt","w");
fwrite($fprows,"$lc\n");
$flevel = fopen($dir.$keyval."nlevels.txt","w");

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

////////////////empty ban and white list/////////////////////////////

$sid1=$keyval."ban";
$sid2=$keyval."white";
$wdir="./data/"; 

$Textban=$wdir.$sid1.".txt";
$Textwhite=$wdir.$sid2.".txt";

$fpb = fopen($Textban,"w");
$fpw = fopen($Textwhite,"w");

$datpost="From\tTo\n";

fwrite($fpb,"$datpost");
fwrite($fpw,"$datpost");
if($searchID!="")
{

$runtime=exe_time($keyval,$parent_number,$k_number);
?>
<div id="outernew">
<p><h3><?php 
print("Estimated run time for current dataset using default settings: $runtime seconds");
?>
<br><br></h3>
</p>
<br>
</div>
<?php
}
?>

</body>
</html>


