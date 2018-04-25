function [ labels, cases, dag, node_sizes, ord_flag ] = checkStructure(labels, cases, dag, node_sizes)
    %checkStructure Check to see if nodes are sorted correctly.  Nodes must be
    %   in topological order (i.e., parents before children) before parameter
    %   learning can take place. This function performs this sorting.
    %
    %Input and output have the same meaning.  The output has just been
    %topologically ordered.
    %   labels = cell array with the names of the nodes.
    %   cases = cell array with the data.
    %   dag = matrix with the strucutre of the network.
    %   node_sizes = vector with the size of each node.

%make connections array
%count how big you need the connections array to be
nnodes = size(dag,1);
narcs = 0;
for i = 1:nnodes
    for j = 1:nnodes
        if dag(i,j) == 1
            narcs = narcs + 1;
        end
    end
end
%fill connections array with label names
connections = cell(narcs,2);
ncount = 0;
for i = 1:nnodes
    for j = 1:nnodes
        if dag(i,j) == 1
            ncount = ncount + 1;
            connections{ncount,1} = labels{i};
            connections{ncount,2} = labels{j};
        end
    end
end

%get topologically sorted dag and labels
[new_dag, new_labels] = mk_adj_mat(connections, labels, 1);

%check to see if order changed
ord_flag = 0;
for i = 1:nnodes
    if ~strcmp(new_labels{i},labels{i})
        ord_flag = 1;
    end
end

if ord_flag
    %get new ordering of nodes
    order = cell(1,nnodes);
    for i = 1:nnodes
        for j = 1:nnodes
            if strcmp(new_labels{j},labels{i})
                order{i} = j;
            end
        end
    end

    %reorder cases and node_sizes
    new_cases = cell(size(cases));
    for i = 1:nnodes
        new_cases(order{i},:) = cases(i,:);
    end
    new_node_sizes = zeros(1,nnodes);
    for i = 1:nnodes
        new_node_sizes(order{i}) = node_sizes(i);
    end


    dag = new_dag;
    cases = new_cases;
    node_sizes = new_node_sizes;
    labels = new_labels;    
end

end
%end checkStructure.m

