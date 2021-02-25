<?php 

///////This code will allow users to group variables in tier. getcombineDescription() function combined all data and take you to "tier_description_processing_gom.php" for preparation of ban and whitelist //////////

include("header_new.inc");
include("runtime_check.php");
include("input_validate.php");
$keyval=$_GET["My_key"];

//$dir="./data/";
$dir="/tmp/bnw/";

$type_n=array();

//Get number of tier data and key value for changes in number of tier

if(isset($_POST["nm_tier"]))
{
   $type_n=explode("|",$_POST["nm_tier"]);
}

$tier_number=trim($type_n[0]);   


if($keyval=="")
  $keyval=$type_n[1];

if($tier_number=="")
{
  $tier_number=3;
}



$nf=$dir.$keyval."nnode.txt";
$node=trim(file_get_contents("$nf"));
$maxplist=$node-1;


//////////////////Check execution time//////////////////////////////////////////////
$keyval=valid_keyval($keyval);
?>
<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="input_check.php?My_key=<?php print($keyval);?>" target="_blank">View uploaded variables and data</a>
  <li><a href="help.php#constraint_interface" target="_blank">About this page</a>
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<div id="outernew_sci">
<br>
<p onClick="getcombineDescription(ntiers,'ban_from','ban_to','white_from','white_to','<?php print($keyval);?>')"><a class=button3 href="javascript:void(0)" >Click here to perform Bayesian network modeling after creating tiers</a></p>
<br>
<table align="center">
<tr>
<td align="left">
<h3>1. Set number of tiers:</h3>
</td>
<td align="left"> 
     <form method="post" action="create_tiers_gom_part2.php" name="form">
      <SELECT style='font-size:16px;' NAME="nm_tier"  onchange="form.submit();">
      <?php for($i=2;$i<10;$i++)
      {
        ?>
           <option value=<?php $combined=$i."|".$keyval; print($combined)?> <?php if($tier_number == $i) {print "selected";}?>><?php print($i)?></option>
     <?php
     }
   
     ?>  
      </SELECT>
   </form>
</td>
</tr>
</table>
</br>


<?php


if(isset($_POST["bantext"]))
{
   $ban_search=$_POST["searchkey"];
}

if(isset($_POST["whitetext"]))
{
   $white_search=$_POST["searchkey"];
}

?>

<!DOCTYPE html> 
<html> 
<head> 
<title>Drag and Drop test</title> 
<style type="text/css"> 
	#nodelist{
		width:200px;
		font-weight:bold;
                border: 2px solid;
		}
        #tiers{
                position:absolute;
                left:215px;
                white-space: nowrap; 
                min-width: 2200px;
                float:top;
                }

        #int_box1{
                white-space: nowrap;
                min-width: 2200px;
                }
        #int_box2{
                white-space: nowrap;
                min-width: 2200px;
                }
        #int_box3{
                white-space: nowrap;
                min-width: 2200px;
                }
        #int_box4{
                white-space: nowrap;
                min-width: 2200px;
                }

        #outer_tier_desc{
                margin-top: 100px;
                }

        #outer_tier_desc1{
                width:2200px;
                margin-top: 20px;
                }

	#outer_box_lists{
                margin-top: 100px;
		}
	#nodelist2{
		width:200px;
		font-weight:bold;
                border: 2px solid;
                float:left;
		}
	#ban_outer {
                position:absolute;
                left: 215px;
                width:449px;
		border: 2px solid;
                background-color:#C0C0C0;
		font-weight:bold;
                margin-left:15px;
	}
        #ban_from {
                width:200px;
                border:2px solid;
                margin-left:15px;
        }
        #ban_to {
                width:200px;
                border:2px solid;
                margin-left:15px;
        }
	#white_outer {
                position:absolute;
                left:700px;
                width:449px;
		border: 2px solid;
                background-color:#C0C0C0;
		font-weight:bold;
	}
        #white_from {
                width:200px;
                border:2px solid;
                margin-left:15px;
        }
        #white_to {
                width:200px;
                border:2px solid;
                margin-left:15px;
        }


	.tier {
		width:200px;
		border: 2px solid;
		font-weight:bold;
                float:left;
                background-color:#FFFFFF;
                margin-left:5px;
	        display: inline-block;
	}
	.int {
		border: 2px solid;
		font-weight:bold;
                float:left;
                background-color:#FFFFFF;
                margin-left:0px;
	        display: inline-block;
	}
	.int1 {
		border: 2px solid;
                background-color:#FFFFFF;
	        clear: left;
	        float:left;
                margin-left:0px;
	        display: inline-block;
	}
	.node1 {
		width:150px;
		height:30px;
		float:left;	
		margin-left:10px;
		margin-top:10px;
		border: 2px dashed;
                background-color:LightGrey;
		text-align:center;
	}
	.node2 {
		width:150px;
		height:30px;
		float:left;	
		margin-left:10px;
		margin-top:10px;
		border: 2px dashed;
                background-color:LightGrey;
	}

</style> 

<script type="text/javascript">

//There are three groups of functions here:
//The first group is involved with dragging and dropping nodes
// between different locations.
//The second group is involved with creating the divs that are needed
// based on the number of nodes and number of tiers and organizing
// them on the webpage.
//The third group determines which divs the nodes are located in
// to group the nodes into tiers and make ban and white lists.


//Drag and drop functions:
function drag(drop_target, e) {
		e.dataTransfer.setData('Text', drop_target.id);
		}

function drop(drop_target, e) {
		var id = e.dataTransfer.getData('Text');
		drop_target.appendChild(document.getElementById(id));
		e.preventDefault();
	        } 

function dropCopy(ev) {
           ev.stopPropagation();
           ev.preventDefault();
           var src = ev.dataTransfer.getData("Text");
           var orig = document.getElementById(src);
           var pid = orig.parentNode.id;
           var target_id = ev.target.id;
           //document.write(pid);
           if (pid != target_id){
                var origclone = orig.cloneNode(true);
                var newid = src+"a";
                origclone.setAttribute('id',newid);
                document.getElementById(pid).appendChild(origclone);
                ev.target.appendChild(orig);
           }
           else {
                document.write(pid,target_id);
           }
           return false; 
}




function loadFunction(nnodes,ntiers) {
             makeNodeList(nnodes,'nodelist','tr');
             makeNodes(nnodes,'nodelist');
             makeTiers(nnodes,ntiers);
             makeNodes(nnodes,'nodelist2','bw');
             makeTierDesc1(ntiers);
             makeTierDesc2(ntiers);
             makeTierDesc3(ntiers);
             makeTierDesc4(ntiers);
             makeBWLists(nnodes);
}


//Function to make the NodeList. It is similar to the above function.
function makeNodeList(nnodes,nlist) {
               var element1 = document.createElement('div');
               var newheight = 45*nnodes + 40;
               newheight = newheight+'px';                  
               element1.setAttribute('id',nlist);
               element1.setAttribute('ondrop','drop(this, event)');
               element1.setAttribute('ondragenter','return false');
               element1.setAttribute('ondragover','return false');
               element1.style.height=(newheight);
               element1.innerHTML = "Nodes<br>";
               document.getElementById('tier_box').appendChild(element1);
}


//This is the function that makes the nodes. It is called when the
// page loads. The nodes are placed in the "nodelist" div.
//Will need to mofidy this function to pass it a list of the node
// names. The inner html is what is used in the later functions
// that make the tier list, banlist, and white list.
function makeNodes(nnodes,nlist,suffix) {

<?php $xyz=1;
$nm=$dir."$keyval"."name.txt";

$namelist=file_get_contents("$nm");
$str_arrname=array();
$str_arrname=explode("\n",$namelist);
$dataname=array();
$dataname=explode("\t",$str_arrname[0]);
             for ($i=1;$i<=$node;$i++){
$ii=$i-1;
$npr=trim($dataname[$ii]);
?>             
               i="<?php print($i);?>";
               var newname = 'node'+i;
               var element1 = document.createElement('div');
               element1.setAttribute('draggable','true');
               element1.setAttribute('class','node1');
               element1.setAttribute('ondragstart','drag(this, event)');
               element1.setAttribute('id',newname+suffix);
               element1.setAttribute('ondragover','return false');
               element1.innerHTML = "<?php print($npr);?>";
               document.getElementById(nlist).appendChild(element1);
           <?php  }
          ?>
     }


//Function to make the Tiers. It is similar to the above function.
function makeTiers(nnodes,ntiers) {
           for (i=1;i<=ntiers;i++){
               var newname = 'Tier'+i;
               var element1 = document.createElement('div');
               //var newpos = i*205;
               //newpos = 'left: '+newpos+'px';
               var newheight = nnodes*45 + 40;
               newheight = newheight+'px';
               //var newstyle = newpos + newheight;         
               element1.setAttribute('class','tier');
               element1.setAttribute('id',newname);
               element1.setAttribute('ondrop','drop(this, event)');
               element1.setAttribute('ondragenter','return false');
               element1.setAttribute('ondragover','return false');
               //element1.setAttribute('style',newpos);
               element1.style.height=(newheight);
               element1.innerHTML = newname +"<br>";
               document.getElementById('tiers').appendChild(element1);
            }
               var newwidth = ntiers*205 + 20;
               newwidth = newwidth+'px';
               document.getElementById('tiers').style.width=newwidth;
}



function makeTierDesc1(ntiers) {
               var element1 = document.createElement('div');
               var newheight = '20px';
               var newwidth = '220px';
               element1.setAttribute('id','int_box1_first');
               element1.setAttribute('class','int');
               element1.style.height=(newheight);
               element1.style.width=(newwidth);
               //element1.innerHTML = "st<br>";
               document.getElementById('int_box1').appendChild(element1);
	       for (i=1;i<=ntiers;i++) {
		 var newname = 'int_box1'+i;
                 var newheight = '20px';
                 var newwidth = '205px';
                 //var newwidth = ntiers*65 + 20;
                 //newwidth = newwidth+'px';
                 var element1 = document.createElement('div');
                 element1.setAttribute('id',newname);
                 element1.setAttribute('class','int');
                 element1.innerHTML = "&nbsp&nbspTier"+i+"<br>";
		 element1.style.height=(newheight);
		 element1.style.width=(newwidth);
		 document.getElementById('int_box1').appendChild(element1);
	       }
                 
}
function makeTierDesc2(ntiers) {
               var element1 = document.createElement('div');
               var newheight = '50px';
               var newwidth = '220px';
               element1.setAttribute('id','int_box2_first');
               element1.setAttribute('class','int1');
               element1.style.height=(newheight);
               element1.style.width=(newwidth);
               element1.innerHTML = "Are within tier <br>interactions allowed?<br>";
               document.getElementById('int_box2').appendChild(element1);
	       for (i=1;i<=ntiers;i++) {
		 var newname = 'int_box2'+i;
                 var newheight = '50px';
                 var newwidth = '205px';
                 //var newwidth = ntiers*65 + 20;
                 //newwidth = newwidth+'px';
                 var element1 = document.createElement('div');
                 element1.setAttribute('id',newname);
                 element1.setAttribute('class','int');
                 element1.innerHTML = "<br>";
		 element1.style.height=(newheight);
		 element1.style.width=(newwidth);
		 document.getElementById('int_box2').appendChild(element1);
                       //create form to hold yes/no radio boxes
                       var form1 = document.createElement('form');
                       //form1.innerHTML = "Allow edges between nodes in Tier"+i+"?<br>";
                       var radio_yes = document.createElement('input');
                       radio_yes.setAttribute('type','radio');
                       radio_yes.setAttribute('name',"r_yes_no_"+newname);
                       radio_yes.setAttribute('id',"r_yes_"+newname);

                       radio_yes.value = "r_yes_"+newname;
                       radio_yes.setAttribute('checked','checked');
                       form1.appendChild(radio_yes);
                       var yes_label = document.createElement('label');
                       yes_label.setAttribute('for',radio_yes.id);
                       yes_label.innerHTML = "Yes&nbsp&nbsp&nbsp&nbsp&nbsp";
                       form1.appendChild(yes_label);
                       var radio_no = document.createElement('input');
                       radio_no.setAttribute('type','radio');
                       radio_no.setAttribute('name',"r_yes_no_"+newname);
                       radio_no.setAttribute('id',"r_no_"+newname);
                       radio_no.value = "r_no_"+newname;
                       form1.appendChild(radio_no);
                       var no_label = document.createElement('label');
                       no_label.setAttribute('for',radio_no.id);
                       no_label.innerHTML = "No<br>";
                       form1.appendChild(no_label);
                       element1.appendChild(form1);
                     //  alert(radio_yes.value);
                     //  alert(radio_yes.checked);
                     //  alert(radio_no.value); 
                     //  alert(radio_no.checked);
	       }
                 
}

function makeTierDesc3(ntiers) {
               var element1 = document.createElement('div');
               var newwidth = '220px';
               var newheight = ntiers*10 + 25;
               newheight = newheight + 'px'; 
               //var newheight = '50px';
               //var newwidth = '250px';
               element1.setAttribute('id','int_box3_first');
               element1.setAttribute('class','int1');
               element1.style.height=(newheight);
               element1.style.width=(newwidth);
               element1.innerHTML = "Which tiers contain nodes that <br>can be the parents of this tier?<br>";
               document.getElementById('int_box3').appendChild(element1);
	       for (i=1;i<=ntiers;i++) {
		 var newname = 'int_box3'+i;
                 //var newheight = '50px';
                 var newwidth = '205px';
                 //newwidth = newwidth+'px';
                 var element1 = document.createElement('div');
                 element1.setAttribute('id',newname);
                 element1.setAttribute('class','int');
                 element1.innerHTML = "<br>";
		 element1.style.height=(newheight);
		 element1.style.width=(newwidth);
		 document.getElementById('int_box3').appendChild(element1);
                       //create form to hold allowed parents
                       var form2 = document.createElement('form');
                       //form2.innerHTML = "Which tiers can be the "+
                       //    "parents of the nodes in Tier"+i+"?<br>";
		       var k = 0;
                       for (j=1;j<=ntiers;j++) {
                          if (j!=i) {
			    k = k + 1;
                             var pbox = document.createElement('input');
                             pbox.setAttribute('type','checkbox');
                            // pbox.setAttribute('name',"par_"+i);
                             pbox.setAttribute('name',"par_"+i+"_"+j);
                             pbox.setAttribute('id',"par_"+i+"_"+j);   
                             pbox.value = "par_"+i+"_"+j;
                             if (j<i) {
                                pbox.setAttribute('checked','checked');
                             }
                             var plabel = document.createElement('label');
                             plabel.setAttribute('for',pbox.id);
                             if (k%2 == 0) {
                             plabel.innerHTML = "Tier"+j+"&nbsp&nbsp<br>";
                             } else {
                             plabel.innerHTML = "Tier"+j+"&nbsp&nbsp";
                             }
                             form2.appendChild(pbox);
                             form2.appendChild(plabel);  
                           }
                        }
                       element1.appendChild(form2);
	       }
}

function makeTierDesc4(ntiers) {
               var element1 = document.createElement('div');
               //var newheight = '50px';
               var newwidth = '220px';
               var newheight = ntiers*10 + 25;
               newheight = newheight + 'px'; 
               element1.setAttribute('id','int_box4_first');
               element1.setAttribute('class','int1');
               element1.style.height=(newheight);
               element1.style.width=(newwidth);
               element1.innerHTML = "Which tiers contain nodes that<br> can be the children of this tier?<br>";
               document.getElementById('int_box4').appendChild(element1);
	       for (i=1;i<=ntiers;i++) {
		 var newname = 'int_box4'+i;
                 //var newheight = '50px';
                 //var newwidth = ntiers*65 + 20;
                 var newwidth = '205px';
                 var element1 = document.createElement('div');
                 element1.setAttribute('id',newname);
                 element1.setAttribute('class','int');
                 element1.innerHTML = "<br>";
		 element1.style.height=(newheight);
		 element1.style.width=(newwidth);
		 document.getElementById('int_box4').appendChild(element1);
                       //create form to hold allowed children
                       var form3 = document.createElement('form');
                       //form3.innerHTML = "Nodes in which tiers can be <br>the children of this tier?<br>";
                       var k = 0;
                       for (j=1;j<=ntiers;j++) {
                          if (j!=i) {
			     k = k + 1;
                             var cbox = document.createElement('input');
                             cbox.setAttribute('type','checkbox');
                             //cbox.setAttribute('name',"child_"+i);
                             cbox.setAttribute('name',"child_"+i+"_"+j);
                             cbox.setAttribute('id',"child_"+i+"_"+j);

                             cbox.value = "child_"+i+"_"+j;
                             if (j>i) {
                                cbox.setAttribute('checked','checked');
                             }
                             var clabel = document.createElement('label');
                             clabel.setAttribute('for',cbox.id);
                             if (k%2 == 0) {
                                clabel.innerHTML = "Tier"+j+"&nbsp&nbsp<br>";
			      } 
			       else 
                              {
                               clabel.innerHTML = "Tier"+j+"&nbsp&nbsp";
			      }
                             form3.appendChild(cbox);
                             form3.appendChild(clabel);  
                           }
                        }
                       element1.appendChild(form3);
	       }
}



//Old function to make section of page that allows for tier description.
//Replaced by the four functions above.
function makeTierDesc(ntiers) {
                for (i=1;i<=ntiers;i++) {
                       //var i = 2;
                       var newname = 'desc_tier'+i;
                       
                       //create the outer division to hold the other boxes
                       var out_div = document.createElement('div');
                       out_div.setAttribute('id',newname);
                       if (i==1) {
                             out_div.innerHTML = "Tier"+i+"<br>";
                       } else {
                              out_div.innerHTML = "<br><br>Tier"+i+"<br>";
                       }
                       document.getElementById('outer_tier_desc').appendChild(out_div);

                       //create form to hold yes/no radio boxes
                       var form1 = document.createElement('form');
                       form1.innerHTML = "Allow edges between nodes in Tier"+i+"?<br>";
                       var radio_yes = document.createElement('input');
                       radio_yes.setAttribute('type','radio');
                       radio_yes.setAttribute('name',"r_yes_no_"+newname);
                       radio_yes.setAttribute('id',"r_yes_"+newname);

                       radio_yes.value = "r_yes_"+newname;
                       radio_yes.setAttribute('checked','checked');
                       form1.appendChild(radio_yes);
                       var yes_label = document.createElement('label');
                       yes_label.setAttribute('for',radio_yes.id);
                       yes_label.innerHTML = "Yes    ";
                       form1.appendChild(yes_label);
                       var radio_no = document.createElement('input');
                       radio_no.setAttribute('type','radio');
                       radio_no.setAttribute('name',"r_yes_no_"+newname);
                       radio_no.setAttribute('id',"r_no_"+newname);
                       radio_no.value = "r_no_"+newname;
                       form1.appendChild(radio_no);
                       var no_label = document.createElement('label');
                       no_label.setAttribute('for',radio_no.id);
                       no_label.innerHTML = "No<br>";
                       form1.appendChild(no_label);
                       out_div.appendChild(form1);
                     //  alert(radio_yes.value);
                     //  alert(radio_yes.checked);
                     //  alert(radio_no.value); 
                     //  alert(radio_no.checked);


                       //create form to hold allowed parents
                       var form2 = document.createElement('form');
                       form2.innerHTML = "Which tiers can be the "+
                           "parents of the nodes in Tier"+i+"?<br>";
                       for (j=1;j<=ntiers;j++) {
                          if (j!=i) {
                             var pbox = document.createElement('input');
                             pbox.setAttribute('type','checkbox');
                            // pbox.setAttribute('name',"par_"+i);
                             pbox.setAttribute('name',"par_"+i+"_"+j);
                             pbox.setAttribute('id',"par_"+i+"_"+j);   
                             pbox.value = "par_"+i+"_"+j;
                             if (j<i) {
                                pbox.setAttribute('checked','checked');
                             }
                             var plabel = document.createElement('label');
                             plabel.setAttribute('for',pbox.id);
                             plabel.innerHTML = "Tier"+j;
                             form2.appendChild(pbox);
                             form2.appendChild(plabel);  
                           }
                        }
                       out_div.appendChild(form2);
            
                    //   alert(pbox.value);
                   //    alert(pbox.checked);
                      
   

                       //create form to hold allowed children
                       var form3 = document.createElement('form');
                       form3.innerHTML = "Which tiers can be the "+
                           "children of the nodes in Tier"+i+"?<br>";
                       for (j=1;j<=ntiers;j++) {
                          if (j!=i) {
                             var cbox = document.createElement('input');
                             cbox.setAttribute('type','checkbox');
                             //cbox.setAttribute('name',"child_"+i);
                             cbox.setAttribute('name',"child_"+i+"_"+j);
                             cbox.setAttribute('id',"child_"+i+"_"+j);

                             cbox.value = "child_"+i+"_"+j;
                             if (j>i) {
                                cbox.setAttribute('checked','checked');
                             }
                             var clabel = document.createElement('label');
                             clabel.setAttribute('for',cbox.id);
                             clabel.innerHTML = "Tier"+j;
                             form3.appendChild(cbox);
                             form3.appendChild(clabel);  
                           }
                        }
                       out_div.appendChild(form3);
                    //var break = document.createElement('div');
                    //break.innerHTML = "<br>";
                    //out_div.appendChild(break);
                    //out_div.appendChild(break);
                     //  alert(cbox.value);
                      // alert(cbox.checked);

                  }
}

//Function to give the divs for the ban and white lists the correct
// dimensions.
function makeBWLists(nnodes) {
                var newheight = 45*nnodes + 40;
                var nlheight = newheight+'px';
                document.getElementById('nodelist2').style.height=nlheight;
                var inheight = newheight*2; 
                var outheight = inheight + 50;
                inheight=inheight+'px';
                outheight = outheight+'px';
                document.getElementById('ban_outer').style.height=outheight;
                document.getElementById('white_outer').style.height=outheight;
                document.getElementById('ban_from').style.height=inheight;
                document.getElementById('ban_to').style.height=inheight;
                document.getElementById('white_from').style.height=inheight;
                document.getElementById('white_to').style.height=inheight;
          
}


//Functions that get the locations of the nodes to group the
// nodes into tiers and make ban and white lists.
//Will probably need to add a function to get the tier description information.
//Might be able to do that with just php though?
function getNodesInTiers(ntiers) {
                output = ntiers+",\n";
                for (i=1;i<=ntiers;i++){
                var newname = 'Tier'+i;
                children = document.getElementById(newname).childNodes;
                temp = children.length - 2;
                temp = newname + ",\t" +temp+ ",\t"
                for (j=2;j<children.length;j++){
                temp = temp + children[j].innerHTML + ",\t"
                }
                output = output + temp +"\n"
                }
                
                
                return output;
                
               // window.open("http://compbio.uthsc.edu/BNServer/tier.php?tier="+output,"Ratting","width=950,height=270,0,status=0,");
                
}

function getNodesInList(from_div,to_div) {
                children_from = document.getElementById(from_div).childNodes;
                children_to = document.getElementById(to_div).childNodes;
                if(children_from.length == children_to.length){
                    temp = "";                
                    for (i=2;i<children_from.length;i++){
                    temp = temp+children_from[i].innerHTML;
                    temp = temp+",\t"+children_to[i].innerHTML+",\n";
                    }
                  
    

                 }
                 else 
                 {
                   alert('Error in node list');
                   temp="";
                 }
               return temp;
}


//Functions that get the description of the tiers. 
function getDescribeTiers(ntiers) {
              output = "";
              for (i=1;i<=ntiers;i++) {
               temp="";
               var newname = 'int_box2'+i;
                
               var temp_id_yes="r_yes_"+newname;
               var temp_id_no="r_no_"+newname;
               
                temp_yes = document.getElementById(temp_id_yes);
                temp_no = document.getElementById(temp_id_no);

               // temp = temp + temp_yes.value + ",\t"
                temp = temp + temp_yes.checked + ",\t"

               // temp = temp + temp_no.value + ",\t"
                temp = temp + temp_no.checked + ",\t"
 
                for (j=1;j<=ntiers;j++) {
                    if(j!=i)
                    {
                      var pbox_id="par_"+i+"_"+j;
                      var cbox_id="child_"+i+"_"+j;
                      temp_p = document.getElementById(pbox_id);
                      temp_c = document.getElementById(cbox_id); 
  
                      temp = temp + temp_p.checked + ",\t"

                      temp = temp + temp_c.checked + ",\t"

                      
                     }
                }   
               // alert(temp); 
                
                output = output + temp +"\n"
               
}
              

              //  window.open("http://compbio.uthsc.edu/BNServer/tierdescription.php?tierdesc="+output,"Ratting","width=950,height=270,0,status=0,");
              return output;
                
}


//Combined all three function together and then execute structure learning

function getcombineDescription(ntiers,ban_from,ban_to,white_from,white_to,keyv)
{
  var tier=getNodesInTiers(ntiers);
  var tierdesc=getDescribeTiers(ntiers);
  var ban=getNodesInList(ban_from,ban_to);
  var white=getNodesInList(white_from,white_to);
  window.open("tier_description_processing_gom.php?tier="+tier+"&tierdesc="+tierdesc+"&ban="+ban+"&white="+white +"&My_key="+keyv,'_self',false);
}

function clearBWLists()
{
  var element = document.getElementById('ban_from');
  var children = element.childNodes;
  while (children.length>2) {
    element.removeChild(element.lastChild);
    var children = element.childNodes;
  }
  var element = document.getElementById('ban_to');
  var children = element.childNodes;
  while (children.length>2) {
    element.removeChild(element.lastChild);
    var children = element.childNodes;
  }
  var element = document.getElementById('white_from');
  var children = element.childNodes;
  while (children.length>2) {
    element.removeChild(element.lastChild);
    var children = element.childNodes;
  }
  var element = document.getElementById('white_to');
  var children = element.childNodes;
  while (children.length>2) {
    element.removeChild(element.lastChild);
    var children = element.childNodes;
  }
}





</script> 
</head> 


<script type="text/javascript">
     var nnodes =<?php print($node);?>;
     var ntiers = <?php print($tier_number);?>;
</script>

<body onload="loadFunction(nnodes,ntiers)">
</br>
        <p><h3>2. Assign variables to tiers:<br></h3>
         </p>
        <br>
        <div id="tier_box">
        <div id="tiers"></div>
       
       </div>

           
       <div id="outer_tier_desc1">
       <p><h3>3. Define interactions allowed between tiers:<br></h3> 
       </p>
       <br>
       <div id="int_box1">
       </div>
       <br>
       <div id="int_box2">
       </div>
       <br>
       <div id="int_box3">
       </div>
       <br>
       <div id="int_box4">
       </div>
       </div>
       <br>
       <br>
       <br>
           
        <div id="outer_box_lists">
       <p><h3><br>4. Specify additional constraints:<br></h3>
       </p>         
       <br>
       <div><input type="button" class=button2 value="Clear lists of banned and required edges" onClick="clearBWLists()"/></div><br>
        <div id="nodelist2" ondrop="return false"
        ondragcenter="return false" ondragover="return false" >Nodes<br>
        </div>
         
        <div id="ban_outer">Banned edges<br>
        <div id="ban_from" class="tier" ondrop="return dropCopy(event)"
        ondragenter="return false" ondragover="return false">From<br></div>
        <div id="ban_to" class="tier" ondrop="return dropCopy(event)"
        ondragenter="return false" ondragover="return false">To<br></div>
        </div>
        <div id="white_outer">Required edges<br>
        <div id="white_from" class="tier" ondrop="return dropCopy(event)"
        ondragenter="return false" ondragover="return false">From<br></div>
        <div id="white_to" class="tier" ondrop="return dropCopy(event)"
        ondragenter="return false" ondragover="return false">To<br></div>
        </div>
        </div>
       
        <br>
        <br>
   
        

</body> 
</div>
</html>

