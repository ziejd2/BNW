function  [ ] = removeNodes( pre_old, pre_new )
   %  This function will allow users to delete variables from uploaded input file.
   %      For example, if an input file contains 20 variables, but the user is
   %      only interested in using 10 of these variables in a particular model,
   %      they can use this function to delete the variable.
   %
   %
   %  Input: 
   %   1) pre_oldcontinuous_input_orig.txt
   %    This is the original input file that is uploaded to BNW.
   %    It is directly written out by the BNW php code with no modification.
   %    The file format is a header line containing the variable names
   %     followed by the data, with each case in a row.
   %   2) pre_olddel_var.txt
   %    This is a list of the names of the variables that should be delete.   
   %
   %  Output: 
   %   pre_newcontinuous_input_orig.txt-- input file with variables deleted.
   %


%  open file for input, include error handling
dfile=strcat(pre_old,'continuous_input_orig.txt');

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


%  open file for input, include error handling
dvarfile=strcat(pre_old,'del_var.txt');
fin2 = fopen(dvarfile,'r');
if fin2 < 0
   dellabels = {};
   ndel = 0;
else
  buffer = fgetl(fin2);    %get first line as a string
  if buffer < 0
    dellabels = {};
    ndel = 0;
  else
    ndel = numel(strfind(buffer," ")) + 1;
    dellabels = cell(1,ndel);
    for j=1:ndel
      [next,buffer] = strtok(buffer);
      dellabels{j} = next;
    end
  end
end

%get index of variables to delete
if ndel > 0
  delindex = [];
  for j=1:nnodes
    for k = 1:ndel
      if strcmp(labels{j},dellabels{k})
	delindex = [delindex;j];
      endif
    end
   end

   labels(:,[delindex])=[];
   data(:,[delindex])=[];
end

outfile = strcat(pre_new,'continuous_input_orig.txt');
fout = fopen(outfile,'w');
fprintf(fout,'%s\t',labels{1:end-1});
fprintf(fout,'%s\n',labels{end});
for i = 1:ncases
      fprintf(fout,'%s\t',data{i,1:end-1});
      fprintf(fout,'%s\n',data{i,end});
end
fclose(fout);

end
