#!/bin/bash
k=10
rdir=./../iris3/resdir
for ((  i = 0 ;  i < k;  i++  ))
do
#if which dot > /dev/null; then
    ${rdir}/../../src/net2parents ${rdir}/${i}net ${rdir}/${i}parent \
	| ${rdir}/../../src/parents2arcs ${rdir}/${i}parent ${rdir}/${i}arc \
	| ${rdir}/../../src/arcs2dot ${rdir}/../iris.vd ${rdir}/${i}arc ${rdir}/dot_$i \
	| dot ${rdir}/dot_$i -Tps -o ${rdir}/dot_res_${i}.ps
    echo See $d/resdir/iris.ps for a postscript picture of the net.
#fi
done