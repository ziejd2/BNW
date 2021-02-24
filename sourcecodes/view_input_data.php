<head>
<meta charset="utf-8">
<script src="./scripts/d3.v4.min.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");
if($_GET["My_key"]!="")
  $keyval=valid_keyval($_GET["My_key"]);
if($_POST["My_key"]!="")
  $keyval=valid_keyval($_POST["My_key"]);

$input_table_file="./data/".$keyval."input_table.txt";
$input_data_file="./data/".$keyval."continuous_input_orig.txt";


?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!--[if !IE]><!-->
  <style>
  * { 
margin: 0; 
padding: 0; 
}
/* 
   Generic Styling, for Desktops/Laptops 
*/
table { 
width: 100%; 
  border-collapse: collapse; 
}
/* Zebra striping */
tr:nth-of-type(odd) { 
background: LightGrey; 
}
th { 
background: #3071a9; 
color: white; 
  font-size: 12pt; 
}
td, th { 
padding: 6px; 
border: 1px solid #ccc; 
    text-align: left; 
}

th.des:after {
content: "\21E9";
}
    
th.aes:after {
content: "\21E7";
}

/* 
   Max width before this PARTICULAR table gets nasty
   This query will take effect for any screen smaller than 760px
   and also iPads specifically.
*/
@media 
only screen and (max-width: 760px),
  (min-device-width: 768px) and (max-device-width: 1024px)  {
  
  /* Force table to not be like tables anymore */
  table, thead, tbody, th, td, tr { 
  display: block; 
  }
  
  /* Hide table headers (but not display: none;, for accessibility) */
  thead tr { 
  position: absolute;
  top: -9999px;
  left: -9999px;
  }
  
  tr { border: 1px solid #ccc; }
      
      td { 
      /* Behave  like a "row" */
    border: none;
      border-bottom: 1px solid #eee; 
	position: relative;
      padding-left: 50%; 
    }
    
  td:before { 
      /* Now like a table header */
    position: absolute;
      /* Top/left values mimic padding */
    top: 6px;
    left: 6px;
    width: 45%; 
      padding-right: 10px; 
      white-space: nowrap;
    }
    
    /*
      Label the data
    */
  td:before {
    content: attr(data-th) ": ";
      font-weight: bold;
    width: 6.5em;
    display: inline-block;
    }
  }
  
  /* Smartphones (portrait and landscape) ----------- */
  @media only screen
    and (min-device-width : 320px)
    and (max-device-width : 480px) {
    body { 
    padding: 0; 
    margin: 0; 
    width: 320px; }
  }
  
  /* iPads (portrait and landscape) ----------- */
  @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
    body { 
    width: 495px; 
    }
  }
  
  </style>
      <!--<![endif]-->

</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div id="violin_div">

  <h1>D3.js Sortable & Responsive Table</h1>
    
  <p>Click the table header to sort data according to that column</p>

  </div>
  <script type="text/javascript">
  d3.text("<?php print($input_table_file);?>", function(error, raw){
  var dsv = d3.dsvFormat("\t")
  var data = dsv.parse(raw)
    var caption_text = data.pop();
  console.log(caption_text.Variable);
      if (error) throw error;
      var sortAscending = true;
      var table = d3.select('#violin_div').append('table');
      var caption = table.append("caption").text(caption_text.Variable);
      var titles = d3.keys(data[0]);
      var headers = table.append('thead').append('tr')
	.selectAll('th')
	.data(titles).enter()
	.append('th')
	.text(function (d) {
	    return d;
	  })
	.on('click', function (d) {
	    headers.attr('class', 'header');
	       
	    if (sortAscending) {
	      rows.sort(function(a, b) { return b[d] < a[d]; });
	      sortAscending = false;
	      this.className = 'aes';
	    } else {
	      rows.sort(function(a, b) { return b[d] > a[d]; });
	      sortAscending = true;
	      this.className = 'des';
	    }
	       
	  });
        
      var rows = table.append('tbody').selectAll('tr')
	.data(data).enter()
	.append('tr');
      rows.selectAll('td')
	.data(function (d) {
	    return titles.map(function (k) {
		return { 'value': d[k], 'name': k};
	      });
	  }).enter()
	.append('td')
	.attr('data-th', function (d) {
	    return d.name;
	  })
	.text(function (d) {
	    return d.value;
	  });
    });
 

</script>
</div>
</body>


