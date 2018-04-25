function runBN_initial(pre)
% runBN_initial is used to create the net_figure file
%   for a network without entered evidence or intervention.
%
% The input is 'pre'-- the prefix for the network and data
%    in BNW. It uses this identifier to read several files from
%    BNW.
% 
% The output is ???net_figure.txt. It also calls writeParameters 
%    to write the parameter file.
%
% runBN_initial is called by run_octave in the 'sourcecodes' directory.
%

sfile=strcat(pre,'structure_input.txt');
dfile=strcat(pre,'continuous_input.txt');

nnodefile=strcat(pre,'nnode.txt');
fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');


mapfilename=strcat(pre,'mapdata.txt');
mapvalfilename=strcat(pre,'map.txt');

mapfile = fopen(mapfilename,'w');

mapval = fopen(mapvalfilename,'w');


Std_flag=true;
[labels,cases,bnet,node_sizes,data,labelsold]=readInput(dfile,sfile,nnodes,Std_flag);
s=std(data,0,1);
m=mean(data);

for i=1:nnodes
  fprintf(mapval,'%s\t%f\t%f\n',labelsold{i},s(i),m(i));
end

fprintf(mapfile,'%s',labels{1});
for i=2:nnodes
  fprintf(mapfile,'\t%s',labels{i});
end
fprintf(mapfile,'\n');
fclose(mapval);
fclose(mapfile);

%Need to rearrange the means and stdevs to match the new labeling.
means = cell(1,nnodes);
stdevs = cell(1,nnodes);
for i = 1:nnodes
    for j = 1:nnodes
       if strcmp(labels{i},labelsold{j})
          means{i} = m(j);
          stdevs{i} = s(j);
          break
       end
    end
end


[bnet]=parameterLearning(bnet,cases);

filename=strcat(pre,'net_figure.txt');

drawFigure(nnodes,bnet,labels,filename,cases,stdevs,means);

writeParameters(pre,nnodes,bnet,labels,cases,labelsold,s,m);

end
