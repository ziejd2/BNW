//HR: formetted only, no code change

#include <string>
#include <cmath>
#include <iostream>
#include "cfg.h"

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <list>
#define EPSILON 0.001


#include "files.h"

using namespace std;

int h;

//class for bparents
//HR:F
//HR: (variable set, score) pair
//This class is to store the best parent set and the corresponding score
class score_network{
   	public:
    	score_t scores;
		varset_t bsps;

       	score_network(score_t a,varset_t b){ 
        	scores=a;
          	bsps=b;
      	}

        score_network(){
          	scores=bsps=0;
       	}
     
      	friend int operator<(score_network s1,score_network s2);
      	friend int operator==(score_network s1,score_network s2);
      	friend int operator>(score_network s1,score_network s2);
      	friend bool same_obj(score_network s1, score_network s2);
         

};

//HR:F
/*================================================================*/

void print_queue(list<score_network>* v){

	list<score_network>::iterator iter;

	for(iter=v->begin();iter!=v->end();iter++){
		cout<<"    "<<(*iter).bsps<<","<<(*iter).scores;
	}
    
}
	
//HR:F
/*================================================================*/

int operator<(score_network s1,score_network s2){
 	if(fabs(s1.scores-s2.scores)<EPSILON){
     	return false;
   	}
    return(s1.scores<s2.scores);

}


int operator>(score_network s1,score_network s2){

	if(fabs(s1.scores-s2.scores)<EPSILON){
    	return false;
    }
    return(s1.scores>s2.scores);

}

int operator==(score_network s1,score_network s2){
	return(fabs(s1.scores-s2.scores)<EPSILON);

}


bool same_obj(score_network s1, score_network s2){
    return((s1==s2)&&(s1.bsps==s2.bsps));
    
}

/*================================================================*/
//HR:F
//To insert into the best parent set list
void insert_vec(list<score_network>* v,score_network s, int k){
  	list<score_network>::iterator it;

	if( s < v->back()){
		v->push_back(s);
   	}
    else{
      	for(it=v->begin();it!=v->end();it++){
			if(same_obj(s,*it)){
				break;
	        }
            else if(s>(*it)){
              	v->insert(it,s);
				break;
			}

		}
	}
     
	if(v->size()>k){
		v->pop_back();
  	}   

}

/*================================================================*/

void get_best_parents(int nof_vars, char* dirname, int k){
	varset_t nof_parsets = 1U<<(nof_vars-1);
	int i;
//		cout<<endl<<"Inside fn";
	list<score_network> v[nof_parsets];
    list<score_network>::iterator it;
	score_network s;
   	string str;
    char str1[40];
    
    //HR: For each var
  	for(i=0;i<nof_vars; ++i){
		/* READ SCORES FOR i */
		str.assign(dirname);
        str.append("/");
        sprintf(str1,"%d",i); 
        str.append(str1);
		//	cout<<str;
		FILE* fin = fopen(str.c_str(),"rb");

		//	cout<<fin;
		//	scanf("%d",&h);
		
		//HR: for all the possible parents 
		//Read local score and add it to the queue
		for(int j=0;j<nof_parsets;j++){
			fread(&s.scores, sizeof(score_t), 1, fin);
			//	cout<<s.scores;
			//	scanf("%d",&h);
			s.bsps = j;
			
			//HR: At this time, every queue has only one element s
			v[j].push_back(s);


		}
		fclose(fin);


		/* FIND BEST PARENTS AND THEIR SCORES IN EACH SET FOR i */

		varset_t ps;
		
		//HR: for each possible parent set cs 
		//For each possible parent set compare and  add to the list if it is within best k parent sets
		for(ps=0; ps<nof_parsets; ++ps){
			varset_t psel = 1U;

			//HR: psel represents the number equals the single element which is not in the ps
			//
			for(psel=1; psel<nof_parsets; psel <<= 1){
				if (ps & psel){
					
					//HR: use xor to get the subset whose size is 1 smaller
					varset_t subset = ps^psel;                                        
					for(it=v[subset].begin();it!=v[subset].end();it++){
						if(v[ps].size()<k){
							insert_vec(&v[ps],*it, k);

						}
						else if(v[ps].size()==k){
							if(((*it)<v[ps].back())||(*it)==v[ps].back()){
								break;
							}
							else{
								insert_vec(&v[ps], *it, k);


							}
						}
					}
				}
			}
		}



		/* WRITE BEST PARENTS FOR i */
		str.append(".kbps");
		FILE* fout = fopen(str.c_str(),"wb");
		double sentinel = 1000.0;
		//printf("nofparset: %u",nof_parsets);				
		for(int j=0;j<nof_parsets;j++){
			//	int g=0;
			//	print_queue(&v[j]);
			//	printf("\n");
			for(it=v[j].begin();it!=v[j].end();it++){
				//	g++;
				//fwrite(&v[j]
				fwrite(&(it->scores), sizeof(double), 1, fout);
				fwrite(&(it->bsps),sizeof(unsigned int),1,fout);
				//print_queue(&v[j]);
				//printf("\n");
				//fprintf(fout,"\n");
			}
						
			fwrite(&sentinel,sizeof(double),1,fout);
			v[j].clear();
							
		}
		//printf("\n");
		fclose(fout);
				
	}// endfor(i=0;i<nof_vars; ++i)
	
}

/*================================================================*/

//HR: F
int main(int argc, char* argv[]){
	
	//    cout<<endl<<"Get K Best Parent sets"<<endl;      
   	if (argc!=4){
      	fprintf(stderr, "Usage: best_parents nof_vars dirname k\n");
       	return 1;
  	}
      
	char str[32];
	int nof_vars = atoi(argv[1]);
   	int k = atoi(argv[3]);
      
   	//strcpy(str,argv[2]);
      
   	get_best_parents(nof_vars, argv[2],k);
  
   	return 0;
}
