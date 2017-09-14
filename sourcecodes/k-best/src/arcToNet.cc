#include <iostream>
#include <fstream>
#include <iomanip>
//#include "varpar.h"
#include <stdlib.h>
#include <math.h>
#include <list>
#include <stdio.h>
#include <vector>
//#include "cfg.h"
#include <string.h>
#include <cmath>
//HR
//#define EPSILON 0.001
#define EPSILON 0.00001


//HR: Add for hash_set
#if __GNUC__ < 3 && __GNUC__ >= 2 && __GNUC_MINOR__ >= 95
# include <hash_set>
# include <functional>
# define gnu_namespace std
#elif __GNUC__ >= 3
# include <ext/hash_set>
# if __GNUC_MINOR__ == 0
# include <functional>
# define gnu_namespace std
# else
# include <ext/functional>
# define gnu_namespace __gnu_cxx
# endif
#else
# include <hash_set.h>
# include <functional.h>
# define gnu_namespace std
#endif
using namespace gnu_namespace;
#include <set>
//


using namespace std;


//HR: add from Engine.h in BayesNw
#define MARK 9e99
#define LOG_ZERO -1e101
//#define LOGMINUS_NEW(logA, logB) if(logA == MARK) logA = logB; else if (logA == LOG_ZERO) logA = logB; else if (logB == LOG_ZERO) logA = logA; else logA = logA + log(1-exp(logB-logA));
//HR: add from UpdateHR.h in BayesNw to deal with the problem in sum = 0 in comp_post()
//call by reference to the original value
//the log sum result is stored in logA
//may change double into long double; no, use double
void logAddComp(double & logA, double & logB){
	    if(logA == MARK){
    		logA = logB;
    	}
    	else if (logA == LOG_ZERO){
    		logA = logB;
    	}
    	else if(logB == MARK){
    		logA = logA;
    	}
    	else if(logB == LOG_ZERO){
    		logA = logA;
    	}
    	//Nither logA nor logB is MARK/LOG_ZERO
    	else{
    		//1
    		if(logA < 0 && logB < 0 ){
    			logA = logA + log( 1 + exp(logB-logA) );
    		}
    		//2
    		else if(logA > 0 && logB > 0){
    			//always make exp(.): . > 0
    			//2.1
    			if(logA >= logB){
    				logA = -( -logA + log( 1 + exp(-logB+logA) ) );
    			}
    			//2.2
    			else{
    				logA = -( -logB + log( 1 + exp(-logA+logB) ) );
    			}
    			
    		}
    		//3
    		else if(logA < 0 && logB > 0){
    			//3.1
    			if(logA + logB > 0){   				
    				//logA = logA + log( 1 - exp(-logB-logA) );
    				logA = -logB + log( exp(logA + logB) - 1 );
    				
    			}
    			//3.2
    			else if(logA + logB < 0){
    				logA = -( logA + log( exp(-logB-logA)  - 1) );
    			}
    			//3.3
    			else{
    				logA = LOG_ZERO;
    			}
    		}
    		//4
    		else if(logA > 0 && logB < 0){
    			//4.1
    			if(logA + logB > 0){
    				logA = -logA + log( exp(logB+logA) - 1);
    			}
    			//4.2
    			else if(logA + logB < 0){
    				//logA = -( -logA + log(1 - exp(logB+logA)) );
    				logA = -( logB + log( exp(-logA-logB) - 1) );
    				
    			}
    			//4.3
    			else{
    				logA = LOG_ZERO;
    			}			
    		}
		
    	}
	
}


//HR: Add
//Will be used for checking edges-based feasure for Engineer in BayesNW
void arcToNet(char * inFeasFileName, char* outFeasFileName){

	cout << "inFeasFileName: " << inFeasFileName << endl;
	
	cout << "outFeasFileName: " << outFeasFileName << endl;
	
	int MAX_VAR_NUM = 32;
	
	int inFeatureEdges[MAX_VAR_NUM];
	for(int i = 0; i < MAX_VAR_NUM; i++){
		inFeatureEdges[i] = 0;
	}
	
	string str;
	str.assign(inFeasFileName);
	
  
	FILE* fp = fopen(str.c_str(), "r");

	int fromNode;
	int toNode;
	//int i=0;
	while(!feof(fp)){
		char c;
		fromNode = -1;
		toNode = -1;

		fscanf(fp,"%d",&fromNode);
		fscanf(fp,"%d",&toNode);
		fscanf(fp,"%c",&c);
		
		cout << "fromNode = "<< fromNode << endl;
		cout << "toNode = "<< toNode << endl;

		inFeatureEdges[toNode] += (1 << fromNode);


		
	}
	fclose(fp);
	int no_var = toNode;
	
	for(int i = 0; i < MAX_VAR_NUM; i++){
		cout << "inFeatureEdges[" << i << "] " << inFeatureEdges[i] << endl; 
	}
	
	
	int outFeatureEdges[MAX_VAR_NUM];
	for(int i = 0; i < MAX_VAR_NUM; i++){
		outFeatureEdges[i] = 0;
	}
	
	string str2;
	str2.assign(outFeasFileName);
	
  
	fp=fopen(str2.c_str(), "r");

	//int fromNode;
	//int toNode;
	//int i=0;
	while(!feof(fp)){
		char c;
		fromNode = -1;
		toNode = -1;

		fscanf(fp,"%d",&fromNode);
		fscanf(fp,"%d",&toNode);
		fscanf(fp,"%c",&c);
		
		cout << "fromNode = "<< fromNode << endl;
		cout << "toNode = "<< toNode << endl;

		outFeatureEdges[toNode] += (1 << fromNode);


		
	}
	fclose(fp);
	//int no_var = toNode;
	
	
	for(int i = 0; i < MAX_VAR_NUM; i++){
		cout << "outFeatureEdges[" << i << "] " << outFeatureEdges[i] << endl; 
	}
	
	
	
	
}

	

//HR:F
int main(int argc, char* argv[]){
	
	if (argc!=3){
    	cout << "Please type the correct format: arcToNet inFeatureFileName outFeatureFileName\n" << endl;
        return 1;
    }
      
     arcToNet(argv[1], argv[2]);
		
}
	


