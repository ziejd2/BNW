#!/bin/bash
#d=`dirname $0`
#ess=1
#DIR="$d/data/$1"
#DIR_1="$d/data/$1/"
cd /var/lib/genenet/bnw


mkdir -p /var/lib/genenet/bnw/$1

maxparent=$2
k=$3
THR=$4

./network_score /var/lib/genenet/bnw/$1continuous_input.txt /var/lib/genenet/bnw/$1ban.txt /var/lib/genenet/bnw/$1white.txt $maxparent /var/lib/genenet/bnw/$1

./k-best/src/data2netk_poster.sh /var/lib/genenet/bnw/$1continuous_input.txt /var/lib/genenet/bnw/$1 $k $maxparent

./structure /var/lib/genenet/bnw/$1continuous_input.txt /var/lib/genenet/bnw/$1/postProbEachEdge.txt /var/lib/genenet/bnw/$1structure_input_temp.txt /var/lib/genenet/bnw/$1structure_input.txt $THR

rm -r /var/lib/genenet/bnw/$1

if [ $k = 1 ]
then
    rm -r /var/lib/genenet/bnw/$1structure_input_temp.txt
fi
