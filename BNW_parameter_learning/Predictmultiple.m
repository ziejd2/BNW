function Predictmultiple(pre)

dfile=strcat(pre,'structure_input.txt');
sfile=dfile;
dfile=strcat(pre,'continuous_input.txt');
nnodefile=strcat(pre,'nnode.txt');
fnnode = fopen(nnodefile,'r');
nnodes = fscanf(fnnode,'%d');



%nnodes=5;
Std_flag=true;
[labels,cases,bnet]=readInput(dfile,sfile,nnodes,Std_flag);


%name
%labels
%map

[bnet]=parameterLearning(bnet,cases);
%[predict_mean,predict_sd,q_sq]=looCrossValid(bnet,cases);
fvarfile=strcat(pre,'var.txt');

fvar = fopen(fvarfile,'r');
                            
select_var_new = fscanf(fvar,'%d');

fvardfile=strcat(pre,'vardata.txt');

fvard = fopen(fvardfile,'r');

select_var_data_new = fscanf(fvard,'%f');

filename=strcat(pre,'net_figure_new.txt');

drawFigureM(nnodes,bnet,labels,filename,cases,select_var_new,select_var_data_new);

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
end