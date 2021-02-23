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


//$matfile="./data/".$keyval."structure_input.txt";
//$fout=fopen("./data/".$keyval."graphviz.txt","w");
$matfile="/tmp/bnw/".$keyval."structure_input.txt";
$fout=fopen("/tmp/bnw/".$keyval."graphviz.txt","w");

$matrix1=file_get_contents("$matfile");           
$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);
$datamat=array();
$data_cell=array(); 

$dataname=array();
$dataname=explode("\t",$str_arrmat[0]);
$n=count($dataname);

$initialstring="digraph G {\n"."size=\"6,8\";\n"."node [shape=square,width=1.5];\n";
$endstring="}";
fwrite($fout,"$initialstring");
//fwrite($fouttemp,"$initialstring_temp");


//$g_file_name="./data/".$keyval."grviz_name_file.txt";
$g_file_name="/tmp/bnw/".$keyval."grviz_name_file.txt";
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

//$file1="./data/".$keyval."run_initialstructure.sh";
//$initiallines=file_get_contents("./data/temp_shell_file_initial_structure");
$file1="/tmp/bnw/".$keyval."run_initialstructure.sh";
$initiallines=file_get_contents("/tmp/bnw/temp_shell_file_initial_structure");
$all_lines="$initiallines"."$keyval\nfi\nexit";
$fp = fopen($file1,"w"); 
fwrite($fp, "$all_lines\n");
fclose($fp);
//prepare and execute shell script for matlab with a write lock
//$cmd="./runmat.sh $keyval";
//system($cmd);
//$str_temp="./data/".$keyval."structure_input_temp.txt";
//$str_temp="/tmp/bnw/".$keyval."structure_input_temp.txt";
//if (file_exists($str_temp)) {
  shell_exec('./run_scripts/run_octave '.$keyval);
//} else {
  //  shell_exec('cp ./data/'.$keyval.'structure_input.txt ./data/'.$keyval.'structure_input_temp.txt');
//  shell_exec('cp /tmp/bnw/'.$keyval.'structure_input.txt /tmp/bnw/'.$keyval.'structure_input_temp.txt');
//  shell_exec('./run_scripts/run_octave '.$keyval);
//}
?>
<script>
window.open("layout_svg_no.php?My_key=<?php print($keyval);?>",'_self',false);
</script>



