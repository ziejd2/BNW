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
k=$1;		shift

mkdir -p $rdir
nof_vars=`cat $vdfile | wc -l`
START=$(date +%s)
$binpath/get_kbest_parents $nof_vars ${rdir} $k
$binpath/get_kbest_nets $nof_vars ${rdir} $k
END=$(date +%s)
DIFF=$(( $END - $START ))
echo "Top k took $DIFF seconds"
