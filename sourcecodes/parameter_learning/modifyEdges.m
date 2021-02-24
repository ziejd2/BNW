function  [ ] = modifyEdges( pre_old, pre_new )
   %  This function will allow users to add or delete edges from the network.
   %
   %  Input: 
   %  pre_oldstructure_input.txt-- old structure file that is just used to make sure the node order is consistent
   %  pre_oldmodify_edge.txt-- file with network structure after modification
   %
   %  Output: 
   %   pre_newstructure_input.txt-- structure file with edges added/deleted.
   %   pre_newstructure_input_temp.txt-- structure file with model averaging scores.
   %  September 16, 2020: I am only writing the file with model averaging scores
   %         if no edges were added to the network.
   %


%  open file for input, include error handling
dfile=strcat(pre_old,'structure_input.txt');
fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end


%  open file for input, include error handling
mfile=strcat(pre_old,'modify_edge.txt');
fin2 = fopen(mfile,'r');
if fin2 < 0
   error(['Could not open ',mfile,' for input']);
end
% Read in first line to get the number of nodes
nnodes = str2num(fgetl(fin2));

% Read in first line of old structure file to get the node labels.
buffer = fgetl(fin);    %get header line as a string
labels = cell(1,nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels{j} = next;
end

%Read in network structure information
buffer = fgetl(fin2);
buffer = buffer(2:end-1);
buffer = strrep(buffer,"\"","");
labels2 = cell(1,nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer,",");
    labels2{j} = next;
end

nedges = str2num(fgetl(fin2));
buffer = fgetl(fin2);
buffer = buffer(2:end-1);
buffer = strrep(buffer,"\"","");
sources = cell(1,nedges);
for j=1:nedges
    [next,buffer] = strtok(buffer,",");
    sources{j} = str2num(next);
end

buffer = fgetl(fin2);
buffer = buffer(2:end-1);
buffer = strrep(buffer,"\"","");
targets = cell(1,nedges);
for j=1:nedges
    [next,buffer] = strtok(buffer,",");
    targets{j} = str2num(next);
end

buffer = fgetl(fin2);
buffer = buffer(2:end-1);
weights = cell(1,nedges);
for j=1:nedges
    [next,buffer] = strtok(buffer,",");
    weights{j} = next;
end

%label_map is the index in "labels" that corresponds to each label in "labels2"
label_map = cell(1,nnodes);
for i = 1:nnodes
  current = labels2{i};
  for j = 1:nnodes
    if strcmp(current,labels{j})
      label_map{i} = j;
    endif
  end
end


edges_out = cell(nnodes,nnodes);
scores_out = cell(nnodes,nnodes);

for i = 1:nedges
  source_i = label_map{sources{i}};
  target_i = label_map{targets{i}};
  scores_out(source_i,target_i) = weights{i};
  edges_out(source_i,target_i) = "1";
end

%scores_out


tf = cellfun('isempty',edges_out);
edges_out(tf) = {"0"};
scores_out(tf) = {"0"};

test_score = 1;
for i=1:nnodes
  for j=1:nnodes
     if strcmp(scores_out{i,j},"1.1")
       test_score = 0;
     end
  end
end

if test_score == 1
outfile = strcat(pre_new,'structure_input_temp.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
for i = 1:nnodes
      fprintf(fout,'%s\t',scores_out{i,1:end-1});
      fprintf(fout,'%s\n',scores_out{i,end});
end
fclose(fout);
end

outfile2 = strcat(pre_new,'structure_input.txt');
fout2 = fopen(outfile2,'w');
fprintf(fout2,'%s\t',labels{1:end-1});
fprintf(fout2,'%s\n',labels{end});
for i = 1:nnodes
      fprintf(fout2,'%s\t',edges_out{i,1:end-1});
      fprintf(fout2,'%s\n',edges_out{i,end});
end
fclose(fout2);








end
