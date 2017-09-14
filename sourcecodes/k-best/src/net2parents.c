#include "cfg.h"

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

void net2parents(char* netfile, char* parfile)
{
  int v;
  varset_t varset;
//score_t s;
//FILE *fp=fopen("/home/grad/lram/RA stuff/try/src/kd.log","w+");


  FILE* netf = strcmp(netfile,"-") ? fopen(netfile, "r") : stdin;
  FILE* parf = strcmp(parfile,"-") ? fopen(parfile, "w") : stdout;
//fprintf(fp, "great one was here");
//fclose(fp);

//fscanf(netf,"%f",&s);
  for(v=0; 1 == fscanf(netf, "%u", &varset); ++v){
    int p;
//printf("Var %d", v);

    for(p=0; varset; ++p, varset>>=1){
      if(varset & 1) {
	fprintf(parf, "%d", p);
	if(varset>1)fprintf(parf, " ");
      }
    }
//	printf("Var %d", v);
    fprintf(parf,"\n");
  }
//printf("Here");
  fclose(parf);
  fclose(netf);
}

int main(int argc, char* argv[])
{

  if (argc!=3) {
    fprintf(stderr, "Usage: net2parents netfile parentfile\n");
    return 1;
  }

  net2parents(argv[1], argv[2]);

  return 0;
}
