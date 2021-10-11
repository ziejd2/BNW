#!/bin/bash
gcc ./localscore/network_score.c ./localscore/libRmath.so -lm -o network_score
gcc ./print_structure/structure.c -lm -o structure

cd ./k-best/src/
sh buildk_poster.sh



