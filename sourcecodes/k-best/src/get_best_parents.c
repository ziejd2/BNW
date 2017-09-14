#include "cfg.h"

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "files.h"


void get_best_parents(int nof_vars, char* dirname) {
//printf("I'm here!!!");

  varset_t nof_parsets = 1U<<(nof_vars-1);
  int i;
//  FILE* fp;
  score_t*  scores = malloc(nof_parsets*sizeof(score_t));
  varset_t* bsps   = malloc(nof_parsets*sizeof(varset_t));
// fp = fopen("/home/grad/lram/RA stuff/check/src/out.txt","w");

  for(i=0;i<nof_vars; ++i){
    
    { /* READ SCORES FOR i */
      FILE* fin = open_file(dirname, i, "", "rb");
      fread(scores, sizeof(score_t), nof_parsets, fin);
  //    fp = fopen("out.txt","a");
// fp = fopen("out.txt","a");
    //  fprintf(fp,"\n\nLocal Scores for Var %d:",i);
     // for(j=0;j<nof_parsets;j++)
     // {
       //  fprintf(fp,"%8.10f\n ",scores[j]);
    //  }

      fclose(fin);
    }

    
    { /* FIND BEST PARENTS AND THEIR SCORES IN EACH SET FOR i */

      varset_t ps;

      for(ps=0; ps<nof_parsets; ++ps){
	varset_t psel = 1U;

	bsps[ps] = ps;
	for(psel=1; psel<nof_parsets; psel <<= 1){
	  if (ps & psel){
	    varset_t subset = ps^psel;
	    if (scores[subset] >= scores[ps]){
	      scores[ps] = scores[subset];
	      bsps[ps]   = bsps[subset];
	    }
	  }
	}
      }
    }
    
    
    { /* WRITE BEST PARENTS FOR i */

      FILE* fout = open_file(dirname, i, ".bps", "wb");
      fwrite(bsps, sizeof(varset_t), nof_parsets, fout);

//fprintf(fp,"\n\nBest parent sets for var %d",i);
 //for(j=0;j<nof_parsets;j++)
   //   {
     //    fprintf(fp,"%u ",bsps[j]);
      //}




      fclose(fout);
    }

    { /* WRITE BEST SCORES FOR i */

      FILE* fout = open_file(dirname, i, ".bss", "wb");
      fwrite(scores, sizeof(score_t), nof_parsets, fout);

//fprintf(fp,"\n\nBest score for var %d",i);
 //for(j=0;j<nof_parsets;j++)
   //   {
     //    fprintf(fp,"%g ",scores[j]);
      //}




      fclose(fout);
    }
  }
//fclose(fp);
  free(bsps);
  free(scores);
}


int main(int argc, char* argv[])
{
//FILE *fp=fopen("/home/grad/lram/RA stuff/try/src/kd4.log", "w+");
//fprintf(stdout, "loose kutty");
//fclose(fp);
  if (argc!=3) {
    fprintf(stderr, "Usage: best_parents nof_vars dirname\n");
    return 1;
  }

  {
    int nof_vars = atoi(argv[1]);
    get_best_parents(nof_vars, argv[2]);
  }

  return 0;
}
