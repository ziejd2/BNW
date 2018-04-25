function [] = writeParameters_ev(pre,bnet,nnodes,labels,cases,stdevs,means,selectvar,selectdata)
%Writes a file that contains the parameters of the network after entering evidence.
%
% The file is called ???parameters_ev.txt where ??? is the prefix in BNW 
%     for the network.
%
% It is called by Predictmultiple.m


%Read in original node labels to get node IDs.
infile = strcat(pre,'continuous_input.txt');
fin = fopen(infile,'r');
labelsold = cell(1,nnodes);
buffer = fgetl(fin);
for j = 1:nnodes
    [next,buffer] = strtok(buffer);
    labelsold{j} = next;
end
fclose(fin);


evidence = cell(1,nnodes);
engine = jtree_inf_engine(bnet);

m = size(selectvar,1);

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
%	 levels{i,j} = uint16(str2num(next));
	 levels{i,j} = next;
       end
       if length(buffer) < 1
        break
       end
     end
end


ev_dat = zeros(1,nnodes);
for i = 1:m,
    di=selectvar(i,1);
    ev_dat(di)=selectdata(i,1);
%Need to standardize evidence for continuous nodes.
    if bnet.node_sizes(di) == 1,
        ev_dat(di) = (ev_dat(di) - means{di})/stdevs{di};
    end
    evidence{di} = ev_dat(di);
end

[engine,loglik]=enter_evidence(engine,evidence);

%Open output file.
filename = strcat(pre,'parameters_ev.txt');
fileID = fopen(filename,'w');

for i = 1:nnodes
    for j = 1:nnodes
	if strcmp(labelsold{i},labels{j});
            nodeid = j;
            break
        end
    end
    %%%Print the name of the node
    fprintf(fileID,'%s\n',labels{nodeid});
    predict = marginal_nodes(engine,nodeid);
    if isempty(evidence{nodeid})
       %%%Print the type of node
       if bnet.node_sizes(nodeid) == 1;
           line = 'Continuous parameters considering evidence:\n';
           fprintf(fileID,line);
           %line = 'Mean and standard deviation of Gaussian distribution\n';
           %fprintf(fileID,line);
	     adj_mu = predict.mu*stdevs{nodeid}+means{nodeid};
             adj_sigma = stdevs{nodeid}*predict.Sigma;
             fprintf(fileID,'%6.4f\t%6.4f\n\n',adj_mu,adj_sigma);
       else
           line = 'Probability of states considering evidence:\n';
           fprintf(fileID,line);
           nodeid2 = 0;
           for k = 1:ndisc_nodes,
	     if strcmp(levels{k,1},labels{nodeid}),
                nodeid2 = k;
                break
             end
            end
	    for j = 1:bnet.node_sizes(nodeid),
		%%%For discrete nodes, the state and the percent of that state
%		fprintf(fileID,'%i\t%6.4f\n',levels{nodeid2,j+1},predict.T(j));
		fprintf(fileID,'%s\t%6.4f\n',levels{nodeid2,j+1},predict.T(j));
           end;
           fprintf(fileID,'\n')
       end
   else
       if bnet.node_sizes(nodeid) == 1;
          line = 'Evidence was observed for this node. The observed value was:\n';
          fprintf(fileID,line);  
          adj_mu = ev_dat(nodeid)*stdevs{nodeid}+means{nodeid};
          fprintf(fileID,'%6.4f\n\n',adj_mu);
       else
          nodeid2 = 0;
          for k = 1:ndisc_nodes,
	    if strcmp(levels{k,1},labels{nodeid}),
               nodeid2 = k;
               break
            end
          end
	 line = 'Evidence was observed for this node. The observed state was:\n';
         fprintf(fileID,line);
         state_ev =   uint16(ev_dat(nodeid));
%         fprintf(fileID,'%i\n\n',levels{nodeid2,state_ev+1});
         fprintf(fileID,'%s\n\n',levels{nodeid2,state_ev+1});
       end
   end
end



fclose(fileID);

end

