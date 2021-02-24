<head>
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);
//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";

$param_file = $keyval."parameters.txt";
$param_ev_file = $keyval."parameters_ev.txt";

?>

</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="about_this_page.php#parameters">About this page</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div class='table_div' id='parameter_div'>
  <?php 
  $table_text = json_encode(file("file://".$dir.$param_file));
  ?>
  <script type="text/javascript">
  d3.text("", function(error, raw){
      var data1 = <?php echo $table_text;?>;
      var data2 = data1.join("");
      var data = data2.split("\n\n");
      var dsv = d3.dsvFormat("\t");
   var i;
   for (i = 0; i < data.length; i++) {
     var data_temp = data[i].split("\n");
     var caption_text = data_temp.shift();
     var data_temp = data_temp.join("\n");
     var newDiv = document.createElement('div');
     newDiv.id = 'table_div'+i;
     newDiv.className = 'd3_table';
     var dataCurrent = dsv.parse(data_temp);
     var div_id = '#table_div'+i;
     var paramDiv = document.getElementById("parameter_div");
     paramDiv.appendChild(newDiv);
     tabulate_caption(div_id,dataCurrent,caption_text);
   }
   if (error) throw error;
   add_header();
    });
</script>
<?php
if(file_exists($dir.$param_ev_file)) 
{
$table_text = json_encode(file("file://".$dir.$param_ev_file));
?>
  <script type="text/javascript">
  d3.text("", function(error, raw){
      var data1 = <?php echo $table_text;?>;
      var data2 = data1.join("");
      var data = data2.split("\n\n");
      var dsv = d3.dsvFormat("\t");
   var i;
   for (i = 1000; i < data.length + 1000; i++) {
     var data_temp = data[i-1000].split("\n");
     var caption_text = data_temp.shift();
     var data_temp = data_temp.join("\n");
     var newDiv = document.createElement('div');
     newDiv.id = 'table_div'+i;
     newDiv.className = 'd3_table';
     var dataCurrent = dsv.parse(data_temp);
     var div_id = '#table_div'+i;
     var paramDiv = document.getElementById("parameter_div");
     paramDiv.appendChild(newDiv);
     tabulate_caption(div_id,dataCurrent,caption_text);
   }
   if (error) throw error;
   add_header2();
    });
</script>
<?php
}
?>
</div>
<div id="table_header">
<h3 id="header">Original network parameters</h3>
</div>
<div id="download">
&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 target="_blank" href="<?php print("reroute.php?".$param_file);?>">Download original parameters</a><br><br>
<?php
if(file_exists($dir.$param_ev_file)) 
{?>
<h3> Network parameters considering evidence/intervention</h3>
<?php
}
?>
</div>
  <script type="text/javascript">
  function add_header() {
  var element = document.getElementById("table_header");
  var parentNode = document.getElementById("parameter_div");
  console.log(parentNode);
  parentNode.insertBefore(element,parentNode.firstChild);
  var element = document.getElementById("download");
  parentNode.appendChild(element);
}
</script>
<?php
if(file_exists($dir.$param_ev_file)) 
{?>
<div id="download2">
&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print("reroute.php?".$param_ev_file);?>">Download parameters considering evidence/intervention</a><br><br>
</div>
<?php
}
?>
  <script type="text/javascript">
  function add_header2() {
  var parentNode = document.getElementById("parameter_div");
  var element = document.getElementById("download2");
  parentNode.appendChild(element);
}
</script>

</body>
