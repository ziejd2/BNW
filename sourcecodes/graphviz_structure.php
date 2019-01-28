<script language="JavaScript">
<!--
function calcHeight()
{
  //find the height of the internal page
  var the_height=
    document.getElementById('the_iframe').contentWindow.
      document.body.scrollHeight;

  //change the height of the iframe
  document.getElementById('the_iframe').height=
      the_height;
}
//-->
</script>


<?php 
include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

/////////////////////////////////structure matrix//////////////////////////////////////////////////////////


$matfile="./data/".$keyval."structure_input.txt";
$fout=fopen("./data/".$keyval."graphviz.txt","w");

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
//fwrite($fouttemp,"$initialstring_temp");


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
//fwrite($fouttemp,"$endstring");
 
//shell_exec('/usr/bin/dot -Tpng -o /var/www/html/compbio/BNW/graphviz.jpg /var/www/html/compbio/BNW/graphviztemp.txt');

$file1="./data/".$keyval."run_initialstructure.sh";
$initiallines=file_get_contents("./data/temp_shell_file_initial_structure");
$all_lines="$initiallines"."$keyval\nfi\nexit";
$fp = fopen($file1,"w"); 
fwrite($fp, "$all_lines\n");
fclose($fp);
//prepare and execute shell script for matlab with a write lock
//$cmd="./runmat.sh $keyval";
//system($cmd);
shell_exec('./run_scripts/run_octave '.$keyval);
?>
<script>
window.open("layout.php?My_key=<?php print($keyval);?>",'_self',false);
</script>



