#!/bin/bash

binpath=`dirname $0`

if [ $# -ne 4 ]; then
    echo Usage: data2net.sh datafile resultdir k 1>&2
    exit 1
fi

datafile=$1;  shift
#score=$1;     shift
rdir=$1;      shift
k=$1;shift
n=$1;shift

nof_vars=`head -1 $datafile|wc -w`
echo "The number of variables is $nof_vars."

#HR:
if [ "$n" -eq 0 ]
then
    maxindegree=`expr $nof_vars - 1`
else
    maxindegree=$n
fi

#maxindegree=`expr $nof_vars - 1`
echo "maxindegree is $maxindegree."

#HR:
nof_instances=`cat $datafile | wc -l`
#delete the header and type
nof_instances=`expr $nof_instances - 2`
echo "The number of instances is $nof_instances."


START=$(date +%s%N)
$binpath/get_kbest_parents $nof_vars ${rdir} $k
#HR
END=$(date +%s%N)
#DIFF=$(( $END - $START ))
DIFF2=$(( $END - $START ))
echo "get_kbest_parents is done."
echo "get_kbest_parents took $DIFF2 n-seconds."

START=$(date +%s%N)
$binpath/get_kbest_nets $nof_vars ${rdir} $k
#HR
END=$(date +%s%N)
#DIFF=$(( $END - $START ))
DIFF3=$(( $END - $START ))
echo "get_kbest_nets is done."
echo "get_kbest_nets took $DIFF3 n-seconds."

DIFF=`expr $DIFF1 + $DIFF2 + $DIFF3`
echo "The total process took $DIFF n-seconds."
