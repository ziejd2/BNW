# BNW

This contains the code for the Bayesian Network Webserver (BNW).

The original BNW website is compbio.uthsc.edu/BNW

The current development version of BNW is compbio.uthsc.edu/BNW_1.11

There are a couple of intermediate versions that I kept on the BNW server. Some of them are related to the branches below.

There are four branches.
1) master: This is currently identical to octave_php_separate

2) matlab_original (compbio.uthsc.edu/BNW_1.01): This is functionally identical to the original BNW code. The only difference is that unused files were removed.

3) octave_version (compbio.uthsc.edu/BNW_1.02): This should perform similarly to the original version of BNW. However, instead of Matlab, Octave is used.

4) octave_php_separate (compbio.uthsc.edu/BNW_1.1): The main goal here is to rewrite the code so that all calculations are performed in Octave. PHP code only reads from files and displays figures.  I am making other small changes along the way.


