#!/bin/bash
cd ./data/$1/
FILES=$2*
for f in $FILES
do
  echo "Processing $f file..."
  new=${f//$2/$3} 
  # take action on each file. $f store current file name
  # echo "new Processing $new file..."
  cp $f /tmp/bnw/$new
done