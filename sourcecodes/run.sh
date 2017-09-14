#!/bin/bash
d=`dirname $0`
#ess=1
DIR="$d/data/$1"
DIR_1="$d/data/$1/"

mkdir -p $DIR

maxparent=$2
k=$3
THR=$4

./network_score $d/data/$1continuous_input.txt $d/data/$1ban.txt $d/data/$1white.txt $maxparent $DIR

$d/k-best/src/data2netk_poster.sh $d/data/$1continuous_input.txt $DIR $k $maxparent

./structure $d/data/$1continuous_input.txt $DIR/postProbEachEdge.txt $d/data/$1structure_input_temp.txt $d/data/$1structure_input.txt $THR

rm -r $DIR_1*
rmdir $DIR
