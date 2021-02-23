<head>
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");

?>

</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="bn_file_load_gom.php">Load data and begin modeling</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div id="outernew">
  <h3>Sample input data file</h3>
  <br>A sample input file is shown below. A text version can be viewed or downloaded <a href="./data/sample_input.txt">here</a>.<br><br>
  The first row contains the variable names. Each remaining row is a case or sample.<br><br>
  The first column is a discrete genotype variable which has 2 states (A and B). The remaining columns contain 4 continuous variables.<br><br>
  Two cases have unknown values (Genotype and Trait2 for rows 3 and 4, respectively). These are represented with "NA" in the input file. Rows containing "NA"
for any variable are ignored by BNW.<br><br>
  <div class="d3_table" id="table_div1">
  <script type="text/javascript">
  d3.text("./data/sample_input.txt", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data = dsv.parse(raw)
  var caption_text = "Sample Input File";
  if (error) throw error;
      tabulate_caption("#table_div1",data,caption_text);
    });
</script>
</div>
<br>
<br>
<br>
</div>
</body>


