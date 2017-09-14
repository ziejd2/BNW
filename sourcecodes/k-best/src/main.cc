#include<stdio.h>
#include<stdlib.h>
#include"math.h"

#include"Arguments.h"
#include"Model.h"
#include"Engine.h"

void welcome(){
	fprintf(stderr,
	" ~~~ Welcome ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n"); 
	fprintf(stderr, 
	" Poster:  The Tool for Computing Posterior \n");
	fprintf(stderr,
	" ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n"); 

}
void goodbye(){
	fprintf(stderr,
	" ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n"); 
}



int main(int argc, char **argv){
	welcome();

	Arguments::init(argc, argv);
	
	Model model;

	model.init();
	
	//HR
	//model.makeADTree();
	////model.MakeADContiTable();
	//model.testHR_ADtree();
	//model.freeADTree();
	//return 1;
	
	//cerr << "Start Engine.h " << endl;
	
	Engine engine;

	engine.init(&model);

	engine.compute_edge_probabilities();

	goodbye();
	return 1;
}
