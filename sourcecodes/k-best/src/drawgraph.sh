#!/bin/bash
k=$1
rdir=$2
for ((  i = 0 ;  i < k;  i++  ))
do
	${rdir}/../../src/arcs2dot ${rdir}/../iris.vd ${rdir}/$arc$i ${rdir}/dot_$i 
done

