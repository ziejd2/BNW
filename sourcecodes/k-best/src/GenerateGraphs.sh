#!/bin/bash
k=100
rdir=./../iris3/resdir
for ((  i = 0 ;  i < k;  i++  ))
do
  dot ${rdir}/dot_$i -Tps -o ${rdir}/dotRes_$i.ps
done