function looCrossValid(pre,predict_label)
% This function will peform leave-one-out cross-validation.
% This requires the specification of the name of the variable
%   that you want to predict.
% Modifying to output a table.  Sept 2020


sfile=strcat(pre,'structure_input.txt');
dfile=strcat(pre,'continuous_input.txt');
nnodefile=strcat(pre,'nnode.txt');
fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');

Std_flag=true;
[labels,cases,bnet]=readInput(dfile,sfile,nnodes,Std_flag);

for i=1:nnodes
    if strcmp(labels(i),predict_label)
       predict_node = i;
    end
end

predict_cases = bnet.node_sizes(predict_node);

if predict_cases == 1
	looCV_continuous(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases);
else
	looCV_discrete(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases);
endif

end

function looCV_continuous(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases)

ncases = size(cases,2);

%%Read in original means and standard deviations to report output as
%% untransformed values.
means_orig=cell(1,nnodes);
stdevs_orig=cell(1,nnodes);
labels_orig=cell(1,nnodes);
%Read in original means and standard deviations
mapfile = strcat(pre,'map.txt');
fmap = fopen(mapfile,'r');
for i=1:nnodes
    buffer = fgetl(mapfile);
    temp = cell(1,3);
    for j=1:3
        [next,buffer] = strtok(buffer);
	temp{j} = next;
    end
    labels_orig{i} = temp{1};
    means_orig{i} = str2num(temp{3});
    stdevs_orig{i} = str2num(temp{2});
end
fclose(fmap);
%Need to map the means and stdevs to the correct labels
means = cell(1,nnodes);
stdevs = cell(1,nnodes);
for i = 1:nnodes
    for j = 1:nnodes
       if strcmp(labels{i},labels_orig{j})
          means{i} = means_orig{j};
          stdevs{i} = stdevs_orig{j};
          break
       end
    end
end

loopredictions=zeros(size(cases,2),2);

%t=cputime;
%First get loo predictions
for i =1:ncases
%  i
  current_data = cases(:,i);
  cases_new = cases;
  cases_new(:,i) = [];
  evidence = current_data;
  evidence{predict_node} = {};
  [bnet]=parameterLearning(bnet,cases_new);
  engine = jtree_inf_engine(bnet);
  [engine,loglik] = enter_evidence(engine,evidence);
  predict = marginal_nodes(engine,predict_node);
  adj_mu = predict.mu*stdevs{predict_node}+means{predict_node};
  adj_sigma = stdevs{predict_node}*predict.Sigma;
  loopredictions(i,1) = adj_mu;
  loopredictions(i,2) = adj_sigma;
end
%e=cputime-t;

%Open output file.
filename = strcat(pre,'looCV.txt');
fileID = fopen(filename,'w');

%fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);

%Calculate RMSEP (root mean square error of prediction) and q^2
%First, calculate TSS (total sum of squares) and
%  PRESS (sum of squares of prediction errors)
average = mean(cell2mat(cases'))(predict_node);

%undo standardization
average = average*stdevs{predict_node}+means{predict_node};
tss = 0;
press = 0;
case_adj=zeros(size(cases,2),1);
for i =1:ncases
	case_adj(i) = cases{predict_node,i}*stdevs{predict_node}+means{predict_node};
	tss = (case_adj(i)-average)^2 + tss;
	press = (loopredictions(i,1)-case_adj(i))^2 + press;
end
rmsep = sqrt(press/ncases);
q_squared = 1 - press/tss;



%%Print the predictions
%%fprintf(fileID,'Predicted mean and standard deviation for each case:\n');
fprintf(fileID,'CaseRow\tActualValue\tPredictionMean\tPredictionStDev\n');
for i = 1:ncases
     fprintf(fileID,'%i\t%6.4f\t',i,case_adj(i));
     fprintf(fileID,'%6.4f\t%6.4f\n',loopredictions(i,:));
end
%% Print rmse and q^2
fprintf(fileID,'LOOCV predictions of %s; RMSE= %6.4f; Q^2= %6.4f\n',predict_label,rmsep,q_squared);

fflush(fileID);
fclose(fileID);


end


function looCV_discrete(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases)

ncases = size(cases,2);

%This next section just gets the original names of the levels.
% so they can be written to the output file.
%%Get the maximum_number of states so array will be big enough
%%Add 1 because the input includes the node name
max_states = max(bnet.node_sizes) + 1;
disc_nodes = size(bnet.dnodes,2);

%%Get mapping of discrete levels.
levelfile = strcat(pre,'nlevels.txt');
flevels = fopen(levelfile,'r');
levels = cell(disc_nodes,max_states);
ndisc_nodes = 0;
for i=1:disc_nodes
    ndisc_nodes = ndisc_nodes + 1;
    buffer = fgetl(flevels);
     for j = 1:max_states
       [next,buffer] = strtok(buffer);
       if j == 1
          levels{i,j} = next;
       else
          levels{i,j} = next;
       end
       if length(buffer) < 1
        break
       end
     end
end

pred_levels = cell(1,predict_cases);
for i = 1:disc_nodes
   if strcmp(levels{i,1},predict_label);
	for j = 1:predict_cases
	    pred_levels{j} = levels{i,j+1};
	end
        break
   end
end

loopredictions=zeros(size(cases,2),predict_cases);

%t=cputime;
%First get loo predictions
for i =1:ncases
%  i
  current_data = cases(:,i);
  cases_new = cases;
  cases_new(:,i) = [];
  evidence = current_data;
  evidence{predict_node} = {};

  [bnet]=parameterLearning(bnet,cases_new);
  engine = jtree_inf_engine(bnet);
  [engine,loglik] = enter_evidence(engine,evidence);
  predict = marginal_nodes(engine,predict_node);
  for j = 1:predict_cases
	loopredictions(i,j) = predict.T(j);
  end
end
%e=cputime-t;

%Now compare with actual outcomes
%actual_states = zeros(1,predict_cases);
%for i=1:ncases
%    for j = 1:predict_cases
%	if cell2mat(cases(predict_node,i)) == j
%           actual_states(j) = actual_states(j) + 1;
%	end
%    end
%end

pred_states = zeros(1,ncases);

for i=1:ncases
    max_state = 1;
    for j = 2:predict_cases
       if loopredictions(i,j) > loopredictions(i,max_state)
           max_state = j;
	end
    end
    pred_states(i) = max_state;
end

correct = 0;
for i=1:ncases
    if pred_states(i) == cell2mat(cases(predict_node,i))
	correct = correct + 1;
    end
end

accuracy = correct/ncases;

%Open output file.
filename = strcat(pre,'looCV.txt');
fileID = fopen(filename,'w');

%fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);


%%Print the accuracy
%fprintf(fileID,'Fraction of accurate predictions: %6.4f\n\n',accuracy);

%%Print the predictions
fprintf(fileID,'%s\t%s\t','CaseRow','ActualState');
fprintf(fileID,'%s\t',pred_levels{1:end-1});
fprintf(fileID,'%s\n',pred_levels{end});
for i = 1:ncases
     fprintf(fileID,'%i\t',i);
     case_level = pred_levels{cases{predict_node,i}};
     fprintf(fileID,'%s\t',case_level);
     fprintf(fileID,'%6.4f\t',loopredictions(i,1:end-1));
     fprintf(fileID,'%6.4f\n',loopredictions(i,end));
end

fprintf(fileID,'LOOCV predictions of %s; Fraction of accurate predictions= %6.4f\n',predict_label,accuracy);

fflush(fileID);
fclose(fileID);

end
