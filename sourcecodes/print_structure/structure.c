#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<math.h>

struct st{
char *name;	
double *data;	
}*st_tab;

main(int argc,char *argv[])
{
FILE *fp1,*fp2,*fp3;
char line[2500];
int i,j,rd;
double d,row;
double thr=atof(argv[5]);

fp1=fopen(argv[2],"r");
fp2=fopen(argv[3],"w");

fp3=fopen(argv[4],"w");


row=0.0;
while(fgets(line,2500,fp1)!=NULL)
   row++;

rd=(int)sqrt(row);

printf("%d\n",rd);

fclose(fp1);
fp1=fopen(argv[1],"r");

st_tab=(struct st *)malloc(sizeof(struct st)*rd);

for(i=0;i<rd;i++)
{
   fscanf(fp1,"%s",line);	
   st_tab[i].name=(char *)malloc(sizeof(char)*(sizeof(line)+1));
   strcpy(st_tab[i].name,line);
   st_tab[i].data=(double *)calloc(rd,sizeof(double));
}

fclose(fp1);
fp1=fopen(argv[2],"r");

for(i=0;i<rd;i++)
{
    for(j=0;j<rd;j++)
    {
          fscanf(fp1,"%s",line);
	   fscanf(fp1,"%s",line);
	   fscanf(fp1,"%s",line);		
   	   fscanf(fp1,"%lf",&d);
          st_tab[i].data[j]=d;
    
    }
}

/*
for(i=0;i<rd;i++)
{
    for(j=0;j<rd;j++)
    {
         if(st_tab[i].data[j]>=st_tab[j].data[i])
                 st_tab[j].data[i]=0.0;
    
    }
}
*/

for(i=0;i<rd;i++)
{ 
    fprintf(fp2,"%s\t",st_tab[i].name);
    fprintf(fp3,"%s\t",st_tab[i].name);
}

fprintf(fp2,"\n");
fprintf(fp3,"\n");

for(i=0;i<rd;i++)
{ 
    for(j=0;j<rd;j++)
    {
   	   fprintf(fp2,"%lf\t",st_tab[j].data[i]);
          if(st_tab[j].data[i]>thr)	
   	     fprintf(fp3,"1\t");
          else 	
   	     fprintf(fp3,"0\t");
    }
    fprintf(fp2,"\n");
    fprintf(fp3,"\n");
}

}
