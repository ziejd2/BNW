function  [ ] = createSVG( pre )
   %  This function will create a JSON file for network visualization.
   %
   %  Input: 
   %   1) prestructure_input.txt
   %    Structure file.
   %   2) prestructure_input_temp.txt
   %    Structure file with model averaging scores.
   %   3) pregrviz_name_file.txt
   %    Node names in graph viz file.   
   %   4) pregraphviz.txt
   %    Original graphviz input file.   
   %
   %  Output: 
   %   pregraphviz_svg.txt-- Modified graphviz input file.
   %


%  open file for input, include error handling
dfile=strcat(pre,'structure_input.txt');

fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Read in first line to get the number of nodes and the node labels.
buffer = fgetl(fin);    %get header line as a string
nnodes = numel(strfind(buffer,"\t")) + 1;
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


% open file to read in model averaging scores
dfile2=strcat(pre,'structure_input_temp.txt');

if (exist(dfile2) == 0)
  scores = cell(nnodes,nnodes);
  for i = 1:nnodes
    for j = 1:nnodes
      scores{i,j} = edges{i,j};
    end
  end
else
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
end

% open file to get positions of variables
dfile3=strcat(pre,'grviz_name_file.txt');

fin3 = fopen(dfile3,'r');
if fin3 < 0
   error(['Could not open ',dfile3,' for input']);
end

grphviz_names = cell(nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    grphviz_names{j} = next;
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


outfile = strcat(pre,'graphviz_svg.txt');
fout = fopen(outfile,'w');
fprintf(fout,"digraph G {\n");
%fprintf(fout,"size=\"10,10\"; ratio = fill;\n");
fprintf(fout,"size=\"10,10\"; remincross = true;\n");
fprintf(fout,"node [shape=rectangle, width=1.0, fontsize=22];\n");
fprintf(fout,"edge [fontsize=16];\n");
for i = 1:nedges
  fprintf(fout,"\"%s\" -> \"%s\" [ label=\"%3.2f\", penwidth=\"%3.2f\" ];\n",labels{sources(i)},labels{targets(i)},scores1(i),2*scores1(i));
end
fprintf(fout,"}/n");
fclose(fout);


outfile2 = strcat(pre,'graphviz_svg_no_edge.txt');
fout2 = fopen(outfile2,'w');
fprintf(fout2,"digraph G {\n");
%fprintf(fout,"size=\"10,10\"; ratio = fill;\n");
fprintf(fout2,"size=\"10,10\"; remincross = true;\n");
fprintf(fout2,"node [shape=rectangle, width=1.0, fontsize=22];\n");
fprintf(fout2,"edge [fontsize=16];\n");
for i = 1:nedges
  fprintf(fout,"\"%s\" -> \"%s\" [ penwidth=\"%3.2f\" ];\n",labels{sources(i)},labels{targets(i)},2*scores1(i));
end
fprintf(fout2,"}/n");
fclose(fout2);



end
