#ifndef _XTAB_H_
#define _XTAB_H_

#include <string.h>

typedef struct xentry xentry;
typedef unsigned char uchar;
typedef unsigned int uint;


//Each xentry is one node in the linked list
struct xentry {
  uchar* key;  //Key value as a string
  int*  val;   //value as int array
  xentry* next; //pointer to the next xentry i.e. the next node in this linked list
  uint h;       /* h denotes hash key i.e to which row in the hash table it belongs to*/
};

typedef struct xtab xtab;

struct xtab {
  int range;        /* range of hash keys, for example 1024 */ //i.e. number of rows in hash table
  xentry** ix;      /* hashkey to xentry pointer mapping    */ //pointer pointing to start of each row in the hash table
  xentry* free;     /* pointer to unused xentries           */
  xentry* xentries; //Each entry in hash table
};

//Number of entries in the hash table are counted by (free-xentries)
#define xcount(t) ((t)->free - (t)->xentries)

/*Reset memory to 0's from ix to the end of last xentry*/

# define xreset(t) \
{\
  memset((t)->ix, 0, (t)->range * sizeof(xentry*));\
  memset((t)->xentries, 0, xcount(t) * sizeof(xentry));\
  (t)->free = (t)->xentries;\
}

xentry* xadd(xtab* t, uint h, uchar* key, int klen, int* new) {

  /* starting from ix[h], go through x:s and try to find key */

  //Search in the linked list for that particular hash value and see if such a linked list has the particular key already
  xentry* x = t->ix[h];
  xentry** p = t->ix + h; /* prev-pointer to chain the possibly new xentry */
  
  //Compares the entry to be added with old entries. If the same key value does not already exist in the linked list, then just go to the next xentry and compare
  //memcmp returns 0 if the strings are identical
  for(; x && memcmp(x->key, key, klen); x = x->next)
    p = &(x->next);

  //If the key value is not already present in the linked list for that hash value
  *new = !x;

  if(*new){
    x = (t->free)++;  //Free memory starts one block later because new element is inserted
    x->h = h;  //Set hash value
    *p = x; /* link the new x */
  }

  return x;
}

xtab* xcreate(int range, int nof_xentries){
  xtab* t     = calloc(1,sizeof(xtab));
  t->range    = range;
  t->ix       = calloc(range, sizeof(xentry*));
  t->xentries = calloc(nof_xentries, sizeof(xentry));
  t->free     = t->xentries;
  return t;
}
 
void  xdestroy(xtab* t) {
  free(t->ix);
  free(t->xentries);
  free(t);
}

#endif
