function  [ ] = modifyEdges( pre_old, pre_new )
   %  This function will allow users to add or delete edges from the network.
   %
   %  Input: 
   %   1) pre_oldstructure_input.txt
   %    Original structure file.
   %   2) pre_olddel_edge.txt
   %    A list of the edges that should be deleted.   
   %   3) pre_oldadd_edge.txt
   %    A list of the edges that should be added.
   %
   %  Output: 
   %   pre_newstructure_input.txt-- structure file with edges added/deleted.
   %


%  open file for input, include error handling
dfile=strcat(pre_old,'structure_input.txt');

fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Read in first line to get the number of nodes and the node labels.
buffer = fgetl(fin);    %get header line as a string
nnodes = numel(strfind(buffer,"\t"));
labels = cell(1,nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels{j} = next;
end

% Read in the edges
edges = cell(nnodes,nnodes);
for i = 1:nnodes
    buffer = fgetl(fin);
    for j = 1:nnodes
         [next,buffer] = strtok(buffer);
         edges{i,j} = next;
    end
end


%  open file with edges to be deleted
dedgefile=strcat(pre_old,'del_edge.txt');
fin2 = fopen(dedgefile,'r');
ndel=fskipl(fin2,Inf) - 1;
frewind(fin2);

delfrom = cell(1,ndel);
delto = cell(1,ndel);

buffer = fgetl(fin2);    %get header line
for j=1:ndel
  buffer = fgetl(fin2);    %get line with actual variables
  [next,buffer] = strtok(buffer);
  delfrom{j} = next;
  [next,buffer] = strtok(buffer);
  delto{j} = next;
end

%get index of edges to delete
idel_from = [];
idel_to = [];
for j = 1:ndel
  for k = 1:nnodes
    if strcmp(labels{k},delfrom{j})
      idel_from = [idel_from;k];
    endif
    if strcmp(labels{k},delto{j})
      idel_to = [idel_to;k];
    endif
  end
end

for i = 1:ndel
  edges(idel_from(i),idel_to(i)) = "0";
end

fclose(fin2);

%  open file with edges to be added
aedgefile=strcat(pre_old,'add_edge.txt');
fin3 = fopen(aedgefile,'r');
nadd=fskipl(fin3,Inf) - 1;
frewind(fin3);

addfrom = cell(1,nadd);
addto = cell(1,nadd);

buffer = fgetl(fin3);    %get header line
for j=1:nadd
  buffer = fgetl(fin3);    %get line with actual variables
  [next,buffer] = strtok(buffer);
  addfrom{j} = next;
  [next,buffer] = strtok(buffer);
  addto{j} = next;
end

%get index of edges to added
iadd_from = [];
iadd_to = [];
for j = 1:nadd
  for k = 1:nnodes
    if strcmp(labels{k},addfrom{j})
      iadd_from = [iadd_from;k];
    endif
    if strcmp(labels{k},addto{j})
      iadd_to = [iadd_to;k];
    endif
  end
end

for i = 1:nadd
  edges(iadd_from(i),iadd_to(i)) = "1";
end





outfile = strcat(pre_new,'structure_input.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
for i = 1:nnodes
      fprintf(fout,'%s\t',edges{i,1:end-1});
      fprintf(fout,'%s\n',edges{i,end});
end
fclose(fout);

end
