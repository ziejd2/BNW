<?php
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);
shell_exec('./delete_cv_pred.sh '.$keyval);

?>
<script>
window.open("cv_predictions.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
