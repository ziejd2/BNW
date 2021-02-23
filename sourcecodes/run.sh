#!/bin/bash
#d=`dirname $0`
#ess=1
#DIR="$d/data/$1"
#DIR_1="$d/data/$1/"
cd /tmp/bnw


mkdir -p /tmp/bnw/$1

maxparent=$2
k=$3
THR=$4

./network_score /tmp/bnw/$1continuous_input.txt /tmp/bnw/$1ban.txt /tmp/bnw/$1white.txt $maxparent /tmp/bnw/$1

./k-best/src/data2netk_poster.sh /tmp/bnw/$1continuous_input.txt /tmp/bnw/$1 $k $maxparent

./structure /tmp/bnw/$1continuous_input.txt /tmp/bnw/$1/postProbEachEdge.txt /tmp/bnw/$1structure_input_temp.txt /tmp/bnw/$1structure_input.txt $THR

rm -r /tmp/bnw/$1

if [ $k = 1 ]
then
    rm -r /tmp/bnw/$1structure_input_temp.txt
fi
