#include<stdio.h>
#include<stdlib.h>

#include<iostream>
#include<vector>
#include<deque>
#include<string>
#include<fstream>
#include<math.h>

#include"Arguments.h"
#include"Model.h"

using namespace std;


struct ADTreenode{
	int count;
	
	//i.e. start attr which = *;
	int startVaryNodeInd;
	
	//struct ADVarynode * ADVaryNodePs[];
	struct ADVarynode ** ADVaryNodePs;
};

typedef struct ADTreenode ADTreeNode;

struct ADVarynode{
	//Represent Vary Index
	int index;
	
	int mcv;
	
	//struct ADTreenode * ADTreeNodePs[];
	struct ADTreenode ** ADTreeNodePs;
	
};

typedef struct ADVarynode ADVaryNode;


typedef struct {
	//the count for each arrangment of contiTableAttrs
	//contiTableCounts.size() == the product of the arity of each element in contiTableAttrs
	//do not explicitly record the whole contiTable
	vector<int> contiTableCounts;
	
	//invariant: in the decreasing order
	vector<int> contiTableAttrs;
	
	//condiAttrs.size() == condiVals.size()
	//invariant: in the increasing order
	vector<int> condiAttrs;
	vector<int> condiVals;
	
	//invariant: contiTableAttrs.size() + condiAttrs.size() == the whole original inquiry length
	//			finally: condiAttrs.size() == 0
} CondiContiTable;





ADTreeNode * MakeADTree(int, vector<int> &, vector< vector<int> > &, vector<int> &);
ADVaryNode * MakeVaryNode(int, vector<int> &, vector< vector<int> > &, vector<int> &);
void PrintADTreeNode(ADTreeNode *, int, const int, vector<int> &);
void PrintADVaryNode(ADVaryNode *, int, const int, vector<int> &);
void FreeADTreeNode(ADTreeNode *, const int, vector<int> &);
void FreeADVaryNode(ADVaryNode *, const int, vector<int> &);


CondiContiTable MakeContab(deque<int>, ADTreeNode *, 
		vector<int> &, vector<int> &, const vector<int> &);
		
void MinusContab(CondiContiTable &, CondiContiTable *, int, int);

void ConcatContab(CondiContiTable *, int, int, CondiContiTable &);

		



//HR:
//	a_i is in {0, ..., M-1}
//  dm: the data matrix: R * M
//  dmIndex: initially stores {0, 1, ..., R - 1}, it is just the light-weight index for dm
//  arities: stores the arity of each attr
//  could use vector<int> & dmIndex instead of vector<int> dmIndex
ADTreeNode * MakeADTree(int a_i, vector<int> & dmIndex, vector< vector<int> > & dm, vector<int> & arities){
	//M = num of attr
	int M = dm[0].size();
	
	ADTreeNode * ADTreeNodeP1 = (ADTreeNode *) malloc(sizeof(ADTreeNode));
	ADTreeNodeP1->count = dmIndex.size();
	ADTreeNodeP1->startVaryNodeInd = a_i;
	//base
	if(a_i >= M){
		ADTreeNodeP1->ADVaryNodePs = NULL;
	}
	else{
		ADTreeNodeP1->ADVaryNodePs = (ADVaryNode **) malloc(sizeof(ADVaryNode*) * 
	                              (M - 1 - a_i + 1));
	}
	for(int a_j = a_i; a_j <= M-1; a_j++){
		ADTreeNodeP1->ADVaryNodePs[a_j - a_i] = MakeVaryNode(a_j, dmIndex, dm, arities);
	}
	return ADTreeNodeP1;
	                              
}//end ADTreeNode * MakeADTree()



//HR:
//	a_i is in {0, ..., M-1}
//  dm: the data matrix: R * M
//  dmIndex: initially stores {0, 1, ..., R - 1}
//  arities: stores the arity of each attr
//  could use vector<int> & dmIndex instead of vector<int> dmIndex
ADVaryNode * MakeVaryNode(int a_i, vector<int> & dmIndex, vector< vector<int> > & dm, vector<int> & arities){
	ADVaryNode * ADVaryNodeP1 = (ADVaryNode *) malloc(sizeof(ADVaryNode));
	ADVaryNodeP1->index = a_i;
	int n_i = arities[a_i];//may be used as global var
	ADVaryNodeP1->ADTreeNodePs = (ADTreeNode **) malloc(sizeof(ADTreeNode *) * n_i);
	vector<int> dmIndexSub[n_i];
	for(unsigned int j = 0; j <= dmIndex.size() - 1; j++){
		int v = dm[dmIndex[j]][a_i];
		dmIndexSub[v].push_back(dmIndex[j]);
	}
	ADVaryNodeP1->mcv = 0;
	int maxCount = dmIndexSub[0].size();
	for(int j = 1; j <= n_i - 1; j++){
		if((int) dmIndexSub[j].size() > maxCount){
			maxCount = dmIndexSub[j].size();
			ADVaryNodeP1->mcv = j;
		}
	}
	for(int j = 0; j <= n_i - 1; j++){
		if(dmIndexSub[j].size() == 0 || j == ADVaryNodeP1->mcv){
			ADVaryNodeP1->ADTreeNodePs[j] = NULL;
		}
		else{
			ADVaryNodeP1->ADTreeNodePs[j] = MakeADTree(a_i+1, dmIndexSub[j], dm, arities);
		}
	}
	//not necessary in fact
	//dmIndex.clear();
	//HR: Neither necessary nor correct
	//delete dmIndex;
	return ADVaryNodeP1;
	
	
}//end ADVaryNode * MakeVaryNode()


//HR:
//  ADTreeNodeP1: pointers to the ADTreeNode
//  level: starting from 0 
//  M: the num of attr
//  arities: stores the arity of each attr 
//  initial call: PrintADTreeNode(ADTreeNodeP1, 0, M, arities) in Model.h
//  pre-order to print the ADtree
void PrintADTreeNode(ADTreeNode * ADTreeNodeP1, int level, const int M, vector<int> & arities){
	string strSpace = "";
	for(int i = 0; i < level; i++){
		strSpace += "  ";
	}
	cout << strSpace << "ADTreeNode" << endl;
	cout << strSpace << "Count = " << ADTreeNodeP1->count << endl;
	cout << strSpace << "startVaryNodeInd = " << ADTreeNodeP1->startVaryNodeInd << endl;
	if(ADTreeNodeP1->ADVaryNodePs == NULL){
		return;
	}
	else{
		for(int i = 0; i < M - 1 - ADTreeNodeP1->startVaryNodeInd + 1; i++){
			PrintADVaryNode(ADTreeNodeP1->ADVaryNodePs[i], level+1, M, arities);
		}
	}
} //end void PrintADTreeNode()


//HR:
//  ADVaryNodeP1: pointers to the ADVaryNode
//  level: starting from 0 
//  M: the num of attr
//  arities: stores the arity of each attr 
//  pre-order to print the ADtree
void PrintADVaryNode(ADVaryNode * ADVaryNodeP1, int level, const int M, vector<int> & arities){
	string strSpace = "";
	for(int i = 0; i < level; i++){
		strSpace += "  ";
	}
	cout << strSpace << "ADVaryNode" << endl;
	cout << strSpace << "index = " << ADVaryNodeP1->index << endl;
	cout << strSpace << "mcv = " << ADVaryNodeP1->mcv << endl;
	int n_i = arities[ADVaryNodeP1->index]; //may be changed to global var
	for(int j = 0; j <= n_i - 1; j++){
		if(j == ADVaryNodeP1->mcv){
			cout << strSpace << "  mcv" << endl;		
		}
		else if(ADVaryNodeP1->ADTreeNodePs[j] == NULL){
			cout << strSpace << "  NULL" << endl;
		}
		else{
			PrintADTreeNode(ADVaryNodeP1->ADTreeNodePs[j], level+1, M, arities);
		}
	}

}//end void PrintADVaryNode()



//HR:
//  ADTreeNodeP1: pointers to the ADTreeNode
//  M: the num of attr
//  arities: stores the arity of each attr 
//  initial call: FreeADTreeNode(ADTreeNodeP1, M, arities) in Model.h
//  pre-order to free the ADtree
void FreeADTreeNode(ADTreeNode * ADTreeNodeP1, const int M, vector<int> & arities){

	if(ADTreeNodeP1->ADVaryNodePs != NULL){
		for(int i = 0; i < M - 1 - ADTreeNodeP1->startVaryNodeInd + 1; i++){
			FreeADVaryNode(ADTreeNodeP1->ADVaryNodePs[i], M, arities);
		}
		free(ADTreeNodeP1->ADVaryNodePs);
	}

	free(ADTreeNodeP1);
}  //end void FreeADTreeNode()


//HR:
//  ADVaryNodeP1: pointers to the ADVaryNode
//  M: the num of attr
//  arities: stores the arity of each attr 
//  pre-order to free the ADtree
void FreeADVaryNode(ADVaryNode * ADVaryNodeP1, const int M, vector<int> & arities){

	int n_i = arities[ADVaryNodeP1->index]; //may be changed to global var
	for(int j = 0; j <= n_i - 1; j++){
		if(j == ADVaryNodeP1->mcv){
			;		
		}
		else if(ADVaryNodeP1->ADTreeNodePs[j] == NULL){
			;
		}
		else{
			FreeADTreeNode(ADVaryNodeP1->ADTreeNodePs[j], M, arities);
		}
	}
	free(ADVaryNodeP1->ADTreeNodePs);
	free(ADVaryNodeP1);

}//end void FreeADVaryNode()



//Error: if use ofstream of1 instead of ofstream & of1
//In file included from Model.h:19,
//                 from main.cc:6:
//UpdateHR2.h: In copy constructor `std::basic_ios<char, std::char_traits<char> >::basic_ios(const std::basic_ios<char, std::char_traits<char> >&)':
///usr/lib/gcc/i386-redhat-linux/3.4.6/../../../../include/c++/3.4.6/bits/ios_base.h:781: error: `std::ios_base::ios_base(const std::ios_base&)' is private
//UpdateHR2.h:220: error: within this context
//UpdateHR2.h: In copy constructor `std::basic_filebuf<char, std::char_traits<char> >::basic_filebuf(const std::basic_filebuf<char, std::char_traits<char> >&)':
///usr/lib/gcc/i386-redhat-linux/3.4.6/../../../../include/c++/3.4.6/streambuf:769: error: `std::basic_streambuf<_CharT, _Traits>::basic_streambuf(const std::basic_streambuf<_CharT, _Traits>&) [with _CharT = char, _Traits = std::char_traits<char>]' is private
//UpdateHR2.h:220: error: within this context
//UpdateHR2.h: In function `void print_CondiContiTable(CondiContiTable&, std::ofstream)':
//UpdateHR2.h:220: error:   initializing argument 2 of `void print_vec(std::vector<int, std::allocator<int> >, std::ofstream)'


void print_vec(vector<int> & vec1, ofstream & of1){
	for(int i = 0; i < (int) vec1.size(); i++){
		of1 << vec1[i] << " ";
	}
	of1 << endl;
}

void print_deque(deque<int> & v1, ofstream & of1){
	for(int i = 0; i < (int) v1.size(); i++){
		of1 << v1[i] << " ";
	}
	of1 << endl;
}

void print_CondiContiTable(CondiContiTable & ccTable1, ofstream & of1){
	of1 << "ccTable1.contiTableCounts: " << endl;
	print_vec(ccTable1.contiTableCounts, of1);
	
	of1 << "ccTable1.contiTableAttrs: " << endl;
	print_vec(ccTable1.contiTableAttrs, of1);
	
	of1 << "ccTable1.condiAttrs: " << endl;
	print_vec(ccTable1.condiAttrs, of1);
	
	of1 << "ccTable1.condiVals: " << endl;
	print_vec(ccTable1.condiVals, of1);
	
}



void print_vec(vector<int> & vec1){
	for(int i = 0; i < (int) vec1.size(); i++){
		cout << vec1[i] << " ";
	}
	cout << endl;
}


void print_deque(deque<int> & v1){
	for(int i = 0; i < (int) v1.size(); i++){
		cout << v1[i] << " ";
	}
	cout << endl;
}

void print_CondiContiTable(CondiContiTable & ccTable1){
	cout << "ccTable1.contiTableCounts: " << endl;
	print_vec(ccTable1.contiTableCounts);
	
	cout << "ccTable1.contiTableAttrs: " << endl;
	print_vec(ccTable1.contiTableAttrs);
	
	cout << "ccTable1.condiAttrs: " << endl;
	print_vec(ccTable1.condiAttrs);
	
	cout << "ccTable1.condiVals: " << endl;
	print_vec(ccTable1.condiVals);
	
}



//HR: 
//	inquiryAttrs: {a_i_1, ..., a_i_n}
//	ADNP: The current pointer to ADTreeNode
//	givenAttrs, givenVals explicilty record the meaning of ADNP
//	{b_i_1, ..., b_i_m} = {value_b_i_1, .. value_b_i_m}
//	pre:
//	inquiryAttrs is in the increasing order
//	givenAttrs is in the increasing order
CondiContiTable MakeContab(deque<int> inquiryAttrs, ADTreeNode * ADNP, 
		vector<int> & givenAttrs, vector<int> & givenVals, const vector<int> & arities){
			
//	cout << "\nMakeContab()" << endl;
//	cout << "inquiryAttrs" << endl;
//	print_deque(inquiryAttrs);
//	cout << "givenAttrs" << endl;
//	print_vec(givenAttrs);
//	cout << "givenVals" << endl;
//	print_vec(givenVals);
	
	
	
	
	//base 1
	if(ADNP == NULL){
		CondiContiTable bottomTable;
		
		//base 1.1
		//if(inquiryAttrs.size() == 0){
		if(inquiryAttrs.empty()){
			bottomTable.contiTableCounts.push_back(0);
			//bottomTable.contiTableAttrs is empty
			bottomTable.condiAttrs = givenAttrs;
			bottomTable.condiVals = givenVals;
			
			//cout << "//base 1.1: if(ADNP == NULL) and if(inquiryAttrs.empty())" << endl;
			//print_CondiContiTable(bottomTable);
			
		}
		//base 1.2
		//if(inquiryAttrs.size() > 0)
		else{
			int rowNum = 1;
			for(unsigned int i = 0; i < inquiryAttrs.size(); i++){
				//Note: I explicitly write inquiryAttrs[inquiryAttrs.size() - 1 - i ] instead of 
				//	inquiryAttrs[i] because this is really the ordering to create the whole contiTable
				int lastAttr = inquiryAttrs[inquiryAttrs.size() - 1 - i ];
				rowNum *= arities[lastAttr];
				
				bottomTable.contiTableAttrs.push_back(lastAttr);
			}		
			bottomTable.condiAttrs = givenAttrs;
			bottomTable.condiVals = givenVals;
			
			for(int i = 0; i < rowNum; i++){
				bottomTable.contiTableCounts.push_back(0);
			}
			
			//cout << "//base 1.2: if(ADNP == NULL) and if(!inquiryAttrs.empty())" << endl;
			//print_CondiContiTable(bottomTable);
		}
		
		
		
		return bottomTable;
		
	}// end if(ADNP == NULL)
	
	//base 2
	else if (inquiryAttrs.empty()){
		CondiContiTable bottomTable;
		bottomTable.contiTableCounts.push_back(ADNP->count);
		//bottomTable.contiTableAttrs is empty
		bottomTable.condiAttrs = givenAttrs;
		bottomTable.condiVals = givenVals;
		
		//cout << "//base 2: if(ADNP != NULL) and if(inquiryAttrs.empty())" << endl;
		//print_CondiContiTable(bottomTable);
			
		return bottomTable;
	}
	else{
		int a_i_1 = inquiryAttrs.front();
		int VNIndex = a_i_1 - ADNP->startVaryNodeInd;
		ADVaryNode * VNP = ADNP->ADVaryNodePs[VNIndex];
		int mcv = VNP->mcv;
		int n_i_1 = arities[a_i_1];
		CondiContiTable CTs[n_i_1];
		
		//delete the 1st element a_i_1 of inquiryAtts, will never use the 1st element a_i_1 again
		//but does not allow the sub_program to delete more elements, so use deque<int> inquiryAttrs instead of deque<int> & inquiryAttrs
		//vector<int> & givenAttrs, vector<int> & givenVals can be used because the sub_program will change but then restore it
		inquiryAttrs.pop_front();
		
		for(int k = 0; k < n_i_1; k++){
			if(k != mcv){
				ADTreeNode * ADNP_k = VNP -> ADTreeNodePs[k];

				vector<int> newGivenAttrs = givenAttrs;
				newGivenAttrs.push_back(a_i_1);
				vector<int> newGivenVals = givenVals;
				newGivenVals.push_back(k);
				CTs[k] = MakeContab(inquiryAttrs, ADNP_k, newGivenAttrs, newGivenVals, arities);
				
			}
		}
		CondiContiTable sumCTs = MakeContab(inquiryAttrs, ADNP, givenAttrs, givenVals, arities);
		MinusContab(sumCTs, CTs, n_i_1, mcv);
		
		CondiContiTable result;
		ConcatContab(CTs, n_i_1, a_i_1, result);
		
		//cout << "//non-base: " << endl;
		//print_CondiContiTable(result);
		
		return result;
	}
	
}// end CondiContiTable MakeContab()


void MinusContab(CondiContiTable & sumCTs, CondiContiTable * CTs, int n_i_1, int mcv){
	CTs[mcv].contiTableCounts = sumCTs.contiTableCounts;
	CTs[mcv].contiTableAttrs = sumCTs.contiTableAttrs;
	for(unsigned int i = 0; i < CTs[mcv].contiTableCounts.size(); i++){
		for(int k = 0; k < n_i_1; k++){
			if(k != mcv){
				CTs[mcv].contiTableCounts[i] -= CTs[k].contiTableCounts[i];
			}
		}
	}
	//assume arities of each attr >=2
	if(mcv != 0){
		CTs[mcv].condiAttrs = CTs[0].condiAttrs;
		CTs[mcv].condiVals = CTs[0].condiVals;
	}
	else{
		CTs[mcv].condiAttrs = CTs[1].condiAttrs;
		CTs[mcv].condiVals = CTs[1].condiVals;
	}
	CTs[mcv].condiVals.pop_back();
	CTs[mcv].condiVals.push_back(mcv);
	
} //end void MinusContab()


void ConcatContab(CondiContiTable * CTs, int n_i_1, int a_i_1, CondiContiTable & concatedTable){
	for(int i = 0; i < n_i_1; i++){
		//for each i, CTs[i].contiTableCounts.size() is the same
		for(unsigned int j = 0; j < CTs[i].contiTableCounts.size(); j++){
			concatedTable.contiTableCounts.push_back(CTs[i].contiTableCounts[j]);
		}
	}
	
	//check: a_i_1 == CTs[0].condiAttrs.back();
	if(a_i_1 != CTs[0].condiAttrs.back()){
		cout << "Error: a_i_1 != CTs[0].condiAttrs.back()" << endl;
		cout << "a_i_1 = " << a_i_1 << endl;
		cout << "CTs[0].condiAttrs.back() = " << CTs[0].condiAttrs.back() << endl;
	}
	
	//move a_i_1 from the end of CTs[0].condiAttrs to the end of concatedTable.contiTableAttrs
	concatedTable.contiTableAttrs = CTs[0].contiTableAttrs;
	concatedTable.contiTableAttrs.push_back(a_i_1);
	
	concatedTable.condiAttrs = CTs[0].condiAttrs;
	concatedTable.condiAttrs.pop_back();
	
	concatedTable.condiVals = CTs[0].condiVals;
	concatedTable.condiVals.pop_back();
	
} //end void ContatContab()


		




