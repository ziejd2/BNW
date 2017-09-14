#include<stdio.h>
#include <stdlib.h>
#include<string.h>
#define MATHLIB_STANDALONE 1
#include "matrix.h"
#include "postc0.c"
#include "modified_postc.c" // only line that has been changed in this code
//#define OUT_DIR "./Result/"  //WHERE BENE READ DATA FOR STRUCTURE LEARNING //change it to #define OUT_DIR "/var/www/html/compbio/BNW/bene-0.9-4/example/resdir/" for server. Also change   from sprintf(line+9,"%d",i);  to sprintf(line+52,"%d",i);

struct white{
int n;
int *list;
};

struct ban{
int n;
int *list;
};

struct ndata{
int idx,type,score_i;
char **ddata;
char **label;              //for descrete node
int lindex;
double *prob,*cdata,*post_alpha;	
double score_zeroparent;
double *score;
struct white wlist;
struct ban blist;
};


struct nd{
int num,nd,*discrete,nc,*continuous,row;
double score;
struct ndata *node;
};

void get_next_seq(int *,int *,int);
void  learn_node(struct nd *);
double learn_parent(struct nd  *,int,int);

double run_strtof (const char * input)  //Convert string to double
{
    double output;
    char * end;
    output = strtod (input, & end);
    if (end == input) {
     return 0.0;    
    }
    else {
        return output;
    }
}

 
char* itoa(int val, int base){  //convert number to string
	
	static char buf[32] = {0};
	
	int i = 30;
	
	for(; val && i ; --i, val /= base)
	
		buf[i] = "0123456789abcdef"[val % base];
	
	return &buf[i+1];
	
}

double standarizedata(double val,double mean,double std)
{
  return (val-mean)/std;

}

double mean(double *a,int l)
{
int i;
double m;
m=0;
for(i=0;i<l;i++)
{
	m+=a[i];
}
m=m/l;
return m;
}


double stdev(double *a,int l,double m)
{
int i;
double sqsum=0.0;

for(i=0;i<l;i++)
{
   sqsum+=(a[i]-m)*(a[i]-m);
 
}

return sqrt(sqsum/(l-1));
}


main(int argc, char *argv[])
{
 FILE * pFile,*banf,*whitef;
 int i,s_i,j,k,l,m,id,ic,c,row,imatch,flag,bit,bit_count,ccount,maxccount;
 int n = 0,ban_flag,white_flag,white_flag_all;
 char line[2500],nf[250],nt[250],*file_name;
 char **name;  //name of variables
 double tc,tcm,score_val;
 struct nd network;
 int MAX_PARENT;
 FILE **inputFiles;
 

 double min_score; 
 

 if(argc<=5)
 {
   printf("Use 5 arguments: 1. input data file name. 2. ban list file name. 3. white list file name. 4. integer value of the maximum number of parents are allowed and 5. output file directory\n");
   return; 
 } 

 
 
 pFile=fopen (argv[1],"r");

  
 if (pFile==NULL) perror ("Error opening file");
 else
 {//reading data from input file
    

    	row=0;
    	do{   row++;
    	} while (fgets (line,2500,pFile)!=NULL);  
  
    	row-=3;  
    	network.row=row;
    	fclose(pFile);
   	pFile=fopen (argv[1],"r");
       ccount=0;  
       maxccount=0;
   	do {
      		c = fgetc (pFile);
      		if (c == '\t') 
              {     
                   n++;
                   if(maxccount<ccount)
                      maxccount=ccount;
                   ccount=0;
              }
              else
                  ccount++;
               

    	} while (c != '\n');
    

    	network.num=n+1;
    	network.discrete=(int *)malloc(sizeof(int)*network.num);
    	network.continuous=(int *)malloc(sizeof(int)*network.num); 
    	network.node=(struct ndata *)malloc(sizeof(struct ndata)*network.num);

       MAX_PARENT=atoi(argv[4]);  //maximum number of parents for each node
       if(MAX_PARENT==0)  //no restriction on parents
           MAX_PARENT=network.num+1;

       name=(char **)malloc(sizeof(char *)*network.num);

    	for(i=0;i<network.num;i++)
           name[i]=(char *)malloc(sizeof(char)*(maxccount+1));


    
 	id=0;
       ic=0; 
    	for(i=0;i<=n;i++)
     	{
	  	fscanf(pFile,"%d",&c);
	  	network.node[i].idx=i;
	  	network.node[i].type=c;
	  	if(c==1)
	  	{
	    		network.continuous[ic]=i;
	    		ic++;	
	    		network.node[i].prob=(double *)malloc(sizeof(double)*2); //probability array of size 2 for continuous node
	    		network.node[i].prob[0]=0.0;  //initialization
	    		network.node[i].prob[1]=0.0; 
	    	       network.node[i].cdata=(double *)malloc(sizeof(double)*row); //data matrix for continuous node
	  	}
	  	else
	  	{
	  		network.discrete[id]=i; 
              	network.node[i].post_alpha=(double *)calloc(network.node[i].type,sizeof(double));  //prepare total count of each discrete data lebel initialize with zero by calloc which is used in learnnode
			id++;
	    		network.node[i].prob=(double *)malloc(sizeof(double)*c); //probability array of size equals to number of levels for discrete node 
	    		network.node[i].ddata=(char **)malloc(sizeof(char *)*row); //data matrix for continuous node
	    		network.node[i].label=(char **)malloc(sizeof(char *)*c);  //label array
	    		for(j=0;j<c;j++)
			   network.node[i].prob[j]=1.0/c;
	  	}
    	 }
	 network.nd=id;  network.nc=ic; 
        j=0;
   	 
        do{  //calculate average and prob for each node
     	 	for(i=0;i<=n;i++) //n is nimber of column in the data matrix
     		{
     			if(network.node[i].type==1)   //if continuous
     			{
     	         		fscanf(pFile,"%s",line);
     	         		tc=run_strtof(line);
		  		//network.node[i].prob[1]+=tc;   //summation for average
		  		network.node[i].cdata[j]=tc;  // data values in data column vector
     			}
     			else   //discrete
     	 	       {
				fscanf(pFile,"%s",line);
				network.node[i].ddata[j]=(char *)malloc(sizeof(char)*strlen(line));
				if(j==0)    //first iteration of do while
				{ 
	    	         		network.node[i].lindex=0;
		   	  		network.node[i].label[0]=(char *)malloc(sizeof(char)*strlen(line));
		   	  		strcpy(network.node[i].label[0],line);  //new label assigned to label list
		   	  		network.node[i].post_alpha[0]=1;
		   	
				}
				else  //store different label for discrete node also count number of ocarance of each label for calculation of post alpha score
				{
		   	 		imatch=0;
		   	 		for(k=0;k<=network.node[i].lindex;k++)
		   	 		{
		   	   	 		if(strcmp(network.node[i].label[k],line)==0)
		   	   	 		{
		   	   	    			imatch=1; 
							network.node[i].post_alpha[k]+=1;	
		   	   	    			break; 	
		   	   	 		}
		   	   		}
		   	 		if(imatch==0)
		   	 		{
		   	       		network.node[i].lindex++;
		   	       		k=network.node[i].lindex;
		   	       		network.node[i].label[k]=(char *)malloc(sizeof(char)*strlen(line));
		   	       		strcpy(network.node[i].label[k],line);  //new label assigned to label list
						network.node[i].post_alpha[k]+=1;
		   	 		}
				}
				strcpy(network.node[i].ddata[j],line);
     	 		}
     	
     		}
      		j++;  
    	} while (fgets (line,250,pFile)!=NULL);  //end of do while to read the input file  
     
       //////////////normalize continuous data/////////////
       for(i=0;i<=n;i++)
   	{
     		if(network.node[i].type==1)  
     		{    	  		

                     tcm=mean(network.node[i].cdata,row);
                     tc=stdev(network.node[i].cdata,row,tcm);
          	  	for(j=0;j<row;j++)
		  	{
			    network.node[i].cdata[j]=standarizedata(network.node[i].cdata[j],tcm,tc); //dataval.data mean, standard deviation
			}
     		}
	 } 
     
       //////////////////adjust average and prob for normalized data
     	for(i=0;i<=n;i++)
   	{
     		if(network.node[i].type==1)  //claculate average and prob for continuous nodes
     		{
     	  		network.node[i].prob[1]=mean(network.node[i].cdata,row);
	
          	  	for(j=0;j<row;j++)
		  	{
			    network.node[i].prob[0]+=(network.node[i].cdata[j]-network.node[i].prob[1])*(network.node[i].cdata[j]-network.node[i].prob[1]);
			}
       	  	network.node[i].prob[0]*=1.0/(double)(row-1);	
		  	network.node[i].prob[0]*=(double)(row-1.0)/(double)row;                    

     		}
	 } 

  fclose (pFile);
  }//end of else part for open input file

  pFile=fopen (argv[1],"r");

  for(i=0;i<network.num;i++)
      fscanf(pFile,"%s",name[i]);

  fclose(pFile);

  learn_node(&network);    //call function to learn network and get local score whell 0 parent for all nodes



  //printf("%.2lf\n",learn_parent(&network,6,176));
  // printf("%.2lf\n",learn_parent(&network,6,47));
  // printf("%.2lf\n",learn_parent(&network,6,18));
  //printf("%.2lf\n",learn_parent(&network,7,80));
  ////////printing trylist to a file////////////////////////////////

  //fprintf(outfile,"Number of nodes\n%d\n",network.num);
  //fprintf(outfile,"Possible sets of parents per node\n");
  j=(int)pow(2.0,network.nd-1);  //possible number of parents for discrete node
  k=(int)pow(2.0,network.num-1); //possible number of parents for continuous nodes


  for(i=0;i<network.num;i++)
  {
     network.node[i].score=(double *)malloc(sizeof(double)*(k+1));
     network.node[i].score_i=0;

 
     network.node[i].wlist.n=0;
     network.node[i].blist.n=0;
     network.node[i].blist.list=(int *)malloc(sizeof(int)*network.num);
     network.node[i].wlist.list=(int *)malloc(sizeof(int)*network.num);
    // if(network.node[i].type>1)  //discrete
      //     fprintf(outfile,"%d\t",j);
     //else   //continuous
       //    fprintf(outfile,"%d\t",k);
  }

  banf=fopen (argv[2],"r");  //ban list file name
  whitef=fopen (argv[3],"r");  //white list file name

  //read banlist
strcpy(nf,"");
strcpy(nt,"");

while (fgets (line,2500,banf)!=NULL){  
  
   fscanf(banf,"%s",nf);  //node from
   fscanf(banf,"%s",nt);  //node to
   
   for(i=0;i<network.num;i++)
   {
       if(strcmp(name[i],nt)==0)
       {
              j=network.node[i].blist.n;
              for(l=0;l<network.num;l++)
   		{
                    if(strcmp(name[l],nf)==0)
                    {
                        ban_flag=0;
                        for(k=0;k<network.node[i].blist.n;k++)
                             {    
                                 if(network.node[i].blist.list[k]==l)
                                  {           		   
                       		         ban_flag=1;
                          		         break;
                                  }
                             }
                        if(ban_flag==0) 
                        {
                             network.node[i].blist.list[j]=l;
                             j++;
                             network.node[i].blist.n=j;
                        }

                        
                    } 
              }
       }   
  }

}   



//read whitelist
strcpy(nf,"");
strcpy(nt,"");

while (fgets (line,2500,whitef)!=NULL){  

fscanf(whitef,"%s",nf);  //node from
fscanf(whitef,"%s",nt);  //node to

for(i=0;i<network.num;i++)
  {
       if(strcmp(name[i],nt)==0)
       {
              j=network.node[i].wlist.n;
              for(l=0;l<network.num;l++)
   		{
                    if(strcmp(name[l],nf)==0)
                    {
                         white_flag=0;
                        for(k=0;k<network.node[i].wlist.n;k++)
                             {    
                                 if(network.node[i].wlist.list[k]==l)
                                  {           		   
                       		         white_flag=1;
                          		         break;
                                  }
                             }
                             if(white_flag==0) 
                             {

                                 network.node[i].wlist.list[j]=l;
                                 j++;
                                 network.node[i].wlist.n=j;
                             }

 
                    } 
              }
       }   
  }

}   

  //fprintf(outfile,"\nChild	Parents	Score\n");

  m=(int)pow(2.0,network.num)-1; //number of combinations of parents
  min_score=1.0;
  for(i=0;i<network.num;i++)
  {
 
    if(network.node[i].wlist.n==0)
    {
            network.node[i].score[0]=network.node[i].score_zeroparent;
            if(min_score>network.node[i].score_zeroparent)
                min_score=network.node[i].score_zeroparent;

            //fprintf(outfile,"%d\t0\t%lf\n",i+1,network.node[i].score_zeroparent);  //printing score for zero parent 
    }
    else
    {
          network.node[i].score[0]=1.0;
          //fprintf(outfile,"%d\t0\t0.0\n",i+1);
          
     }
    network.node[i].score_i=1;


     for(j=0;j<m;j++)
     {   

     

         bit= (j+1 >> i) & 1;

         if(bit!=1)  //check if the child itself is present in parent list
         { 

          		bit_count=0;
               	//fprintf(outfile,"%d\t",i+1);
      			for(l=0;l<network.num;l++)
                     {
                       		bit= (j+1 >> l) & 1;
                           		if(bit==1)
                           		{
                                         	//fprintf(outfile,"%d",l+1);
                   				bit_count++;
 	            				
                                   }  

                     }


    	            if(network.node[i].type>1)  //if discrete node checck if any continuous node is included in the parent list
              	    {     
                  		flag=0;
                              ban_flag=0;

                  		for(l=0;l<network.num;l++)
                  		{
                        		bit= (j+1 >> l) & 1;
                        
	  	          		if(bit==1 && network.node[l].type==1) //check if continuous is parent of discrete
 	   	           		{   
                             		 flag=1;
                             		 break;
                         		}
	  	          		if(bit==1)
                                   { 
                                           for(k=0;k<network.node[i].blist.n;k++)
                                           {    
                                               if(network.node[i].blist.list[k]==l)
                                                {           		   
                             		         ban_flag=1;
                             		         break;
                                                }
                                           }


                                           if(ban_flag==1)
                                              break;
                         		}



                   		}
                   		if(flag==0)  //if no continuous parent for discrete node then learn the network with the list of parents  
                   		{

					if(bit_count>MAX_PARENT)
                                   {
                                              //fprintf(outfile,"\t0.0\n");   

                                              s_i=network.node[i].score_i;
                                              network.node[i].score[s_i]=1.0;
                                              network.node[i].score_i=s_i+1;



                                   }
                                   else if(ban_flag==1)
                                    {        
                                              //fprintf(outfile,"\t0.0\n");   
                                              s_i=network.node[i].score_i;
                                              network.node[i].score[s_i]=1.0;
                                              network.node[i].score_i=s_i+1;
                                    }
                                   else  // calculate score if white list check get satisfied
                         		{
                                          if(network.node[i].wlist.n==0)
                                           {  
                                              score_val=learn_parent(&network,i,j);

            					    if(min_score>score_val)
					                min_score=score_val;



                                              //fprintf(outfile,"\t%lf\n",score_val);   
                                              s_i=network.node[i].score_i;
                                              network.node[i].score[s_i]=score_val;
                                              network.node[i].score_i=s_i+1;
                                           }                                                 
                                          else if(bit_count<network.node[i].wlist.n)  //white list is greater than list of parents
                                          {
						    //fprintf(outfile,"\tfor white\t0.0\n"); 
                                              s_i=network.node[i].score_i;
                                              network.node[i].score[s_i]=1.0;
                                              network.node[i].score_i=s_i+1;
                                          }
                                          else
                                          {
                                                white_flag=0;  
                                                white_flag_all=0;  

     			                          for(l=0;l<network.num;l++)
                        		            {
                            	          	  bit= (j+1 >> l) & 1;

                            		         if(bit==1)
                                		         {
                                                        for(k=0;k<network.node[i].wlist.n;k++)
                                                        {     
                                                               if(network.node[i].wlist.list[k]==l)
                                                               {           		   
                             		                        white_flag=1;
                             		                        break;
                                                               }
                                                        }
                                                        if(white_flag!=1)
                                                        {    
                                                              white_flag_all=1;
                                                              break; 

                                          	        }  
                        
                                    	           }
                                                }
                                                if(white_flag_all==0)
                                       	      {
                                                      score_val=learn_parent(&network,i,j);
	             					     if(min_score>score_val)
					                        min_score=score_val;

                                                      //fprintf(outfile,"\t%lf\n",score_val);   
                                                      s_i=network.node[i].score_i;
                                                      network.node[i].score[s_i]=score_val;
                                                      network.node[i].score_i=s_i+1;
                                                }
                                                else
                                                { 
                                                     //fprintf(outfile,"\t0.0\n");  
	                                              s_i=network.node[i].score_i;
	                                              network.node[i].score[s_i]=1.0;
	                                              network.node[i].score_i=s_i+1;
							}

                                           } 
                                   }

                   		}
                            else  //fill up data when continuous is parent of discrete
                            {
					//fprintf(outfile,"\t0.0\n"); 
                                   s_i=network.node[i].score_i;
                                   network.node[i].score[s_i]=1.0;
                                   network.node[i].score_i=s_i+1;

                             }  
               	}
               	else  //if continuous node then learn the network with the parent set
               	{

                            ban_flag=0;

       
                   		for(l=0;l<network.num;l++)
                   		{
                       		bit= (j+1 >> l) & 1;
                       		if(bit==1)
                            	{

                                           for(k=0;k<network.node[i].blist.n;k++)
                                           {    
                                               if(network.node[i].blist.list[k]==l)
                                                {           		   
                             		         ban_flag=1;
                             		         break;
                                                }
                                           }
                                           if(ban_flag==1)
                                              break;
                         		}


                   		}
				if(bit_count>MAX_PARENT)
                            {
                                   //fprintf(outfile,"\t0.0\n"); 
                                   s_i=network.node[i].score_i;
                                   network.node[i].score[s_i]=1.0;
                                   network.node[i].score_i=s_i+1;
                            } 
                            else if(ban_flag==1)
                            {
					//fprintf(outfile,"\t0.0\n"); 
                                   s_i=network.node[i].score_i;
                                   network.node[i].score[s_i]=1.0;
                                   network.node[i].score_i=s_i+1;
				}				
                            else
                            {
                                          if(network.node[i].wlist.n==0)
                                      	 {
                                                      score_val=learn_parent(&network,i,j);
       	      					     if(min_score>score_val)
						                min_score=score_val;

                                                      //fprintf(outfile,"\t%lf\n",score_val);   
                                                      s_i=network.node[i].score_i;
                                                      network.node[i].score[s_i]=score_val;
                                                      network.node[i].score_i=s_i+1;
                                            }
                                          else if(bit_count<network.node[i].wlist.n)  //white list is greater than list of parents
                                          {
                                                     //fprintf(outfile,"\t0.0\n"); 
                                                     s_i=network.node[i].score_i;
                                                     network.node[i].score[s_i]=1.0;
                                                     network.node[i].score_i=s_i+1;
                                           } 
                                          else
                                          {
                                                white_flag=0;  
                                                white_flag_all=0;  

     			                          for(l=0;l<network.num;l++)
                        		            {
                            	          	  bit= (j+1 >> l) & 1;

                            		         if(bit==1)
                                		         {
                                                        for(k=0;k<network.node[i].wlist.n;k++)
                                                        {     
                                                               if(network.node[i].wlist.list[k]==l)
                                                               {           		   
                             		                        white_flag=1;
                             		                        break;
                                                               }
                                                        }
                                                        if(white_flag!=1)
                                                        {    
                                                              white_flag_all=1;
                                                              break; 

                                          	        }  
                        
                                    	           }
                                                }
                                                if(white_flag_all==0)
                                      	      {
                                                      score_val=learn_parent(&network,i,j);
            					            if(min_score>score_val)
					                       min_score=score_val;

                                                      //fprintf(outfile,"\t%lf\n",score_val);   
                                                      s_i=network.node[i].score_i;
                                                      network.node[i].score[s_i]=score_val;
                                                      network.node[i].score_i=s_i+1;
                                                }
                                                else
                                                {
       						    // fprintf(outfile,"\t0.0\n");  
                                              	     s_i=network.node[i].score_i;
	                                               network.node[i].score[s_i]=1.0;
	                                               network.node[i].score_i=s_i+1;
							}

                                           } 
                              }


                	}


           }  
       }
  }  


//print scores to separate files

min_score*=10;
inputFiles= (FILE **) malloc(network.num * sizeof(FILE*));


for(i=0;i<network.num;i++)
{
  strcpy(line,argv[5]);
  k=strlen(line);
  sprintf(line+k,"/%d",i); 

  inputFiles[i] = fopen(line, "wb");

  //print content  
  for(j=0;j<network.node[i].score_i;j++)
  {      
          if(network.node[i].score[j]<1.0)
            fwrite(&network.node[i].score[j],sizeof(double),1,inputFiles[i]); // fprintf(inputFiles[i],"%lf\n",network.node[i].score[j]);
          else 
            fwrite(&min_score,sizeof(double),1,inputFiles[i]); //fprintf(inputFiles[i],"%lf\n",min_score);
  }  

  fclose(inputFiles[i]);
}






fclose(banf);
fclose(whitef);


// printf("Time elapsed: %f\n", ((double)clock() - start) / CLOCKS_PER_SEC);

}


/* learn the network for zero parent */

void  learn_node(struct nd  *net)
{
  int i,j,k,nk,l,m,i1,j1;
  long num,temp;
  double n,N,s2;
  struct nd network=*net; //create a local copy
  double *alpha,nu,rho,mu,phi,tau;
  double post_nu,post_rho,post_mu,post_phi,post_tau;
  double loglik,post_loglik;
 
  num=network.num;

  nk=1;
  
  for(i=0;i<num;i++)
  {
     if(network.node[i].type>1)
        nk*=network.node[i].type;
  }
  
  	   


  for(i=0;i<num;i++)
  {	   
     if(network.node[i].type>1)
     {
	      	
	  alpha=(double *)malloc(sizeof(double)*network.node[i].type);
	  k=1;
			  
	   for(j=0;j<num;j++)
          {
          	  if(i!=j && network.node[j].type>1)
                 k*=network.node[j].type;
		          	
          }
          N=0.0; n=0.0;
	   for(j=0;j<network.node[i].type;j++)
          {
              alpha[j]=2.0*k;
              network.node[i].post_alpha[j]+=alpha[j];
          ////////update loglik from udisclik
              n+=alpha[j]; 
              N+=network.node[i].post_alpha[j];
          }
     
	  
         loglik=0.0;
         post_loglik=0.0;
  
     	  for(j=0;j<network.node[i].type;j++)
             post_loglik+=lgammafn(network.node[i].post_alpha[j])-lgammafn(alpha[j]);

          post_loglik+=lgammafn(n);
          post_loglik-=lgammafn(N);
 
     
	}
	else
	{
	    	nu=nk*2.0;
	    	tau=nu;
	    	rho=nk*2.0;	
		mu=network.node[i].prob[1];
		phi=network.node[i].prob[0]*nk;
		loglik=0.0;
              post_mu=mu;
              post_tau=tau;
              post_rho=rho;
              post_phi=phi;
	       post_loglik=loglik; 

              postc0(&post_mu,&post_tau,&post_rho,&post_phi,&post_loglik,network.node[i].cdata,&network.row);

     		    	
	}
      network.node[i].score_zeroparent=post_loglik; // store the zero parent score 
		  
   }
    	
   if(nk!=1)
     free(alpha);

   *net=network;  //coppied back to original	
}


/*
learn node for a list of parents
*/

double learn_parent(struct nd  *net,int child,int p)
{
  int i,j,jj,k,nk,l,m,i1,j1,k1,k2,cn_send;
  long num,temp;
  double n,N,s2;
  struct nd network=*net; //create a local copy
  int cn,ds,bit,*discrete,*continuous;
  double alpha,*postalpha,alpha_parent,*postalpha_parent,nu,rho,mu,phi,tau;
  double post_nu,post_rho,*post_mu,post_phi,post_tau;
  double loglik,post_loglik=0,post_loglik_parent=0;
  double *mu_mat,**tau_mat,tau11,tau12,tau22,div,phiinv;
  char **label; 
  int *a,*l_list,flag,y_count,z_count;
  double *y,mu_p,phi_p,*tau_send,*z_send,loglik_sum;



  num=network.num;

  continuous=(int *)malloc(sizeof(int)*network.nc);
  discrete=(int *)malloc(sizeof(int)*network.nd); 

  nk=1;
  for(i=0;i<num;i++)
  {
	if(network.node[i].type>1)
	    nk*=network.node[i].type;
  }
	   
//list of discrete snd continuous parents
  cn=0;
  ds=0;
  alpha_parent=0;
  alpha=2;
 
  k1=network.node[child].type;
  k2=1;
 
 //printf("%d\n",child);
  for(j=0;j<network.num;j++)   //list the continuous parents and discrete parents
	 {
	  	bit= (p+1 >> j) & 1;
              
              
	  	if(bit==1 && j!=child)
	  	{   
                 
	  	   if(network.node[j].type>1)
	  	    {
			   discrete[ds]=j;
                        //printf("discrete parent=%d\n",j);
                        k1*=network.node[j].type;
                        k2*=network.node[j].type;
     			   ds++;
	  	    }
	   	    else
	  	    {
			   continuous[cn]=j; 
                        //printf("continuous parent=%d\n",j);
			   cn++;
	  	    }	
	  	}
              else if(j!=child)
              {
                 alpha*=network.node[j].type;
              }  
	 }
    
       if(network.nd<=2)
          alpha=2; 

       i=child;   //i is the node with changes in its parrent nodes 
         
       if(network.node[i].type>1 && cn==0)  // no continuous node  //prepare alpha and post alpha and then calculate local score
	{   
         
	   k=k1;
      	   postalpha=(double *)calloc(network.node[i].type,sizeof(double));
  
          label=(char **)malloc(sizeof(char *)*(ds+1)); //label array
  	   a=(int *)calloc((ds+1),sizeof(int));
          l_list=(int *)malloc(sizeof(int)*(ds+1));

          for(j=0;j<=ds;j++)
              label[j]=(char *)malloc(sizeof(char)*100);
       	   
          for(j=0;j<ds;j++)
          {
               l=discrete[j];
               l_list[j]=network.node[l].type; 
              
          }
          l_list[ds]=network.node[i].type;
                   
          for(j=0;j<k;j++)                
          {
 	     
	             get_next_seq(a,l_list,ds+1);   // prepare a new combination of labels for parent
       	      for(i1=0;i1<ds;i1++)
             		{
                 		j1=a[i1];
                 		l=discrete[i1];
                 		strcpy(label[i1],network.node[l].label[j1]); 
                     }  
                     
             		j1=a[i1];
                     strcpy(label[i1],network.node[child].label[j1]); 
                     for(i1=0;i1<network.node[i].type;i1++)
               		postalpha[i1]=0;
             		for(i1=0;i1<network.row;i1++)  // search through parents data and count how many number of matches for current label list. prepare post alpha from count+alpha
             		{
                     	flag=0;
                     	for(m=0;m<ds;m++)
                     	{
                        		j1=a[m];
                       		 l=discrete[m];
                        		if(strcmp(label[m],network.node[l].ddata[i1])!=0)
                        		{
                          			flag=1;
                        		}      
                     	}  
                    		j1=a[m];
                    		if(strcmp(label[m],network.node[child].ddata[i1])!=0)
                        	{
                          		flag=1;
                        	}     
                   		if(flag==0)
                   		{
                        		j1=a[ds];
                        		postalpha[j1]+=1;

                   		}
             		}
             		for(m=0;m<network.node[i].type;m++)
                     {   
                             if(postalpha[m]!=0)
                             {
  	                        	postalpha[m]+=alpha;
                                   post_loglik+=lgammafn(postalpha[m])-lgammafn(alpha);
                             }
                     } 
          }

	
          k=k2;   // repeat the last step of preparing alpha and post alpha for only parent set. this time exclude the child node label from computation. prepare alpha_parent and post_alphaparent
	   l=discrete[ds-1];       
	   postalpha_parent=(double *)calloc(network.node[l].type,sizeof(double));

	   for(j=0;j<ds;j++)
	   {
	  	    l=discrete[j];
	      	    l_list[j]=network.node[l].type;
	           a[j]=0; 

	    }
           for(j=0;j<k1/k2;j++)
                 alpha_parent+=alpha; 
	    for(j=0;j<k;j++)
	    {
		    	get_next_seq(a,l_list,ds);

	      		for(i1=0;i1<ds;i1++)
			{
		  		j1=a[i1];
		  		l=discrete[i1];
		  		strcpy(label[i1],network.node[l].label[j1]);
                            
			}

	      		l=discrete[ds-1]; 
	      		for(i1=0;i1<network.node[l].type;i1++)
				postalpha_parent[i1]=0;
             
			for(i1=0;i1<network.row;i1++)
			{
		  		flag=0;
		  		for(m=0;m<ds;m++)
		    		{
		      			j1=a[m];
		      			l=discrete[m];
		      			if(strcmp(label[m],network.node[l].ddata[i1])!=0)
                        		{
                          			flag=1;
                        		}
		    		}
                    
		  		if(flag==0)
		    		{
		      			j1=a[ds-1];
		       		postalpha_parent[j1]+=1;

		    		}
			}
	      		l=discrete[ds-1];
	      		for(m=0;m<network.node[l].type;m++)
			{ 
                             if(postalpha_parent[m]!=0)
                              {  
                                    postalpha_parent[m]+=alpha_parent;
       	                      post_loglik_parent+=lgammafn(postalpha_parent[m])-lgammafn(alpha_parent);
                              }
			}
	    }

      
         loglik=post_loglik-post_loglik_parent;
	  free(postalpha);
         free(postalpha_parent);
         for(j=0;j<=ds;j++)
             free(label[j]);
	  free(label);
 	  free(a);
 	  free(l_list);

	}
       else if(network.node[i].type==1 && ds==0)  // no discrete node  //prepare continuous parameter set
	{
           z_count=0;
           tau_mat=(double **)malloc(sizeof(double *)*(cn+1));

           z_send=(double *)malloc(sizeof(double)*(cn+1)*network.row);
           tau_send=(double *)malloc(sizeof(double)*(cn+1)*(cn+1));
  
	    mu_mat=(double *)malloc(sizeof(double)*(cn+1));
           nu=nk*2.0;  rho=nk*2.0+cn; 
           mu_mat[0]=network.node[i].prob[1];
           for(j=0;j<cn;j++)
           {
              mu_mat[j+1]=0;
              tau_mat[j]=(double *)malloc(sizeof(double)*(cn+1));
 
           } 
         
           tau_mat[j]=(double *)malloc(sizeof(double)*(cn+1));

           for(j=0;j<network.row;j++)
           {
               z_send[z_count]=1;
               z_count++;
               for(j1=0;j1<cn;j1++)
               {
                  l=continuous[j1];
                  z_send[z_count]=network.node[l].cdata[j];   
                  z_count++;                                            
               }
                  
           }
           
             phi=network.node[i].prob[0]*nk;	
               
             //preparing tau
      
             //**tau_mat
               for(j=0;j<=cn;j++)
                     for(m=0;m<=cn;m++)
                     {
                      tau_mat[j][m]=1.0;
                     }
              
              for(j=0;j<cn;j++)
              {
                 l=continuous[j];
                 mu_p=network.node[l].prob[1];
                 phi_p=network.node[l].prob[0]*nk;
                 phiinv=1/phi_p; 
                 tau11 = 1/nu + mu_p*phiinv*mu_p;
                 tau22 = phiinv;
                 tau12 = -mu_p*phiinv;
                 div=tau11*tau22-tau12*tau12;
                 if(j==0) 
                    tau_mat[0][0]=tau22/div;
                 tau_mat[0][j+1]=-tau12/div;
                 tau_mat[j+1][0]=tau_mat[0][j+1];
                 tau_mat[j+1][j+1]=tau11/div;
              }

               for(j=0;j<=cn;j++)
                     for(m=0;m<=cn;m++)
                     {
                       if(tau_mat[j][m]==1.0)
                          { 
                                tau_mat[j][m]=tau_mat[0][j]*(tau_mat[0][m]/tau_mat[0][0]);
                               
                          }
                       j1=j*(cn+1)+m;
                       tau_send[j1]=tau_mat[j][m];
                      
                     }
                      
              loglik=0.0;
              
              cn_send=cn+1; 
              
              postc(mu_mat,tau_send,&rho,&phi,&loglik,network.node[i].cdata,z_send,&network.row,&cn_send);
             
 	
 		free(z_send);
 		free(tau_send);
              for(j=0;j<=cn;j++)
                 free(tau_mat[j]);
  		free(tau_mat);
 		free(mu_mat);
 		    	
	}
       else  //hybrid cases
       {
              if(cn==0)   //if parents are all discrete and child is continuous
              { 
   
                     ///////////////////prepare y array and run c code////////////////////////////////
              	loglik_sum=0;
              	k=k2;
      	       	y=(double *)calloc(network.row,sizeof(double));
              	label=(char **)malloc(sizeof(char *)*(ds));  //label array
  	       	a=(int *)calloc((ds+1),sizeof(int));
              	l_list=(int *)malloc(sizeof(int)*(ds));

          		for(j=0;j<ds;j++)
              		label[j]=(char *)malloc(sizeof(char)*100);
       	   
          		for(j=0;j<ds;j++)
          		{
              		l=discrete[j];
               		l_list[j]=network.node[l].type; 
              
          		}

                   
          		for(j=0;j<k;j++)                
              	{

                     	nu=(nk*2.0)/k2;
	    			tau=nu;
	    			rho=nu;	
				mu=network.node[i].prob[1];
				phi=network.node[i].prob[0]*nu/2;
				loglik=0.0;

 	               	get_next_seq(a,l_list,ds);   // prepare a new combination of labels for parent
       	      		for(i1=0;i1<ds;i1++)
             			{
                 			j1=a[i1];
                 			l=discrete[i1];
                 			strcpy(label[i1],network.node[l].label[j1]); 
                     	}  
             	       	y_count=0; 	
             			for(i1=0;i1<network.row;i1++)  // search through parents data and count how many number of matches for current label list. prepare post alpha from count+alpha
             			{
                     		flag=0;
                     		for(m=0;m<ds;m++)
                     		{
                        		
                       			l=discrete[m];
                        			if(strcmp(label[m],network.node[l].ddata[i1])!=0)
                        			{
                          				flag=1;
                        			}      
                     		}  
                    		     
                   			if(flag==0)
                   			{
                        			y[y_count]=network.node[i].cdata[i1];
                        			y_count++;
             
                   			}
             			}
          
             			postc0(&mu,&tau,&rho,&phi,&loglik,y,&y_count);
                     	loglik_sum+=loglik;
          

              	}

              	loglik=loglik_sum;
                      
                     free(y);
                     for(j=0;j<ds;j++)
                         free(label[j]);
			free(label);
			free(a);
			free(l_list);
          
 
              }//end of continuous node discrete parent check
              else //all the other hybrid cases when parents are both continuous and discrete 
              {
                     
                   
          	      tau_mat=(double **)malloc(sizeof(double *)*(cn+1));
		      z_send=(double *)malloc(sizeof(double)*(cn+1)*network.row);
         	      tau_send=(double *)malloc(sizeof(double)*(cn+1)*(cn+1));
	    	      mu_mat=(double *)malloc(sizeof(double)*(cn+1));
           	      
           	       for(j=0;j<cn;j++)
           		{
              	   tau_mat[j]=(double *)malloc(sizeof(double)*(cn+1));
 	              } 
         
      		       
           		tau_mat[j]=(double *)malloc(sizeof(double)*(cn+1));          

                     ///////////////////prepare parameters  and run c code////////////////////////////////
              	loglik_sum=0.0;
              	k=k2;
      	       	y=(double *)calloc(network.row,sizeof(double));
              	label=(char **)malloc(sizeof(char *)*(ds));  //label array
  	       	a=(int *)calloc((ds+1),sizeof(int));
              	l_list=(int *)malloc(sizeof(int)*(ds));

          		for(j=0;j<ds;j++)
              		label[j]=(char *)malloc(sizeof(char)*100);
       	   
          		for(j=0;j<ds;j++)
          		{
              		l=discrete[j];
               		l_list[j]=network.node[l].type; 
              
          		}

                   
          		for(jj=0;jj<k;jj++)                
              	{

                     	nu=(nk*2.0)/k2;
	      			rho=nu+cn;	
				mu_mat[0]=network.node[i].prob[1];

                            for(j1=0;j1<cn;j1++)
           		            mu_mat[j1+1]=0;

				phi=network.node[i].prob[0]*nu/2;
	                    //preparing tau
                   		//**tau_mat
             			for(j=0;j<=cn;j++)
                               for(m=0;m<=cn;m++)
                               {
                   			   tau_mat[j][m]=1.0;
                     	    }
             
              		for(j=0;j<cn;j++)
              		{
                 			l=continuous[j];
                 			mu_p=network.node[l].prob[1];
                 			phi_p=network.node[l].prob[0]*(nk/k2);
                
                 			phiinv=1/phi_p; 
                 			tau11 = 1/nu + mu_p*phiinv*mu_p;
                 			tau22 = phiinv;
                 			tau12 = -mu_p*phiinv;
                 			div=tau11*tau22-tau12*tau12;
                 			if(j==0) 
                  			    tau_mat[0][0]=tau22/div;
                 
			              tau_mat[0][j+1]=-tau12/div;
                 			tau_mat[j+1][0]=tau_mat[0][j+1];
                 			tau_mat[j+1][j+1]=tau11/div;
       
                
              		}
               		for(j=0;j<=cn;j++)
                    			 for(m=0;m<=cn;m++)
                    			 {
			                       if(tau_mat[j][m]==1.0)
                       		         { 
			                            tau_mat[j][m]=tau_mat[0][j]*(tau_mat[0][m]/tau_mat[0][0]);
                              		  }

                       			j1=j*(cn+1)+m;
                       			tau_send[j1]=tau_mat[j][m];
                                           
                     		   //printf("%.2lf\t",tau_mat[j][m]);
                                    }
                                    //printf("\n");
              	       loglik=0.0;
              
  	      
             		       cn_send=cn+1; 

 	               	
                            get_next_seq(a,l_list,ds);   // prepare a new combination of labels for parent
       	      		for(i1=0;i1<ds;i1++)
             			{
                 			j1=a[i1];
                 			l=discrete[i1];
                 			strcpy(label[i1],network.node[l].label[j1]); 
                     	}  
             	       	y_count=0;
                            z_count=0; 	
             			for(i1=0;i1<network.row;i1++)  // search through parents data and count how many number of matches for current label list. prepare post alpha from count+alpha
             			{
                     		flag=0;
                     		for(m=0;m<ds;m++)
                     		{
                        		
                       			l=discrete[m];
                        			if(strcmp(label[m],network.node[l].ddata[i1])!=0)
                        			{
                          				flag=1;
                        			}      
                     		}  
                    		     
                   			if(flag==0)
                   			{
                        			y[y_count]=network.node[i].cdata[i1];

                                          //prepare z send matrix
                                          z_send[z_count]=1;
                                          z_count++;
                                          for(j1=0;j1<cn;j1++)
                                          {
                 			          l=continuous[j1];
                 			          z_send[z_count]=network.node[l].cdata[i1];   
                                             z_count++;                                            
                                          }
                        			y_count++;
                   			}
             			}
          
                            postc(mu_mat,tau_send,&rho,&phi,&loglik,y,z_send,&y_count,&cn_send);
 
                     	loglik_sum+=loglik;
          

              	}

              	loglik=loglik_sum;
                        
                     free(y);
 			//free(z);
 			free(z_send);
 			free(tau_send);
                     for(j=0;j<=cn;j++)
                        free(tau_mat[j]);
          	       free(tau_mat);
 			free(mu_mat);
                     for(j=0;j<ds;j++)
                         free(label[j]);  
 			free(label);
 			free(a);
 			free(l_list);       

              }//end of else part for hybrid when parents are mixed       

       }//end of else part for hybrid
       
 free(continuous);
 free(discrete); 
 return loglik;

	
}

void get_next_seq(int *a,int *l,int size)
{
  int j,idx;
  idx=size-1;
  a[idx]+=1;
  if(a[idx]==l[idx])
  {
      a[idx]=0;
      for(j=idx-1;j>=0;j--){
        a[j]+=1;
        if(a[j]==l[j])
           a[j]=0;
        else
            break;
      }
  }
}


