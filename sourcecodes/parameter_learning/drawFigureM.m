function [] = drawFigureM(nnodes,bnet,labels,filename,cases,stdevs,means,selectvar,selectdata,pre)
%drawFigureM writes the parameters and data that are needed to draw the
%structure of a Bayesian network after adding evidence or intervention
%It creates the net_figure_new file after evidence/intervetion.
%
% The output file is specified by 'filename'.
%   For BNW, the file is named ???net_figure_new.txt
%       where ??? is the prefix.
%
% drawFigureM is called by Predictmultiple.m and Predictmultipleintervention.m



fileID = fopen(filename,'w');


A=cell2mat(cases');
Amax=max(A);
Amin=min(A);


evidence = cell(1,nnodes);
engine = jtree_inf_engine(bnet);

m = size(selectvar,1);

ev_dat = zeros(1,nnodes);
for i = 1:m,
    di=selectvar(i,1);
    ev_dat(di)=selectdata(i,1);   
%Need to standardized evidence for continuous nodes.
    if bnet.node_sizes(di) == 1
        ev_dat(di) = (ev_dat(di) - means{di}) / stdevs{di};
    end
    evidence{di}=ev_dat(di);
    fprintf(fileID,'%i\t',di);
end

fprintf(fileID,'\n');

[engine,loglik]=enter_evidence(engine,evidence);

%Open the file, and write the nodes to a file.
%%% The number of nodes
fprintf(fileID,'%i\n',nnodes);
%Get canvas size
labels_temp = cellstr(labels);
[x,y] = make_layout(bnet.dag);
x = x - min(x);
y = 1 - y;
y = y - min(y);
[x_dim,y_dim] = canvasSize(nnodes,x,y);

%Open file to write violin plot data
violin_file = strcat(pre,'violin_evidence.txt');
vfile = fopen(violin_file,'w');


%%% The dimensions of the canvas for the javascript code
fprintf(fileID,'%i\t%i\t\n',x_dim,y_dim);
x = x*x_dim;
y = y*y_dim;
for i = 1:nnodes,
%%% The name and X- and Y-positions of each node
    fprintf(fileID,'%s\t%i\t%i\n',labels{i},round(x(i)),round(y(i)));
end

%Get the number of parents and children for each node.
num_par = zeros(1,nnodes);
%For parents, sum down columns
for i = 1:nnodes,
    for j = 1:nnodes,
        if bnet.dag(j,i) == 1,
            num_par(i) = num_par(i) + 1;
        end
    end
end
num_child = zeros(1,nnodes);
for i = 1:nnodes,
    for j = 1:nnodes,
        if bnet.dag(i,j) == 1,
            num_child(i) = num_child(i) + 1;
        end
    end
end

for i = 1:nnodes,
    %%% The name and type of each node (1=continuous, the number of states
    %%% if it is discrete
    fprintf(fileID,'%s\t%i\n',labels{i},bnet.node_sizes(i));
    %%% The size of the node, I am going to keep them 
    %%% 250(width) by 150(height) for now
    %Could modify this to change the width based on the length of the node
    %name
    fprintf(fileID,'%i\t%i\n',250,150);
    %%% The number of parents of the node, and the parents
    if num_par(i) == 0;
        %%% If no parents:
        fprintf(fileID,'%i\n',num_par(i));
    else
        parents = zeros(1,num_par(i));
        k = 1;
        for j = 1:nnodes,
           if bnet.dag(j,i) == 1,
             parents(1,k) = j;
             k = k + 1;
           end
        end
        format = '%i\t';
        for j = 1:num_par(i)-1,
            format = strcat(format,'%i\t');
        end
        format = strcat(format,'%i\n');
        %%%If there are parents:
        fprintf(fileID,format,num_par(i),parents(1,:));
    end
    
    
    %%% The number of children of the node, and the children
    if num_child(i) == 0;
        %%% If no children:
        fprintf(fileID,'%i\n',num_child(i));
    else
        children = zeros(1,num_child(i));
        k = 1;
        for j = 1:nnodes,
           if bnet.dag(i,j) == 1,
             children(1,k) = j;
             k = k + 1;
           end
        end
        format = '%i\t';
        for j = 1:num_child(i)-1,
            format = strcat(format,'%i\t');
        end
        format = strcat(format,'%i\n');
        %%%If there are parents:
        fprintf(fileID,format,num_child(i),children(1,:));
    end
    
    predict = marginal_nodes(engine,i);
    if isempty(evidence{i})
      if bnet.node_sizes(i) ~= 1,
        for j = 1:bnet.node_sizes(i),
            %%%For discrete nodes, the state and the percent of that state
            fprintf(fileID,'%i\t%6.4f\n',j,predict.T(j));
        end;
      else
        %% The name of the node for the violin plot data
        if bnet.node_sizes(i) == 1;
            fprintf(vfile,'Data for new node\n');
            fprintf(vfile,'%s\n',labels{i});
        end

        %Get random samples from normals to make violin plots
        violin_data = normrnd(predict.mu,sqrt(predict.Sigma),1000,1);
        %violin_data = normrnd(s.mean(j),sqrt(s.cov(j)),1000,1);
        for k = 1:size(violin_data)
            fprintf(vfile,'%6.4f\n',violin_data(k));
        end



        [x_vals,y_vals] = calcGaussian(predict.mu,sqrt(predict.Sigma),Amax(i),Amin(i));
        %%%For continuous nodes, print x and the pdf of a normal curve.
        for j = 1:101,
            %%Undo standardization
            x_vals(j,1) = x_vals(j,1)*stdevs{i}+means{i};
            fprintf(fileID,'%6.4f\t%6.4f\n',x_vals(j,1),y_vals(j,1));
        end;
      end;
    else
      if bnet.node_sizes(i) == 1,
	fprintf(fileID,'%6.4f\t%6.4f\n',ev_dat(i)*stdevs{i}+means{i},1);
      else
	fprintf(fileID,'%6.4f\t%6.4f\n',ev_dat(i),1);
      endif 
    end
    
end

fclose(fileID);
fclose(vfile);
end









function [x_dim, y_dim] = canvasSize(nnodes,x,y)
%canvasSize Function to calculate the size of the canvas to
%           build the network structure


%I am going to assume that the node size will be
% height = 150, width = 250
% so there will be a node spacing of 
% 200 (in y-dim) and 300 (in x-dim).
y_space = 200;
x_space = 300;

%Set default minimum x and y dimensions
x_dim = 1200;
y_dim = 1200;

%get unique y values
y_unique = unique(y);
size_y = size(y_unique,2);
y_dim_temp = size_y*y_space;

%get the maximum nodes in any layer
size_x = zeros(1,size_y);
for i = 1:size_y,
    for j = 1:nnodes,
        if y_unique(i) == y(j),
            size_x(1,i) = size_x(1,i) + 1;
        end;
    end;
end;
size_x = max(size_x);
x_dim_temp = size_x*x_space;

if x_dim_temp > x_dim,
    x_dim = x_dim_temp;
end;

if y_dim_temp > y_dim,
    y_dim = y_dim_temp;
end;
end

function [x_vals,y_vals] = calcGaussian(mu,Sigma,maxval,minval)
%Function to calculate 101 points of Gaussian function to use in plotting
% Gets the probability density of the mean value and 50 evenly spaced
% points up to 3Sigma below the mean and 50 evenly space points up to
% 3Sigma above the mean.
%maxval
%minval
x_vals = zeros(101,1);
y_vals = zeros(101,1);

%x_vals(1,1) = mu - 3*Sigma;
x_vals(1,1) = minval - 1;
gap=((maxval+1)-(minval - 1))/100;
%x_vals(1,1) = 0;%mu - 3*Sigma;
for i = 1:100,
   % x_vals(i+1,1) = x_vals(1,1) + i*6*Sigma/100;
    x_vals(i+1,1) = x_vals(i,1) + gap;
 %x_vals(i+1,1) = x_vals(i,1) + 1/100;
end

for i = 1:101,
    y_vals(i,1) = normpdf(x_vals(i,1),mu,Sigma);
end

end
