# BNW

This contains the code for the Bayesian Network Webserver (BNW).

The master branch will be the stable version of the code for running with GeneNetwork. The original BNW website is compbio.uthsc.edu/BNW, which currently redirects to BNW_1.2.

The current development version of BNW is compbio.uthsc.edu/BNW_1.22. This is the 1.22_development branch. I added a couple of new visualizations to this version.

There are a couple of files in the info_files directory that provide descriptions of the BNW code and other information.

There are six branches.
1) master: This will have the code to pull and use in the guix container on Gene Network cluster.

2) matlab_original (compbio.uthsc.edu/BNW_1.01): This is functionally identical to the original BNW code. The only difference is that unused files were removed.

3) octave_version (compbio.uthsc.edu/BNW_1.02): This should perform similarly to the original version of BNW. However, instead of Matlab, Octave is used and a couple of new features were added.

4) octave_php_separate (compbio.uthsc.edu/BNW_1.1): The main goal here is to rewrite the code so that all calculations are performed in Octave. PHP code only reads from files and displays figures.  I am making other small changes along the way. This branch could probably be deleted at some point.

5) version-1.21: Temporarily keeping this version while cleaning up code and testing things in version 1.22.

6) 1.22-development: Current development version which I will keep on genenet.
