function CPD = tabular_CPD(bnet, self, varargin)
% TABULAR_CPD Make a multinomial conditional prob. distrib. (CPT)
%
% CPD = tabular_CPD(bnet, node) creates a random CPT.
%
% The following arguments can be specified [default in brackets]
%
% CPT - specifies the params ['rnd']
%   - T means use table T; it will be reshaped to the size of node's family.
%   - 'rnd' creates rnd params (drawn from uniform)
%   - 'unif' creates a uniform distribution
% CPT_orig - specifies the distribution based on original data
% adjustable - 0 means don't adjust the parameters during learning [1]
% prior_type - defines type of prior ['none']
%  - 'none' means do ML estimation
%  - 'dirichlet' means add pseudo-counts to every cell
%  - 'entropic' means use a prior P(theta) propto exp(-H(theta)) (see Brand)
% dirichlet_weight - equivalent sample size (ess) of the dirichlet prior [1]
% dirichlet_type - defines the type of Dirichlet prior ['BDeu']
%  - 'unif' means put dirichlet_weight in every cell
%  - 'BDeu' means we put 'dirichlet_weight/(r q)' in every cell
%    where r = self_sz and q = prod(parent_sz) (see Heckerman)
% trim - 1 means trim redundant params (rows in CPT) when using entropic prior [0]
% entropic_pcases - list of assignments to the parents nodes when we should use 
%      the entropic prior; all other cases will be estimated using ML [1:psz]
% sparse - 1 means use 1D sparse array to represent CPT [0]
%
% e.g., tabular_CPD(bnet, i, 'CPT', T)
% e.g., tabular_CPD(bnet, i, 'CPT', 'unif', 'dirichlet_weight', 2, 'dirichlet_type', 'unif')
%
% REFERENCES
% M. Brand - "Structure learning in conditional probability models via an entropic  prior
%   and parameter extinction", Neural Computation 11 (1999): 1155--1182
% M. Brand - "Pattern discovery via entropy minimization" [covers annealing]
%   AI & Statistics 1999. Equation numbers refer to this paper, which is available from
%   www.merl.com/reports/docs/TR98-21.pdf
% D. Heckerman, D. Geiger and M. Chickering, 
%   "Learning Bayesian networks: the combination of knowledge and statistical data",
%   Microsoft Research Tech Report, 1994


if nargin==0
  % This occurs if we are trying to load an object from a file.
  CPD = init_fields;
  CPD = class(CPD, 'tabular_CPD', discrete_CPD(0, []));
  return;
elseif isa(bnet, 'tabular_CPD')
  % This might occur if we are copying an object.
  CPD = bnet;
  return;
end
CPD = init_fields;

ns = bnet.node_sizes;
ps = parents(bnet.dag, self);
fam_sz = ns([ps self]);
psz = prod(ns(ps));
CPD.sizes = fam_sz;
CPD.leftright = 0;
CPD.sparse = 0;

% set defaults
CPD.CPT = mk_stochastic(myrand(fam_sz));
CPD.CPT_orig = mk_stochastic(myrand(ns([self])));
CPD.adjustable = 1;
CPD.prior_type = 'none';
dirichlet_type = 'BDeu';
dirichlet_weight = 1;
CPD.trim = 0;
selfprob = 0.1;
CPD.entropic_pcases = 1:psz;

% extract optional args
args = varargin;
% check for old syntax CPD(bnet, i, CPT) as opposed to CPD(bnet, i, 'CPT', CPT)
if ~isempty(args) && ~ischar(args{1})
  CPD.CPT = myreshape(args{1}, fam_sz);
  args = [];
end

for i=1:2:length(args)
  switch args{i},
   case 'CPT',
    T = args{i+1};
    if ischar(T)
      switch T
       case 'unif', CPD.CPT = mk_stochastic(myones(fam_sz));
       case 'rnd',  CPD.CPT = mk_stochastic(myrand(fam_sz));
       otherwise,   error(['invalid CPT ' T]);       
      end
    else
      CPD.CPT = myreshape(T, fam_sz);
    end
   case 'prior_type', CPD.prior_type = args{i+1};
   case 'dirichlet_type', dirichlet_type = args{i+1};
   case 'dirichlet_weight', dirichlet_weight = args{i+1};
   case 'adjustable', CPD.adjustable = args{i+1};
   case 'clamped', CPD.adjustable = ~args{i+1};
   case 'trim', CPD.trim = args{i+1};
   case 'entropic_pcases', CPD.entropic_pcases = args{i+1};
   case 'sparse', CPD.sparse = args{i+1};
   otherwise, error(['invalid argument name: ' args{i}]);       
  end
end

switch CPD.prior_type
 case 'dirichlet',
  switch dirichlet_type
   case 'unif', CPD.dirichlet = dirichlet_weight * myones(fam_sz);
   case 'BDeu',  CPD.dirichlet = (dirichlet_weight/psz) * mk_stochastic(myones(fam_sz));
   otherwise, error(['invalid dirichlet_type ' dirichlet_type])
  end
 case {'entropic', 'none'}
  CPD.dirichlet = [];
 otherwise, error(['invalid prior_type ' prior_type])
end

  

% fields to do with learning
if ~CPD.adjustable
  CPD.counts = [];
  CPD.nparams = 0;
  CPD.nsamples = [];
else
  %CPD.counts = zeros(size(CPD.CPT));
  CPD.counts = zeros(prod(size(CPD.CPT)), 1);
  psz = fam_sz(1:end-1);
  ss = fam_sz(end);
  if CPD.leftright
    % For each of the Qps contexts, we specify Q elements on the diagoanl
    CPD.nparams = Qps * Q;
  else
    % sum-to-1 constraint reduces the effective arity of the node by 1
    CPD.nparams = prod([psz ss-1]);
  end
  CPD.nsamples = 0;
end

CPD.trimmed_trans = [];
fam_sz = CPD.sizes;

%psz = prod(fam_sz(1:end-1));
%ssz = fam_sz(end);
%CPD.trimmed_trans = zeros(psz, ssz); % must declare before reading

%sparse CPT
if CPD.sparse
   CPD.CPT = sparse(CPD.CPT(:));
end

CPD = class(CPD, 'tabular_CPD', discrete_CPD(~CPD.adjustable, fam_sz));


%%%%%%%%%%%

function CPD = init_fields()
% This ensures we define the fields in the same order 
% no matter whether we load an object from a file,
% or create it from scratch. (Matlab requires this.)

CPD.CPT = [];
CPD.CPT_orig = [];
CPD.sizes = [];
CPD.prior_type = [];
CPD.dirichlet = [];
CPD.adjustable = [];
CPD.counts = [];
CPD.nparams = [];
CPD.nsamples = [];
CPD.trim = [];
CPD.trimmed_trans = [];
CPD.leftright = [];
CPD.entropic_pcases = [];
CPD.sparse = [];

