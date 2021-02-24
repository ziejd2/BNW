<?php
include("input_validate.php");

//Get data sent from cytoscape file
//$json = $_POST['json'];
$old_key = $_POST['old_key'];
$nnodes =  $_POST['nnodes'];
$node_labs =  $_POST['node_labs'];
$nedges =  $_POST['nedges'];
$sources =  $_POST['sources'];
$targets =  $_POST['targets'];
$weights =  $_POST['weights'];

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


//$filename = "./data/".$old_key."modify_edge.txt";
//$filename = "/tmp/bnw/".$old_key."modify_edge.txt";
$filename = "/var/lib/genenet/bnw/".$old_key."modify_edge.txt";

$file = fopen($filename,'w');
//fwrite($file,$json);
fwrite($file,$nnodes);
fwrite($file,"\n");
fwrite($file,$node_labs);
fwrite($file,"\n");
fwrite($file,$nedges);
fwrite($file,"\n");
fwrite($file,$sources);
fwrite($file,"\n");
fwrite($file,$targets);
fwrite($file,"\n");
fwrite($file,$weights);
fwrite($file,"\n");
fclose($file);

shell_exec('./run_scripts/run_mod_edges '.$old_key.' '.$keyval);


$new_page = "graphviz_structure.php?My_key=".$keyval;
echo $new_page;
//echo "<script type='text/javascript'>location.href=".$new_page."</script>;";



?>
