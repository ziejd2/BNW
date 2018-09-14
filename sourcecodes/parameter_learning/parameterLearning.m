function [ bnet ] = parameterLearning( bnet,cases,engine_name )
%parameterLearning Do parameter learning and inference
% It returns the bnet with parameters learned from the data in cases.
% 
% This is very basic now. It could be modified to use different engine
%  types in the future. Now, I always use the 'jtree_inf_engine'.
%  with dirichlet priors
%
% parameterLearning is called by runBN_initial.m, 
%   Predictmultiple.m, and Predictmultipleintervention.m


%engine is an optional argument
if nargin < 3
    engine_name = 'jtree_inf_engine';
end


%First do parameter learning with all the data
[bnet] = getParams(bnet,cases);




end

function [ bnet ] = getParams( bnet, cases )
%getParams Code to initialize CPT and do parameter learning.
%This will be very basic for now.  I can add more options later.
%

dnodes = bnet.dnodes;
cnodes = bnet.cnodes;
nnodes = size(dnodes,2)+size(cnodes,2);

%make dnodes tabular_CPT
for i = 1:size(dnodes,2)
%    bnet.CPD{dnodes(i)} = tabular_CPD(bnet,dnodes(i));
    bnet.CPD{dnodes(i)} = tabular_CPD(bnet,dnodes(i),'prior_type','dirichlet');
end

for i = 1:size(cnodes,2)
    bnet.CPD{cnodes(i)} = gaussian_CPD(bnet,cnodes(i));
end

bnet = learn_params(bnet,cases);


end
