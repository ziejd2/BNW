<head>
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);
$dir="./data/";
//$dir="/tmp/bnw/";

$score_file = $dir.$keyval."structure_input_temp.txt";
$mat_file = $dir.$keyval."structure_input.txt";

?>

</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></a></li>
  <li><a href="about_this_page.php#matrix">About this page</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div class="table_div">
  <div class="d3_table" id="table_div1">
  <script type="text/javascript">
  d3.text("<?php print($mat_file);?>", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data = dsv.parse(raw)
  var caption_text = "Structure matrix";
  if (error) throw error;
      tabulate_header_row("#table_div1",data,caption_text);
    });
</script>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print($mat_file);?>">Download structure matrix</a>
<br>
<br>
<?php
$filename1="/tmp/bnw/".$keyval."structure_input_temp.txt";
if(file_exists($filename1))
      {
?>
  <div class="d3_table" id="table_div2">

  <script type="text/javascript">

  d3.text("<?php print($score_file);?>", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data = dsv.parse(raw)
  var caption_text = "Model averaging scores";
      if (error) throw error;
      tabulate_header_row("#table_div2",data,caption_text);
    });

</script>
 </div>

&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print($score_file);?>">Download model averaging scores<br></a>
<br>
<?php
      }
?>
<br>
<br>
<br>
<br>
<br>
</div>
</body>


