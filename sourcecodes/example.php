<?php

$oldkeyval=$_GET["My_key"];

$type_n=array();

$type_n=explode("|",$oldkeyval);


$example=trim($type_n[0]);   
$oldkeyval=trim($type_n[1]);   

/////////////Generate a random key/////////////////////
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;
shell_exec('./examplefilecopy.sh '.$example.' '.$oldkeyval.' '.$keyval);

?>
<script>
window.open("layout_example.php?My_key=<?php print($keyval);?>",'_self',false);
</script>



