#ifndef __FILES_H__
#define __FILES_H__

#include <stdio.h>

int    nof_lines(char* filename);
char*  create_fn(char* dirname, int i, char* ext);
FILE*  open_file(char* dirname, int i, char* ext, char* mode);
FILE** open_files(int nof_vars, char* dirname, char* ext, char* mode);
void   free_files(int nof_vars, FILE** files);

#endif
