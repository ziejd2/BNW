#include <iostream>
#include <fstream>
#include <iomanip>
//#include "varpar.h"
#include <stdlib.h>
#include <math.h>
#include <list>
#include <stdio.h>
#include <vector>
#include "cfg.h"
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


#define MARK 9e99
#define LOG_ZERO -1e101

//HR: add from UpdateHR.h to deal with the problem in sum = 0 in comp_post()
//call by reference to the original value
//the log sum result is stored in logA

#define epsilon 1.0e5

bool logSpecValEquals(double logX, double logSpecVal){
	return (logX >= logSpecVal - epsilon && logX <= logSpecVal + epsilon);
}

void logAddComp(double & logA, double & logB){

    	//if(logA == MARK){
	    if(logSpecValEquals(logA, MARK)){
    		logA = logB;
    	}
    	//else if (logA == LOG_ZERO){
    	else if (logSpecValEquals(logA, LOG_ZERO)){
    		logA = logB;
    	}
    	//else if(logB == MARK){
    	else if(logSpecValEquals(logB, MARK)){
    		logA = logA;
    	}
    	//else if(logB == LOG_ZERO){
    	else if(logSpecValEquals(logB, LOG_ZERO)){
    		logA = logA;
    	}
    	
    	
    	//Neither logA nor logB is MARK/LOG_ZERO
    	else{
    		//1
    		if(logA < 0 && logB < 0 ){
    			//1.1
				//Gaurd logB-logA <= 100 to avoid that exp(logB-logA) go to inf
	    		if(logB - logA <= 100 ){
	    			logA = logA + log( 1 + exp(logB-logA) );
	
	    		} 
	    		//1.2		
	    		//if logB - logA > 100, then logB > 100 + logA so that B is much bigger than A (B > e^100 * A)
	    		//    So (A + B) almost = B 
	    		else{
	    			//logA = logB; 
		    		//The following is the real formula,; but it is almost same as //logA = logB; 
		    		logA = logB + log( exp(logA - logB) + 1);
	    		}
    		}
    		
    		//2
    		else if(logA > 0 && logB > 0){
    			//always make exp(.): . > 0
    			if(-logB + logA <= 100){
    				//2.1
    				logA = -( -logA + log( 1 + exp(-logB+logA) ) );
    			} 			
    			else{
    				//2.2
    				logA = -( -logB + log( 1 + exp(-logA+logB) ) );
    			}
    			
    		}
    		//3
    		else if(logA < 0 && logB > 0){
    			//3.1
    			if(logA + logB > 0){   	
    				if(logA + logB <= 100){
    					//3.1.2
    					logA = -logB + log( exp(logA + logB) - 1 );
    				}	
    				else{
    					//3.1.1
    					logA = logA + log( 1 - exp(-logB-logA) );
    				}	
	
    			}
    			//3.2
    			else if(logA + logB < 0){
    				if(-logA - logB <= 100){
    					//3.2.2
    					logA = -( logA + log( exp(-logB-logA)  - 1) );
    				}
    				else{
    					//3.2.1
    					logA = - ( -logB + log(1 - exp(logA+logB) ) );
    				}

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
    				if(logA + logB <= 100){
    					//4.1.2
    					logA = -logA + log( exp(logB+logA) - 1);
    				}
    				else{
    					//4.1.1
    					logA = logB + log( 1 - exp(-logA-logB) );
    				}
    				
    			}
    			//4.2
    			else if(logA + logB < 0){
    				if(-logA-logB <= 100){
    					//4.2.2
    					logA = -( logB + log( exp(-logA-logB) - 1) );
    				}
    				else{
    					//4.2.1
    					logA = -( -logA + log(1 - exp(logB+logA)) );
    				}
	
    			}
    			//4.3
    			else{
    				logA = LOG_ZERO;
    			}			
    		}
		
    	}
	
} // end of void logAddComp(double & logA, double & logB)c



//HR: Note: The whole program supposes that max no. of vars = 32 and hardcord 32


//Convert the parent set to the appropriate variable set
//HR:F
varset_t parset2varset(int v, varset_t set){
  	varset_t sinkleton = 1U<<v;
  	varset_t mask      = sinkleton - 1;
  	varset_t low       = set &  mask;
  	varset_t high      = set & ~mask;
  	return (high<<1) | low;
}



/*================================================================*/
//class for bnets 
//HR:F
//for (net, score) pair
//  each net[j] (0 <= j <= 32-1) is the binary repres of the parents
class net_set{
	public:
		score_t score;
		varset_t net[32];

		net_set(){
			score=0.0000;
			for(int j=0;j<32;j++){
				net[j]=0;
			}
		}

		friend int operator<(net_set s1,net_set s2);
		friend int operator==(net_set s1,net_set s2);
		friend int operator>(net_set s1,net_set s2);
		friend bool same_obj(net_set s1, net_set s2, int nof_vars);

};

//HR:F
/*================================================================*/

int operator<(net_set s1,net_set s2){
	if(fabs(s1.score-s2.score)<EPSILON){
		return false;
	}
    return(s1.score<s2.score);
}

int operator>(net_set s1,net_set s2){
	if(fabs(s1.score-s2.score)<EPSILON){
		return false;
	}
    return(s1.score>s2.score);
}

int operator==(net_set s1,net_set s2){
    return(fabs(s1.score-s2.score)<EPSILON);
}

//HR: check whether s1 and s2 are the same
bool same_obj(net_set s1, net_set s2, int nof_vars){

    bool flag=true;

    for(int j=0;j<nof_vars;j++){
        if(s1.net[j]!=s2.net[j]){
			//cout<<"************************";
            flag=false;
            break;
        }
    }
	//cout<<"Flag "<<flag;
	//		if(flag==true)
	//	{
		//	if(fabs(s1.score-s2.score)<0.1)
			//	flag=true;
		//	else flag=false;
	//	}
			
    return(flag);
}

//HR: check whether s1 and s2 are the same
bool same_obj(net_set s1, net_set s2){

    bool flag=true;

    for(int j=0;j<MAX_NOF_VARS;j++){
        if(s1.net[j]!=s2.net[j]){
			//cout<<"************************";
            flag=false;
            break;
        }
    }
	//cout<<"Flag "<<flag;
	//		if(flag==true)
	//	{
		//	if(fabs(s1.score-s2.score)<0.1)
			//	flag=true;
		//	else flag=false;
	//	}
			
    return(flag);
}
//

/*================================================================*/
//HR:F
//HR: Refined
void insert_vec(list<net_set>* v,net_set s, int k, int nof_vars){
    list<net_set>::iterator it;
    //HR: can improve its time
    //first check whether v->size() > 0 && s < v->back()
    //see Lav's comment
    
    
	bool flag=false;

     
     //HR: Refined
     //
     if( (v->size() > 0) && (s < v->back()) ){
     	v->push_back(s);
     	flag=true;
     }
     else{
     //
     
        for(it=v->begin();it!=v->end();it++){
        	if(same_obj(s,*it,nof_vars)){
//				cout<<"Same object so not inserted!!!!!!!!!!!!!"<<endl;
				flag=true;
                break;
            }
            else if(s>(*it)){
	//			cout<<"score of s:"<<s.score<<"Score of kbnet element "<<(*it).score<<endl;
                v->insert(it,s);
				flag=true;
                break;
            }
						
        }
        
     }

		if(flag==false){
			v->push_back(s);
		}
    	if(v->size()>k){
        	v->pop_back();
      	}
      	
		//v->unique();
}

/*================================================================*/
//class for bparents
//HR:F
//HR: (variable set, score) pair
class score_network{
	public:
		score_t scores;
		varset_t vset;

	friend int operator<(score_network s1,score_network s2);
    friend int operator==(score_network s1,score_network s2);
    friend int operator>(score_network s1,score_network s2);
    friend bool same_obj(score_network s1, score_network s2);

};


//HR:F
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
    return((s1==s2)&&(s1.vset==s2.vset));
}




void insert_vec(list<score_network>* v,score_network s, int k)
  {
    list<score_network>::iterator it;

    if( s < v->back())
      {
        v->push_back(s);
      }
    else
      {
        for(it=v->begin();it!=v->end();it++)
          {
    //        if(same_obj(s,*it))
      //        {
        //        break;
          //    }
//            else
 if(s>(*it))
              {
                v->insert(it,s);
                break;
              }

          }
      }

    if(v->size()>k)
      {
        v->pop_back();
      }

  }

/*================================================================*/
//class for node
//HR:F
class node{
	public:
		int ip;
		int in;
		score_t score;

		node(int x, int y, score_t z){
        	ip=x;
          	in=y;
			score=z;
        }

		friend int operator<(node s1,node s2);
		friend int operator==(node s1,node s2);
		friend int operator>(node s1,node s2);
		friend bool same_obj(node s1, node s2);
};

/*================================================================*/

int operator<(node s1,node s2){
 	if(fabs(s1.score-s2.score)<EPSILON){
     	return false;
    }
    return(s1.score<s2.score);
}


int operator>(node s1,node s2){

	if(fabs(s1.score-s2.score)<EPSILON){
        return false;
   	}
    return(s1.score>s2.score);
}


int operator==(node s1,node s2){
	return(fabs(s1.score-s2.score)<EPSILON);
}


bool same_obj(node s1, node s2){
	return((s1==s2)&&(s1.ip==s2.ip)&&(s1.in==s2.in));
}

/*================================================================*/

//HR: F
void print_queue(list<net_set> v[],int size,int nof_vars){

    list<net_set>::iterator iter;

	for(int j=0;j<size;j++){
    	for(iter=v[j].begin();iter!=v[j].end();iter++){
      		//HR:
      		//kbnetscore[j] can have at most k (net, score) pairs
        	cout<<"kbnetscore "<<j<<"    Score:"<<(*iter).score<<"   net   ";
			for(int p=0;p<nof_vars;p++){
				cout<<(*iter).net[p];
			}
			cout<<endl;
    	}
	}
}

//HR: Add
void print_netscore(net_set ns,int nof_vars){


        cout<<"netscore "<<"    Score:"<<ns.score<<" \tnet   "; //<<endl;
      	for(int p=0;p<nof_vars;p++){
         	cout<<ns.net[p] << " ";
        }
        cout<<endl;
        cout<<endl;

}
//

void print_queue(vector<score_network>* v){

    vector<score_network>::iterator iter;

    for(iter=v->begin();iter!=v->end();iter++){
        cout<<"bparents    "<<(*iter).vset<<","<<(*iter).scores<<"  ,,  ";
    }

}

void print_queue(list<node>* v){

   	list<node>::iterator iter;
	cout<<"fringe"<<endl;
    for(iter=v->begin();iter!=v->end();iter++){
      	cout<<"   ip "<<(*iter).ip<<", in "<<(*iter).in<<" score "<<(*iter).score;
    }

}

void print_queue(vector<net_set>* v,int nof_vars){

    vector<net_set>::iterator iter;

    for(iter=v->begin();iter!=v->end();iter++){
//      cout<<"    "<<(*iter).bsps<<","<<(*iter).scores;
		cout<<"bnets "<<"    Score:"<<(*iter).score<<"   net   "<<endl;
        for(int p=0;p<nof_vars;p++){
            cout<<(*iter).net[p];
        }

    }
}

void print_queue(list<net_set>* v,int nof_vars){

    list<net_set>::iterator iter;

    for(iter=v->begin();iter!=v->end();iter++){
//    	cout<<"    "<<(*iter).bsps<<","<<(*iter).scores;
        cout<<"kbnet "<<"    Score:"<<(*iter).score<<"   net   "<<endl;
      	for(int p=0;p<nof_vars;p++){
         	cout<<(*iter).net[p];
        }
    }
}



  
//HR: call insert_vec(&fringe,n,k);
//HR: by this way, fringe is in descending order
//HR: Lav's code is wrong
//HR: corrected
void insert_vec(list<node>* v,node s, int k){
    list<node>::iterator it;
    
    //HR: test
    //bool sameObj = false;
    //bool inserted = false;
    //

    //if( s < v->back()){ same
    if( (v->size() == 0) || (s < v->back())){    	
        v->push_back(s);
    }
    else{
        for(it=v->begin();it!=v->end();it++){
        //comment it out will give the different answer. Because Lav's original is wrong, it will still insert the same object. It will eventually pop the correct end > k
        //
            if(same_obj(s, *it)){
                break;                
//                sameObj = true;
//                cout << "s: ";
//                print_node(s);
//                cout << "*it: ";
//                print_node(*it);
//                cout << endl;
//                print_queue(v);
             
            }
            else{
         //
 				if(s>(*it)){
                	v->insert(it,s);
                	//inserted = true;
                	break;
            	}
            }
            
      	}
      	
      	//HR: test
//      	if(sameObj == true && inserted == true){
//      		cout << "Error: sameObj == true && inserted = true" << endl;
      		
//      		  cout << "s: ";
//                print_node(s);
//                print_queue(v);    		
//      	}
  	}

    if(v->size()>k){
    	//cout << "\n\nv->size()>k happens,  v->pop_back()\n" << endl;
        v->pop_back();  
    }
	//	cout<<"Inserted:"<<endl;
}
  



//===================================================================//
//HR: Add hash_set to store net_set (i.e. (net, score) pair
namespace gnu_namespace {
	
	template<>

	struct hash<const net_set*> {
	
		hash<unsigned> hasher_ns;
		
		size_t operator()(const net_set* ns) const {
			return hasher_ns((unsigned) roundl(- ns->score*10)); // wrong hash function??
		}

	}; //end of struct

} //end of namespace



struct eqNetScore{
	bool operator()(const net_set* ns1, const net_set* ns2) const{
		return same_obj(*ns1, *ns2);
	}
};



typedef hash_set<const net_set *, hash<const net_set *>, eqNetScore> NetScoreHashSet;

NetScoreHashSet knets;


//HR: Add
void print_nsHashSet(NetScoreHashSet nsHSet, int nof_vars){
	for(NetScoreHashSet::iterator it = nsHSet.begin(); it != nsHSet.end(); it++){
		print_netscore(**it, nof_vars);
	}
}
//



//=====================================================================================//

/*================================================================*/

//HR:F
//HR: call gettopk(&bnets,&bparents,&kbnetscore[varset],k,sink,nof_vars);
void gettopk(vector<net_set>* bnets, vector<score_network>* bparents, list<net_set>* kbnet, int k, varset_t sink,int nof_vars){
	int h;
	int ipmax=bparents->size();
//		cout<<"ipmax: "<<ipmax;
		
	int inmax=bnets->size();
	//	cout<<"inmax: "<<inmax;
	
	//HR: Add hash_set generated here
	
	//
	
	int ip,in;

	net_set * netwP;
	
	score_t score;
	
	list<node> fringe;
	
	//HR: different from Algo4, here start from 0 vs. 1
	node n(0,0,(bparents->at(0)).scores + (bnets->at(0)).score);
	insert_vec(&fringe,n,k);
	//	print_queue(&fringe);
		// scanf("%d",&h);
		
	NetScoreHashSet::iterator iter1;

	while(!fringe.empty()){
		//	cout<<"Fringe not empty"<<endl;		
		n = fringe.front();
		fringe.pop_front();
		//	cout<<"Fringe After popping"<<endl;
		//	print_queue(&fringe);
		ip=n.ip;
		in=n.in;
		score=n.score;				
		//	cout<<"Popped node"<<endl;
		//	cout<<"ip "<<ip<<"in "<<in<<"score "<<score<<endl;
			
		if((kbnet->size())<k){
			//		cout<<"Size < k"<<endl;
			//HR: Add
			//net_set netw;
			//
			netwP = new net_set();
			for(int j=0;j<nof_vars;j++){
				(*netwP).net[j]=(bnets->at(in)).net[j];
				//			cout<<"Net w j"<<netw.net[j]<<endl;
			}						
			(*netwP).net[sink]=((bparents->at(ip)).vset);
			//cout<<"net w sinkleton"<<netw.net[sinkleton]<<endl;
			(*netwP).score=score;
			//cout<<"net score "<<netw.score<<endl;
			//cout<<"Before inserting:"<<endl;
			//	print_queue(kbnet,nof_vars);
			
			//HR: Add hash_set knets check here
			//if not find netw
			if((iter1 = knets.find(netwP)) == knets.end()){
				//Lav
			 	//every time call by value of netw

				insert_vec(kbnet, *netwP, k, nof_vars);
				//cout<<"After inserting"<<endl;
				//print_queue(kbnet,nof_vars);
				//scanf("%d",&h);
				//cout<<"Inserted into kbnetscore"<<endl;
				//
				//HR: Add to knets
				knets.insert(netwP);

		   	}
		   	
		   	else{
		   		if( same_obj(*netwP, **iter1, nof_vars) == false ){
		   			cout<<"Error: same_obj(*netwP, **iter1, nof_vars) == false"<<endl;
		   			return;
		   		}

		   		//delete the memory
		   		delete netwP;

		   	}
		   	
			//
			
		}
				
		else if((fabs(score-(kbnet->back()).score)>EPSILON)&&(score>(kbnet->back().score))){
					//	cout<<"score>kbnetback's score"<<endl;
				
			//HR: Add
			//net_set netw;
			//	
			netwP = new net_set();
			for(int j=0;j<nof_vars;j++){
                (*netwP).net[j]=(bnets->at(in)).net[j];
				//		cout<<"Net w j"<<netw.net[j]<<endl;
             }
            (*netwP).net[sink]=(bparents->at(ip)).vset;				
			//cout<<"net w sinkleton"<<netw.net[sinkleton]<<endl;
			(*netwP).score=score;
			//cout<<"net score"<<netw.score<<endl;
			//cout<<"Before inserting:"<<endl;
			//	print_queue(kbnet,nof_vars);

			
			//HR: Add hash_set knets check here
			//if not find
			if((iter1 = knets.find(netwP)) == knets.end()){
				
				net_set lastNs = kbnet->back();
				
				//Lav
				insert_vec(kbnet,*netwP,k,nof_vars);           
				//cout<<"After inserting"<<endl;
				//print_queue(kbnet,nof_vars);
				//scanf("%d",&h);
				//
				//HR: Add to knets
				knets.insert(netwP);

				iter1 = knets.find(&lastNs);
				
				if(iter1  == knets.end()){
					cout << "Error: iter1 should != knets.end() since lastNs should be found in kents" << endl;
					return;
				}
				const net_set * nsTempP = * iter1;
				
				knets.erase(&lastNs);  //do not remove 
				delete nsTempP;

				//HR: remove from kbnet has been done inside knets.insert(netwP);
			}
			
			else{
		   		if( same_obj(*netwP, **iter1, nof_vars) == false ){
		   			cout<<"Error: same_obj(*netwP, **iter1, nof_vars) == false"<<endl;
		   			return;
		   		}
				
				//delete the memory
				delete netwP;
				

		   	}
		   	


		}
		else{
			return;
		}
		
		//HR: since start from 0 not 1; so use ip<ipmax-1
		if(ip<ipmax-1){
			//cout<<"before child:"<<endl;
			node child(ip+1,in,(bparents->at(ip+1)).scores + (bnets->at(in)).score);
			//cout<<"after child"<<endl;
			//cout<<"Fringe before inserting:"<<endl;
			//print_queue(&fringe);
	
			//may add hash_set generated check here
			
			insert_vec(&fringe,child,k);
			//cout<<"Fringe after inserting"<<endl;
			//print_queue(&fringe);
			//scanf("%d",&h);

		}
		if(in<inmax-1){
			//	cout<<"Fringe before inserting:"<<endl;
           	// print_queue(&fringe);

			node child(ip,in+1,(bparents->at(ip)).scores + (bnets->at(in+1)).score);
			
			//may add hash_set generated check here
			
			insert_vec(&fringe,child,k);
			//cout<<"Fringe after inserting"<<endl;

			//	print_queue(&fringe);
			// scanf("%d",&h);

		}
	}//end of while		
	//	scanf("%d",&h);
}

/*================================================================*/
//converts from the best parent sets obtained to actual variable set
//HR: call conv_net(&kbnetscore[nof_combs-1],k,nof_vars);
//HR:F
void conv_net(list<net_set>* kbnet, int k,int nof_vars){	
	list<net_set>::iterator it;
 	//int j=0;
 	//each it is one of k best 
	for(it=kbnet->begin();it!=kbnet->end();it++){
		for(int p=0;p<nof_vars;p++){

			(*it).net[p]=parset2varset(p,((*it).net[p]));
	//		j++;
		}
	}
}

/*================================================================*/
//To convert net string into arc set
//HR: call net2arc(dirname,k,nof_vars);
//HR:F
void net2arc(char* dirname,int k,int nof_vars){
	score_t s;
	string str;
	unsigned int vset;
	char str1[32];
	//HR: for each j'th best network
	for(int j=0;j<k;j++){
		str.assign(dirname);
	  	str.append("/");
    	sprintf(str1,"%d",j);
    	str.append(str1);
		str.append("net");
		FILE* netf=fopen(str.c_str(),"r");
		if(netf==NULL)
			break;
		str.assign(dirname);
      	str.append("/");
		str.append("arc");
      	sprintf(str1,"%d",j);
      	str.append(str1);

		FILE* arcf=fopen(str.c_str(),"w");
		fscanf(netf,"%f",&s);
		//	printf("\n ===network %d",j);
		for(int p=0;p<nof_vars;p++){
			//		printf("\n\nVar %d's parents:\n\n",p);
			fscanf(netf,"%u",&vset);
			const int SHIFT = 8 * sizeof( unsigned ) - 1;
   			const unsigned MASK = 1 << SHIFT;
			for ( int i = 1; i <= SHIFT + 1; i++ ) {
      			//		cout << ( vset & MASK ? '1' : '0' );
				if(vset&MASK){
					fprintf(arcf,"%u %d",SHIFT+1-i,p);
					fprintf(arcf,"\n");
				}
				vset <<= 1;
      	 	 }
			//	printf("\n\n");
		}
		fclose(netf);			
		fclose(arcf);
	}
}

/*================================================================*/
//Write net file
//HR: call write_net(kbnetscore,dirname,k,nof_vars);
//HR: get files: 0net, 1net, ..., (k-1)net
void write_net(list<net_set> kbnetscore[],char* dirname,int k,int nof_vars)
{
	string str;
	list<net_set>::iterator it;
	char str1[10];
	int j=0;
	//HR: each it is the one of the k best
	for(it=kbnetscore[(1U<<(nof_vars))-1].begin();it!=kbnetscore[(1U<<(nof_vars))-1].end();it++){
    	str.assign(dirname);
      	str.append("/");
      	sprintf(str1,"%d",j++);
      	str.append(str1);
      	str.append("net");
      	FILE* netf=fopen(str.c_str(),"w");
      	fprintf(netf,"%f \n",(*it).score);
		//	cout<<(*it).score<<endl;
      	for(int p=0;p<nof_vars;p++){
			//					(*it).net[p]=parset2varset(
      		fprintf(netf,"%u \n",(*it).net[p]);
        }
      	fclose(netf);
	}

}

/*================================================================*/

void print_scores(list<net_set> kbnetscore[],char* dirname,int k,int nof_vars){
//	string str;
	list<net_set>::iterator it;
		
	int j=0;
	for(j=0;j<((1U<<nof_vars)-1);j++) 
	for(it=kbnetscore[j].begin();it!=kbnetscore[j].end();it++){
     	cout<<(*it).score<<endl;
  	}
	
}

/*================================================================*/


//HR: Add
//HR: Update from comp_post_compl, now compute the exact post. prob. using
//    denominator P(D) instead of \hat{P}(D) = \sum{G \in the set of k best G's} P(G,D)
//    require exactPD.txt (computed by POSTER) is stored under the directory
//Compute each edge out of nof_vars * nof_vars, instead of just the best network

void comp_postExact_compl(list<net_set>* kbnet, int k, char* dirname, int nof_vars){
	
	//cout << "Start comp_postExact_compl()" << endl;
	//long double Sum=0.0;
	//double logSum = LOG_ZERO;
	
	list<net_set>::iterator it;
	
	//HR: assume k < 5000
	long double postExact[5000];

	string str;
	char str1[32];
	
	//cout << "dirname = " << dirname << endl;
	str.assign(dirname);
    str.append("/");
    str.append("exactPD.txt");
    //str.assign("exactPD.txt");
    
    FILE *fp;
	fp=fopen(str.c_str(),"r");
	double exactPD = 0.0;

	if(!feof(fp)){
		char c;
		fscanf(fp, "%lf", &exactPD);
		fscanf(fp,"%c",&c);	
	}
	fclose(fp);
	//printf("exactPD = %18.8f\n", exactPD);

	
	str.assign(dirname);
	str.append("/netpostExact");
	fp=fopen(str.c_str(),"w");

	//for each it of k best networks
//	for(it=kbnet->begin();it!=kbnet->end();it++){
//		//HR: change it
//		//sum+=exp((*it).score);
//		logAddComp(logSum, (*it).score); //HR
//	}	
//	cout << "logSum: " << logSum << endl; //HR
//	printf("logSum = %18.6f\n", logSum);
//	//
//	long double Sum = exp(logSum); //HR
//	cout << "Sum: " << exp(logSum) << endl; //HR
	//
	
	int i=0;
	//for each it of k best networks,  compute equation (4)
	//each post[i] ( 0 <= i <= k - 1). repre post prob for i'th best network  
	for(it=kbnet->begin();it!=kbnet->end();it++){
		//post[i]=(exp((*it).score))/sum;
		//double logScore = (*it).score;
		//LOGMINUS_NEW(logScore, logSum)
		postExact[i]=exp( (*it).score - exactPD ); //
		fprintf(fp,"%Lg\n",postExact[i]);				
		i++;
	}
	fclose(fp);
	
	
	//Start the update
	
	//define and init
	long double postProbExact[nof_vars][nof_vars];
	for(int i = 0; i < nof_vars; i++){
		for (int j = 0; j < nof_vars; j++){
			postProbExact[i][j] = 0.0;
		}
	}

	int listSize = kbnet->size();
	int num1 = -1;
	int num2 = -1;
	
	
	//HR: for each j of k best networks, j >=1
	for(int j=0; j<listSize; j++){
		str.assign(dirname);
    	str.append("/");
    	str.append("arc");
    	sprintf(str1,"%d",j);
		str.append(str1);
		fp=fopen(str.c_str(),"r");
		//				fp=fopen(str.c_str(),"r");
		
		//HR: for each edge in j'th best network
		while(!feof(fp)){
			char c;
			fscanf(fp,"%d",&num1);
			fscanf(fp,"%d",&num2);
			fscanf(fp,"%c",&c);	
			postProbExact[num1][num2] += postExact[j];
			//HR: needed! why? Otherwise there is error. fscanf() could not exactly overwrite the content of &num1/&num2
			num1 = -1;
			num2 = -1;

		}//HR: end of while(!feof(fp));
		fclose(fp);
	}//HR: end of for
	
	str.assign(dirname);
	str.append("/postProbExactEachEdge.txt");
	ofstream totalOf1(str.c_str());

	for(int i = 0; i < nof_vars; i++){
		for (int j = 0; j < nof_vars; j++){
			totalOf1 << "" << j << " -> " << i << " \t" << postProbExact[j][i] << endl;
		}
	}
			
} //end void comp_postExact_compl()




//HR: Add
//HR: Update from comp_post
//Compute each edge out of nof_vars * nof_vars, instead of just the best network
void comp_post_compl(list<net_set>* kbnet, int k, char* dirname, int nof_vars){
	//long double Sum=0.0;
	double logSum = LOG_ZERO;
	list<net_set>::iterator it;
	//HR: assume k < 5000
	long double post[5000];

	string str;
	char str1[32];
	str.assign(dirname);
	str.append("/netpost");
	FILE *fp=fopen(str.c_str(),"w");

	//for each it of k best networks
	for(it=kbnet->begin();it!=kbnet->end();it++){
		//HR: change it
		//sum+=exp((*it).score);
		logAddComp(logSum, (*it).score); //HR
	}	
	//cout << "logSum: " << logSum << endl; //HR
	//printf("logSum = %18.6f\n", logSum);
	//
	long double Sum = exp(logSum); //HR
	//cout << "Sum: " << exp(logSum) << endl; //HR
	//
	
	int i=0;
	//for each it of k best networks,  compute equation (4)
	//each post[i] ( 0 <= i <= k - 1). repre post prob for i'th best network  
	for(it=kbnet->begin();it!=kbnet->end();it++){
		//post[i]=(exp((*it).score))/sum;
		//double logScore = (*it).score;
		//LOGMINUS_NEW(logScore, logSum)
		post[i]=exp( (*it).score - logSum ); //
		fprintf(fp,"%Lg\n",post[i]);				
		i++;
	}
	fclose(fp);
	
	
	//Start the update
	
	//define and init
	long double postProb[nof_vars][nof_vars];
	for(int i = 0; i < nof_vars; i++){
		for (int j = 0; j < nof_vars; j++){
			postProb[i][j] = 0.0;
		}
	}

	int listSize = kbnet->size();
	int num1 = -1;
	int num2 = -1;
	
	
	//HR: for each j of k best networks, j >=1
	for(int j=0; j<listSize; j++){
		str.assign(dirname);
    	str.append("/");
    	str.append("arc");
    	sprintf(str1,"%d",j);
		str.append(str1);
		fp=fopen(str.c_str(),"r");
		//				fp=fopen(str.c_str(),"r");
		
		//HR: for each edge in j'th best network
		while(!feof(fp)){
			char c;
			fscanf(fp,"%d",&num1);
			fscanf(fp,"%d",&num2);
			fscanf(fp,"%c",&c);	
			postProb[num1][num2] += post[j];
			//HR: needed! why? Otherwise there is error. fscanf() could not exactly overwrite the content of &num1/&num2
			num1 = -1;
			num2 = -1;

		}//HR: end of while(!feof(fp));
		fclose(fp);
	}//HR: end of for
	
	str.assign(dirname);
	str.append("/postProbEachEdge.txt");
	ofstream totalOf1(str.c_str());

	for(int i = 0; i < nof_vars; i++){
		for (int j = 0; j < nof_vars; j++){
			totalOf1 << "" << j << " -> " << i << " \t" << postProb[j][i] << endl;
		}
	}
			
}



//Computation of posterior probabilities - This function's call has been commented. Remove the commenting slashes if the posterior probability needs to be computed.
//HR: call comp_post(&kbnetscore[nof_combs-1],k,dirname);

void comp_post(list<net_set>* kbnet, int k, char* dirname){
	//long double Sum=0.0;
	double logSum = LOG_ZERO;
	list<net_set>::iterator it;
	long double post[5000];

	string str;
	char str1[32];
	str.assign(dirname);
	str.append("/netpost");
	FILE *fp=fopen(str.c_str(),"w");

	//for each it of k best networks
	for(it=kbnet->begin();it!=kbnet->end();it++){
		//HR: change it
		//sum+=exp((*it).score);
		logAddComp(logSum, (*it).score); //HR
	}	
	cout << "logSum: " << logSum << endl; //HR
	//
	long double Sum = exp(logSum); //HR
	cout << "Sum: " << exp(logSum) << endl; //HR
	//
	
	int i=0;
	//for each it of k best networks,  compute equation (4)
	//each post[i] ( 0 <= i <= k - 1). repre post prob for i'th best network  
	for(it=kbnet->begin();it!=kbnet->end();it++){
		//post[i]=(exp((*it).score))/sum;
		//double logScore = (*it).score;
		//LOGMINUS_NEW(logScore, logSum)
		post[i]=exp( (*it).score - logSum ); //
		fprintf(fp,"%Lg\n",post[i]);				
		i++;
	}
	fclose(fp);
	
	
	ifstream indata;
	int n=0;
	int num[50][2];
	int num1,num2;
	//		char f[50][20];
	long double postf[50];
	str.assign(dirname);
    str.append("/");
    str.append("arc0");
	fp=fopen(str.c_str(),"r");
	//   	fp=fopen(str.c_str(),"r");
	i=0;
	while(!feof(fp)){
		char c;
		//HR: for parent
		fscanf(fp,"%d",&num[i][0]);
		//HR: for var
		fscanf(fp,"%d",&num[i][1]);
		fscanf(fp,"%c",&c);

		//indata>>num[i][1];
		//HR:
		//				cout<<"HIIIII"<<num[i][0]<<"'"<<num[i][1]<<endl;

		//				indata.getline(f[i],100);
		i++;
	}
	//HR:
	//i: the no. of lines in the arc0; so only for the edges in the best network
	n=i-1;
	cout<<n<<endl;
	
	//HR: postf vs. post
	//HR: due to equality (5)
	for(i=0;i<n;i++){
		postf[i]=post[0];
	}
		
	//indata.close();
	fclose(fp);
	int j;
	
	//HR: for each j of k best networks, j >=1
	for(j=1;j<k;j++){
		str.assign(dirname);
    	str.append("/");
    	str.append("arc");
    	sprintf(str1,"%d",j);
		str.append(str1);
		fp=fopen(str.c_str(),"r");
		//				fp=fopen(str.c_str(),"r");
		
		//HR: for each edge in j'th best network
		while(!feof(fp)){
			char c;
			fscanf(fp,"%d",&num1);
			fscanf(fp,"%d",&num2);
			fscanf(fp,"%c",&c);
			//						indata>>num1;
			//					indata>>num2;
	
			//HR:
			//						cout<<endl<<endl<<endl<<num1<<",,,"<<num2<<endl;
			//HR: check whether such edge is one of the edeges in the best network 0net
			for(i=0;i<n;i++){
//				if(!strcmp(str1,f[i]))
				//HR: if such edge is one of the edeges in the best network 0net
				if(num1==num[i][0]&&num2==num[i][1]){
					postf[i]+=post[j];
					//HR: can add break; since each edge out of n edges in the best net is different
				}	
			}
			num1=num2=-1;
		}//HR: end of while(!feof(fp));
		fclose(fp);
	}//HR: end of for

	//HR: print out the posterior prob. for each edge in the best network
	for(j=0;j<n;j++)
		cout<<postf[j]<<endl;
			
}


/*================================================================*/
//inside it: get_top_k for the features
//HR:F
void get_kbest_nets(int nof_vars, FILE** fp,int k, char* dirname){
    int h;
    //HR: 2^nof_vars
	varset_t nof_combs = 1U<<(nof_vars);
	//HR:
	//cout<<"No of combs:  "<<nof_combs<<endl;
    //cout<<"Get k best nets"<<endl;
    //
	
	//cout << "before list<net_set> kbnetscore[nof_combs]" << endl;
	//cerr << "before list<net_set> kbnetscore[nof_combs]" << endl;
	//HR: net_set is the (net, score) pair
	//HR: have not yet set each list kbnetscore[W] the size k
	list<net_set> kbnetscore[nof_combs];
	//cerr << "after list<net_set> kbnetscore[nof_combs]" << endl;
	//cout << "after list<net_set> kbnetscore[nof_combs]" << endl;
    list<net_set>::iterator it;
    
	vector<net_set> bnets;
	
	//HR: score_network is (variable set, score) pair
	score_network s;
	
	vector<score_network> bparents;
	
	//HR: Add hash_set knets here; global may be better so that gettopk can access
	//NetScoreHashSet knets;
	//
	//cout << "before net_set netw" << endl;
	
	net_set netw;
	netw.score=0;
	for(int j=0;j<nof_vars;j++)
		netw.net[j]=0;
	kbnetscore[0].push_back(netw);
	
	//cout << "before insert_vec(&kbnetscore[0],netw,k,nof_vars)" << endl;
		
	//HR
	insert_vec(&kbnetscore[0],netw,k,nof_vars);
		
	//HR: call void print_queue(list<net_set> v[],int size,int nof_vars)
	//cout << "print_queue(kbnetscore)" << endl;
	//print_queue(kbnetscore,(1U<<nof_vars),nof_vars);
//	scanf("%d",&h);
//	cout<<"Pushed!!!"<<endl;
	//
	
	varset_t varset;
	int sink;
	
	//HR: W \subset V except empty set; 	
	//HR: varset is the binary repre of W
	for(varset=1;varset<nof_combs;varset++){
		//HR
//		if(varset % 10000 == 1){
//			cout<<"====Varset==="<<varset<<endl; //
//		}
		//
		
		
		//HR: Add for hash_set knets
		//totally clean
		//cout << "knets.clear() begins:" << endl;		
		int knetsSize = knets.size() ;
		const net_set * nsPtrA[knetsSize];
		int j = 0;
		for(NetScoreHashSet::iterator it = knets.begin(); it != knets.end(); it++){
			nsPtrA[j++] = *it;
		}
		knets.clear();

		//really delete each object
		for(j = 0; j < knetsSize; j++){
			delete nsPtrA[j];  //delete (*nsPtrA[j]) is wrong 
		}		
		//cout << "knets.clear() ends." << endl;
		//
		

 		//HR: for each sink
		for(sink = 0; sink<nof_vars;sink++){
			//		cout<<"Sink "<<sink<<endl;
			varset_t sinkleton = 1U<<sink;
			//			cout<<"sinkleton"<<sinkleton<<endl;
			varset_t upvars;
			
			//HR: if sink is the subset of W
			if (varset & sinkleton){
				//				cout<<"inside if loop";
				upvars = varset& ~sinkleton;
				//			cout<<"Upvars:"<<upvars<<endl;
				
				//HR: construct array bnets from kbnetscore[upvars]
				bnets.clear();
				bnets.resize(kbnetscore[upvars].size());
				copy(kbnetscore[upvars].begin(), kbnetscore[upvars].end(), bnets.begin());
				//	print_queue(&bnets,nof_vars);
				//	scanf("%d",&h);
				//cout<<"Copied"<<endl;
				
				//HR: construct array bparents from kbps[s][upvars]
				bparents.clear();
				while(1){
					fread(&s.scores,sizeof(score_t),1,fp[sink]);
					//cout<<"read score"<<endl;
					if(s.scores==1000.0)
						break;

					fread(&s.vset,sizeof(varset_t),1,fp[sink]);	
					//	cout<<"read varset"<<endl;
					//int num =k;
					//insert_vec(&bparents,s,num);
										
					bparents.push_back(s);
					//	print_queue(&bparents);
					//scanf("%d",&h);
				}
				
				//scanf("%d",&h);
				//printf("\n\n\nB PARENTS ============\n\n");
				//print_queue(&bparents);
				//scanf("%d",&h);

				//printf("\n\n\nB nets =================\n\n");
				//print_queue(&bnets,nof_vars);
				//scanf("%d",&h);

				//printf("\n\n\nKBNETSCORE %u =================\n\n",varset);
				//print_queue(&kbnetscore[varset],nof_vars);
				//scanf("%d",&h);

				////printf("K = %d",k);

				//printf("Sink = %d",sink);



				//cout<<"Goin to call gettopk"<<endl;
            	gettopk(&bnets,&bparents,&kbnetscore[varset],k,sink,nof_vars); //new function: for each combination from n: totally 2^n
				//printf("\n\n\nAfter topkKBNETSCORE %u =================\n\n",varset);
				//print_queue(&kbnetscore[varset],nof_vars);
				
			}//end of if


		}//end of for each sink
		//scanf("%d",&h);
		
	}//end of for each W
	//HR: last clear
	//cout << "knets.clear() at the end of V begins: " << endl;
		int knetsSize = knets.size() ;
		const net_set * nsPtrA[knetsSize];
		int j = 0;
		for(NetScoreHashSet::iterator it = knets.begin(); it != knets.end(); it++){
			nsPtrA[j++] = *it;
		}
		knets.clear();

		//really delete each object
		for(j = 0; j < knetsSize; j++){
			delete nsPtrA[j];  //delete (*nsPtrA[j]) is wrong 
		}
	//cout << "knets.clear() at the end of V ends. " << endl;
	

	
	//HR
	//print_queue(kbnetscore,32,5); //32 = 2^5

	//cout << "main part of get__kbest_nets() is done." << endl;
	
	//HR: only consider V, the bianary repre is nof_combs-1
	conv_net(&kbnetscore[nof_combs-1],k,nof_vars);
	//cout << "conv_net(&kbnetscore[nof_combs-1],k,nof_vars) is done." << endl;
		
	//HR: get files: 0net, 1net, ..., (k-1)net	
	write_net(kbnetscore,dirname,k,nof_vars);
	//cout << "write_net(kbnetscore,dirname,k,nof_vars) is done." << endl;
	
	net2arc(dirname,k,nof_vars);
	//cout << "net2arc(dirname,k,nof_vars) is done." << endl;
	
	//HR:
	//cout << endl;
	//cout << "kbnetscore[nof_combs-1].size() = " << kbnetscore[nof_combs-1].size() << endl;
	//cout << "1st network score: " << kbnetscore[nof_combs-1].front().score << endl;
	//cout << "last network score: " << kbnetscore[nof_combs-1].back().score << endl;
	//cout << "The difference of the scores = " << ( kbnetscore[nof_combs-1].front().score - kbnetscore[nof_combs-1].back().score ) << endl;
	//cout << endl;
	//

	//comp_post(&kbnetscore[nof_combs-1],k,dirname);  //new function: compute the posterior prob.
	comp_post_compl(&kbnetscore[nof_combs-1],k,dirname, nof_vars);	
	cout << "comp_post_compl(&kbnetscore[nof_combs-1],k,dirname) is done." << endl;
	
	//comp_postExact_compl(&kbnetscore[nof_combs-1],k,dirname, nof_vars);
	cout << "comp_postExact_compl(&kbnetscore[nof_combs-1],k,dirname, nof_vars) is not done. we will change it if requires" << endl;
	
	//HR:
	//	print_scores(kbnetscore,dirname,k,nof_vars);	
	
}//end of get_kbest_nets

/*================================================================*/

//HR:F
int main(int argc, char* argv[]){
	FILE* fp[32];
	string str;
	char str1[10];
	//	printf("argc : %d \n\n",argc);
	if(argc!=4){
		fprintf(stderr, "Usage: get_kbest_nets nof_vars dirname k\n");
        return 1;
	}
		
	int nof_vars = atoi(argv[1]);
	int k = atoi(argv[3]);
	//	printf("No of vars: %d   K: %d\n\n",nof_vars,k);
	for(int j=0;j<nof_vars;j++){
		str.assign(argv[2]);
		str.append("/");
		sprintf(str1,"%d",j);
		str.append(str1);
		str.append(".kbps");
		//		cout<<str<<endl;
		//HR:   In order to open a file as a binary file, a "b" character has to be included in the mode string
		fp[j]=fopen(str.c_str(),"rb");
	}
	//HR:
	//cout<<"Calling function getkbestnets\n";
	//
	get_kbest_nets(nof_vars, fp, k,argv[2]);
	//cout<<"Success!"<<endl;
	return 0;
		
}
	


