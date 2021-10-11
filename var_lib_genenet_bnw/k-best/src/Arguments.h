#ifndef ARGUMENTS_H
#define ARGUMENTS_H

#include<stdio.h>
#include<stdlib.h>

using namespace std;

class Arguments {
public:
	static char* datafile;
	static char* layeringfile;
	static char* maxindegree;
	static char* model;
	static char* task;
	static char* maxnumrecords;
	
	//HR: Add for in_out features
	static char* inFeasFileName;
	static char* outFeasFileName;
	//
	
	//HR: Add for in_out features
	static int option;
	//
	
	//HR: Add for ADtree option
	static int ADtree;
	//
	
	//HR: Add for Topk dir option
	static char* directoryName;
	//



	static void init(int argc, char **args){
		for(int i = 1; i < argc; i ++){
			
			if(args[i][0]=='-' && args[i][1]=='d' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::datafile = args[j];
					j ++;
				} 
			}
			else if(args[i][0]=='-' && args[i][1]=='l' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::layeringfile = args[j]; 
					j ++;
				}
			}
			else if(args[i][0]=='-' && args[i][1]=='m' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::maxindegree = args[j]; 
					j ++;
				}
			}
			else if(args[i][0]=='-' && args[i][1]=='u' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::maxnumrecords = args[j]; 
					j ++;
				}
			}
			else if(args[i][0]=='-' && args[i][1]=='M' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::model = args[j]; 
					j ++;
				}
			}
			else if(args[i][0]=='-' && args[i][1]=='T' && args[i][2]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::task = args[j]; 
					j ++;
				}
			}
			//HR: Add for in_out feature
			else if(args[i][0]=='-' && args[i][1]=='i' && args[i][2]=='n' && args[i][3]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::inFeasFileName = args[j]; 
					j ++;
				}
			}
			else if(args[i][0]=='-' && args[i][1]=='o' && args[i][2]=='u' && args[i][3]=='t' && args[i][4]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::outFeasFileName = args[j]; 
					j ++;
				}
			}
			//
			//HR: Add for in_out feature
			else if(args[i][0]=='-' && args[i][1]=='o' && args[i][2]=='p' && args[i][3]=='t' && args[i][4]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::option = atoi(args[j]); 
					j ++;
				}
			}
			//
			//HR: Add for ADtree feature
			else if(args[i][0]=='-' && args[i][1]=='a' && args[i][2]=='d' && args[i][3]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::ADtree = atoi(args[j]); 
					j ++;
				}
			}
			//
			//HR: Add for Topk dir
			else if(args[i][0]=='-' && args[i][1]=='d' && args[i][2]=='i' && args[i][3]=='r' && args[i][4]=='\0'){
				int j = i + 1;
				while (j < argc && args[j][0]!='-') {
					Arguments::directoryName = args[j];
					j++;
				}
			}
			//
		}
	
		print_arguments(stderr);
		
	}
	static void print_arguments(FILE *f){
		fprintf(f, " -d Data file:\n");
		fprintf(f, "   %62s\n", Arguments::datafile);	
		//fprintf(f, " -l Layering file:\n");
		//fprintf(f, "   %62s\n", Arguments::layeringfile);
		fprintf(f, " -m Maximum indegree:\n");
		fprintf(f, "   %62s\n", Arguments::maxindegree);
		fprintf(f, " -u Maximum number of data records read:\n");
		fprintf(f, "   %62s\n", Arguments::maxnumrecords);
		//fprintf(f, " -M Model:\n");
		//fprintf(f, "   %62s\n", Arguments::model);
		//fprintf(f, " -T Task (Infer=I, Generate=G):\n");
		//fprintf(f, "   %62s\n", Arguments::task);
		
		//HR: Add for in_out
		fprintf(f, " -opt Option:\n");
		fprintf(f, "   %62d\n", Arguments::option);
		if(Arguments::option == 5){
			fprintf(f, " -in In_Feature File:\n");
			fprintf(f, "   %62s\n", Arguments::inFeasFileName);
			fprintf(f, " -out Out_Feature File:\n");
			fprintf(f, "   %62s\n", Arguments::outFeasFileName);
		}
		//
		//HR: Add for ADtree
		fprintf(f, " -ad ADtree:\n");
		fprintf(f, "   %62d\n", Arguments::ADtree);
		//
		//HR: Add for Topk dir
		fprintf(f, " -dir directory name:\n");
		fprintf(f, "   %62s\n", Arguments::directoryName);
		//
	}
};

char* Arguments::datafile = "testdata.dat";
char* Arguments::layeringfile = "%";
char* Arguments::maxindegree = "3";
char* Arguments::model = "M";
char* Arguments::task = "I";
char* Arguments::maxnumrecords = "999999";


//HR: For test
//char* Arguments::datafile = "cases/iris.idt";
//char* Arguments::layeringfile = "%";
//char* Arguments::maxindegree = "4";
//char* Arguments::model = "M";
//char* Arguments::task = "I";
//char* Arguments::maxnumrecords = "150";


//HR: Add for in_out
char* Arguments::inFeasFileName = "inFeature.txt";
char* Arguments::outFeasFileName = "outFeature.txt";
int Arguments::option = 5;

//HR: Add for ADtree
int Arguments::ADtree = 0;


//HR: Add for Topk dir option
char* Arguments::directoryName = "./";
//

#endif
