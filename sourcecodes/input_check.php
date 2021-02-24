<head>
<meta charset="utf-8">
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);

$dir = "/var/lib/genenet/bnw/";

$input_table_file=$keyval."input_table.txt";
$input_data_file=$keyval."continuous_input_orig.txt";


?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
 <li><a href="about_this_page.php#input_check">About this page</a>
 <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div class="table_div">
  <p class=basic_header>Description of network variables:</p>
  <div class="d3_table" id="table_div1">
  <?php
   $table_text = json_encode(file("file://".$dir.$input_table_file)); 
  ?> 
<script type="text/javascript">
  d3.text("", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data1 = <?php echo $table_text?>;
  var data2 = data1.join("")
  var data = dsv.parse(data2)
  var caption_text = data.pop();
  if (error) throw error;
      tabulate_caption("#table_div1",data,caption_text.Variable);
    });
</script>
</div>
<br>
  <div class="d3_table" id="table_div2">

  <?php
   $table_text = json_encode(file("file://".$dir.$input_data_file)); 
  ?>
  <script type="text/javascript">

  d3.text("", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data1 = <?php echo $table_text?>;
  var data2 = data1.join("")
  var data = dsv.parse(data2)
  var caption_text = "Input data file:";
      if (error) throw error;
      tabulate_caption("#table_div2",data,caption_text);
    });

</script>
 </div>



&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print("reroute.php?".$input_data_file);?>">Download original input file<br></a>
<br>
<br><br>
</div>
</body>

