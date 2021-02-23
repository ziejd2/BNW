function  [ ] = testSetPredictions( pre )
   %   
   %  This function will make predictions for the cases included
   %   in the uploaded data file.
   %      
   %
   %  Input: ???ts_upload.txt
   %    This is the input file that is uploaded to BNW.
   %    It is directly written out by the BNW php code with no modification.
   %    The file format is a header line containing the variable that you want to predict.
   %     Then, there is a second header line with the variable names
   %     Finally, the file contains the data, with each case in a row.
   %     If there is missing data, an "NA" should be entered.
   %
   %  Output: ???ts_output.txt
   %
   % It is called by the run_test_set script in the 'sourcecodes' directory.


%  open file for input, include error handling
dfile=strcat(pre,'ts_upload.txt');

fin = fopen(dfile,'r');
if fin < 0
   error(['Could not open ',dfile,' for input']);
end

% Get the number of cases (the number of rows in the file excluding the header)
ntestcases = fskipl(fin,Inf) - 2;

frewind(fin);



% Read in first line to get the node label of the variable that should be predicted.
buffer = fgetl(fin);
[predict_label,buffer] = strtok(buffer);

% Read in second line to get the number of nodes and the node labels.
buffer = fgetl(fin);    %get header line as a string
nnodes = numel(strfind(buffer,"\t")) + 1;
labels_test = cell(1,nnodes);
for j=1:nnodes
    [next,buffer] = strtok(buffer);
    labels_test{j} = next;
end

% Read in the test_data
data_test_temp = cell(ntestcases,nnodes);
for i = 1:ntestcases
    buffer = fgetl(fin);
    for j = 1:nnodes
         [next,buffer] = strtok(buffer);
         data_test_temp{i,j} = next;
    end
end


% Read in the training (original) data and network structure.
sfile=strcat(pre,'structure_input.txt');
dfile=strcat(pre,'continuous_input.txt');
nnodefile=strcat(pre,'nnode.txt');
fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');

Std_flag=true;
[labels,cases,bnet]=readInput(dfile,sfile,nnodes,Std_flag);
[bnet] = parameterLearning(bnet,cases);

%Get node id in actual network for variable to be predicted
for i=1:nnodes
   if strcmp(labels(i),predict_label)
	predict_node = i;
   end
end

%Reformat test data so the columns match bnet structure
label_map = cell(2,nnodes);
for i=1:nnodes
    label_map{1,i} = labels_test{i};
    for j=1:nnodes
	if strcmp(labels_test(i),labels(j))
		label_map{2,i} = j;
		break
	end
     end
end


data_test = cell(ntestcases,nnodes);
for i=1:nnodes
   data_test(:,label_map{2,i}) = data_test_temp(:,i);
end

%%Read in training data means and standard deviations
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

%Get mapping of discrete levels.
max_states = max(bnet.node_sizes) + 1;
disc_nodes = size(bnet.dnodes,2);
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

%Standardize continuous data and map data to levels.
for i=1:nnodes
  if bnet.node_sizes(i) == 1
    for j=1:ntestcases
	if !strcmp(data_test{j,i},"NA")
          data_test{j,i} = (str2num(data_test{j,i}) - means{i})/stdevs{i};
        end
     end
  else
     for j=1:ntestcases
        if !strcmp(data_test{j,i},"NA")
	    for jj = 1:size(levels,1)
		if strcmp(labels{i},levels{jj,1})
		   break
                endif
            end
	    for k = 1:bnet.node_sizes(i)
               if strcmp(data_test{j,i},levels{jj,k+1})
                 data_test{j,i} = k;
               end
            end
        end
     end
end
end


%Now make predictions
predict_cases = bnet.node_sizes(predict_node);

if predict_cases == 1
      ts_continuous(pre,bnet,nnodes,predict_label,predict_node,data_test,means,stdevs);
else
      pred_levels = cell(1,predict_cases);
      for i = 1:disc_nodes
	if strcmp(levels{i,1},predict_label);
            for j = 1:predict_cases
                pred_levels{j} = levels{i,j+1};
	    end
            break
         end
      end
      ts_discrete(pre,bnet,nnodes,predict_label,predict_node,data_test,pred_levels,predict_cases);
end

%delete(dfile)

end

function ts_continuous(pre,bnet,nnodes,predict_label,predict_node,data_test,means,stdevs)

ntestcases = size(data_test,1);

predictions = zeros(ntestcases,2);

for i = 1:ntestcases
   evidence = data_test(i,:);
   evidence{predict_node} = {};
   for j=1:nnodes
      if strcmp(evidence{j},"NA")
          evidence{j} = {};
      end
   end
   engine = jtree_inf_engine(bnet);
   [engine,loglik] = enter_evidence(engine,evidence);
   predict = marginal_nodes(engine,predict_node);
   adj_mu = predict.mu*stdevs{predict_node}+means{predict_node};
   adj_sigma = stdevs{predict_node}*predict.Sigma;
   predictions(i,1) = adj_mu;
   predictions(i,2) = adj_sigma;
end

%Open output file.
filename = strcat(pre,'ts_output.txt');
fileID = fopen(filename,'w');

%fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);


%Calculate RMSEP (root mean square error of prediction) and q^2
%First, calculate TSS (total sum of squares) and
%  PRESS (sum of squares of prediction errors)
%Get rid of 'NA' data for predicted data.
actual_values = [];
predictions_removeNA = [];
for i=1:ntestcases
   if !strcmp(data_test(i,predict_node),'NA')
        actual_values = [actual_values, cell2num(data_test(i,predict_node))];
	predictions_removeNA = [predictions_removeNA,predictions(i,1)];
   end
end

average = mean(actual_values);
average = average*stdevs{predict_node}+means{predict_node};
tss = 0;
press = 0;
for i=1:length(actual_values)
	actual_values(i) = actual_values(i)*stdevs{predict_node}+means{predict_node};
	tss = (actual_values(i)-average)^2 + tss;
	press = (predictions_removeNA(i)-actual_values(i))^2 + press;
end
rmsep = sqrt(press/length(actual_values));
q_squared = 1 - press/tss;

%% Print rmseq and q^2
%fprintf(fileID,'RMS error of predictions: %6.4f\n',rmsep);
%fprintf(fileID,'Q^2 of predictions: %6.4f\n\n',q_squared);


%%Print the predictions
%fprintf(fileID,'Predicted mean and standard deviation for each case:\n');
fprintf(fileID,'CaseRow\tActualValue\tPredictionMean\tPredictionStDev\n');
for i = 1:ntestcases
     if strcmp(data_test{i,predict_node},"NA")
	fprintf(fileID,'%i\t%s\t',i,data_test{i,predict_node});
     else
        temp = data_test{i,predict_node}*stdevs{predict_node}+means{predict_node};
	fprintf(fileID,'%i\t%6.4f\t',i,temp);
     end
     fprintf(fileID,'%6.4f\t%6.4f\n',predictions(i,:));
end

fprintf(fileID,'Test set predictions for %s; RMSE= %6.4f; Q^2= %6.4f\n',predict_label,rmsep,q_squared);

fflush(fileID);
fclose(fileID);

end

function ts_discrete(pre,bnet,nnodes,predict_label,predict_node,data_test,pred_levels,predict_cases)

ntestcases = size(data_test,1);

predictions=zeros(ntestcases,predict_cases);

for i = 1:ntestcases
   evidence = data_test(i,:);
   evidence{predict_node} = {};
   for j=1:nnodes
      if strcmp(evidence{j},"NA")
          evidence{j} = {};
      end
   end
   engine = jtree_inf_engine(bnet);
   [engine,loglik] = enter_evidence(engine,evidence);
   predict = marginal_nodes(engine,predict_node);
   for j = 1:predict_cases
         predictions(i,j) = predict.T(j);
   end
end


pred_states = [];
for i=1:ntestcases
   if !strcmp(data_test(i,predict_node),'NA')
       max_state = 1;
       for j = 2:predict_cases
          if predictions(i,j) > predictions(i,max_state)
             max_state = j;
          end
       end
       pred_states = [pred_states,max_state];
    end
end


correct = 0;
j = 0;
for i=1:ntestcases
   if !strcmp(data_test(i,predict_node),'NA')
	j = j + 1;
	if pred_states(j) == cell2mat(data_test(i,predict_node))
            correct = correct + 1;
        end
   end
end

accuracy = correct/length(pred_states);


%Open output file.
filename = strcat(pre,'ts_output.txt');
fileID = fopen(filename,'w');

%fprintf(fileID,'Variable that was predicted: %s\n\n',predict_label);

%%Print the accuracy
%fprintf(fileID,'Fraction of accurate predictions: %6.4f\n\n',accuracy);

%%Print the predictions
%fprintf(fileID,'Predicted likelihood of each state for each case:\n');
fprintf(fileID,'%s\t%s\t','CaseRow','ActualState');
fprintf(fileID,'%s\t',pred_levels{1:end-1});
fprintf(fileID,'%s\n',pred_levels{end});
for i = 1:ntestcases
     fprintf(fileID,'%i\t',i);
     if strcmp(data_test{i,predict_node},"NA")
	fprintf(fileID,'%s\t',data_test{i,predict_node});
     else
	case_level = pred_levels{data_test{i,predict_node}};
	fprintf(fileID,'%s\t',case_level);
     end
     fprintf(fileID,'%6.4f\t',predictions(i,1:end-1));
     fprintf(fileID,'%6.4f\n',predictions(i,end));
end

fprintf(fileID,'Test set predictions of %s; Fraction of accurate predictions= %6.4f\n',predict_label,accuracy);

fflush(fileID);
fclose(fileID);



end

