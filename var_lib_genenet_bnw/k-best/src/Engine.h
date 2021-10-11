#ifndef ENGINE_H
#define ENGINE_H

#include<stdio.h>
#include<stdlib.h>

#include"Arguments.h"
#include"Model.h"

#include <sys/time.h>
#include <time.h> 

#define MARK 9.0e99

// Substitutes la += log(1+exp(lb-la)) <=> a = a + b.
#define LOGADD(la,lb) if(la>8e99||lb-la>100) la=lb;else la+=log(1+exp(lb-la));

#define LOG_ZERO -1.0e101

using namespace std;

#include"UpdateHR.h"



//----------------
// Fast Möbius transform routines.
void sub_fumt(int j, int d, int S, int overlap, double *t, double *s, int n, int k){
	if (d < n && overlap < k){
		sub_fumt(j, d+1, S, overlap, t, s, n, k);
		
		
		S |= (1 << d);
		overlap += (d >= j+1);
		sub_fumt(j, d+1, S, overlap, t, s, n, k); 
	}
	
	else{
		//base case: 
		//since s[S] = MARK initially,  and the following 3 cases, s[S] must be assigned some score (neither MARK nor undecided)
		s[S] = MARK;
		
		//jinS: j is in S
		int jinS = ((S >> j) & 1);
		if (overlap + jinS <= k) s[S] = t[S];
		//if (jinS) LOGADD(s[S], t[S - (1 << j)]);
		if (jinS) logAdd_New(s[S], t[S - (1 << j)]);
	}
}

// Fast upward Möbius transform:
// s(S) := \sum_{T \subseteq S : |T| \leq k} t(T).
void fumt(double *t, double *s, int n, int k){
	double *tmp, *sprev = new double[1 << n];
	for (int T = 0; T < (1<<n); T ++) 
		sprev[T] = t[T];
	for (int j = 0; j < n; j ++){
		//for each j
		sub_fumt(j, 0, 0, 0, sprev, s, n, k);
		//recyclely use sprev, s
		tmp = sprev; 
		sprev = s; 
		s = tmp;
	}
	if (n % 2 == 0){ 
		for (int S = 0; S < (1<<n); S ++){ 
			s[S] = sprev[S];
		}
	}
	else{ 
		tmp = sprev; 
		sprev = s; 
		s = tmp;
	}
	delete [] sprev;
}
void test_fumt(){
	int n = 4;
	double t[16], s[16];
	for (int S = 0; S < 16; S ++){
		t[S] = log(S+1);
	}
	fumt(t, s, n, 2);
	for (int S = 0; S < 16; S ++){
		cerr<<" s["<<S<<"] = "<<exp(s[S])<<endl;
	}
}


void sub_fdmt(int j, int d, int T, int overlap, double *s, double *t, int n, int k){
	//cerr<<" T = "<<T<<":"; print_set(cerr, T); 
	//cerr<<" overlap = "<<overlap<<endl; 
	if (d < n && overlap <= k){
		sub_fdmt(j, d+1, T, overlap, s, t, n, k);
		T |= (1 << d);
		overlap += (d <= j);
		sub_fdmt(j, d+1, T, overlap, s, t, n, k); 
	}
	else if (d == n){
		t[T] = s[T];
		int jinT = ((T >> j) & 1);
		//if (jinT == 0) LOGADD(t[T], s[T + (1 << j)]);
		if (jinT == 0) logAdd_New(t[T], s[T + (1 << j)]);
		if (overlap > k) t[T] = MARK;
	}
}
// Fast downward Möbius transform:
// t(T) := \sum_{T \subseteq S} s(S).
void fdmt(double *s, double *t, int n, int k){
	double *tmp, *tprev = new double[1 << n];
	for (int S = 0; S < (1<<n); S ++) tprev[S] = s[S];
	for (int j = 0; j < n; j ++){
		
		sub_fdmt(j, 0, 0, 0, tprev, t, n, k);	
		tmp = t; t = tprev; tprev = tmp;
	}
	if (n % 2 == 0){ for (int T = 0; T < (1<<n); T ++) t[T] = tprev[T];}
	else{ tmp = t; t = tprev; tprev = tmp;}
	delete [] tprev;
}


void sub_fdmtLogAddComp(int j, int d, int T, int overlap, double *s, double *t, int n, int k){
	//cerr<<" T = "<<T<<":"; print_set(cerr, T); 
	//cerr<<" overlap = "<<overlap<<endl; 
	if (d < n && overlap <= k){
		sub_fdmtLogAddComp(j, d+1, T, overlap, s, t, n, k);
		T |= (1 << d);
		overlap += (d <= j);
		sub_fdmtLogAddComp(j, d+1, T, overlap, s, t, n, k); 
	}
	else if (d == n){
		t[T] = s[T];
		int jinT = ((T >> j) & 1);
		if (jinT == 0) logAddComp(t[T], s[T + (1 << j)]);
		if (overlap > k) t[T] = MARK;
	}
}
// Fast downward Möbius transform:
// t(T) := \sum_{T \subseteq S} s(S).
void fdmtLogAddComp(double *s, double *t, int n, int k){
	double *tmp, *tprev = new double[1 << n];
	for (int S = 0; S < (1<<n); S ++) tprev[S] = s[S];
	for (int j = 0; j < n; j ++){
		
		sub_fdmtLogAddComp(j, 0, 0, 0, tprev, t, n, k);	
		tmp = t; t = tprev; tprev = tmp;
	}
	if (n % 2 == 0){ for (int T = 0; T < (1<<n); T ++) t[T] = tprev[T];}
	else{ tmp = t; t = tprev; tprev = tmp;}
	delete [] tprev;
}


void sub_fdmtNoLog(int j, int d, int T, int overlap, double *s, double *t, int n, int k){
	//cerr<<" T = "<<T<<":"; print_set(cerr, T); 
	//cerr<<" overlap = "<<overlap<<endl; 
	if (d < n && overlap <= k){
		sub_fdmtNoLog(j, d+1, T, overlap, s, t, n, k);
		T |= (1 << d);
		overlap += (d <= j);
		sub_fdmtNoLog(j, d+1, T, overlap, s, t, n, k); 
	}
	else if (d == n){
		t[T] = s[T];
		int jinT = ((T >> j) & 1);
		//if (jinT == 0) LOGADD(t[T], s[T + (1 << j)]);
		//if (jinT == 0) logAdd_New(t[T], s[T + (1 << j)]);
		if (jinT == 0) t[T] += s[T + (1 << j)];
		if (overlap > k) t[T] = MARK;
	}
}

// Fast downward Möbius transform:
// t(T) := \sum_{T \subseteq S} s(S).
void fdmtNoLog(double *s, double *t, int n, int k){
	double *tmp, *tprev = new double[1 << n];
	for (int S = 0; S < (1<<n); S ++) tprev[S] = s[S];
	for (int j = 0; j < n; j ++){
		
		sub_fdmtNoLog(j, 0, 0, 0, tprev, t, n, k);	
		tmp = t; t = tprev; tprev = tmp;
	}
	if (n % 2 == 0){ for (int T = 0; T < (1<<n); T ++) t[T] = tprev[T];}
	else{ tmp = t; t = tprev; tprev = tmp;}
	delete [] tprev;
}


void test_fdmt(){
	int n = 4;
	double t[16], s[16];
	for (int S = 0; S < 16; S ++){
		s[S] = log(S+1);
	}
	fdmt(s, t, n, 3);
	for (int T = 0; T < 16; T ++){
		cerr<<" t["<<T<<"] = "<<exp(t[T])<<endl;
	}
}

//----------------






// NOTE: Engine only calls a veru limited set of interface functions
// implemented in Model.
class Engine {
public:
	Engine(){}
	~Engine(){}	
	
	void init(Model *m){
		model = m;
		//test();
	
	}
	void test(){
		vector<int> T;
		T.push_back(1); T.push_back(2);
		
		cerr<<" log p(x_0 | x_1,2): "<<model->log_lcp(0, T)<<endl;
	
		for (int h = 0; h < model->num_layers(); h ++){
			int *Vh, *Vu, *Vl;
			int nh, nu, nl;
			model->layer(h, &Vh, &nh);	
			model->upper_layers(h, &Vu, &nu);	
			model->lower_layers(h, &Vl, &nl);
			cerr<<"Layer "<<h<<":";
			cerr<<endl<<" layer: ";
			print_nodes(cerr, Vh, nh);
			cerr<<endl<<" upper: ";
			print_nodes(cerr, Vu, nu);
			cerr<<endl<<" lower: ";
			print_nodes(cerr, Vl, nl);
			cerr<<endl;	
		}
		
		//test_fumt();
		test_fdmt(); 
		exit(1);

	}
	// Computes all edge probabilities.
	void compute_edge_probabilities(){
//		int l = model->num_layers();
	
		//Arguments::option == 0 means the rebel method with new Dirichlet hyper-param (see sub_beta)
		if(Arguments::option == 0){
			compute_edge_probabilities(0);
		}
		//Arguments::option == 1 means the forward method
		else if(Arguments::option == 1){
			compute_edge_probabilities_forward(0);
		}
		//Arguments::option == 2 means the backward method
		else if(Arguments::option == 2){
			compute_edge_probabilities_backward(0);
		}
		//Arguments::option == 3 means the mix method in the UAI paper
		else if(Arguments::option == 3){
			compute_edge_probabilities_mixIndegree(0);
		 	//compute_edge_probabilities_mixIndegreeNoLog(0);
		}
		//Arguments::option == 4 means the computation used in top-k
		else if(Arguments::option == 4){
			compute_edge_probabilities_top_k(0);
		}
		//Arguments::option == 5 means the computation required for in and out features
		else if(Arguments::option == 5){
			//For In_Out features
			compute_edge_probabilities_in_out_features(0);
		}
		
		
	}
	// Computes probabilities for edges pointing to the h-th layer.
	void compute_edge_probabilities(int h){
		cerr<<"\nREBEL Method:" << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
		
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);

		
		beta = new double*[nh];
		alpha = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		struct timeval startTime;
		struct timeval endTime;
		struct timezone myTimeZone;

		
		double betaTime;
		double alphaTime;
		double forwardTime;
		double backwardTime;
		double gammaTime;
		
		double step2BTime;
	
		gettimeofday(&startTime, &myTimeZone);		

		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			model->makeADTree();
		}
		
		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];
			//int i = Vh[j];
			compute_beta(j, Vh, nh, Vu, nu);
		
		}
		
		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
		
		gettimeofday(&endTime, &myTimeZone);		
		betaTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		           
		cerr<<" . Tables beta are now ready."<<endl;
		
		gettimeofday(&startTime, &myTimeZone);
		for (int j = 0; j < nh; j ++){

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
		
			//cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		gettimeofday(&endTime, &myTimeZone);		
		alphaTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		cerr<<" . Tables alpha are now ready."<<endl;

			
		// Step 3: Compute g_forward[];
		gf = new double[1 << nh];
		
		gettimeofday(&startTime, &myTimeZone);
		
		compute_g_forward(nh);
		
		gettimeofday(&endTime, &myTimeZone);		
		forwardTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		//cerr<<" . gf[all] = "<<gf[(1<<nh)-1]<<endl;
		
		// Step 4: Compute g_backward[];
		
		gb = new double[1 << nh];
		
		gettimeofday(&startTime, &myTimeZone);
		
		compute_g_backward(nh);
		
		gettimeofday(&endTime, &myTimeZone);		
		backwardTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		//cerr<<" . gb[all] = "<<gb[(1<<nh)-1]<<endl;
	
		// Step 5: Loop over end-point nodes j.
		
		gammaTime = 0.0;
		step2BTime = 0.0;
		
		double *gamma = new double[1 << nh];
		double *gfb = new double[1 << nh];
		for (int j = 0; j < nh; j ++){
			// Compute gfb[]. 
			
			//start counting gammaTime for each j
			gettimeofday(&startTime, &myTimeZone);
			
			for (int S = 0; S < (1<<nh); S ++){
				
				//if j \in the binary repre of S
				if ((S>>j) & 1){
					gfb[S] = -MARK;
				}
				else{
					int cS = (1 << nh) - 1 - S - (1 << j);
					gfb[S] = gf[S] + gb[cS];
					//cerr<<" j = "<<j<<": ";
					//print_set(cerr, S);
					//cerr<<"; ";
					//print_set(cerr, cS);
					//cerr<<": "<<gfb[S];
					//cerr<<endl;
				}
			}
			// Compute gamma_j() := gamma[].
			fdmt(gfb, gamma, nh, k);
			
			gettimeofday(&endTime, &myTimeZone);		
			gammaTime += (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
			//end counting gammaTime for each j
			
			//cerr<<" gamma[0]: "<<gamma[0]<<endl;
			//cerr<<" gfb[0]: "<<gfb[0]<<endl;
			
			cerr<<" . Incoming edges for node "<<j<<":";
			// Loop over start-point nodes i.
			
			//start counting step2BTime for each j
			gettimeofday(&startTime, &myTimeZone);
			
			for (int i = 0; i < nh; i ++){
				if (i == j) continue;
				cerr<<" "<<i;		
				double log_prob = eval_edge(i,  j, gamma, beta[j], nh, k);
		
				model->print_edge_prob(
					cout, Vh[i], Vh[j], exp(log_prob - gb[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)
			
			}
			gettimeofday(&endTime, &myTimeZone);		
			step2BTime += (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
			//end counting step2BTime for each j
			
			
			
			cerr<<endl;
			
		} 
		
		
		cout << "\n\n\nTime Statistics" << endl;
		cout << "betaTime = " << betaTime <<  endl;				
		cout << "alphaTime = " << alphaTime <<  endl;
		cout << "forwardTime = " << forwardTime <<  endl;
		cout << "backwardTime = " << backwardTime <<  endl;				
		cout << "gammaTime = " << gammaTime <<  endl;
		
		cout << "step2BTime = " << step2BTime <<  endl;
		
		double totalTime = betaTime + alphaTime + forwardTime 
						+ backwardTime + gammaTime + step2BTime;
		cout << "totalTime = " << totalTime <<  endl;
		
		/*
		ofResult << "\n\n\nTime Statistics" << endl;
		ofResult << "betaTime = " << betaTime <<  endl;				
		ofResult << "alphaTime = " << alphaTime <<  endl;
		ofResult << "RRFuncTime = " << RRFuncTime <<  endl;
		ofResult << "HFuncTime = " << HFuncTime <<  endl;
		ofResult << "KFuncTime = " << KFuncTime <<  endl;		
				
		ofResult << "GammaTime = " << GammaTime <<  endl;
		ofResult << "step2Time = " << step2Time <<  endl;
		double totalTime = betaTime + alphaTime + RRFuncTime 
						+ HFuncTime + KFuncTime + GammaTime + step2Time;
		ofResult << "totalTime = " << totalTime <<  endl;
		*/
		
		
		delete [] gamma;
		delete [] gfb;
	
		delete [] gf; delete [] gb;
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; delete [] alpha[j];
		}
		delete [] beta; delete [] alpha;
		cerr<<" Edge probabilities now computed."<<endl; 
	}
	
	
	void compute_edge_probabilities_ind(int h){
		cerr<<"\nREBEL Method with each individual edge:" << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
				
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
				
		beta = new double*[nh];
		alpha = new double*[nh];
		
		betaNume = new double*[nh];
		alphaNume = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];

			//int i = Vh[j];
			compute_beta(j, Vh, nh, Vu, nu);
			
			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
			
			//alphaNume[j] = new double[1 << nh];	
			//compute_alphaNume(j, Vh, nh);
		
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
	
	  for(int uu = 0; uu < nh; uu++){
	   for(int vv = 0; vv < nh; vv++){
	  	if(vv != uu){

		cerr<<" Compute edge ("<< uu <<"<-" << vv <<")" <<endl;

		for (int j = 0; j < nh; j ++){
			betaNume[j] = new double[1 << nh];
			compute_betaNume(j, Vh, nh, Vu, nu, uu, vv);
			
			alphaNume[j] = new double[1 << nh];	
			compute_alphaNume(j, Vh, nh);
		
			cerr<<" . Tables betaNume and alphaNume computed for node "<<j<<"."<<endl; 
		
		}
	
		cerr<<" . Tables beta/betaNume and alpha/alphaNume are now ready."<<endl;

			
		// Step 3: Compute g_forward[];
		gf = new double[1 << nh];

		compute_g_forward(nh);

		gfNume = new double[1 << nh];
		compute_gNume_forward(nh);
		
		cerr << "\nalpha" << endl;
		for (int j = 0; j < nh; j ++){		
			for(int index=0; index < (1 << nh); index++){
				cerr<< "alpha[" << j << "][" << index << "] =" <<  alpha[j][index] << endl;
			}
		}
		
		cerr << "\nalphaNume" << endl;
		for (int j = 0; j < nh; j ++){		
			for(int index=0; index < (1 << nh); index++){
				cerr<< "alphaNume[" << j << "][" << index << "] =" <<  alphaNume[j][index] << endl;
			}
		}
		
		cerr << "\ngf:" << endl;
		for(int index = 0; index < (1 << nh); index++){
			cerr << "gf[" << index << "] = " << gf[index] << endl;
		}
		
		cerr << "\ngfNume:" << endl;
		for(int index = 0; index < (1 << nh); index++){
			cerr << "gfNume[" << index << "] = " << gfNume[index] << endl;
		}

		cerr<<"gf[all] = "<<gf[(1<<nh)-1]<<endl;
		cerr<<"gfNume[all] = "<<gfNume[(1<<nh)-1]<<endl;
		
		//For checking: Compute g_backward[];
		
		gb = new double[1 << nh];
		compute_g_backward(nh);	
		cerr<<" gb[all] = "<<gb[(1<<nh)-1]<<endl;
				
		cerr<<" . Incoming edges from node " << vv << " to node " << uu <<" :";
		model->print_edge_prob(cout, Vh[vv], Vh[uu], exp(gfNume[(1<<nh)-1] - gf[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)		
		cerr<<endl;
		
	  	}
	   }
	  }
		
		delete [] gf; 
		delete [] gfNume; 
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] betaNume[j];
			delete [] alpha[j];
			delete [] alphaNume[j];
		}
		delete [] beta; 
		delete [] betaNume; 
		delete [] alpha;
		delete [] alphaNume;
		
		cerr<<" Edge probabilities now computed."<<endl; 
		
		return;
		
		

	}
	
		
	//HR: new forward method, compute for each individual edge
	void compute_edge_probabilities_forward(int h){
		cerr<<"Forward method: " << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
				
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
				
		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			model->makeADTree();
		}

	
		beta = new double*[nh];
		alpha = new double*[nh];
		
		betaNume = new double*[nh];
		alphaNume = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];
					
			//int i = Vh[j];
			compute_beta(j, Vh, nh, Vu, nu);

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
				
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		
		hFunc = new double[1 << nh];
		compute_h_Fast(nh, hFunc, alpha);
		cerr<<"hFunc[all] = "<<hFunc[(1<<nh)-1]<<endl;

		
	    for(int uu = 0; uu < nh; uu++){
	    	for(int vv = 0; vv < nh; vv++){
	  			if(vv != uu){
	  		
					cerr<<"Compute edge ("<< uu <<"<-" << vv <<")" <<endl;
	
					for (int j = 0; j < nh; j ++){
						betaNume[j] = new double[1 << nh];

						compute_betaNume(j, Vh, nh, Vu, nu, uu, vv);
			
						alphaNume[j] = new double[1 << nh];	
						compute_alphaNume(j, Vh, nh);
		
						//cerr<<" . Tables betaNume and alphaNume computed for node "<<j<<"."<<endl; 
		
					}
		
		
					cerr<<" . Tables beta/betaNume and alpha/alphaNume are now ready."<<endl;

		
					hFuncNume = new double[1 << nh];
					compute_h_Fast(nh, hFuncNume, alphaNume);

					cerr<<"hFunc[all] = "<<hFunc[(1<<nh)-1]<<endl;
					cerr<<"hFuncNume[all] = "<<hFuncNume[(1<<nh)-1]<<endl;
				
					cerr<<" . Incoming edges from node " << vv << " to node " << uu <<" :";
					model->print_edge_prob(cout, Vh[vv], Vh[uu], exp(hFuncNume[(1<<nh)-1] - hFunc[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)		
					cerr<<endl;
		
	  			}
	   		}
	  	}
		
		
		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
		
		delete [] hFunc; 
		
		//it better to move the following line inside the double for loop
		delete [] hFuncNume; 
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] betaNume[j];
			delete [] alpha[j];
			delete [] alphaNume[j];
		}
		delete [] beta; 
		delete [] betaNume; 
		delete [] alpha;
		delete [] alphaNume;
		cerr<<" Edge probabilities now computed."<<endl; 
		
		return;
		
		

	}
	
	
	//HR: new backward method, compute for each individual edge
	void compute_edge_probabilities_backward(int h){
		cerr<<"Backward method: " << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
				
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
		
		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			model->makeADTree();
		}
	
		beta = new double*[nh];
		alpha = new double*[nh];
		
		betaNume = new double*[nh];
		alphaNume = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];

			compute_beta(j, Vh, nh, Vu, nu);

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
			
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		
		RRFunc = new double[1 << nh];
		compute_RR_Fast(nh, RRFunc, alpha);
		cerr<<"RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl;
		
					
		//int uu;
		//int vv;
	    //uu = 2;
		//vv = 1;	
	    for(int uu = 0; uu < nh; uu++){
	    	for(int vv = 0; vv < nh; vv++){
	  			if(vv != uu){
	  		
					cerr<<"Compute edge ("<< uu <<"<-" << vv <<")" <<endl;
	
					for (int j = 0; j < nh; j ++){

						betaNume[j] = new double[1 << nh];
						compute_betaNume(j, Vh, nh, Vu, nu, uu, vv);
			
						alphaNume[j] = new double[1 << nh];	
						compute_alphaNume(j, Vh, nh);
		
						cerr<<" . Tables betaNume and alphaNume computed for node "<<j<<"."<<endl; 
		
					}
				
					cerr<<" . Tables beta/betaNume and alpha/alphaNume are now ready."<<endl;
		
					RRFuncNume = new double[1 << nh];
					compute_RR_Fast(nh, RRFuncNume, alphaNume);

					cerr<<"RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl;
					cerr<<"RRFuncNume[all] = "<<RRFuncNume[(1<<nh)-1]<<endl;

					cerr<<" . Incoming edges from node " << vv << " to node " << uu <<" :";
					model->print_edge_prob(cout, Vh[vv], Vh[uu], exp(RRFuncNume[(1<<nh)-1] - RRFunc[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)		
					cerr<<endl;
		
	  			}
	   		}
	  	}
		
		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
		
		delete [] RRFunc;
		
		//it better to move the following line inside the double for loop 
		delete [] RRFuncNume; 
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] betaNume[j];
			delete [] alpha[j];
			delete [] alphaNume[j];
		}
		delete [] beta; 
		delete [] betaNume; 
		delete [] alpha;
		delete [] alphaNume;
		cerr<<" Edge probabilities now computed."<<endl; 
		
		return;
		
		

	}
	

	void compute_edge_probabilities_in_out_features(int h){
		cerr<<"Backward method for in and out features: " << endl;
		//cerr<<" Compute poster probabilities for Layer "<<h<<":"<<endl;
				
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
		
		beta = new double*[nh];
		alpha = new double*[nh];
		
		betaInOut = new double*[nh];
		alphaInOut = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;

		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			model->makeADTree();
		}

		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];
			compute_beta(j, Vh, nh, Vu, nu);			

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
				
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		
	
		inFeatureEdges = new int[nh];
		outFeatureEdges = new int[nh];	
		//cerr << "call: getInOutFeatures(" << nh << ")" << endl;	
		getInOutFeatures(nh);		
		//cerr << "return: getInOutFeatures()"  << endl;
		
//		for(int i = 0; i < nh; i++){
//			cerr << "inFeatureEdges[" << i << "] = " << inFeatureEdges[i] << endl;
//		}
//		for(int i = 0; i < nh; i++){
//			cerr << "outFeatureEdges[" << i << "] = " << outFeatureEdges[i] << endl;
//		}
		
		for (int j = 0; j < nh; j ++){
			//cerr<<"\nj = " << j << endl;
			betaInOut[j] = new double[1 << nh];
			compute_betaInOut(j, Vh, nh, Vu, nu);

			alphaInOut[j] = new double[1 << nh];			
			compute_alphaInOut(j, Vh, nh);

			cerr<<" . Tables betaInOut and alphaInOut computed for node "<<j<<"."<<endl; 

		}
				
		cerr<<" Tables beta/betaInOut and alpha/alphaInOut are now ready."<<endl;

		cerr << " Compute the postier probability for the specified In-Out features:" << endl;
		RRFunc = new double[1 << nh];
		compute_RR_Fast(nh, RRFunc, alpha);
		//compute_h_Fast(nh, RRFunc, alpha); //To confirm that the result is the same for forward and backward

		RRFuncInOut = new double[1 << nh];
		compute_RR_Fast(nh, RRFuncInOut, alphaInOut);
		//compute_h_Fast(nh, RRFuncInOut, alphaInOut); //To confirm that the result is the same for forward and backward

		cerr<<"RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl;
		cerr<<"RRFuncInOut[all] = "<<RRFuncInOut[(1<<nh)-1]<<endl;

		//cerr<<" . Incoming edges from node " << vv << " to node " << uu <<" :";
		//model->print_edge_prob(cout, Vh[vv], Vh[uu], exp(RRFuncNume[(1<<nh)-1] - RRFunc[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)
		cerr << "\nThe postier probability for the specified In-Out features: " <<  exp(RRFuncInOut[(1<<nh)-1] - RRFunc[(1<<nh)-1]);		
		cerr<<endl;
		
		cout << "The postier probability for the specified In-Out features: " <<  exp(RRFuncInOut[(1<<nh)-1] - RRFunc[(1<<nh)-1]);		
		cout<<endl;
		
		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
		
		delete [] RRFunc; 
		delete [] RRFuncInOut; 
	
		
		delete [] inFeatureEdges;
		delete [] outFeatureEdges;	
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] betaInOut[j];
			delete [] alpha[j];
			delete [] alphaInOut[j];
		}
		delete [] beta; 
		delete [] betaInOut; 
		delete [] alpha;
		delete [] alphaInOut;
		cerr<<" Poster probabilities now computed."<<endl; 
		
		return;
		
		
	}


	void compute_edge_probabilities_top_k(int h){
		cerr<<"For top-k: compute p(D): " << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;		
		
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
		
		
		beta = new double*[nh];
		alpha = new double*[nh];
		
		//betaNume = new double*[nh];
		//alphaNume = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			model->makeADTree();
		}

		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];
		
			//cerr<<" Start compute_beta for node "<<j<<"."<<endl;
			compute_beta(j, Vh, nh, Vu, nu);
			//cerr<<" Tables beta computed for node "<<j<<"."<<endl;

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
				
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}

		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
			
		RRFunc = new double[1 << nh];
		
		cerr<<"\n Compute_RR_Fast(): "<<endl;		
		compute_RR_Fast(nh, RRFunc, alpha);
		cerr<<"\n RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl <<endl;
		cout<<"\n RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl <<endl;

		fprintf(stderr, "RRFunc[all] = %18.8f\n", RRFunc[(1<<nh)-1]);
		
		//write to 
		FILE * fp_PD = fopen("exactPD.txt", "a");
		if(fp_PD){
			fprintf(fp_PD, "%18.8f\n", RRFunc[(1<<nh)-1]);
			fclose(fp_PD);
			
		}
		else{
			cerr <<"fopen exactPD.txt fails" << endl;
			
		}
		
	
		delete [] RRFunc; 
		//delete [] RRFuncNume; 
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			//delete [] betaNume[j];
			delete [] alpha[j];
			//delete [] alphaNume[j];
		}
		delete [] beta; 
		//delete [] betaNume; 
		delete [] alpha;
		//delete [] alphaNume;
		//cerr<<" Edge probabilities now computed."<<endl; 
		
		return;

	}

		
	//HR: new mix with max in-degree method, 
	void compute_edge_probabilities_mixIndegree(int h){
		cerr<<" Mix with max in-degree method: " << endl;
		//cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
				
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
		
		//Step 1(a) (b)
		
		beta = new double*[nh];
		alpha = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;
		
		struct timeval startTime;
		struct timeval endTime;
		struct timezone myTimeZone;
		
		double betaTime;
		double alphaTime;
		double RRFuncTime;
		double HFuncTime;
		double KFuncTime;
		double GammaTime;
		
		double step2Time;
		
		
		//Step 1(a)

		gettimeofday(&startTime, &myTimeZone);
		
		//If Arguments::ADtree == 1, user wants to use ADtree, then make the whole tree first
		if(Arguments::ADtree == 1){
			//cout << "Arguments::ADtree == 1"  << endl;
			model->makeADTree();
		}
		else{
			//cout << "Arguments::ADtree == 0"  << endl;
		}

		for (int j = 0; j < nh; j ++){
			beta[j] = new double[1 << nh];
					
			//int i = Vh[j];
			compute_beta(j, Vh, nh, Vu, nu);

			//cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		
		//If Arguments::ADtree == 1, then delete the whole tree
		if(Arguments::ADtree == 1){
			model->freeADTree();
		}
		
		gettimeofday(&endTime, &myTimeZone);		
		betaTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
				
		cerr<<" . Tables beta have been computed" << endl;


		//HR: Add for Top-k inside void compute_edge_probabilities_mixIndegree(int h)
		//HR: Use the Poster tool to compute the local scores for all the families of each variable (without max-indegree
		//	restriction). So for each of n variables, there are 2^(n-1) family scores.
		write_family_scores_to_files(nh);
		

		//step 1(b)
		gettimeofday(&startTime, &myTimeZone);
		for (int j = 0; j < nh; j ++){

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
			
			//cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		gettimeofday(&endTime, &myTimeZone);
		alphaTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		
		cerr<<" . Tables alpha has been computed" << endl;
		

		//Step 1(c)
		//HR: backward method to compute: for all S \subset V RR(S)
		RRFunc = new double[1 << nh];
		
		cerr<<" . To compute Table RR" << endl;
		
		gettimeofday(&startTime, &myTimeZone);
		
		compute_RR_Fast(nh, RRFunc, alpha);
		//compute_RR_Iter(nh, RRFunc, alpha);
		
		gettimeofday(&endTime, &myTimeZone);		
		RRFuncTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		//cerr<<"RRFunc[all] = "<<RRFunc[(1<<nh)-1]<<endl;
		//fprintf(stderr, "RRFunc[all] = %18.8f\n", RRFunc[(1<<nh)-1]);
		cerr<<" . Table RR computed." <<endl;


		//HR: merge the function of opt 4 into here.
		//write to exactPD.txt
		//string directory_name = "./family_scores";
		string str;
		str.assign(Arguments::directoryName);
		str.append("/");
		str.append("exactPD.txt");

		//FILE * fp_PD = fopen(str.c_str(), "a");
		FILE * fp_PD = fopen(str.c_str(), "w");
		if(fp_PD){
			fprintf(fp_PD, "%18.8f\n", RRFunc[(1<<nh)-1]);
			fclose(fp_PD);

		}
		else{
			cerr <<"fopen exactPD.txt fails" << endl;

		}
		

		//Step 1(d)
		//HR: forward method to compute: for all S \subset V H(S)
		
		hFunc = new double[1 << nh];
		
		cerr<<" . To compute Table H" << endl;
		
		gettimeofday(&startTime, &myTimeZone);

		compute_h_Fast(nh, hFunc, alpha);	
		//compute_h_Iter(nh, hFunc, alpha);	
		
		gettimeofday(&endTime, &myTimeZone);
		HFuncTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		
		//cerr<<"hFunc[all] = "<<hFunc[(1<<nh)-1]<<endl;
		//fprintf(stderr, "hFunc[all] = %18.8f\n", hFunc[(1<<nh)-1]);
		cerr<<" . Table H computed" <<endl;

			
		//Step 1 (e)
		KFunc = new double*[nh]; 
		cerr<<" . To compute Table K" << endl; 
		
		gettimeofday(&startTime, &myTimeZone);
		for(int i = 0; i < nh; i++){
			//cerr<<" For v = " << i << endl;
			KFunc[i] = new double [1<<nh];
			compute_all_K_v(nh, KFunc[i], i, alpha);
			
		}
		
		gettimeofday(&endTime, &myTimeZone);	
		KFuncTime = (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		
		           
		cerr<<" . Table K computed" <<endl;
		
	
		// Step 1 (f): For all i \in V, for all Pa_i \subset V - {i} with |Pa_i| <= k, compute Gamma_i(Pa_i)
		
		double * Gamma = new double[1 << nh];
		double * Gfb = new double[1 << nh];

		GammaTime = 0.0;
		step2Time = 0.0;

		for (int v = 0; v < nh; v++){
			//cerr<<" For v = " << v <<": " << endl;
			
			//start counting the GammaTime for each v
			gettimeofday(&startTime, &myTimeZone);
			
			// Compute Gfb[]. 
			for (int U = 0; U < (1<<nh); U++){				
				//if v \in the binary repre of U
				if ((U>>v) & 1){
					Gfb[U] = LOG_ZERO;
				}
				else{

					//Gfb[U] = hFunc[U] + KFunc[v][U];
					if(KFunc[v][U] == LOG_ZERO){
						Gfb[U] = LOG_ZERO;
					}
					//note that KFunc[v][U] == 0 is possible
					else if(KFunc[v][U] <= 0){
						Gfb[U] = hFunc[U] + KFunc[v][U];
					}
					else{
						//Now if Gfb[U] > 0, then Gfb[U]
				
						Gfb[U] = -(hFunc[U] - KFunc[v][U]);
					}
				
					//cerr<<" j = "<<j<<": ";
					//print_set(cerr, S);
					//cerr<<"; ";
					//print_set(cerr, cS);
					//cerr<<": "<<gfb[S];
					//cerr<<endl;
				}
			}
						
			//cerr<< "Table Gfb has been computed" << endl;
					
			// Compute gamma_j() := gamma[].
						
			fdmtLogAddComp(Gfb, Gamma, nh, k);
			
			gettimeofday(&endTime, &myTimeZone);
			 //end counting GammaTime for each v
			GammaTime += (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		           
			
			//cerr<< "Table Gamma has been computed" << endl;
						
			//cerr<< " Gfb[0]: " <<Gfb[0]<<endl;
			//cerr<< " Gamma[0]: " << Gamma[0]<<endl;
			
			
			//Step 2
			//cerr<<" . Incoming edges for node "<< v <<":";
			// Loop over start-point nodes i.
			
			//start counting step2Time for each v
			gettimeofday(&startTime, &myTimeZone);

			for (int i = 0; i < nh; i++){
				if (i == v) continue;
				//cerr<<" "<< i;		
				double log_prob_nume = eval_edge_mixIndegree(i,  v, Gamma, beta[v], nh, k);
		
				model->print_edge_prob(
					cout, Vh[i], Vh[v], exp(log_prob_nume - RRFunc[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)
							
			}
			

			gettimeofday(&endTime, &myTimeZone);
			//end counting step2Time for each v	
			step2Time += (endTime.tv_sec - startTime.tv_sec) + 
		           (endTime.tv_usec - startTime.tv_usec) / 1000000.0 ;
		    	
			//cerr<<endl;
			
		} 
		
		
//		cout << "\n\n\nTime Statistics" << endl;
//		cout << "betaTime = " << betaTime <<  endl;				
//		cout << "alphaTime = " << alphaTime <<  endl;
//		cout << "RRFuncTime = " << RRFuncTime <<  endl;
//		cout << "HFuncTime = " << HFuncTime <<  endl;
//		cout << "KFuncTime = " << KFuncTime <<  endl;		
//				
//		cout << "GammaTime = " << GammaTime <<  endl;
//		cout << "step2Time = " << step2Time <<  endl;
//		double totalTime = betaTime + alphaTime + RRFuncTime 
//						+ HFuncTime + KFuncTime + GammaTime + step2Time;
//		cout << "totalTime = " << totalTime <<  endl;
				
		
		delete [] Gamma;
		delete [] Gfb;

		delete [] hFunc;
		
		delete [] RRFunc; 
		
		delete [] Eta_U;
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] alpha[j];

			delete [] KFunc[j]; 
		
		}
		delete [] beta; 
		delete [] alpha;

		delete [] KFunc;
		
		
		cerr<<" Edge probabilities now computed."<<endl; 
		
		return;

	}
	//HR: end mixIndegree
	
	
	void compute_edge_probabilities_mixIndegreeNoLog(int h){
		cerr<<"Mix with max in-degree method: " << endl;
		cerr<<" Compute edge probabilities for Layer "<<h<<":"<<endl;
		
		
		// Step 1: Compute beta[][];
		// Step 2: Compute alpha[][];
		int *Vh, *Vu; int nh, nu;
		model->layer(h, &Vh, &nh);
		model->upper_layers(h-1, &Vu, &nu);
		
		//Step 1(a) (b)
		
		beta = new double*[nh];
		alpha = new double*[nh];
		
		k = model->max_indegree();
		
		cerr<<" . "<<nh<<" nodes"<<endl;

		for (int j = 0; j < nh; j ++){

			beta[j] = new double[1 << nh];
					
			//int i = Vh[j];
			compute_beta(j, Vh, nh, Vu, nu);

			alpha[j] = new double[1 << nh];	
			compute_alpha(j, Vh, nh);
				
			cerr<<" . Tables beta and alpha computed for node "<<j<<"."<<endl; 
		
		}
		
		
		//Step 1(c)
		//HR: backward method to compute: for all S \subset V RR(S)
		RRFunc = new double[1 << nh];
		
		cerr<<" . To compute Table RR" << endl;
		
		compute_RR_Fast(nh, RRFunc, alpha);
		
		cerr<<" . Table RR computed." <<endl;
		
		
		//Step 1(d)
		//HR: forward method to compute: for all S \subset V H(S)
		
		hFunc = new double[1 << nh];
		
		cerr<<" . To compute Table H" << endl;
		
		compute_h_Fast(nh, hFunc, alpha);	
		
		cerr<<" . Table H computed" <<endl;

			
		//Step 1 (e)
		KFunc = new double*[nh]; 
		cerr<<" . To compute Table K" << endl; 
		for(int i = 0; i < nh; i++){
			cerr<<" For v = " << i << endl;
			KFunc[i] = new double [1<<nh];
			compute_all_K_v(nh, KFunc[i], i, alpha);
			
		}
		cerr<<" . Table K computed" <<endl;		
		
		// Step 2: Loop over end-point nodes v \in V
		
		//No Log transformation
		double * Gamma = new double[1 << nh];
		double * Gfb = new double[1 << nh];
		ofstream ofNoLog("debugEngineNoLog.txt");
		
		for (int v = 0; v < nh; v++){
			cerr<<" For v = " << v <<": " << endl;
			// Compute Gfb[]. 
			for (int U = 0; U < (1<<nh); U++){				
				//if v \in the binary repre of U
				if ((U>>v) & 1){
					//Gfb[U] = LOG_ZERO;
					//Gfb[U] = 0;
					Gfb[U] = LOG_ZERO;//
				}
				else{
					//Gfb[U] = hFunc[U] + KFunc[v][U];
					
					if(KFunc[v][U] == LOG_ZERO){
						Gfb[U] = exp(hFunc[U]);
					}
					else if (KFunc[v][U] > 0){
						Gfb[U] = - exp(hFunc[U] - KFunc[v][U]);
						
					}
					else{
						Gfb[U] = exp(hFunc[U] + KFunc[v][U]);
					}
					//
					if(Gfb[U] == 0){
						Gfb[U] = LOG_ZERO;
					}
					else{
						Gfb[U] = log(Gfb[U]);
					}
					//
										
					//cerr<<" j = "<<j<<": ";
					//print_set(cerr, S);
					//cerr<<"; ";
					//print_set(cerr, cS);
					//cerr<<": "<<gfb[S];
					//cerr<<endl;
				}
			}
			cerr<< "Table Gfb has been computed" << endl;
					
			ofNoLog <<"For v = " << v <<": " << endl;
			ofNoLog << "\nGfb: " << endl;
			for(int index = 0; index < (1 << nh); index++){
				ofNoLog << "Gfb[" << index << " ] =" << Gfb[index] << endl;
			}
			ofNoLog << endl;
			
			
			// Compute gamma_j() := gamma[].
			//fdmtNoLog(Gfb, Gamma, nh, k);
			fdmt(Gfb, Gamma, nh, k);
			
			cerr<< "Table Gamma has been computed" << endl;
			
			cerr<< " Gamma[0]: " << Gamma[0]<<endl;
			cerr<< " Gfb[0]: " <<Gfb[0]<<endl;
			
			cerr<<" . Incoming edges for node "<< v <<":";
			// Loop over start-point nodes i.
			for (int i = 0; i < nh; i++){
				if (i == v) continue;
				cerr<<" "<< i;		
				double log_prob_nume = eval_edge_mixIndegree(i,  v, Gamma, beta[v], nh, k);
		
				model->print_edge_prob(
					cout, Vh[i], Vh[v], exp(log_prob_nume - RRFunc[(1<<nh)-1])); //HR: - gb[(1<<nh)-1] mean /L(v)
			
			}
			cerr<<endl;
			
		} 
	
		delete [] Gamma;
		delete [] Gfb;

		delete [] hFunc;
		
		delete [] RRFunc; 
		
		delete [] Eta_U;
		
		for (int j = 0; j < nh; j ++){
			delete [] beta[j]; 
			delete [] alpha[j];

			delete [] KFunc[j]; 
		
		}
		delete [] beta; 
		delete [] alpha;

		delete [] KFunc;
				
		cerr<<" Edge probabilities now computed."<<endl; 
		
		return;

	}
	
	//HR: end mixIndegreeNoLog
	
	
	// beta[j][T] := sum_W gamma[j][T \cup W] .
	void compute_beta(int j, int *Vh, int nh, int *Vu, int nu){
		//cerr<<"Compute beta, j = "<<j<<", i = "<< Vh[j];
		//cerr<<", k = "<<k<<", nh = "<<nh<<":"<<endl;
		
		sub_init(0, 0, beta[j], MARK, 0, nh);

		//cerr<<" Initialization done."<<endl;
		
		int *S = new int[k];		
		sub_beta(-1, nh, beta[j], 0, S, Vh[j], Vu, nu, 0);
		delete [] S;
				
	}// end void compute_beta()
	
	
	// beta[j][T] := sum_W gamma[j][T \cup W] .
	void compute_betaNume(int j, int *Vh, int nh, int *Vu, int nu, int uu, int vv){
		//cerr<<"Compute betaNume, j = "<<j<<", i = "<< Vh[j];
		
		sub_init(0, 0, betaNume[j], MARK, 0, nh);
		
		//cerr<<" Initialization done."<<endl;

		int *S = new int[k];
		
		//check whether uu == j
		if(uu != j){
			sub_beta(-1, nh, betaNume[j], 0, S, Vh[j], Vu, nu, 0);
		}
		else{
			//cerr << "uu == j == " << uu << ": so call sub_betaNume()" << endl;
			sub_betaNume(-1, nh, betaNume[j], 0, S, Vh[j], Vu, nu, 0, uu, vv);
		}
		
		
		
		delete [] S;
	}
	
	
	void compute_betaInOut(int j, int *Vh, int nh, int *Vu, int nu){

		//cerr << "call: sub_init(0, 0, betaInOut[j], MARK, 0, nh)" << endl;
		sub_init(0, 0, betaInOut[j], MARK, 0, nh);
		//cerr << "return: sub_init(0, 0, betaInOut[j], MARK, 0, nh)" << endl;

		//cerr<<" Initialization done."<<endl;

		int *S = new int[k];
		
		//cerr << "call: sub_betaInOut()" << endl;
		
		sub_betaInOut(-1, nh, betaInOut[j], 0, S, Vh[j], Vu, nu, 0);
		//sub_beta(-1, nh, betaInOut[j], 0, S, Vh[j], Vu, nu, 0);
		
		//cerr << "return: sub_betaInOut()" << endl;

		delete [] S;
	
	}//end of compute_betaInOut()
	
	

	void sub_beta(int jprev, int nh, double *b, int d, int *S, int i, int *Vu, int nu, int T){
		//cerr<<" S:"; print_nodes(cerr, S, d); 
		//cerr<<"; T:"; print_nodes(cerr, T, Vu, nu); cerr<<endl;
		
		
		double lb;	
		if(Arguments::option < 1){
		
			//HR: for old prior for q(G_i)and new Dirichlet hyper-param
			//used to compare rebel and out method		

			//lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
			if(Arguments::ADtree == 0){
				lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
			}
			else{
				lb = model->log_prior(i, S, d) + model->log_lcpHR_ADtree(i, S, d);
			}
		}
		else if(Arguments::option >= 1) {					

			//lb = 0 + model->log_lcpHR(i, S, d);
			if(Arguments::ADtree == 0){
				lb = 0 + model->log_lcpHR(i, S, d);
			}
			else{
				lb = 0 + model->log_lcpHR_ADtree(i, S, d);
			}
		}

		//LOGADD(b[T], lb);
		logAdd_New(b[T], lb);
		//
		
		if (d < k){
			for (int j = jprev + 1; j < nu; j ++){	
				if (Vu[j] != i){
					S[d] = Vu[j];
					
					if (j < nh) {
						sub_beta(j, nh, b, d+1, S, i, Vu, nu, T | (1 << j));
					}
					
					else{
						sub_beta(j, nh, b, d+1, S, i, Vu, nu, T);
					}
				}
			}
		}	
	}
	
	                
	void sub_betaNume(int jprev, int nh, double *b, int d, int *S, int i, int *Vu, int nu, int T, int uu, int vv){
		//cerr<<" S:"; print_nodes(cerr, S, d); 
		//cerr<<"; T:"; print_nodes(cerr, T, Vu, nu); cerr<<endl;
		int vvInS = 0;
	
		for(int ind = 0; ind < d; ind++){
			if(vv == S[ind]){
				vvInS = 1;
				break;
			}
		}
		
		if(vvInS == 1){
			double lb;
			if(Arguments::option < 1){
				
				//HR: for old prior for q(G_i)and new Dirichlet hyper-param
				//used to compare rebel and out method

				//lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
				if(Arguments::ADtree == 0){
					lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
				}
				else{
					lb = model->log_prior(i, S, d) + model->log_lcpHR_ADtree(i, S, d);
				}
			}
			else if(Arguments::option >= 1) {					
				//HR: for 1 prior for q(G_i) and new Dirichlet hyper-param
				//lb = 0 + model->log_lcpHR(i, S, d);
				if(Arguments::ADtree == 0){
					lb = 0 + model->log_lcpHR(i, S, d);
				}
				else{
					lb = 0 + model->log_lcpHR_ADtree(i, S, d);
				}
				
			}
		
			//double lb = model->log_prior(i, S, d) + model->log_lcp(i, S, d);
			//double lb = 0 + model->log_lcpHR(i, S, d);

			//LOGADD(b[T], lb);
			logAdd_New(b[T], lb);
		}
		else{
			//how to set? 0-no, MARK-no; log(0) = -inf = LOG_ZERO
			//LOGADD(b[T], LOG_ZERO);
			double tempLogB = LOG_ZERO;
			logAdd_New(b[T], tempLogB);
		}
		

		
		if (d < k){
			for (int j = jprev + 1; j < nu; j ++){	
				if (Vu[j] != i){
					S[d] = Vu[j];
					
					if (j < nh) {
						sub_betaNume(j, nh, b, d+1, S, i, Vu, nu, T | (1 << j), uu, vv);
					}
					
					else{
						sub_betaNume(j, nh, b, d+1, S, i, Vu, nu, T, uu, vv);
					}
				}
			}
		}		
	}
	
	

	void getInOutFeatures(int no_vars){
	
		for(int i = 0; i < no_vars; i++){
			inFeatureEdges[i] = 0;
		}
		for(int i = 0; i < no_vars; i++){
			outFeatureEdges[i] = 0;
		}
			
		FILE * fpIn = fopen(Arguments::inFeasFileName, "r");
	
		int fromNode;
		int toNode;
		//char c;
		//int i=0;
		while(!feof(fpIn)){
			
			fromNode = -1;
			toNode = -1;
			//c = -1;
			
			fscanf(fpIn,"%d->%d\n",&fromNode, &toNode);
			
			//cout << "fromNode = "<< fromNode << endl;
			//cout << "toNode = "<< toNode << endl;
	
			//To guard against toNode == -1
			if(toNode == -1){
				break;
			}
			else{
				inFeatureEdges[toNode] += (1 << fromNode);
			}

		}
		
		fclose(fpIn);
		
//		for(int i = 0; i < no_vars; i++){
//			cout << "inFeatureEdges[" << i << "] " << inFeatureEdges[i] << endl; 
//		}

		
		FILE *fp2 = fopen(Arguments::outFeasFileName, "r");

		while(!feof(fp2)){

			fromNode = -1;
			toNode = -1;
			
			fscanf(fp2,"%d->%d\n",&fromNode, &toNode);
			
			//cout << "fromNode = "<< fromNode << endl;
			//cout << "toNode = "<< toNode << endl;
	
			if(toNode == -1){
				break;
			}
			else{
				outFeatureEdges[toNode] += (1 << fromNode);
			}
			
		
		}
		fclose(fp2);
	
//		for(int i = 0; i < no_vars; i++){
//			cout << "outFeatureEdges[" << i << "] " << outFeatureEdges[i] << endl; 
//		}

	} //end of getInOutFeatures
	
	
	void sub_betaInOut(int jprev, int nh, double *b, int d, int *S, int i, int *Vu, int nu, int T){
//		cerr<< "Start sub_betaInOut()" << endl;
//		cerr<<" S:"; print_nodes(cerr, S, d); 
//		cerr<<" T: " << T << endl;
//		cerr<<" d: " << d << endl;
		//cerr<<"; T:"; print_nodes(cerr, T, Vu, nu); cerr<<endl;
				
		int compBetaNeeded = 0;
		
		//cerr << "inFeatureEdges[" << i << "] = " << inFeatureEdges[i] << endl;
		//cerr << "outFeatureEdges[" << i << "] = " << outFeatureEdges[i] << endl;
		
		//if (each of inFeatureEdges[i] is in T ) && (none of outFeatureEdges[i] is in T )
		if(((inFeatureEdges[i] & T) == inFeatureEdges[i]) && ((outFeatureEdges[i] & T) == 0) ){
			compBetaNeeded = 1;		
			//cerr << "inFeatureEdges[" << i << "] = " << inFeatureEdges[i] << endl;
			//cerr << "outFeatureEdges[" << i << "] = " << outFeatureEdges[i] << endl;
			//cerr << "T = " << T << endl;
			//cerr<<" compBetaNeeded = 1;" << endl;
			
		}
		
		if(compBetaNeeded == 1){
			double lb;
			//Arguments::option must be 5
			if(Arguments::option < 1){
				
				//HR: for old prior for q(G_i)and new Dirichlet hyper-param
				//used to compare rebel and out method
				//lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
				if(Arguments::ADtree == 0){
					lb = model->log_prior(i, S, d) + model->log_lcpHR(i, S, d);
				}
				else{
					lb = model->log_prior(i, S, d) + model->log_lcpHR_ADtree(i, S, d);
				}
			}
			else if(Arguments::option >= 1) {					
				//HR: for 1 prior for q(G_i) and new Dirichlet hyper-param
				//cerr << "start: double lb = 0 + model->log_lcpHR(i, S, d)" << endl;
				//lb = 0 + model->log_lcpHR(i, S, d);
				if(Arguments::ADtree == 0){
					lb = 0 + model->log_lcpHR(i, S, d);
				}
				else{
					lb = 0 + model->log_lcpHR_ADtree(i, S, d);
				}
				//cerr << "end: double lb = 0 + model->log_lcpHR(i, S, d)" << endl;
			}
			
		
			//LOGADD(b[T], lb);
			logAdd_New(b[T], lb);
		}
		else{
			//LOGADD(b[T], LOG_ZERO);
			double tempLogB = LOG_ZERO;
			logAdd_New(b[T], tempLogB);
		}

		if (d < k){
			for (int j = jprev + 1; j < nu; j ++){	
				if (Vu[j] != i){
					S[d] = Vu[j];
					
					if (j < nh) {						
						sub_betaInOut(j, nh, b, d+1, S, i, Vu, nu, T | (1 << j));
					}
					
					else{
						sub_betaInOut(j, nh, b, d+1, S, i, Vu, nu, T);
					}
				}
			}
		}		
	} //end of sub_betaInOut
	
	
	void sub_init(int d, int S, double *a, double value, int ones, int nh){
		//cerr<<" d: "<<d<<endl; 
		a[S] = value;
		
		if (d < nh){
			sub_init(d+1, S, a, value, ones, nh);

			if (ones < k) sub_init(d+1, S | (1 << d), a, value, ones+1, nh);
		}
	}

	//HR: From get_kbest_nets.cc in Top-k
	//HR: add v itself into the binary repres, v posi should be 0
	//Convert the parent set to the appropriate variable set
	unsigned int parset2varset(int v, unsigned int set) {
		unsigned int sinkleton = 1U << v;
		unsigned int mask = sinkleton - 1;
		unsigned int low = set & mask;
		unsigned int high = set & ~mask;
		return (high << 1) | low;
	}


	//HR: Add for Top-k inside void compute_edge_probabilities_mixIndegree(int h)
	//HR: Use the Poster tool to compute the local scores for all the families of each variable (without max-indegree
	//	restriction). So for each of n variables, there are 2^(n-1) family scores.
	//	Then store them as file 0, 1, 2, ..., n-1 for variable 0, 1, ..., n-1.
	void write_family_scores_to_files(int nh){
		FILE* fout;
		//nh is the number of variables
		int num_of_vars = nh;
		unsigned int num_of_parsets = 1U << (num_of_vars - 1);
		//HR: for each variable
		for (int v = 0; v < num_of_vars; v++) {
			char str1[40];
			sprintf(str1, "%d", v);
			string str;
			//string dirname = "./family_scores";
			string dirname = Arguments::directoryName;
			str.append(dirname);
			str.append("/");
			str.append(str1);

			fout = fopen(str.c_str(), "wb");
			unsigned int new_par_set;
			for (unsigned int par_set = 0; par_set < num_of_parsets; par_set++) {
				//HR: add v itself into the binary repres, v posi should be 0
				new_par_set = parset2varset(v, par_set);
				//This is fine.
				//fwrite(beta[v] + new_par_set, sizeof(double), 1, fout);
				//This is also fine.
				fwrite(&(beta[v][new_par_set]), sizeof(double), 1, fout);

			}
			fclose(fout);

		}
//		//test
//		//HR: for each variable
//		FILE* fin;
//		for (int v = 0; v < num_of_vars; v++) {
//			char str1[40];
//			sprintf(str1, "%d", v);
//			string str;
//			//string dirname = "./family_scores";
//			string dirname = Arguments::directoryName;
//			str.append(dirname);
//			str.append("/");
//			str.append(str1);
//
//			fin = fopen(str.c_str(), "rb");
//			unsigned int new_par_set;
//			for (unsigned int par_set = 0; par_set < num_of_parsets; par_set++) {
//				//HR: add v itself into the binary repres, v posi should be 0
//				new_par_set = parset2varset(v, par_set);
//				double temp_family_score;
//				fread(&temp_family_score, sizeof(double), 1, fin);
//				if(temp_family_score != beta[v][new_par_set]){
//					cerr << "temp_family_score = " << temp_family_score << endl;
//					cerr << "beta[" << v << "][" << new_par_set << "] = " << beta[v][new_par_set] << endl;
//					cerr << "Error: temp_family_score != beta["
//							<< v << "][" << new_par_set << "]" << endl;
//					exit(1);
//				}
//			}
//			fclose(fin);
//
//		}
//		//

	}//write_family_scores_to_files(int nh)

	
	// alpha[j][S] = sum_T beta[j][T]  (...times the prior).
	void compute_alpha(int j, int *Vh, int nh){
		// Use fast truncated upward Möbius transform.
		fumt(beta[j], alpha[j], nh, k); 
	}
	

	void compute_alphaNume(int j, int *Vh, int nh){
		// Use fast truncated upward Möbius transform.
		fumt(betaNume[j], alphaNume[j], nh, k); 
	}
	

	void compute_alphaInOut(int j, int *Vh, int nh){
		// Use fast truncated upward Möbius transform.
		fumt(betaInOut[j], alphaInOut[j], nh, k); 
	}
	
	
	void compute_g_forward(int nh){	
		sub_gf(0, nh, 0);
	
	}
	
	
	void compute_gNume_forward(int nh){	
		sub_gfNume(0, nh, 0);
	
	}
	

	void sub_gf(int d, int nh, int S){
		
		if (d < nh){
			sub_gf(d+1, nh, S);
			sub_gf(d+1, nh, S | (1 << d)); 
		}
		else {
			double sum = MARK; 
			int T = S, J = 1;
			
			for (int j = 0; j < nh; j ++){
				if (T & 1){
					// Now S - J is a subset of S.
					
					double w = alpha[j][S - J] + gf[S - J];
					//LOGADD(sum, w);
					logAdd_New(sum, w);
				} 
					
				T >>= 1; J <<= 1;
			}
			if (S == 0) 
				gf[0] = 0; 
			else 
				gf[S] = sum;
		}
	}
	
	
		void sub_gfNume(int d, int nh, int S){
		
		if (d < nh){
			sub_gfNume(d+1, nh, S);
			sub_gfNume(d+1, nh, S | (1 << d)); 
		}
		else {
			double sum = MARK; 
			int T = S, J = 1;
			
			for (int j = 0; j < nh; j ++){				
				if (T & 1){
					// Now S - J is a subset of S.
					
					double w = alphaNume[j][S - J] + gfNume[S - J];
					//LOGADD(sum, w);
					logAdd_New(sum, w);
				} 
	
				T >>= 1; J <<= 1;
			}
			if (S == 0) 
				gfNume[0] = 0; 
			else 
				gfNume[S] = sum;
		}
	}
	
	
	
	void compute_g_backward(int nh){
		sub_gb(0, nh, 0);
	
	}
	
	
	void sub_gb(int d, int nh, int S){
		if (d < nh){
			sub_gb(d+1, nh, S);
			sub_gb(d+1, nh, S | (1 << d)); 
		}
		else {
			double sum = MARK; 
			int T = S, J = 1, complS = (1 << nh) - 1 - S;
			for (int j = 0; j < nh; j ++){
				if (T & 1){
					// Now S - J is a subset of S.
					double w = alpha[j][complS] + gb[S - J];
					//LOGADD(sum, w);
					logAdd_New(sum, w);
				} 		
				T >>= 1; J <<= 1;
			}
			if (S == 0) gb[0] = 0; else gb[S] = sum;
		}
	}
	

	double eval_edge(int i, int j, double *a, double *b, int nh, int k){
		int T = 1 << i;  
		return sub_eval_edge(-1, 1, T, i, j, a, b, nh, k);
	}
	

	double sub_eval_edge(
		int tprev, int d, int T, int i, int j, double *a, double *b, int nh, int k){
		
		//cerr<<" T:"; print_set(cerr, T); 
		//cerr<<": "<<a[T]<<"; "<<b[T]<<endl;
		
		//if T, d = |G_v| = k	
		if (d == k){
			return a[T] + b[T];
		}
		
		//if  T < k, can add more
		double sum = a[T] + b[T];
		for (int t = tprev + 1; t < nh; t ++){
			if (t == i || t == j) continue;
			
			int Tnext = T | (1 << t);
			double w = sub_eval_edge(t, d+1, Tnext, i, j, a, b, nh, k);
			//LOGADD(sum, w);
			logAdd_New(sum, w);
		}
		return sum;
	}
	
	
	double eval_edge_mixIndegree(int i, int j, double * Gamma, double * Beta_j, int nh, int k){
		int T = 1 << i;  
		return sub_eval_edge_mixIndegree(-1, 1, T, i, j, Gamma, Beta_j, nh, k);
	}
	
	
	double sub_eval_edge_mixIndegree(
		int tprev, int d, int T, int i, int j, double * Gamma, double * Beta_j, int nh, int k){
		
		//cerr<<" T:"; print_set(cerr, T); 
		//cerr<<": "<<a[T]<<"; "<<b[T]<<endl;
		
		//if T, d = |G_v| = k	
		if (d == k){
			double sumRes;
			if(Beta_j[T] == MARK){
				cerr<<"\n**** Beta_j[T] == MARK ";
		   	}
			
			//return (Gamma[T] + Beta_j[T]);
			if(Gamma[T] == LOG_ZERO){
				sumRes = LOG_ZERO;
			}
					
			else if(Gamma[T] <= 0){
				sumRes = Beta_j[T] + Gamma[T];
			}
			else{
				cerr<<"\n**** Gamma[T] > 0 ";
				sumRes = - ( Beta_j[T] - Gamma[T] );

			}
			return sumRes;
	
		}
		
		//if  T < k, can add more
		//double sum = Gamma[T] + Beta_j[T];
		double sum;
		if(Beta_j[T] == MARK){
			cerr<<"\n**** Beta_j[T] == MARK ";
		}
		if(Gamma[T] == LOG_ZERO){
				sum = LOG_ZERO;
		}
					
		else if(Gamma[T] <= 0){
			sum = Beta_j[T] + Gamma[T];
		}
		else{
			cerr<<"\n**** Gamma[T] > 0 ";
			sum = - ( Beta_j[T] - Gamma[T] );

		}
		
	
		for (int t = tprev + 1; t < nh; t ++){
			if (t == i || t == j) continue;

			int Tnext = T | (1 << t);
			double w = sub_eval_edge_mixIndegree(t, d+1, Tnext, i, j, Gamma, Beta_j, nh, k);
			logAddComp(sum, w);
		}
		return sum;
	}
	
	
	double eval_edge_mixIndegreeNoLog(int i, int j, double * Gamma, double * Beta_j, int nh, int k){
		int T = 1 << i;  
		return sub_eval_edge_mixIndegreeNoLog(-1, 1, T, i, j, Gamma, Beta_j, nh, k);
	}
	

	double sub_eval_edge_mixIndegreeNoLog(
		int tprev, int d, int T, int i, int j, double * Gamma, double * Beta_j, int nh, int k){		
		//cerr<<" T:"; print_set(cerr, T); 
		//cerr<<": "<<a[T]<<"; "<<b[T]<<endl;
		
		//if T, d = |G_v| = k	
		if (d == k){
			//Lose precision
			//return (Gamma[T] * exp(Beta_j[T]));
			double product_res;
			//if Beta_j[T]  has not been initialized
			if(Beta_j[T] == MARK){
				cerr<<"***Beta_j[T]  has not been initialized" << endl;
				product_res = 0;		
			}
			else{
				if(Gamma[T] == 0){
				 	product_res = exp(Beta_j[T]);
				}
				else if(Gamma[T] > 0){
					product_res = exp(Beta_j[T] + log(Gamma[T]));
				}
				else{
					product_res = - exp(Beta_j[T] + log(-Gamma[T]));
				}
				
			}

			return product_res;
			
		}
		
		//if  T < k, can add more
		//double sum = Gamma[T] * exp(Beta_j[T]);
		double sum;
		double product_res;
		
			//if Beta_j[T]  has not been initialized
			if(Beta_j[T] == MARK){
				cerr<<"***Beta_j[T]  has not been initialized" << endl;
				product_res = 0;		
			}
			else{
				if(Gamma[T] == 0){
				 	product_res = exp(Beta_j[T]);
				}
				else if(Gamma[T] > 0){
					product_res = exp(Beta_j[T] + log(Gamma[T]));
				}
				else{
					product_res = - exp(Beta_j[T] + log(-Gamma[T]));
				}
				
			}
			
		sum = product_res;
		for (int t = tprev + 1; t < nh; t ++){
			if (t == i || t == j) continue;

			int Tnext = T | (1 << t);
			double w = sub_eval_edge_mixIndegreeNoLog(t, d+1, Tnext, i, j, Gamma, Beta_j, nh, k);
			//LOGADD(sum, w);
			sum += w;
		}
		return sum;
	}
	
	
	Model *model;
	
	double **alpha, **beta;
	double **alphaNume, **betaNume;
	
	double *gf, *gb;
	
	double *gfNume;
	int k;
	
	double **alphaInOut, **betaInOut;
	int * inFeatureEdges; 
	int * outFeatureEdges; 
	
	
};



#endif
