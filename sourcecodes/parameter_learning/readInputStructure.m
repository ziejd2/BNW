function [ dag ] = readInputStructure( sfile, labels )
%readInputStructure Read in file with structure information
    %   
    %Input:
	%     sfile  = name of the file containing the data (required)
	%     labels = cell array with node labels. (required)
    %     nnodes  = number of columns in the data file. (required)  
    %
    %   Function assumes the following format for the structure input file:
    %       1) The first line has node labels.  These must be the same as 
    %           in the input data file.  They cannot contain spaces.
    %       2) The remainder of the file contains the structure of the dag.
    %           The structure of a graph is a N-by-N matrix, where N is the
    %           number of nodes.  There are 1's in the matrix representing
    %           parent-child relationships.  For each 1, the row indicates
    %           the parent and the column indicates the child.  For
    %           example, a 1 in the (2,3) position of the matrix indicates
    %           that there is an arc pointing from node 2 to node 3.
    %        
    %
	%  Output:
    %     dag = matrix with the structure.
%
%  readInputStructure is called by runBN_initial.m


%   Read in first line of the structure file
%  open file for input, include error handling
fin = fopen(sfile,'r');
if fin < 0
   error(['Could not open ',sfile,' for input']);
end

nnodes = size(labels,2);
% Read in first line to get the node labels.
labels_test = cell(1,nnodes);
buffer = fgetl(fin);    %get header line as a string
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels_test{j} = next;  
end
    
for j=1:nnodes
    if labels_test{j} ~= labels{j}
        fprintf(['Label of node ',j,' is not consistent in input and structure files'])
    end
end

data = fscanf(fin,'%f');
  
nd = length(data);        %  total number of data points
nr = nd/nnodes;            %  number of rows; check (next statement) to make sure
if nr ~= round(nd/nnodes)
   fprintf(1,'\ndata: nrow = %f\tncol = %d\n',nr,nnodes);
   fprintf(1,'number of data points = %d does not equal nrow*ncol\n',nd);
   error('Structure file does not have the correct dimensions (1)')
end
% check to make sure that structure is square
if nr ~= nnodes
    error('Structure file does not have the correct dimensions (2)')
end

data = reshape(data,nnodes,nr)';   %  have to transpose the reshaped array


dag = zeros(nnodes,nnodes);
for i = 1:size(data,1)
    for j = 1:size(data,2)
        dag(i,j) = data(i,j);
    end
end


end
%  end of readInputStructure.m
