# BNW

This contains the code for the Bayesian Network Webserver (BNW).

The original BNW website is compbio.uthsc.edu/BNW. 

The current development version of BNW is compbio.uthsc.edu/BNW_1.2. I am working on adding cross-validation to this version.

There are a couple of intermediate versions that I kept on the BNW server. Some of them are related to the branches here. Descriptions of the branches are provided below.

I recently added some text files here that provide more in-depth descriptions of the BNW code.
These are not very readable, but will hopefully be useful for identifying which files need to be modified when updating BNW.

-BNW_file_overview.txt lists the files that are needed to run BNW and gives a brief description of the purpose of each file.

-BNW_data_flow.txt goes through a typical session of BNW describing the order in which codes and scripts are used, as well as when output files are written.

There are four branches.
1) master: This is currently compbio.uthsc.edu/BNW_1.12 on the BNW server. It is similar to the octave_php_separate with a couple of additional small changes and bug fixes. I think this version is ready for use as the main version of BNW.

2) matlab_original (compbio.uthsc.edu/BNW_1.01): This is functionally identical to the original BNW code. The only difference is that unused files were removed. Currently, compbio.uthsc.edu/BNW redirects to this version. 

3) octave_version (compbio.uthsc.edu/BNW_1.02): This should perform similarly to the original version of BNW. However, instead of Matlab, Octave is used and a couple of new features were added.

4) octave_php_separate (compbio.uthsc.edu/BNW_1.1): The main goal here is to rewrite the code so that all calculations are performed in Octave. PHP code only reads from files and displays figures.  I am making other small changes along the way.


