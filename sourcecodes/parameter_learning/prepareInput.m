function  [ ] = prepareInput( pre )
   % 
   %  This function takes files that are uploaded to BNW and creates output
   %    files that can be used for structure and parameter learning.
   %  It replaces php code that was previously in bn_file_load_gom.php.
   %    There are several improvements in performance and ease of use:
   %     1) Loading files is significantly (~5x) faster for large input files.
   %     2) The allowed values for discrete variables are more flexible. 
   %           (e.g., A genotype variable be 'B' and 'D' instead of having
   %               to replace to make them '1' and '2'.)
   %     3) Continuous variables may be identified as continuous in some cases
   %            even if there is not a period.
   %     4) The states of discrete variables should be correctly ordered in
   %            almost all cases.
   %     5) An additional output file is written that will let users check if
   %            the input file has been uploaded and parsed correctly.
   %     6) Future updates to this code should be easier than updating the php.
   %      
   %
   %  Input: ???continuous_input_orig.txt
   %    This is the input file that is uploaded to BNW.
   %    It is directly written out by the BNW php code with no modification.
   %    The file format is a header line containing the variable names
   %     followed by the data, with each case in a row.
   %
   %  Output: There are many output files.
   %    1) The main output file is ???continuous_input.txt that can be
   %       used by the structure learning code and parameter learning codes.
   %       The first line is variable names, the second line is the node type
   %          (continuous nodes should have 1, discrete nodes have the number
   %           of states), and the rest is the data.
   %    2) A new output file is ???input_desc.txt, a file that describes the
   %        data so users can check that it has been parsed correctly.
   %    3) ???input_table.txt Similar to above file, but in tabular format.
   %    4) ???nlevels.txt: The states of discrete variables.
   %    5) ???name.txt: The names of the variables as uploaded.
   %    6) ???type.txt: The number of states for each variables
   %            (1 indicates a continuous variable.)
   %    7/8) ???nnode.txt and ???nrows.txt: number of nodes and cases
   %    9-13) ???ban.txt, ???white.txt, ???k.txt, ???thr.txt, and
   %          ???parent.txt: Files with default values for structure learning. 
   %
   % It is called by the run_prep_input script in the 'sourcecodes' directory.


%  open file for input, include error handling
dfile=strcat(pre,'continuous_input_orig.txt');

fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Get the number of cases (the number of rows in the file excluding the header)
ncases = fskipl(fin,Inf) - 1;


frewind(fin);

% Read in first line to get the number of nodes and the node labels.
buffer = fgetl(fin);    %get header line as a string
nnodes = numel(strfind(buffer,"\t")) + 1;
labels = cell(1,nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels{j} = next;
end



% Read in the data
data = cell(ncases,nnodes);
for i = 1:ncases
    buffer = fgetl(fin);
    for j = 1:nnodes
         [next,buffer] = strtok(buffer);
         data{i,j} = next;
    end
end

%Read one more line of the file to make sure that
% it does not contain a final line of data
buffer = fgetl(fin);
if length(buffer) > 1
    ncases = ncases + 1;
    for j = 1:nnodes
         [next,buffer] = strtok(buffer);
         data{ncases,j} = next;
    end
end
  


% Remove all rows that have missing data from data file
remove_count = sum(any(strcmp(data,"NA"),2));
data(any(strcmp(data,"NA"),2),:)=[];
ncases = ncases - remove_count;


% Determine whether or not the nodes are continuous or discrete.
% First, treat them as all discrete and get the states and number of stats(levels).
levels = cell(1,nnodes);
states = [];
for j = 1:nnodes
   states{end+1} = unique(data(:,j));
   levels{j} = size(states{j},1);
end



% Remove any variables that have a single variable
remove_labels = cell(1,nnodes);
remove = [];
for j = 1:nnodes
  if levels{j} == 1
    remove_labels{j} = labels{j};
    remove(end+1) = j;
  end
end

nnodes = nnodes - size(remove,2);
data(:,remove)=[];
labels(remove) = [];
levels(remove) = [];
states(:,remove) = [];

reason = cell(1,nnodes);
%Now do some checks to see if nodes are discrete or continuous
for j = 1:nnodes
    % If there are 3 or less unique values, I will assume that the node is discrete.
    if levels{j} < 4;
        reason{j} = "Small number (<4) of possible values";
        continue
    % If there are as many unique values as a third of the number of cases,
    %      I will assume that the node is continuous.
    elseif levels{j} > ncases/3;
       levels{j} = 1;
       reason{j} = "Large number of possible values compared to the number of cases";
       continue
    % If there are more than twenty unique values,
    %      I will assume that the node is continuous.
    elseif levels{j} > 20;
       levels{j} = 1;
       reason{j} = "Many (>20) possible values";
       continue
    % Otherwise, I will scan through the individual values.
    % If any of the values contain a '.', I will assume it is continuous.
    else
       reason{j} = "Default classification";
       period_test = 0;
       column = data(:,j);
       k = 1;
       while period_test == 0 
           period_test = sum(cell2mat(strfind(column(k),".")));
           if period_test != 0;
              reason{j} = "At least one value contained a period(.).";
              levels{j} = 1;
           end
           k++;
           if k > ncases
              break
           end
        end
    end
end



%I need to check if any discrete nodes are listed after continuous nodes.
%If so, I need to rearrange the columns.
max_disc = 0;
min_cont = nnodes + 1;
for i = 1:nnodes
    if levels{i} > 1
       max_disc = i;
    elseif min_cont == nnodes+1
       min_cont = i;
    end
end
%If max_disc > min_cont, you need to rearrange the nodes
%  to put the discrete nodes first.
if max_disc > min_cont
  levels_old = levels;
  labels_old = labels;
  data_old = data;
  states_old = states;
  reason_old = reason;
  new_order = {};
  for i=1:nnodes
    if levels_old{i} > 1
      new_order{end+1} = i;
    end
  end
  for i=1:nnodes
    if levels_old{i} == 1
      new_order{end+1} = i;
    end
  end
  labels = {};
  levels = {};
  states = {};
  reason = {};
  for i =1:nnodes
    labels{i} = labels_old{new_order{i}};
    levels{i} = levels_old{new_order{i}};
    states{i} = states_old{new_order{i}};
    reason{i} = reason_old{new_order{i}};
    for j=1:ncases
      data{j,i} = data_old{j,new_order{i}};
    end
  end
  
endif

%Write other files that are used by BNW for this key.
%The first group of files establish default settings for structure learning.
outfile = strcat(pre,'white.txt');
fout = fopen(outfile,'w');
fprintf(fout,'From\tTo\n');
fclose(fout);

outfile = strcat(pre,'ban.txt');
fout = fopen(outfile,'w');
fprintf(fout,'From\tTo\n');
fclose(fout);

outfile = strcat(pre,'k.txt');
fout = fopen(outfile,'w');
fprintf(fout,'1\n');
fclose(fout);

outfile = strcat(pre,'parent.txt');
fout = fopen(outfile,'w');
fprintf(fout,'4\n');
fclose(fout);

outfile = strcat(pre,'thr.txt');
fout = fopen(outfile,'w');
fprintf(fout,'0.5\n');
fclose(fout);


%The next group of files have information about the uploaded file.
outfile = strcat(pre,'name.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
fclose(fout);

outfile = strcat(pre,'nnode.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%i\n',nnodes);
fclose(fout);

outfile = strcat(pre,'nrows.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%i\n',ncases);
fclose(fout);

outfile = strcat(pre,'type.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
fprintf(fout,'%i\t',levels{1:end-1});
fprintf(fout,'%i\n',levels{end});
fclose(fout);

%This output file contains the states for discrete nodes.
% The unique matlab function already sorts the states.
outfile = strcat(pre,'nlevels.txt');
fout = fopen(outfile,'w');
for i = 1:nnodes
    if levels{i} > 1
        fprintf(fout,'%s\t',labels{i},states{i}{1:end-1});
        fprintf(fout,'%s\n',states{i}{end});
    end
end
fclose(fout);

%Print a file with a short description of the input.
descfile = strcat(pre,'input_desc.txt');
dout = fopen(descfile,'w');
tablefile = strcat(pre,'input_table.txt');
dout_table = fopen(tablefile,'w');
fprintf(dout,['As loaded, the input file had the following properties:\n\n']);
if size(remove,1) > 0
	fprintf(dout,['The following variables were removed because they contained only one value:\n']);
	for j = 1:size(remove_labels,2)
		if remove_labels{j}!= 1
		  fprintf(dout,'%s\n',remove_labels{j});
		end
	end
	fprintf(dout,['\n']);
end
fprintf(dout,'There are %i variables and %i cases(rows).\n',size(labels,2),ncases);
fprintf(dout,'%i cases(rows) have been removed because they contained NA (missing data).\n',remove_count);
fprintf(dout_table,'Variable\tType\tStates/Mean (St Dev)\tReason for classification\n');
fprintf(dout,'The variable names are:\n');
fprintf(dout,'%s\t',labels{1:end-1});
fprintf(dout,'%s\n\n',labels{end});
for i=1:nnodes
    if levels{i} == 1
       fprintf(dout,'%s is a continuous variable.\n',labels{i});
       fprintf(dout,'%s\n',reason{i});
       column = str2double(data(:,i));
       colmean = mean(column);
       colstd = std(column);
       fprintf(dout,'It has a mean of %6.3f and a standard deviation of %6.3f\n\n',mean(column),std(column));
       fprintf(dout_table,'%s\tContinuous\t%6.3f (%6.3f)\t%s\n',labels{i},mean(column),std(column),reason{i});
    else 
       fprintf(dout,'%s is a discrete variable with %i states.\n',labels{i},levels{i});
       fprintf(dout,'%s\n',reason{i});
       fprintf(dout,'The states are: ');
       fprintf(dout,'%s ',states{i}{1:end-1});
       fprintf(dout,'%s\n\n',states{i}{end});
       fprintf(dout_table,'%s\tDiscrete\t%i\t%s\n',labels{i},levels{i},reason{i});
    end
end
fprintf(dout_table,'There are %i variables and %i cases; %i variables and %i rows were removed.\n',size(labels,2),ncases,size(remove,1),remove_count);
fclose(dout);
fclose(dout_table);

outfile = strcat(pre,'continuous_input.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
fprintf(fout,'%i\t',levels{1:end-1});
fprintf(fout,'%i\n',levels{end});
%Need to replace states in discrete variables with integers for BNT
for i = 1:nnodes
    if levels{i} > 1
	for j = 1:ncases
            for k=1:size(states{i},1)
	      if strcmp(data{j,i},states{i}{k})
                 data{j,i} = sprintf('%i',num2cell(k){1});;
                 break
              end
            end
        end
     end
end
for i = 1:ncases
      fprintf(fout,'%s\t',data{i,1:end-1});
      fprintf(fout,'%s\n',data{i,end});
end
fclose(fout);





end
%  end of prepareInput.m