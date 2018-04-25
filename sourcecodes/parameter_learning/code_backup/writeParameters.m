function [] = writeParameters(pre,nnodes,bnet,labels,cases,labelsold,s,m)
%Writes a file that contains the parameters of the network with no evidence.


%%Get the types of the nodes.
typefile = strcat(pre,'type.txt');
ftype = fopen(typefile,'r');
types = cell(1,nnodes);
buffer = fgetl(ftype);
buffer = fgetl(ftype);
for j = 1:nnodes
    [next,buffer] = strtok(buffer);
    types{j} = uint16(str2num(next));
end

max_states = 0;
disc_nodes = 0;
for j = 1:nnodes
  if types{j} > max_states
    max_states = types{j};
  end
  if types{j} > 1
    disc_nodes = disc_nodes + 1;
  end
end

%Add 1 to max_states to account for node name
max_states = max_states + 1;

%%Get mapping of discrete levels.
levelfile = strcat(pre,'nlevels.txt');
flevels = fopen(levelfile,'r');
levels = cell(disc_nodes,max_states);
ndisc_nodes = 0;
for i=1:disc_nodes
    ndisc_nodes = ndisc_nodes + 1;
    buffer = fgetl(flevels);
     for j = 1:max_states
       [next,buffer] = strtok(buffer);
       if j == 1
          levels{i,j} = next;
       else
%          levels{i,j} = uint16(str2num(next));
          levels{i,j} = next;
       end        
       if length(buffer) < 1
        break
       end
     end
end


evidence = cell(1,nnodes);
engine = jtree_inf_engine(bnet);
[engine,loglik] = enter_evidence(engine,evidence);

%Open output file.
filename = strcat(pre,'parameters.txt');
fileID = fopen(filename,'w');

for i = 1:nnodes
    for j = 1:nnodes
	if strcmp(labelsold{i},labels{j});
            nodeid = j;
            break
        end
    end
    predict = marginal_nodes(engine,nodeid);
    %%%Print the name of the node
    fprintf(fileID,'%s\n',labels{nodeid});
    %%%Print the type of node
    if bnet.node_sizes(nodeid) == 1;
        line = 'Continuous node\n';
        fprintf(fileID,line);
        %%% 'i' in the line below is correct: m and s are had original node labeling
        adj_mu = predict.mu*s(i)+m(i);
        adj_sigma = s(i)*predict.Sigma;
	fprintf(fileID,'%6.4f\t%6.4f\n\n',adj_mu,adj_sigma);
    else
        line = 'Discrete node with %i states\n';
        fprintf(fileID,line,bnet.node_sizes(nodeid));
        %line = 'Probability of each state\n';
        %fprintf(fileID,line);
        nodeid2 = 0;
        for k = 1:ndisc_nodes,
           if strcmp(levels{k,1},labels{nodeid}),
	      nodeid2 = k;
              break
           end
        end
        for j = 1:bnet.node_sizes(nodeid),
            %%%For discrete nodes, the state and the percent of that state
%		  fprintf(fileID,'%i\t%6.4f\n',levels{nodeid2,j+1},predict.T(j));
		  fprintf(fileID,'%s\t%6.4f\n',levels{nodeid2,j+1},predict.T(j));
        end;
        fprintf(fileID,'\n')

    end
end



fclose(fileID);

end

