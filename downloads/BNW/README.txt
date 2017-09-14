=====================
README
=====================
BNW_src.tar contains a standalone version the structure learning method that is implemented in the Bayesian Network Webserver (BNW). This readme focuses on the use of the package. A more compete description of the method is given on the BNW website:
http://compbio.uthsc.edu/BNW


Requirements
============
bash, gcc

Compile
=======
To compile the package, change to the /src directory and type: 

sh build.sh


Test examples
=============
To test the installation, type:

sh run.sh example1
sh run.sh example2

These commands should create the model_structure.txt and model_averaging_probabilities.txt files in the example1 and example2 directories. The output in these files should match test_model_structure.txt and test_model_averaging_probabilites.txt in each directory. Further description of the examples is given in Example Description section.

Running the BNW structure learning package
==========================================
The BNW structure learning package is run with the command

sh run.sh dir

where dir is the name of a directory containing the 3 files described below.
The dir directory should be a subdirectory within the BNW/src directory.


Structure search parameters
===========================
Users can set 3 parameters that will affect the properties of the structure search by modifying the run.sh file.

'maxparent' sets the maximum number of parents that any node in the network can have in a network. By default, the value is set to 4, and each node in the network can have a maximum of 4 direct parents.

'k' sets the number of high scoring networks to include in model averaging. By default, k=100, and the 100 highest scoring networks are included in model averaging. If k=1, no model averaging is performed.

'THR' sets the probability threshold when selecting edges to include in the final network structure after model averaging. By default, THR is set to 0.5, and all edges with a model averaging posterior probability greater than 0.5 are included in the the model_structure.txt file.


Input file format
=================
(Example input files are in ./src/example1 and ./src/example2 directories)
The package requires three tab-delimited text files.

input.txt
Data file that contains sample values for each variables. Variables are listed in the columns and samples are listed in the rows. Variable names are in the first row. The second row describes the type of each variable. In the second row,enter '1' for each continuous variable and the number of discrete states for each discrete variable. For example, Geno1 and Geno2 in the example1 directory are discrete variables with two states (1 and 2) and the remaining variables are continuous variables. Therefore, the second row contains a '2' for Geno1 and Geno2 and a '1' for the other variables. The remaining rows are the sample values of the variables in each column. Also note that discrete variables should be the leftmost columns of the input data file.

banlist.txt
This input file lists edges that should be excluded from structure learning. The first row of the file should always contain "From" and "To", while the remaining rows contain the names of the variables that should be excluded from structure learning. A row containing VariableA followed by VariableB will not allow edges from VariableA to VariableB in the network. If no edges are to be excluded from network structure learning, banlist.txt should still exist and contain a header row. The example1 directory contains a sample empty banlist file, while example2 contains an example with edges that should be banned from the network.

whitelist.txt
This input file lists edges that should be required during structure learning. The format of the file is the same as the banlist.txt file. If an edge is present in whitelist.txt, only those networks that contain the edge will be considered. If no edges are required in the network, whitelist.txt should still exist and contain an header row.


Output file format
=================

model_structure.txt
A structure file with the variable names on the first row. The remainder of the file should be an n x n matrix of 0's and 1's, where n is the number of variables in the network. A '1' in row i and column j in this matrix indicates that there is a directed edge connecting variables i and j, (i.e., there is an edge from i to j in the network). '0' indicate that there is not a directed edge from variable i to variable j. This file can be loaded into BNW using the "Make predictions using a known structure" link on the BNW homepage. 

model_averaging_probabilities.txt
The format of the model_averaging_probability file is similar to model_structure.txt. It is an n x n matrix of values between '0' and '1', where the value gives the posterior probability of the edge after model averaging over the k-best network structures. A value close to '1' in the matrix indicates that the edge was present in most or all of the k-best networks, while a value close to '0' indicates that it was present in few or none of the k-best networks.


Example Description
===================
The example folders contain data from a synthetic genetic network containing two genotypes (Geno1 and Geno2) and 6 quantitative traits. In the example1 directory, no structural constraints have been added to the structure search (i.e. the banlist.txt and whitelist.txt files contain only a file header). The example2 directory contains several structural constraints. First, we do not allow the genotype nodes to be be the parents of each other (Geno1 to Geno2 and Geno2 to Geno1 are in banlist.txt). Second, Trait5 and Trait6 are not allowed to be the parents of the other variables in the network. This type of structural constraint might be relevant if Trait5 and Trait6 had trans-QTLs at Geno1 and/or Geno2, while the other traits had cis-QTLs at these loci or if Trait5 and Trait6 were higher order phenotypes that were explained by cellular or gene-level Traits1, 2, 3, and 4. Finally, whitelist.txt contains two interactions between Trait2 and Trait4 and Trait3 and Trait6 that are required to be in the network, indicating that a known regulatory relationships exist between these variables.


Contact
=======
ycui2@uthsc.edu
