
<?php 
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

//////////////execute c codes for local score///////////////////
$dir="./data/";

$pfile="./data/".$keyval."parent.txt";
$parentf=file_get_contents("$pfile");
//$maxp=valid_input($parentf);
$maxp=trim($parentf);


$kfile="./data/".$keyval."k.txt";
$kf=file_get_contents("$kfile");
//$maxk=valid_input($kf);
$maxk=trim($kf);



$thrfile="./data/".$keyval."thr.txt";
$thr=file_get_contents("$thrfile");
//$thr=valid_input($thr);
$thr=trim($thr);


//$cmd="./run.sh $keyval $maxp $maxk";
//system($cmd);
shell_exec('./run.sh '.$keyval.' '.$maxp.' '.$maxk.' '.$thr);

/////////////////////////////////structure matrix//////////////////////////////////////////////////////////

$matfile=$dir.$keyval."structure_input.txt";

$fout=fopen($dir.$keyval."graphviz.txt","w");

$matrix1=file_get_contents("$matfile");           
$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$dataname=array();
$dataname=explode("\t",$str_arrmat[0]);
$n=count($dataname);

$initialstring="digraph G {\n"."size=\"10,10\";  ratio = fill;\n"."node [shape=square,width=1.5];\n";
$endstring="}";
fwrite($fout,"$initialstring");

$g_file_name="./data/".$keyval."grviz_name_file.txt";
$grviz_name_file=fopen($g_file_name,"w");

for($i=0;$i<$n;$i++)
{
      $row_name=trim($dataname[$i]);  
      fwrite($grviz_name_file,"$row_name\n");
}

for($i=1;$i<=$n;$i++)
{
         $ii=$i-1; 
         $row_name=trim($dataname[$ii]);  

         $col_arr=explode("\t",$str_arrmat[$i]);
        
        
	   for($j=0;$j<$n;$j++)
	   {
               $col_val=trim($col_arr[$j]);
               if($col_val==1)
               { 
                  $col_name=trim($dataname[$j]);
                  fprintf($fout,"%s -> %s;\n",$ii,$j);  
                  //fprintf($fouttemp,"%s -> %s;\n",$row_name,$col_name);
                 
	        }
          }
          
     
} 
fwrite($fout,"$endstring");   
fclose($fout);


shell_exec('./run_octave '.$keyval);

?>
<script>
window.open("layout.php?My_key=<?php print($keyval);?>",'_self',false);
</script>

