#!/bin/bash

if [ ${1:-UNIX} == "WIN" ]; then
    CC="i586-mingw32msvc-gcc"
    EXT=".exe"
else
    CC=gcc
fi

D=g++

#CFLAGS="-Wextra -g -ansi -pedantic"
#CFLAGS="-Wall -O3 -g -pg"
CFLAGS="-Wall  -O3"

$CC $CFLAGS -c -o files.o files.c
$CC $CFLAGS -c -o varpar.o varpar.c

$CC $CFLAGS -o get_local_scores$EXT files.c reg.c ilogi.c ls_XIC.c ls_NML.c ls_BDe.c ls_LOO.c get_local_scores.c -lm
$CC $CFLAGS -o split_local_scores$EXT split_local_scores.c files.o
$CC $CFLAGS -o reverse_local_scores$EXT reverse_local_scores.c files.o
$D -o get_kbest_parents$EXT get_kbest_parents.cc 
$D -o get_kbest_nets$EXT get_kbest_nets.cc 
