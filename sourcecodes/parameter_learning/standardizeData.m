function [ cases ] = standardizeData( labels, node_sizes, cases )
%standardizeData standardizes continuous nodes so they have a mean = 0
%   and standard deviation = 1
%
% standardizeData is called by readInput.m

nnodes = size(labels,2);

%fprintf(['Standardizing data for continuous nodes\n'])
for i = 1:nnodes
    if node_sizes(i) == 1
        temp = cell2num(cases(i,:));
        [temp] = standardize(temp);
        cases(i,:) = num2cell(temp);
    end
end


end

