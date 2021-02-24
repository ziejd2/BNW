function  [ ] = createJSON( pre )
   %  This function will create a JSON file for network visualization.
   %
   %  Input: 
   %   1) prestructure_input.txt
   %    Structure file.
   %   2) prestructure_input_temp.txt
   %    Structure file with model averaging scores.
   %
   %  Output: 
   %   prenetwork.json-- json file.
   %


%  open file for input, include error handling
dfile=strcat(pre,'structure_input.txt');

fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Read in first line to get the number of nodes and the node labels.
buffer = strtrim(fgetl(fin));    %get header line as a string
nnodes = numel(strfind(buffer,"\t"))+1;
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



%Check to see if a file with model averaging scores exists
dfile2=strcat(pre,'structure_input_temp.txt');
%if it does not exist, then just open the other structure file again
if exist(dfile2, 'file') != 2
   dfile2 = dfile;
   frewind(dfile2);
end

fin2 = fopen(dfile2,'r');
if fin2 < 0
   error(['Could not open ',dfile2,' for input']);
end

buffer = fgetl(fin2);    %get header line as a string

% Read in the scores
scores = cell(nnodes,nnodes);
for i = 1:nnodes
    buffer = fgetl(fin2);
    for j = 1:nnodes
         [next,buffer] = strtok(buffer);
         scores{i,j} = next;
    end
end




nedges = 0;
sources = [];
targets = [];
scores1 = [];
for i = 1:nnodes
  for j = 1:nnodes
    if edges{i,j} == "1"
      nedges = nedges + 1;
      sources = [sources; i]; 
      targets = [targets; j]; 
      scores1 = [scores1; str2num(scores{i,j})]; 
    end
  end
end


outfile = strcat(pre,'network.json');
fout = fopen(outfile,'w');
fprintf(fout,"{\n");
fprintf(fout,"  \"nodes\": [\n");
for i = 1:(nnodes-1)
  fprintf(fout,"    {\n");
  fprintf(fout,"    \"data\": {\n");
  fprintf(fout,"      \"id\": \"%i\",\n",i);
  fprintf(fout,"      \"label\": \"%s\"\n",labels{i});
  fprintf(fout,"    }\n");
  fprintf(fout,"    },\n");
end
fprintf(fout,"    {\n");
fprintf(fout,"    \"data\": {\n");
fprintf(fout,"      \"id\": \"%i\",\n",nnodes);
fprintf(fout,"      \"label\": \"%s\"\n",labels{nnodes});
fprintf(fout,"    }\n");
fprintf(fout,"    }\n");
fprintf(fout,"  ],\n");
fprintf(fout,"  \"edges\": [\n");
for i = 1:(nedges-1)
  fprintf(fout,"    {\n");
  fprintf(fout,"    \"data\": {\n");
  fprintf(fout,"      \"id\": \"%i%i\",\n",sources(i),targets(i));
  fprintf(fout,"      \"source\": \"%i\",\n",sources(i));
  fprintf(fout,"      \"target\": \"%i\",\n",targets(i));
  fprintf(fout,"      \"weight\": %3.2f\n",scores1(i));
  fprintf(fout,"    }\n");
  fprintf(fout,"    },\n");
end
fprintf(fout,"    {\n");
fprintf(fout,"    \"data\": {\n");
fprintf(fout,"      \"id\": \"%i%i\",\n",sources(nedges),targets(nedges));
fprintf(fout,"      \"source\": \"%i\",\n",sources(nedges));
fprintf(fout,"      \"target\": \"%i\",\n",targets(nedges));
fprintf(fout,"      \"weight\": %3.2f\n",scores1(nedges));
fprintf(fout,"    }\n");
fprintf(fout,"    }\n");
fprintf(fout,"  ]\n");
fprintf(fout,"}");
%fprintf(fout,'%s\t',labels{1:end-1});
%fprintf(fout,'%s\n',labels{end});
%for i = 1:nnodes
%      fprintf(fout,'%s\t',edges{i,1:end-1});
%      fprintf(fout,'%s\n',edges{i,end});
%end
fclose(fout);

end
