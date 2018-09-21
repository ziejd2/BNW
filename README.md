# BNW

This contains the code for the Bayesian Network Webserver (BNW).

The original BNW website is compbio.uthsc.edu/BNW, which currently redirects to BNW_1.2.

The current development version of BNW is compbio.uthsc.edu/BNW_1.21. I am working on the cross-validation/test set predictions in the development version- these features have been removed from version 1.2. Otherwise, 1.2 and 1.21 should be the same.

I recently added some text files here that provide more in-depth descriptions of the BNW code.
These are not very readable, but will hopefully be useful for identifying which files need to be modified when updating BNW.

-BNW_file_overview.txt lists the files that are needed to run BNW and gives a brief description of the purpose of each file.

-BNW_data_flow.txt goes through a typical session of BNW describing the order in which codes and scripts are used, as well as when output files are written and the output file format.

There are four branches.
1) master: This is currently compbio.uthsc.edu/BNW_1.2 on the BNW server. The main change here is the changes in the parameter learning methods. compbio.uthsc.edu/BNW redirects to this version.

2) matlab_original (compbio.uthsc.edu/BNW_1.01): This is functionally identical to the original BNW code. The only difference is that unused files were removed. Currently, compbio.uthsc.edu/BNW redirects to this version. 

3) octave_version (compbio.uthsc.edu/BNW_1.02): This should perform similarly to the original version of BNW. However, instead of Matlab, Octave is used and a couple of new features were added.

4) octave_php_separate (compbio.uthsc.edu/BNW_1.1): The main goal here is to rewrite the code so that all calculations are performed in Octave. PHP code only reads from files and displays figures.  I am making other small changes along the way. This branch could probably be deleted at some point.


