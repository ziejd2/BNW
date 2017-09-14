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