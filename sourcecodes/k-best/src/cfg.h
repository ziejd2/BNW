#ifndef __CFG_H_
#define __CFG_H_

#define MAX_NOF_VARS (32)
#define LARGEST_SET(NOF_VARS) ((NOF_VARS)==MAX_NOF_VARS?(varset_t)~0:(1U<<(NOF_VARS))-1)
typedef unsigned int varset_t;
typedef double score_t;

#endif
