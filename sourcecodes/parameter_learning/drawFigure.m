function [] = drawFigure(nnodes,bnet,labels,filename,cases,stdevs,means,pre)
%drawFigure writes the parameters and data that are needed to draw the
%structure of a Bayesian network for BNW.
% This is the function that is called to create the initial
%   net_figure file for the network (before evidence or intervention).
%
%
% The output is the file specified by 'filename'.
%  For BNW, this file is called: ???net_figure.txt
%    where ??? is the prefix.
% 
% drawFigure is called by runBN_intial.m
%
% This was modified to show the original distributions of continuous nodes 
%  as mixtures of Gaussian distributions instead of marginalizing the nodes.
%  JZ, 7-15-2019


A=cell2mat(cases');
Amax=max(A);
Amin=min(A);

%Create an empty evidence cell array.
evidence = cell(1,nnodes);
engine = jtree_inf_engine(bnet);
[engine,loglik] = enter_evidence(engine,evidence);

%Open the file, and write the nodes to a file.
fileID = fopen(filename,'w');
%%% The number of nodes
fprintf(fileID,'%i\n',nnodes);

%Open file to write violin plot data

violin_file = strcat(pre,'violin_orig.txt');
vfile = fopen(violin_file,'w');

%Get canvas size

labels_temp = cellstr(labels);
[x,y] = make_layout(bnet.dag);
%[x,y] = layout_dag(bnet.dag);


x = x - min(x);
y = 1 - y;
y = y - min(y);

[x_dim,y_dim] = canvasSize(nnodes,x,y);

%%% The dimensions of the canvas for the javascript code
fprintf(fileID,'%i\t%i\t\n',x_dim,y_dim)

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
    
%    predict = marginal_nodes(engine,i);
    predict = marginal_nodes_no_ev(bnet,engine,i);
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

        %The next line is marginal node method for continuous variables.
        %%[x_vals,y_vals] = calcGaussian(predict.mu,predict.Sigma,Amax(i),Amin(i));
        %Instead get mixture of Gaussian distributions.
	s=struct(bnet.CPD{i});
	no_gaussians = size(s.Wsum)(1);
        Wsum = sum(s.Wsum);
        weights = zeros(no_gaussians,1);
	for j=1:no_gaussians,
            weights(j) = s.Wsum(j)/Wsum;
        end;


	%Get random samples from normals to make violin plots
        for j=1:no_gaussians
                normrnd_count = int16(1000*weights(j));
                if normrnd_count > 0
		    violin_data = normrnd(s.mean(j),sqrt(s.cov(j)),normrnd_count,1);
		%violin_data = normrnd(s.mean(j),sqrt(s.cov(j)),1000,1);
		    for k = 1:size(violin_data)
		      fprintf(vfile,'%6.4f\n',violin_data(k));
		    end
		end
	end
        

        all_y = zeros(101,1);
        for j=1:no_gaussians
            [x_vals,y_vals] = calcGaussian(s.mean(j),sqrt(s.cov(j)),Amax(i),Amin(i));
            y_vals = y_vals*weights(j);
            all_y = all_y + y_vals;
	end;
        
        %%%For continuous nodes, print x and the pdf of curve.
	for j = 1:101,
            %Write data to make violin plots
            %no_lines = int16(100*y_vals(j,1));
            %if no_lines > 0;
	    %	for k = 1:no_lines
	    %	    fprintf(vfile,'%6.4f\n',x_vals(j,1));
            %    end
            %end
            %%Undo standardization
            x_vals(j,1) = x_vals(j,1)*stdevs{i}+means{i};
            %%fprintf(fileID,'%6.4f\t%6.4f\n',x_vals(j,1),y_vals(j,1));
            fprintf(fileID,'%6.4f\t%6.4f\n',x_vals(j,1),all_y(j,1));
        end;

end

end
%fprintf(fileID,'%s\t %\n',labels_temp{:});


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
