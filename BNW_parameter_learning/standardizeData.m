function [ cases ] = standardizeData( labels, node_sizes, cases )
%standardizeData standardizes continuous nodes so they have a mean = 0
%   and standard deviation = 1
%   Detailed explanation goes here

nnodes = size(labels,2);

%fprintf(['Standardizing data for continuous nodes\n'])
for i = 1:nnodes
    if node_sizes(i) == 1
        temp = cell2num(cases(i,:));
        [temp] = standardize(temp);
        cases(i,:) = num2cell(temp);
    end
end

%write standardized data to file
%fprintf(['Standardized data is written to file standardized_data.txt\n'])
%fout = 'standardized_data.txt';
%txt = sprintf([repmat('%s\t',1,size(labels,2))],labels{:});
%dlmwrite(fout,txt,'');
%dlmwrite(fout,cell2num(cases'),'-append','delimiter','\t');

end

