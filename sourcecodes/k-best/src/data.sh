#!/bin/bash

binpath=`dirname $0`

if [ $# -ne 4 ]; then
    echo Usage: data2net.sh vdfile datafile score resultdir 1>&2
    exit 1
fi

vdfile=$1;    shift 
datafile=$1;  shift
score=$1;     shift
rdir=$1;      shift

mkdir -p $rdir
nof_vars=`cat $vdfile | wc -l`

START=$(date +%s)
$binpath/get_best_parents     $nof_vars ${rdir}
$binpath/get_best_sinks       $nof_vars ${rdir} ${rdir}/sinks
$binpath/get_best_order       $nof_vars ${rdir}/sinks ${rdir}/ord
$binpath/get_best_net         $nof_vars ${rdir} ${rdir}/ord ${rdir}/net
$binpath/score_net            ${rdir}/net ${rdir}
END=$(date +%s)
DIFF=$(( $END - $START ))
echo "Best network took $DIFF seconds"

