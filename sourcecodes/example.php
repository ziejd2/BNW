<?php
include("input_validate.php");
$oldkeyval=$_GET["My_key"];
$dir="/tmp/bnw/";


$type_n=array();

$type_n=explode("|",$oldkeyval);


$example=valid_input(trim($type_n[0]));   
$oldkeyval=valid_keyval(trim($type_n[1]));   


/////////////Generate a random key/////////////////////
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;
shell_exec('./examplefilecopy.sh '.$example.' '.$oldkeyval.' '.$keyval);


shell_exec('./run_scripts/run_prep_input '.$keyval);


/////////////////////////////////structure matrix////////////////////////////

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

$initialstring="digraph G {\n"."size=\"6,8\";\n"."node [shape=square,width=1.5];\n";
$endstring="}";
fwrite($fout,"$initialstring");

$g_file_name=$dir.$keyval."grviz_name_file.txt";
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


shell_exec('./run_scripts/run_octave '.$keyval);




?>
<script>
 //window.open("layout_example.php?My_key=<?php print($keyval);?>",'_self',false);
 window.open("layout_svg_no.php?My_key=<?php print($keyval);?>",'_self',false);
</script>



