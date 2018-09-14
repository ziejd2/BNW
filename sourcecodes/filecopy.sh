#!/bin/bash
cd ./data/
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
