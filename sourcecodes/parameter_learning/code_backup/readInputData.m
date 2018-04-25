function  [ labels , node_sizes, cases, data] = readInputData( dfile , nnodes )
	%  readColData  reads data from a file containing data in columns
	%               that have text titles, and possibly other header text
	%   
	%  Input:
	%     dfile  = name of the file containing the data.(required)
	%     nnodes  = number of columns in the data file.  (required)
    %
    %   Function assumes the following format for the input file:
    %       1) First line has labels for each of the nodes.  There cannot
    %              be spaces in any node label.
    %       2) The next line is the "node_sizes" of the nodes.  If the 
    %           nodes are discrete, this number will be equal to the number
    %           of states.  If the nodes are continuous, they should be 
    %           equal to 1.  The function assumes that any nodes with
    %           node_size = 1 is continuous.
    %       3) The rest of the file is numeric data.  The data in the input
    %               data has the number of columns equal to the number of 
    %               nodes in the network and the number of rows equal to
    %               the number of samples.
    %
    %
	%  Output:
	%     labels = cell array with node (column) labels.
    %     node_sizes  =  vector with the size of each node
    %     cases = cell array with the data.  The cases array is transposed
    %       in comparison with the input data to agree with the format of
    %       cell data used in BNT.

%  open file for input, include error handling
fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Read in first line to get the node labels.
labels = cell(1,nnodes);
buffer = fgetl(fin);    %get header line as a string
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels{j} = next;
end

%  Read in the data.  Use the vetorized fscanf function to load all
%  numerical values into one vector.  Then reshape this vector into a
%  matrix.

data = fscanf(fin,'%f');  %  Load the numerical values into one long vector




nd = length(data);        %  total number of data points
nr = nd/nnodes;            %  number of rows; check (next statement) to make sure
if nr ~= round(nd/nnodes)
   fprintf(1,'\ndata: nrow = %f\tncol = %d\n',nr,nnodes);
   fprintf(1,'number of data points = %d does not equal nrow*ncol\n',nd);
   error('data is not rectangular')
end

data = reshape(data,nnodes,nr)';   %  have to transpose the reshaped array


node_sizes = zeros(1,nnodes);
for j = 1:nnodes
    node_sizes(j) = data(1,j);
end

nr = nr - 1;
data(1,:) = [];
cases = cell(nnodes,nr);
cases(:,:) = num2cell(data');

end
%  end of readInputData.m