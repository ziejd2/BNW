function runBN_initial(pre)
sfile=strcat(pre,'structure_input.txt');
dfile=strcat(pre,'continuous_input.txt');

nnodefile=strcat(pre,'nnode.txt');
fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');


mapfilename=strcat(pre,'mapdata.txt');
mapvalfilename=strcat(pre,'map.txt');

mapfile = fopen(mapfilename,'w');

mapval = fopen(mapvalfilename,'w');


%nnodes=5;
Std_flag=true;
[labels,cases,bnet,node_sizes,data,labelsold]=readInput(dfile,sfile,nnodes,Std_flag);
s = std(data,0,1);
m=mean(data);


for i=1:nnodes
  fprintf(mapval,'%s\t%d\t%f\t%f\n',labelsold{i},node_sizes(i),s(i),m(i));
end


% for j=1:nnodes
%     [next,buffer] = strtok(buffer);
%     name{j}=next;
%     for i=1:nnodes    
%         if strcmp(name{j},labels{i})
%             map{j}=i;
%             fprintf(mapfile,'%d\t',i);
%         end
%      end
% end
%name
%labels
%map
fprintf(mapfile,'%s',labels{1});
for i=2:nnodes
  fprintf(mapfile,'\t%s',labels{i});
end
fprintf(mapfile,'\n');

[bnet]=parameterLearning(bnet,cases);
%[predict_mean,predict_sd,q_sq]=looCrossValid(bnet,cases);
%engine=jtree_inf_engine(bnet);
%evidence=cell(1,nnodes);

%varfile='var.txt';
%fvar = fopen(varfile,'r');
%select_var = fscanf(fvar,'%d');
%select_var=map{select_var};
%varfiled='vardata.txt';
%fvard = fopen(varfiled,'r');
%select_var_data = fscanf(fvard,'%f');

%evidence{select_var}=select_var_data;
%[engine,loglik]=enter_evidence(engine,evidence);

%outdata='prediction.txt';
%fout = fopen(outdata,'w');

%for ii = 1:nnodes 
 % i=map{ii};
 % data=marginal_nodes(engine,i);
 % fprintf(fout,'%d\t%d\t%f\t%f\t%f\n',ii,data.domain,data.T,data.mu,data.Sigma);
  %fprintf(1,'%d\n',i);
% end

filename=strcat(pre,'net_figure.txt');
drawFigure(nnodes,bnet,labels,filename,cases);

%quit force;
%marginal_nodes(engine,2)
%marginal_nodes(engine,3)
%marginal_nodes(engine,4)
%marginal_nodes(engine,5)
%evidence{1}=2;
%[engine,loglik]=enter_evidence(engine,evidence)
%marginal_nodes(engine,1)
%marginal_nodes(engine,2)
%marginal_nodes(engine,3)
%marginal_nodes(engine,4)
%marginal_nodes(engine,5)
%evidence{2}=0.6;
%evidence{1}=[];
%[engine,loglik]=enter_evidence(engine,evidence);
%marginal_nodes(engine,3);
%marginal_nodes(engine,4);
%marginal_nodes(engine,5);
fclose(mapval);
fclose(mapfile);
end