<head>
<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>

<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="0">

  <title>Bayesian Network Webserver</title>
  <link rel="stylesheet" href="my_new_style.css">

</head>

<body>
<script type="text/javascript">
   document.body.innerHTML = '';
</script>
<table align=center width="100%" bordercolor=#3071a9 border=1 cellspacing=0 cellpadding=0>
<tr>
   <td width="100%" height=60 class="td_title_home">&nbsp;&nbsp;Bayesian Network Webserver for Biological Network Modeling</td>
</tr>
</table>



</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="bn_file_load_gom.php">Load data and begin modeling</a>
</ul>
   <button class="accordion">Return to a network&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
  <div class="panel">
  <ul class="navbar_ac">
  <li><a href="enter_netID.php">Use a network ID to return to a network</a>
  <li><a href="home_upload.php">Load data for a known structure</a>  
  </ul>
  </div>  
   <button class="accordion">New to BNW?&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
  <div class="panel">
  <ul class="navbar_ac">
  <li><a href="getting_started.php">Getting started</a>
  <li><a href="workflow.php">Tutorials and examples</a>
  <li><a href="help.php">Main help page</a>
  <li><a href="faq.php">FAQ</a>
  </ul>
  </div>  
<ul class="navbar2">
  <li><a href="../downloads/BNW_src_files.tar">Download structure learning package</a>
</ul>


</ul>



<!-- Main content -->
<div id="outer_home">
<br>
    <p align="justify"> The Bayesian Network Web Server (BNW) is a comprehensive web server for Bayesian network modeling of biological data sets. It is designed so that users can quickly and seamlessly upload a dataset, learn the structure of the network model that best explains the data, and use the model to understand and make predictions about relationships between the variables in the model. Many real world data sets, including those used to create genetic network models, contain both discrete (e.g., genotypes) and  continuous (e.g., gene expression traits) variables, and BNW allows for modeling of these hybrid data sets.
<br><br>
<b>    <p align="justify"> BNW has recently been updated. Read more about these updates <a href="help.php#updates">here.</a>
<br><br>
<img class="wide" SRC="BNW_overview_new.PNG" ATL="" BORDER=0 WIDTH=1197 HEIGHT=487><BR>
<br>
<b>
How to cite BNW:
</b>
<br>
1. Ziebarth JD, Bhattacharya A, Cui Y (2013) <a href="http://bioinformatics.oxfordjournals.org/content/29/21/2801.abstract"  target="_blank">Bayesian Network Webserver: a comprehensive tool for biological network modeling.</a> Bioinformatics. 29(21): 2801-2803.
<br>
<br>
   2. Ziebarth JD, Cui Y (2017) <a href="https://link.springer.com/protocol/10.1007/978-1-4939-6427-7_15" target="_blank">Precise network modeling of system genetics data using the Bayesian Network Webserver.</a> In: Schughart K, Williams R (eds) System Genetics. Methods in Molecular Biology, vol 1488. Humana Press, New York, NY.
<br>
<br>
<b>Developed and maintained by: </b><a href="http://compbio.uthsc.edu/" target="_blank">Yan Cui's Lab at University of Tennessee Health Science Center</a>
<br>
</p>
<br>
<br>
<br>
</div>
</body>
</html>

<script>
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera",
			versionSearch: "Version"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod/iPad"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();
if (BrowserDetect.browser=="Explorer")
 {
        alert("Some features of BNW, particularly the structural constraint interface, are not supported by Internet Explorer. We suggest using other browser, such as Firefox or Chorome, when accessing BNW");
 
 }
if (BrowserDetect.dataOS=="iPhone")
 {
        alert("Some features of BNW are not supported by mini devices. We suggest using computer, when accessing BNW");
 
 }

</script>

