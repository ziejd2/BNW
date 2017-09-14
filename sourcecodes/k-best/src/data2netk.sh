#!/bin/bash

binpath=`dirname $0`

if [ $# -ne 5 ]; then
    echo Usage: data2net.sh vdfile datafile score resultdir k 1>&2
    exit 1
fi

vdfile=$1;    shift 
datafile=$1;  shift
score=$1;     shift
rdir=$1;      shift
k=$1;shift

mkdir -p $rdir
nof_vars=`cat $vdfile | wc -l`

START=$(date +%s%N)
$binpath/get_local_scores $vdfile $datafile $score 1 0 ${rdir}/res
#HR
echo "get_local_scores is done."
$binpath/split_local_scores   $nof_vars ${rdir}
#HR
echo "split_local_scores is done."
$binpath/reverse_local_scores $nof_vars ${rdir}
#HR
echo "reverse_local_scores is done."
END=$(date +%s%N)
#DIFF=$(( $END - $START ))
DIFF1=$(( $END - $START ))
echo "Local score took $DIFF1 seconds"

#the above is the same as original bene

START=$(date +%s%N)
$binpath/get_kbest_parents $nof_vars ${rdir} $k
#HR
END=$(date +%s%N)
#DIFF=$(( $END - $START ))
DIFF2=$(( $END - $START ))
echo "get_kbest_parents is done."
echo "get_kbest_parents took $DIFF2 seconds"

START=$(date +%s%N)
$binpath/get_kbest_nets $nof_vars ${rdir} $k
#HR
END=$(date +%s%N)
#DIFF=$(( $END - $START ))
DIFF3=$(( $END - $START ))
echo "get_kbest_nets is done."
echo "get_kbest_nets took $DIFF3 seconds"

DIFF=`expr $DIFF1 + $DIFF2 + $DIFF3`
echo "total process took $DIFF seconds"
