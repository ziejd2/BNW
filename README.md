# BNW

This contains the code for the Bayesian Network Webserver (BNW).

The master branch will be the stable version of the code for running with GeneNetwork. The BNW website is compbio.uthsc.edu/BNW; the GeneNetwork version is at: bnw.genenetwork.org.

There are a couple of files in the info_files directory that provide descriptions of the BNW code and other information.

There are five branches.
1) master: This is the version of BNW that should be loaded to GeneNetwork.

2) matlab_original (compbio.uthsc.edu/BNW_1.01): This is functionally identical to the original BNW code. The only difference is that unused files were removed.

3) octave_version (compbio.uthsc.edu/BNW_1.02): This should perform similarly to the original version of BNW. However, instead of Matlab, Octave is used and a couple of new features were added.

4) genenet6_final_1.3: This is the final version of BNW on the GENENET6 server.

5) genenet8_initial_1.3: This is the working version of BNW on GENENET8 when we transitioned to that server. 
   A couple of notes about this version.
   - This version writes files created by users running BNW to /var/lib/genenet/bnw. 
   - The structure learning package needs to be installed in /var/lib/genenet/bnw. The files in var_lib_genenet_bnw need to be copied to /var/lib/genenet/bnw and build.sh should be run there.
   - There is a python environment on GENENET8 that is used by BNW. This is in bnw-env, but I am not sure how well this will transition to other servers.
        

