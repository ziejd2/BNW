<?php
include("input_validate.php");
$oldkeyval=valid_keyval($_GET["My_key"]);
//if (preg_match('/[^A-Za-z]/',$oldkeyval)) {
//  echo "Key value must contain only uppercase or lowercase letters.";
//  $oldkeyval='';
//}

/////////////Generate a random key/////////////////////
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;
$keyval=valid_keyval($keyval);
shell_exec('./filecopy.sh '.$oldkeyval.' '.$keyval);

?>
<script>
window.open("layout_example.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
