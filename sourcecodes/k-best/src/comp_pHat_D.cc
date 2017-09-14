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
//Update from comp_post
void comp_pHat(){
	//double logSum = -450.491; //iris	
	double logSum = -9418.56; //tic
	
	//double logSum = -637.91; //zoo
	
	cerr << "logSum = " << logSum << endl;
	
	//double truelogP = -450.491; //iris
	double truelogP = -9418.29; //tic
	
	//double truelogP = -619.502; //zoo
	
	cerr << "truelogP = " << truelogP << endl;
	
	int sampleSize = 1000;
	
	long double post[sampleSize];
	double relaLogScore[sampleSize];
	
	double sumPost[sampleSize];

	string str;
	char str1[32];

	FILE *fp = fopen("netpost" ,"r");
	int i = 0;
	
	
		

	while(!feof(fp) && i < sampleSize){
			char c;
			fscanf(fp,"%Lg",&post[i]);
			//cout << " post[" << i << "] = " << post[i] << endl;
			fscanf(fp,"%c",&c);		
			i++;
	}
	
	for(int j = 0; j < sampleSize; j++){
		relaLogScore[j] = log(post[j]) + logSum;
		//cout << " relaLogScore[" << i << "] = " << relaLogScore[j] << endl;
	}
	//cout << " relaLogScore[" << 1 << "] = " << relaLogScore[1] << endl;
	//cout << " relaLogScore[" << 2 << "] = " << relaLogScore[2] << endl;
	
	for(int j = 0; j < sampleSize; j++){
		sumPost[j] = LOG_ZERO;
	}
	
	for(int j = 0; j < sampleSize; j++){
		for(int k = 0; k <= j; k++){
			logAddComp(sumPost[j], relaLogScore[k]);
		}
		//cout << " sumPost[" << j << "] = " << sumPost[j] << endl;
	}
	
	double DRatio[sampleSize];
	
	
	for(int j = 0; j < sampleSize; j++){	
		DRatio[j] =  exp( sumPost[j] - truelogP ) * 100;
		//cout << "" << DRatio[j] << endl;	
	}
	
	double GRatio[sampleSize];
	for(int j = 0; j < sampleSize; j++){	
		GRatio[j] =  post[0]/post[j];
		//cout << "" << GRatio[j] << endl;	
	}
	
	cout << "\n\nDRatio\t GRatio" << endl;
	for(int j = 0; j < sampleSize; j++){	
		cout << "" << DRatio[j] << "\t " << GRatio[j] << endl;	
	}
}

	

//HR:F
int main(int argc, char* argv[]){
	comp_pHat();
		
}
	


