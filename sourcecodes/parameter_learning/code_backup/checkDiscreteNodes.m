function [ ] = checkDiscreteNodes( bnet, cases)
    %checkDiscreteNodes Checks if states of discrete nodes are be integers from 1 to M
    %   where M is the number of states of the node.  (M should be the same as
    %   node_sizes in the bnet).
    % 
    %Input:
    %   bnet: BNT bnet
    %   cases: cell array of data
    %
%
node_sizes = bnet.node_sizes;
dnodes = bnet.dnodes;
ndisc = size(dnodes,2);
ncases = size(cases,2);

%check to see that all data for discrete nodes are integers
for i = 1:ndisc
    inode = dnodes(i);
    data = cases(inode,:);
    isize = node_sizes(inode);
    states = zeros(1,isize);
    for j = 1:isize
        states(j) = j;
    end
    for j = 1:ncases
        k = int64(data{j});
        if ~any(k==states)
            error(['Discrete nodes must be integers from 1 to the number of states']);
        end
    end
end

    
end



