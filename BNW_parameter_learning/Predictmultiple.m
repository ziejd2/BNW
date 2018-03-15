function Predictmultiple(pre)
dfile=strcat(pre,'structure_input.txt');
sfile=dfile;
dfile=strcat(pre,'continuous_input.txt');
nnodefile=strcat(pre,'nnode.txt');

fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');

Std_flag=true;
[labels,cases,bnet]=readInput(dfile,sfile,nnodes,Std_flag);

[bnet]=parameterLearning(bnet,cases);

fvarfile=strcat(pre,'var.txt');
fvar = fopen(fvarfile,'r');                         
select_var_new = fscanf(fvar,'%d');

fvardfile=strcat(pre,'vardata.txt');
fvard = fopen(fvardfile,'r');
select_var_data_new = fscanf(fvard,'%f');

means_orig = cell(1,nnodes);
stdevs_orig = cell(1,nnodes);
labels_orig = cell(1,nnodes);
%Read in original means and standard deviations
mapfile = strcat(pre,'map.txt');
fmap = fopen(mapfile,'r');
for i=1:nnodes
    buffer = fgetl(mapfile);
    temp = cell(1,4);
    for j=1:4
        [next,buffer] = strtok(buffer);
        temp{j} = next;
    end
    labels_orig{i} = temp{1};
    means_orig{i} = str2num(temp{4});
    stdevs_orig{i} = str2num(temp{3});
end
fclose(fmap);

%Need to map the means and stdevs to the correct labels
means = cell(1,nnodes);
stdevs = cell(1,nnodes);
%Read in labels in new order.
labelsnew = cell(1,nnodes);
mapdatafile = strcat(pre,'mapdata.txt');
fmapdata = fopen(mapdatafile,'r');
buffer = fgetl(fmapdata);
for i = 1:nnodes
    [next,buffer ] = strtok(buffer);
    labelsnew{i} = next;
end
fclose(fmapdata);
for i = 1:nnodes
    for j = 1:nnodes
       if strcmp(labelsnew{i},labels_orig{j})
          means{i} = means_orig{j};
          stdevs{i} = stdevs_orig{j};
          break
       end
    end
end


filename=strcat(pre,'net_figure_new.txt');

drawFigureM(nnodes,bnet,labels,filename,cases,stdevs,means,select_var_new,select_var_data_new);

writeParameters_ev(pre,bnet,nnodes,labels,cases,stdevs,means,select_var_new,select_var_data_new);

end
