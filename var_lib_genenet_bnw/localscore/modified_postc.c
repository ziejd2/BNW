/*                               -*- Mode: C -*- 
 * postc.c --- Posterior for continuous node with continuous parents
 * Author          : Claus Dethlefsen
 * Created On      : Tue Mar 12 06:44:35 2002
 * Last Modified By: Claus Dethlefsen
 * Last Modified On: Wed Jun 04 11:56:51 2003
 * Update Count    : 227
 * Status          : Unknown, Use with caution!
 */

/*
  ##
##    Copyright (C) 2002  Susanne Gammelgaard Bøttcher, Claus Dethlefsen
##
##    This program is free software; you can redistribute it and/or modify
##    it under the terms of the GNU General Public License as published by
##    the Free Software Foundation; either version 2 of the License, or
##    (at your option) any later version.
##
##    This program is distributed in the hope that it will be useful,
##    but WITHOUT ANY WARRANTY; without even the implied warranty of
##    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##    GNU General Public License for more details.
##
##    You should have received a copy of the GNU General Public License
##    along with this program; if not, write to the Free Software
##    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
######################################################################
*/

#include "R.h"
#include "Rmath.h"
#include "matrix.h"
#include "modified_matrix.c"

void postc(double *mu, double *tau, double *rho, double *phi, double
	    *loglik, double *y, double *z, int *n, int *d)
{
	int i,j,ii,jj;
	double logscale,logk,mscore;
	double **mtau, **mmu, **tauinv=0;
	double **zero, **zi, **ziy;
       double **oldtau, **oldmu;
       //temp pointers
       double **tm1,**tm2,**tm3,**tm4,**mm1,**sm1,**sm2,**tt1,**tt2;  

	/* allocate space for matrices */
	mtau   = dmatrix(1,*d,1,*d);
     	oldtau   = dmatrix(1,*d,1,*d);

	zi     = dmatrix(1,*d,1,1);
	ziy    = dmatrix(1,*d,1,1);
	mmu    = dmatrix(1,*d,1,1);
	oldmu    = dmatrix(1,*d,1,1);
	zero   = dmatrix(1,*d,1,1);
     	tauinv   = dmatrix(1,*d,1,*d);

     

	/* copy arguments into the matrices */
	asmatrix(mu,mmu,*d,1);
	asmatrix(tau,mtau,*d,*d);
	
	/* show input */

	for(i = 1; i <= *n; i++) {
			for (ii=1; ii<=*d; ii++) {
					for (jj=1; jj<=*d; jj++) {
						 tauinv[ii][jj] = mtau[ii][jj];
					}
			}

		invers(tauinv, *d, zero, 1);

		for (j=1; j<=*d; j++) {
			zi[j][1] = z[j-1+(i-1)*(*d)];
		}
              //define once		
              tt1=transp(zi,*d,1);
  
              tm1=matmult(tauinv,zi,*d,*d,1);
              tm2=matmult(tt1,tm1,1,*d,1);

		logscale = log(*phi) + log1p(tm2[1][1]);
                 		
             free_dmatrix(tm1,1,*d,1,*d);
	      free_dmatrix(tm2,1,1,1,*d);


		logk = lgammafn( 0.5*(1.0+*rho) ) - lgammafn(*rho*0.5);
		logk -= 0.5*(logscale + log(M_PI));
		
              tm1=matmult(tt1,mmu,1,*d,1);
                           

		mscore =  logk - 0.5*(*rho+1)*log1p((y[i-1] - tm1[1][1])*(y[i-1] - tm1[1][1])/exp(logscale));
		*loglik += mscore;
          
             free_dmatrix(tm1,1,1,1,1);

		for (ii=1; ii<=*d; ii++) {
				for (jj=1; jj<=*d; jj++) {
					 oldtau[ii][jj] = mtau[ii][jj];
				}
		}


		for (jj=1; jj<=*d; jj++) {
			 oldmu[jj][1] = mmu[jj][1];
		}
           
              tm1=matmult(zi,tt1,*d,1,*d);
              sm1=matsum(mtau,tm1,*d,*d);               
              free_dmatrix(mtau,1,*d,1,*d);
		mtau = sm1;
              free_dmatrix(tm1,1,*d,1,*d);

		for (ii=1; ii<=*d; ii++) {
					for (jj=1; jj<=*d; jj++) {
						 tauinv[ii][jj] = mtau[ii][jj];
					}
		}


		invers(tauinv, *d, zero, 1);

		for (j=1;j<=*d;j++)
			ziy[j][1] = zi[j][1]*y[i-1];

              
              tm1=matmult(oldtau,mmu,*d,*d,1);
              sm2=matsum(tm1,ziy,*d,1);
              free_dmatrix(mmu,1,*d,1,1);
              mmu=matmult(tauinv,sm2,*d,*d,1);
              free_dmatrix(tm1,1,*d,1,*d);
              free_dmatrix(sm2,1,*d,1,1);


		(*rho)++;

               tm1=matmult(tt1,mmu,1,*d,1);
               mm1=matminus(oldmu,mmu,*d,1);             
               tt2=transp(mm1,*d,1);
               tm3=matmult(oldtau,oldmu,*d,*d,1);
               tm4=matmult(tt2,tm3,1,*d,1);

		 (*phi) += (y[i-1]-tm1[1][1])*y[i-1] + tm4[1][1];

                  
              free_dmatrix(tm1,1,1,1,*d);
              free_dmatrix(mm1,1,*d,1,1);
		free_dmatrix(tt2,1,1,1,*d);
              free_dmatrix(tm3,1,*d,1,*d);
              free_dmatrix(tm4,1,1,1,*d);

              free_dmatrix(tt1,1,1,1,*d);


	} 

       free_dmatrix(mtau,1,*d,1,*d);
	free_dmatrix(zi,1,*d,1,1);
	free_dmatrix(ziy,1,*d,1,1);
	free_dmatrix(mmu,1,*d,1,1);
	free_dmatrix(zero,1,*d,1,1);
	free_dmatrix(tauinv,1,*d,1,*d);
	free_dmatrix(oldtau,1,*d,1,*d);
	free_dmatrix(oldmu,1,*d,1,1);
    
} 

