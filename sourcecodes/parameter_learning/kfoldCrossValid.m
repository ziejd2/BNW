function kfoldCrossValid(pre,predict_label,nfolds)
% This function will peform k-fold cross-validation.
% This requires the specification of the name of the variable
%   that you want to predict and the number of folds that the
%   data should be divided into.

nfolds = uint8(str2num(nfolds));

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

ncases = size(cases,2);

kfold_index = kfold_bin(ncases,nfolds);

if predict_cases == 1
	kfoldCV_continuous(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases,nfolds,kfold_index);
else
	kfoldCV_discrete(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases,nfolds,kfold_index);
endif


end


function kfoldCV_continuous(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases,nfolds,kfold_index)

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

kfoldPredictions=zeros(size(cases,2),2);


%t=cputime;
%First get predictions
for i =1:nfolds
%  i
  test_flag = false(ncases,1);
  for j=1:ncases
    if kfold_index(j) == i
         test_flag(j) = true;
    end
  end
 trainData = cases(:,~test_flag);
 testData = cases(:,test_flag);
 temp_index = zeros(size(testData,2),1);
 temp = 1;
 for j=1:ncases
    if kfold_index(j) == i
       temp_index(temp) = j;
       temp = temp + 1;
    end
 end
 [bnet] = parameterLearning(bnet,trainData);
 for j=1:size(testData,2)
  evidence = testData(:,j);
  evidence{predict_node} = {};
  engine = jtree_inf_engine(bnet);
  [engine,loglik] = enter_evidence(engine,evidence);
  predict = marginal_nodes(engine,predict_node);
  adj_mu = predict.mu*stdevs{predict_node}+means{predict_node};
  adj_sigma = stdevs{predict_node}*predict.Sigma;
  kfoldPredictions(temp_index(j),1) = adj_mu;
  kfoldPredictions(temp_index(j),2) = adj_sigma;
 end
end
%e=cputime-t;


%Open output file.
filename = strcat(pre,'kfoldCV.txt');
fileID = fopen(filename,'w');

fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);

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
	press = (kfoldPredictions(i,1)-case_adj(i))^2 + press;
end
rmsep = sqrt(press/ncases);
q_squared = 1 - press/tss;

%% Print rmseq and q^2
fprintf(fileID,'RMS error of predictions: %6.4f\n',rmsep);
fprintf(fileID,'Q^2 of predictions: %6.4f\n\n',q_squared);


%%Print the predictions
fprintf(fileID,'Predicted mean and standard deviation for each case:\n');
fprintf(fileID,'CaseRow\tFoldNumber\tActualValue\tPredictionMean\tPredictionStDev\n');
for i = 1:ncases
     fprintf(fileID,'%i\t%i\t%6.4f\t',i,kfold_index(i),case_adj(i));
     fprintf(fileID,'%6.4f\t%6.4f\n',kfoldPredictions(i,:));
end

fflush(fileID);
fclose(fileID);

end


function kfoldCV_discrete(pre,predict_label,nnodes,labels,cases,bnet,predict_node,predict_cases,nfolds,kfold_index)

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

kfoldPredictions=zeros(size(cases,2),predict_cases);

%t=cputime;
%First get kfold CV predictions
for i =1:nfolds
  test_flag = false(ncases,1);
  for j=1:ncases
     if kfold_index(j) == i
         test_flag(j) = true;
     end
  end
  trainData = cases(:,~test_flag);
  testData = cases(:,test_flag);
  temp_index = zeros(size(testData,2),1);
  temp = 1;
  for j =1:ncases
      if kfold_index(j) == i
         temp_index(temp) = j;
         temp = temp + 1;
      end
  end
  [bnet] = parameterLearning(bnet,trainData);
  for j = 1:size(testData,2)
    evidence = testData(:,j);
    evidence{predict_node} = {};
    engine = jtree_inf_engine(bnet);
    [engine,loglik] = enter_evidence(engine,evidence);
    predict = marginal_nodes(engine,predict_node);
    for k = 1:predict_cases
	kfoldPredictions(temp_index(j),k) = predict.T(k);
    end
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
       if kfoldPredictions(i,j) > kfoldPredictions(i,max_state)
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
filename = strcat(pre,'kfoldCV.txt');
fileID = fopen(filename,'w');

fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);


%%Print the accuracy
fprintf(fileID,'Fraction of accurate predictions: %6.4f\n\n',accuracy);

%%Print the predictions
fprintf(fileID,'Predicted likelihood of each state for each case:\n');
fprintf(fileID,'%s\t%s\t%s\t','CaseRow','FoldNumber','ActualState');
fprintf(fileID,'%s\t',pred_levels{1:end-1});
fprintf(fileID,'%s\n',pred_levels{end});
for i = 1:ncases
     fprintf(fileID,'%i\t%i\t',i,kfold_index(i));
     case_level = pred_levels{cases{predict_node,i}};
     fprintf(fileID,'%s\t',case_level);
     fprintf(fileID,'%6.4f\t',kfoldPredictions(i,1:end-1));
     fprintf(fileID,'%6.4f\n',kfoldPredictions(i,end));
end

fflush(fileID);
fclose(fileID);

end


function [kfold_index] = kfold_bin(ncases,nfolds);
%This returns indexes for the different cases to separate data into folds.

%Get array with random permutation of the number of cases
p = randperm(ncases);

base_size = idivide(ncases,nfolds);
remainder = rem(ncases,nfolds);

kfold_index = zeros(ncases,1);
group = 1;
count = 0;
for i=1:ncases
    count = count + 1;
    kfold_index(p(i)) = group;
%Need to do some checks if groups cannot be exactly equally sized
%If you have already added an extra member to the group, go to next group
    if count > base_size
       count = 0;
       group = group + 1;
%If you have filled the group, check if an extra is needed
     elseif count == base_size
       if remainder > 0
         remainder = remainder - 1;
       else
         count = 0;
         group = group + 1;
       end
     end
end


end

