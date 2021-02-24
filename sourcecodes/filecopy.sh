#!/bin/bash
#cd ./data/
#cd /tmp/bnw/
cd /var/lib/genenet/bnw/
FILES=$1*
for f in $FILES
do
  #echo "Processing $f file..."
  new=${f//$1/$2} 
  # take action on each file. $f store current file name
  #echo "new Processing $new file..."
  cp $f $new
done
temp='looCV_temp.txt'
rm $2$temp
temp='looCV.txt'
rm $2$temp
temp='kfoldCV_temp.txt'
rm $2$temp
temp='kfoldCV.txt'
rm $2$temp
temp='ts_upload.txt'
rm $2$temp
temp='ts_output.txt'
rm $2$temp
temp='net_figure_new.txt'
rm $2$temp
temp='parameters_ev.txt'
rm $2$temp
temp='vardata.txt'
rm $2$temp
temp='varname.txt'
rm $2$temp
temp='var.txt'
rm $2$temp
temp='violin_evidence.txt'
rm $2$temp
temp='violin_plotly_evidence.html'
rm $2$temp
