function CPD = learn_params_orig(CPD,j,data,ns,cnodes)
% LEARN_PARAMS_ORIG
% Calculate the original distributions of the data.
%   The original distributions are just the percentages of states in the 
%        data file.

local_data = data(j, :); 
nobs = size(local_data,2);
if iscell(local_data)
  local_data = cell2num(local_data);
end
counts = compute_counts(local_data,ns(j));
counts = counts/nobs;
switch CPD.prior_type
 case 'none', CPD.CPT_orig = counts; 
% case 'dirichlet', CPD.CPT = mk_stochastic(counts + CPD.dirichlet); 
% I will use 'dirichlet' priors incorrectly here.
 case 'dirichlet', CPD.CPT_orig = counts; 
 otherwise, error(['unrecognized prior ' CPD.prior_type])
end
