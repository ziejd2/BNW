function [ bnet ] = parameterLearning( bnet,cases,engine_name )
%parameterLearning Do parameter learning and inference

%engine is an optional argument
if nargin < 3
    engine_name = 'jtree_inf_engine';
end


%First do parameter learning with all the data
[bnet] = getParams(bnet,cases);




end

