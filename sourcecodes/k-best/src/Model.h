#ifndef MODEL_H
#define MODEL_H

#include<stdio.h>
#include<stdlib.h>

#include<iostream>
#include<vector>
#include<deque>
#include<string>
#include<fstream>
#include<math.h>

#include"Arguments.h"

using namespace std;

#include"UpdateHR2.h"

#define MAX_COUNT 1000
#define MAX_RQ 8*10000
static double lr[MAX_RQ][MAX_COUNT]; 
static double lr2[MAX_RQ][MAX_COUNT]; 

//ofstream totalOf1("debugModelTotal1.txt");

double log_gammaratio(int a, int z){
	if(z >= MAX_COUNT  || a >= MAX_RQ){
//		if(z >= MAX_COUNT){
//			cerr << " Counts larger than " << MAX_COUNT << " occured. " << endl;
//			cerr << " Increase MAX_COUNT. Exit now.\n"; 
//			
//		}
//		else if(a >= MAX_RQ ){
//			cerr << " a larger than " << MAX_RQ << " occured. " << endl;
//			cerr << " Increase MAX_RQ. Exit now.\n";  
//		} 
//		
//		exit(1); 		

		double log_gam_res = 0;
		//seperate 2 cases:
		//if only z >= MAX_COUNT but a < MAX_RQ
		if(a < MAX_RQ){
			//can compute it faster based on lr
			log_gam_res += lr[a][MAX_COUNT - 1];
			for(int k = MAX_COUNT - 1; k <= z-1; k++){
				log_gam_res += log(1.0/a + k);
			}
			
		}
		//o.w.: start from the beginning
		else{
			for(int k = 0; k <= z-1; k++){
				log_gam_res += log(1.0/a + k);
			}
		}

//		totalOf1 << "log_gam_res = "  << log_gam_res << endl << endl;
		return log_gam_res;

	}
	else if (z < MAX_COUNT && a > 0){
		return lr[a][z];
	}
	else if (z < MAX_COUNT && a < 0){
		return lr2[-a][z];
	}
	else{
		cerr << "Error: a = 0" << endl;
		exit(1); 
	}
}


double log_gammaratio(double z){
	return 0.50 * log(M_PI) + (z - 0.50) * log(z) - z + 1.0 / (12 * z);  
}

template<class T> void printvec(ostream& f, const vector<T>& v){
	int size= v.size();
	int i;
	for(i=0; i<size; i++){
		f<<v.at(i)<<" ";
	}
	f<<endl;
}
template<class T> void printvecs(ostream& f, const vector<vector<T> >& v){
	int size= v.size();
	int i;
	for(i=0; i<size; i++){
		printvec(f, v.at(i));
	}
}


void print_nodes(ostream& f, int *V, int n){
	for (int j = 0; j < n; j ++){
		f<<" "<<V[j];
	}
}

void print_nodes(ostream& f, int T, int *V, int n){
	int j = 0; 
	while (T > 0){           
		if (T & 1){
			f<<" "<<V[j];
		}
		j ++;
		
		T >>= 1; 
	}
}
void print_set(ostream& f, int T){
	int j = 0; 
	while (T > 0){
		if (T & 1){
			f<<" "<<j;
		}
		j ++;
		T >>= 1;
	}
}


// Reads and stores data.
class Data{
public:
	Data(){}
	~Data(){}
	void init(){
		read_data();
		downcode();
	}
	void read_data(){
		ifstream ifs(Arguments::datafile, ios::in);
		if (!ifs){
			fprintf(stderr, " Cannot read file %s.\n", Arguments::datafile);
			exit(1);
		}
		fprintf(stderr, " Reading file %s...\n", Arguments::datafile);
		
		char* buffer= new char[10000];
		ifs.getline(buffer, 10000);
		char* pch= strtok(buffer,", \t");
		while (pch != NULL){
			string tempstring(pch);
			//cerr << "Node = "<< tempstring << endl;
			heads.push_back(tempstring);
			pch = strtok(NULL, ", \t");
        }
		numattributes = heads.size();
	
		fprintf(stderr, " Heading read: %d attributes.\n", numattributes);
	
		dm.clear();
		vector<int> temp;
		while (true){
			temp.clear();
			ifs.getline(buffer, 10000);
			pch = strtok(buffer,", \t");
			while (pch != NULL){
				temp.push_back(atoi(pch));
				pch = strtok(NULL, ", \t");
			}
			if ((int)temp.size() != numattributes) break;
			
			dm.push_back(temp);
			
			if ((int)dm.size() >= atoi(Arguments::maxnumrecords)) break;
		}
		numrecords = dm.size();
		fprintf(stderr, " Data read: %d lines.\n", numrecords); 
		delete [] buffer;
	}
	void downcode(){
		//fprintf(stderr, "downcode() starts. \n"); 
		
		int inuse[4096][256];
		for (int v = 0; v < 255; v ++){
			for (int i = 0; i < numattributes; i ++){
				inuse[v][i] = -1;
			}
		}
		//Purpose: For each i attribute, 
		for (int t = 0; t < numrecords; t ++){
			for (int i = 0; i < numattributes; i ++){
				inuse[dm[t][i]][i] = 1;
			}
		}

		//ofstream of1("debugDowncode.txt");
		//of1 << "\n inuse check" << endl;
		for (int v = 0; v < 30; v ++){			

			
//			for (int i = 0; i < numattributes; i++){
//				of1 << " inuse[" << v << "][" << i << "] =" << inuse[v][i] << endl;
//			}
//			of1 << "\n";
			

         	/*
         	FILE * fp1 = fopen("debug1.txt", "w");
          	fprintf(fp1, "\n inuse check\n");
          	for (int i = 0; i < numattributes; i++){
				fprintf(fp1, " inuse[%d][%d] = %d\n", v, i, inuse[v][i]);
			}
                  fclose(fp1);
 			*/
                  

			/*
      		fprintf(stderr, "\n inuse check\n");
           	for (int i = 0; i < numattributes; i++){    	
				fprintf(stderr, " inuse[%d][%d]=%d\n", v, i, inuse[v][i]);
          	}
			fprintf(stderr, "\n");
			*/
		}

		arities.clear();

		maxarity = 0;

		for (int i = 0; i < numattributes; i ++){
			int count = 0;
			for (int v = 0; v < 255; v ++){
				if (inuse[v][i] == 1){
					inuse[v][i] = count ++;
				}
			}
			arities.push_back(count);	
			if (count > maxarity) maxarity = count;
		}

//      of1 << "maxarity = " << maxarity << endl;
//		printvec(of1, arities);
		
		for (int t = 0; t < numrecords; t ++){
			for (int i = 0; i < numattributes; i ++){
				dm[t][i] = inuse[dm[t][i]][i];

			}
		}
	}//end downcode()
	
	
	int get_index(string s){
		int i = 0;
		while (i < (int)heads.size() && heads[i] != s){ i ++; }
		return i;
	}
	void print_data(ostream & f){
		for (int i = 0; i < numattributes; i ++){
			f << " " << heads[i];
		}
		f << endl; 
		for (int t = 0; t < numrecords; t ++){
			for (int i = 0; i < numattributes; i ++){
				f << " " << dm[t][i];
			}
			f << endl; 
		}
	}
		
	vector<string> heads;

	vector< vector<int> > dm;

	vector<int> arities;

	int numattributes;

	int numrecords;

	int maxarity;
};


// NOTE: We assume that children come first, that is, 
// V[0] is the grandest child and V[n-1] is the grandest parent.
// Layers respectively from 0 to numlayers.
// Note that this is reverse to the input ordering.
class Layering{
public:
	Layering(){}
	~Layering(){ delete [] V;}

	void init(Data & data){
		set_layers(data);
		
		//print_layers();
	
	}
	void set_layers(Data & data){
		
//		ofstream of1("debugSet_layers.txt");
	
		V = new int[data.numattributes];
		int j = 0;
		cnh.clear(); nh.clear(); cnh.clear();
		
		if (Arguments::layeringfile[0] == '%'){
			numlayers = 1;
			int layersize = data.numattributes;			
			nh.push_back(layersize);
			cnh.push_back(j);
			
			for (int l = 0; l < layersize; l ++){
				int i = j;
				V[j++] = i;
			}
			
			edgeswithin.clear();
			edgeswithin.push_back(true);
        	
			return;
		}
	
		parselayeringfile(Arguments::layeringfile, true);
		numlayers = nodesinlayers.size();
	
		for (int h = numlayers-1; h >= 0; h --){
			int layersize = nodesinlayers[h].size();
			nh.push_back(layersize);
			cnh.push_back(j);
			for (int l = 0; l < layersize; l ++){
				int i = data.get_index(nodesinlayers[h][l]);
				V[j++] = i;
			}
		}
	
	}
	void print_layers(){
		for (int h = 0; h < numlayers; h ++){
			cerr<<"Layer "<<h<<":";
			for (int j = 0; j < nh[h]; j ++){
				cerr<<" "<<V[cnh[h] + j];
			}
			cerr<<endl;
		}
	}
	bool edges_within(int h){
		return edgeswithin[numlayers - h - 1];
	}	
	void parselayeringfile(char *layerfilename, bool verbose){
	
		ifstream ifs(layerfilename, ios::in);
		if( !ifs){
			cerr<<"ERROR: The file '"<<layerfilename<<"' could not be opened\n";
			cerr<<"Exiting\n";
			exit(1);
		}
		if(verbose){
			cerr<<"Reading layer file: '"<<layerfilename<<"'\n";
		}

		char* buffer= new char[10000];
		char* pch;
		int i;
		char tempchar;
		while(true){
			ifs.getline(buffer,10000);
			pch = strtok(buffer, ", \t");
			vector<string> tempstringvec;	//will hold the tokenized line
			while(pch != NULL){
				string tempstring(pch);
				tempstringvec.push_back(tempstring);
				pch = strtok(NULL, ", \t");
			}

			if(tempstringvec.size() < 3){
				cerr<<"   ERROR: in reading file '"<<layerfilename<<"'"<<endl;
				cerr<<"          each line must be of the form:"<<endl;
				cerr<<"          layername\t1/0\tn1 ... nn"<<endl;
				cerr<<"          EXITING"<<endl;
				exit(1);
			}

			layernames.push_back(tempstringvec.at(0));
			if(tempstringvec.at(1) == "0"){
				edgeswithin.push_back(0);
			}
			else if( tempstringvec.at(1) == "1"){
				edgeswithin.push_back(1);
			}
			else{
				cerr<<"   ERROR: in reading file '"<<layerfilename<<"'"<<endl;
				cerr<<"          the values in the second column can either be '1' or '0'"<<endl;
				cerr<<"          EXITING"<<endl;
				exit(1);
			}

			vector<string> tempnodesinlayer;
			for(i = 2; i < (int)tempstringvec.size();i ++){
				tempnodesinlayer.push_back(tempstringvec.at(i));
			}
			nodesinlayers.push_back(tempnodesinlayer);

			ifs>>tempchar;
			ifs.putback(tempchar);
			if(ifs.eof()){
				break;
			}
		}
		delete [] buffer;
		buffer = 0;
		pch = 0;
		cerr.width(13);
		cerr<<"Layer Name";
		cerr.width(24);
		cerr<<"Edges Within Allowed";
		cerr.width(14);
		cerr<<"Node Names"<<endl;
		for(i = 0; i < (int)layernames.size(); i ++){
			cerr.width(13);
			cerr<<layernames.at(i);
			cerr.width(15);
			if(edgeswithin.at(i)){
				cerr<<"true";
			}
			else{
				cerr<<"false";
			}
			cerr<<"             ";
			printvec(cerr, nodesinlayers.at(i));
		}
	}


	vector<string> layernames; 
	vector<bool> edgeswithin;
	vector<vector<string> > nodesinlayers;
	int *V;
	int numlayers;
	vector<int> nh;
	vector<int> cnh;
};



// Blocks for computing sufficient statistics.
class Tnode{
public:
	double pseudocount;
	short int arity;
	short int count;
	short int depth;
	Tnode** children;
	
	Tnode(const int a, const int d){
		pseudocount = 1;
		arity = a;
		count = 0;
		depth = d;
		children = new Tnode*[arity];
		for(int v = 0; v < arity; v ++){
			children[v] = NULL;
		}
	}
	~Tnode(){
		for(int v = 0; v < arity; v ++){
			if (children[v] != NULL){
				delete children[v];
			}
		}
		delete [] children;
		//free_memory();
	}
	void free_memory(){
		//cerr<<" Deleting d="<<depth<<" c="<<count<<endl;
		
		for(int v = 0; v < arity; v ++){
			if (children[v] != NULL){
				children[v]->free_memory();
				delete children[v];
			}
		}
		//delete [] children;
	}
	// Note: Actually S is not needed.
	void update(vector<int> & S, vector<int> & u){
		if (u.size() > 0){	
			//int j = S[u.size()-1];			
			int v = u[u.size()-1]; 
			
			u.pop_back();
			if (children[v] == NULL){
				//cerr<<" Creating d="<<depth<<" c="<<count<<endl;
				
				children[v] = new Tnode(arity, depth+1);
			}
			children[v]->update(S, u);
		}
		count ++;
	}
	void print(ostream & f){
		f << "(" << count << ")" << endl;
		for (int v = 0; v < arity; v ++){
			if (children[v] != NULL){
				for (int d = 0; d < depth; d ++) f << " "; 
				f << " " << v << " ";
				children[v]->print(f);
			}
		}
	}
	
	double evaluate(int d, int a, int r){
		double sum = 0;
		
		//if no parent, directly log[gamma(1+count) / gamma(1)  ] (1/a = 1/1 = 1)
		if (depth == d){
				sum = log_gammaratio(a, count);
				//cerr << "count1: " << count << endl;

		}
		else if (depth <= d - 1){
			for (int v = 0; v < arity; v ++){
				if (children[v] != NULL){
					double value = children[v]->evaluate(d, a, r);
					sum += value;
				}
			}
			if (depth == d - 1){
				sum -= log_gammaratio(r, count);
				//cerr << "count2: " << count << endl;

			}
		}
		return sum;
	}
	

	double evaluateHR(int d, int alpha_v_pa, int r){
		//cerr << "start: root.evaluateHR(d ="
		//<< d << ", alpha_v_pa=" << alpha_v_pa << ", r=" << r << ")" << endl;
		double sum = 0;
		
		//if no parent, directly log[gamma(1+count) / gamma(1)  ] (1/a = 1/1 = 1)
		if (depth == d){
				//sum = log_gammaratio(a, count);
				sum = log_gammaratio(alpha_v_pa, count);				
				//cerr << "count1: " << count << endl;

		}
		else if (depth <= d - 1){
			for (int v = 0; v < arity; v ++){
				if (children[v] != NULL){
					double value = children[v]->evaluateHR(d, alpha_v_pa, r);
					sum += value;
				}
			}
			if (depth == d - 1){
				int alpha_pa = alpha_v_pa / r;

				sum -= log_gammaratio(alpha_pa, count);
//				totalOf1 << "\nalpha_pa: " << alpha_pa << endl;
//				totalOf1 << "count2: " << count << endl;
//				totalOf1 << "-log_gammaratio(alpha_pa, count) = " << -log_gammaratio(alpha_pa, count) << endl;
				//cerr << "count2: " << count << endl;
				
			}
		}
		return sum;
	}
	
};


class Model{
public:
	Model(){}
	~Model(){}

	void makeADTree(vector< vector<int> > & dm, vector<int> & arities){
		//Create dmIndex from dm, dmIndex stroes {0, 1, R-1}
		vector<int> dmIndex;
//		cout << "dm.size() = " << dm.size() << endl;
		for(int i = 0; i < (int) dm.size(); i++){
			dmIndex.push_back(i);
		}
//		cout << "dmIndex:" << endl;
//		for(int i = 0; i < (int) dm.size(); i++){
//			cout << "dmIndex[" << i << "] = "<< dmIndex[i] << endl;
//		}
		
		//  M: the num of attr
//		int M = dm[0].size();
//		cout << "M = " << M << endl;
		
		//cout << "Start MakeADTree()" << endl;
		//ADTreeNode * ADTreeNodeP1 = MakeADTree(0, dmIndex, dm, arities);
		ADTreeNodeP1 = MakeADTree(0, dmIndex, dm, arities);
   		//cout << "End MakeADTree()" << endl;

		//cout << "Start PrintADTreeNode()" << endl;
		//PrintADTreeNode(ADTreeNodeP1, 0, M, arities);
		//cout << "End PrintADTreeNode()" << endl;
		
	}

	void makeADTree(){
		makeADTree(data.dm, data.arities);
	}

	void freeADTree(vector< vector<int> > & dm, vector<int> & arities){
		int M = dm[0].size();
		//cout << "M = " << M << endl;
		//cout << "Start FreeADTreeNode()" << endl;
		FreeADTreeNode(ADTreeNodeP1, M, arities);
		//cout << "End FreeADTreeNode()" << endl;
		
	}
	
	void freeADTree(){
		freeADTree(data.dm, data.arities);
	}
	

	void MakeADContiTable(vector< vector<int> > & dm, vector<int> & arities){
		cout << "\nStart MakeADContiTable(vector< vector<int> > & dm, vector<int> & arities)" << endl;
		//  M: the num of attr
		int M = dm[0].size();
		cout << "M = " << M << endl;
		
		deque<int> inquiryAttrs;
	
		for(int i = 0; i < (int) inquiryAttrs.size(); i++){
			cout << "inquiryAttrs[" << i << "] = " << inquiryAttrs[i] << endl;
		}
		
		vector<int> givenAttrs;
		
		vector<int> givenVals;
			
		cout << "Call MakeContab() " << endl;
		CondiContiTable resultTable = MakeContab(inquiryAttrs, ADTreeNodeP1, givenAttrs, givenVals, arities);
		cout << "Return MakeContab() " << endl;
		for(unsigned int i = 0; i < resultTable.contiTableCounts.size(); i++){
			cout << "resultTable.contiTableCounts[" << i << "] = " << resultTable.contiTableCounts[i] << endl; 
		}
		
		cout << "End MakeADContiTable(vector< vector<int> > & dm, vector<int> & arities)" << endl;

		
	} //end void MakeADContiTable()
	
	void MakeADContiTable(){
		MakeADContiTable(data.dm, data.arities);
	}
	
	vector<int> MakeADContiTable(deque<int> inquiryAttrs, vector< vector<int> > & dm, vector<int> & arities){
		//cout << "\nStart MakeADContiTable(vector< vector<int> > & dm, vector<int> & arities)" << endl;
		//  M: the num of attr
		//int M = dm[0].size();
		//cout << "M = " << M << endl;
		
		//deque<int> inquiryAttrs;
		
		//Full inquiry
//		for(int i = 0; i < M; i++){
//			inquiryAttrs.push_back(i);
//		}
		
		
//		for(int i = 0; i < (int) inquiryAttrs.size(); i++){
//			cout << "inquiryAttrs[" << i << "] = " << inquiryAttrs[i] << endl;
//		}
		
		vector<int> givenAttrs;
		
		vector<int> givenVals;
			
		//cout << "Call MakeContab() " << endl;
		CondiContiTable resultTable = MakeContab(inquiryAttrs, ADTreeNodeP1, givenAttrs, givenVals, arities);
		//cout << "Return MakeContab() " << endl;
//		
//		for(unsigned int i = 0; i < resultTable.contiTableCounts.size(); i++){
//			cout << "resultTable.contiTableCounts[" << i << "] = " << resultTable.contiTableCounts[i] << endl; 
//		}
//		
//		cout << "End MakeADContiTable(vector< vector<int> > & dm, vector<int> & arities)" << endl;
		return resultTable.contiTableCounts;
		
	} //end vector<int> MakeADContiTable()
	

	
	void init(){
		data.init();
		m = data.numrecords;
		n = data.numattributes;

		layering.init(data);
			
		for (int a = 1; a < MAX_RQ; a ++){
			double aa = 1.0 / a;
			lr[a][0] = 0; lr2[a][0] = 0;
			
			for (int k = 1; k < MAX_COUNT; k ++){
				lr[a][k] = lr[a][k-1] + log(aa + k - 1);
				lr2[a][k] = lr2[a][k-1] + log(a + k - 1);
			}
		}
	
	}
	
	
	
	
	
	
	
	void test(){
		for (int i = 0; i < 1; i ++){
			vector<int> T;
			T.clear();			
			T.push_back(1);			
			T.push_back(2);
			
		}
		exit(1);
	}
	
	
	void testHR(){
		for (int i = 0; i < 1; i ++){
			vector<int> T;
			T.clear();
			T.push_back(1);
			T.push_back(2);
			T.push_back(3);
			T.push_back(4);
			
		}
	} //end void testHR()
	
	
	void testHR_ADtree(){
		for (int i = 0; i < 1; i ++){
			deque<int> T;
			T.clear();
			T.push_back(1);
			T.push_back(2);
			T.push_back(3);
			T.push_back(4);
			
		}
	} //end void testHR_ADtree()
	
	

	//--- Interface functions --- below ----------
	//
	// Log of local conditional probability, log p(xi | xS).
	double log_lcp(int i, vector<int> & T){
		
		return log_lcp_mult(i, T);
	}
	double log_lcp(int i, int *T, int d){
		vector<int> TT;
		for (int j = 0; j < d; j ++) TT.push_back(T[j]);
		return log_lcp_mult(i, TT);
	}
	

	double log_lcpHR(int i, vector<int> & T){
		
		return log_lcp_multHR(i, T);
	}
	
	double log_lcpHR(int i, int *T, int d){
		vector<int> TT;
		for (int j = 0; j < d; j ++) TT.push_back(T[j]);
		//cerr << "start log_lcp_multHR(i, TT)" << endl;
		double result = log_lcp_multHR(i, TT);
		return result;
	}
	
	
	double log_lcpHR_ADtree(int i, int *T, int d){
		deque<int> TT;
		for (int j = 0; j < d; j ++) TT.push_back(T[j]);
		double result = log_lcp_multHR_ADtree(i, TT);
		return result;
	}
	
	
	double log_prior(int i, int *T, int d){
		return lr[1][d] + lr[1][n-1-d] - lr[1][n-1];
	}
	int num_layers(){
		return layering.numlayers;
	}
	void layer(int h, int** Vh, int* nh){
		*Vh = &(layering.V[layering.cnh[h]]);
		*nh = layering.nh[h];
	}
	void upper_layers(int h, int** Vu, int* nu){//similar to above but upper means parent
		if (h < layering.numlayers){
			*Vu = &(layering.V[layering.cnh[h+1]]);
			*nu = n - layering.cnh[h+1];
		}
		else{ *Vu = NULL; *nu = 0;}	
	}
	void lower_layers(int h, int **Vl, int *nl){//similar to above but lower means children
		if (h > 0){
			*Vl = layering.V;
			*nl = layering.cnh[h];
		}
		else{ *Vl = NULL; *nl = 0;}
	}
	int max_indegree(){
		return atoi(Arguments::maxindegree);
	}
	bool edges_within(int h){
		return layering.edges_within(h);
	}
	int num_nodes(){
		return n;
	}
	
	void print_edge_prob(ostream& f, int i, int j, double p){
		//f<<data.heads[i]<<" -> "<<data.heads[j]<<" "<<p<<endl; 
		f<<data.heads[i]<<" -> "<<data.heads[j]<<" \t" <<p <<endl; 
	} 
	
	//
	//--- Interface functions --- above ----------	
	
	
private:	
	// Dirichlet-multinomial model
	double log_lcp_mult(int i, vector<int> & T){
		vector<int> S;

		S.push_back(i);
				
		for (int k = 0; k < (int)T.size(); k ++){
			S.push_back(T[k]);
		}
		
		//cerr << " Evaluation yields: " << evaluate(S) << " , log_gammaratio(1, 2) = " 
		//	<< log_gammaratio(1, 2) << " , a = " <<  data.arities[S[0]] << endl;
		
		return evaluate(S);	
	}
	
	double evaluate(vector<int> & S){
		
		Tnode root(data.maxarity+1, 0);
		vector<int> u;
		
		//for each record t 
		for (int t = 0; t < m; t ++){
			u.clear();
			for (int k = 0; k < (int)S.size(); k ++){
				int j = S[k];
				u.push_back(data.dm[t][j]);
			}
			//cerr << t+1 << "th update: ";
			
			root.update(S, u);
			//cerr << endl;
			
		}
		//root.print(cerr);
		
	
		return root.evaluate(S.size(), 1, -data.arities[S[0]]);
		
	}
	

	double log_lcp_multHR(int i, vector<int> & T){
		vector<int> S;
				
		S.push_back(i);
				
		for (int k = 0; k < (int)T.size(); k ++){
			S.push_back(T[k]);
		}
	
		//cerr << " Evaluation yields: " << evaluate(S) << " , log_gammaratio(1, 2) = " 
		//	<< log_gammaratio(1, 2) << " , a = " <<  data.arities[S[0]] << endl;
		double result = evaluateHR(S);
		return result;
		
			
	}//end log_lcp_multHR()
	

	double log_lcp_multHR_ADtree(int i, deque<int> & T){
		//totalOf1 << "Inside log_lcp_multHR()" << endl;
		
		double result2 = evaluateHR_ADtree(T);

		int insertIndex = -1;
		for(int k = 0; k < (int)T.size(); k++){
			if(i < T[k]){
				insertIndex = k;
				break;
			}
		}
		if(insertIndex == -1){
			insertIndex = (int)T.size();
		}

		deque<int> S;
		
		for(int k = 0; k <= insertIndex - 1; k++){
			S.push_back(T[k]);
		}
		S.push_back(i);
		for(int k = insertIndex; k < (int)T.size(); k++){
			S.push_back(T[k]);
		}
		
		double result1 = evaluateHR_ADtree(S);
		
		//cerr << " Evaluation yields: " << evaluate(S) << " , log_gammaratio(1, 2) = " 
		//	<< log_gammaratio(1, 2) << " , a = " <<  data.arities[S[0]] << endl;
		double result = result1 - result2;
		return result;
				
	}
	
	
	// Dirichlet-multinomial model
	double evaluateHR(vector<int> & S){
		Tnode * root = new Tnode (data.maxarity+1, 0);
		vector<int> u;
		
		//for each record t 
		for (int t = 0; t < m; t ++){
			u.clear();
			for (int k = 0; k < (int)S.size(); k ++){
				int j = S[k];
				u.push_back(data.dm[t][j]);
			}
			//cerr << t+1 << "th update: ";

			(*root).update(S, u);
			//
			//cerr << endl;
			
		}
		//root.print(cerr);
		
		int alpha_v_pa = 1;
		for(int index = 0; index < (int)S.size(); index++){
			alpha_v_pa *= data.arities[S[index]];
		}
		int r = data.arities[S[0]];
		//return root.evaluateHR(S.size(), alpha_v_pa, r);

		double result = (*root).evaluateHR(S.size(), alpha_v_pa, r);
		//totalOf1 << "    result of root.evaluateHR(" << S.size() <<", " 
		//	<< alpha_v_pa << ", " << r << ") = " << result << endl;
		delete root;
		return result;
		
	} //end double evaluateHR(vector<int> & S)
	

	double evaluateHR_ADtree(deque<int> & S){

		int alpha = 1;
		for(int index = 0; index < (int)S.size(); index++){
			alpha *= data.arities[S[index]];
		}
		
		vector<int> countVec = MakeADContiTable(S, data.dm, data.arities);
		
		double sum = 0;
		for(int i = 0; i < (int) countVec.size(); i++){
			//totalOf1 << "countVec[" << i <<" ]" << countVec[i] << endl;
			sum += log_gammaratio(alpha, countVec[i]);
		}
		
//		totalOf1 << "evaluateHR_ADtree(deque<int> & S) ends" << endl;

		return sum;
		
	} //end double evaluateHR_ADtree()
	
		
	Data data;
	int n;
	int m;
	Layering layering;	
	
	ADTreeNode * ADTreeNodeP1;
	
}; //end of class Model





#endif
