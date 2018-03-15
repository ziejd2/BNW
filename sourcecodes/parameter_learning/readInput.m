function [ labels, cases, bnet, node_sizes, data,labelsold] = readInput( dfile, sfile, nnodes, std_flag )
    %readInput is to be used when reading in a network with a known structure
    %   
    %Input:
	%   dfile  = name of the file containing the data (required)
    %   sfile = name of the file containing the structure (required)
    %   nnodes = number of nodes in the network (required)
    %   std_flag = flag for whether or not to standardize the data.
    %   (optional-- Default is FALSE)
    %
    %   See readInputData.m and readInputStructure.m for description of the
    %       format of the dfile and sfile, respectively. 
    %
    %Output:
    %   labels = cell array with the names of the nodes.
    %   cases = cell array with the data.
    %   bnet = BNT bayesian network with the input structure.

if nargin < 4
    std_flag = false(1);
end

    
% read in the file with the data
[labelsold,node_sizes,cases, data] = readInputData(dfile,nnodes);


% read in the file with the structure
[dag] = readInputStructure(sfile,labelsold);


% check the ordering of the nodes and reorder if necessary
[labels,cases,dag,node_sizes,ord_flag] = checkStructure(labelsold,cases,dag,node_sizes);

dcount = 0;
for i = 1:nnodes
    if node_sizes(i) ~= 1
        dcount = dcount + 1;
    end
end
discrete = zeros(1,dcount);
dcount = 0;
for i = 1:nnodes
    if node_sizes(i) ~= 1
        dcount = dcount + 1;
        discrete(dcount) = i;
    end
end

bnet = mk_bnet(dag,node_sizes,'discrete',discrete,'names',labels);

%bnet.dag

checkDiscreteNodes(bnet,cases);

% standardize continuous data to have a mean = 0 and std = 1
if (std_flag)
    [cases] = standardizeData(labels,node_sizes,cases);
end
        

end
%  end of readInput.m
