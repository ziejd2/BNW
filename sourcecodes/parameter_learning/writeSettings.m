function writeSettings(pre)
  %function to write tables of settings used in structure learning
  % so they can be reviewed by users


parfile = strcat(pre,'parent.txt');
paropen = fopen(parfile,'r');
par_val = fscanf(paropen,'%i');

kfile = strcat(pre,'k.txt');
kopen = fopen(kfile,'r');
k_val = fscanf(kopen,'%i');

thrfile = strcat(pre,'thr.txt');
thropen = fopen(thrfile,'r');
thr_val = fscanf(thropen,'%f');

filename = strcat(pre,'slsettings.txt');
fileID = fopen(filename,'w');
fprintf(fileID,'Structure learning settings\n');
fprintf(fileID,'Parameter\tValue\n');
fprintf(fileID,'Maximum number of parents\t%i\n',par_val);
fprintf(fileID,'Number of structures for model averaging\t%i\n',k_val);
fprintf(fileID,'Model averaging selection threshold\t%6.3f\n',thr_val);
fprintf(fileID,'\n');

tierfile = strcat(pre,'tier.txt');
if exist(tierfile,'file') == 2
	tieropen = fopen(tierfile,'r');
	tiers = fscanf(tieropen,'%s');
	tiers = strsplit(",",tiers);
	tiers(size(tiers,2)) = [];
	fprintf(fileID,'Tier assignments\n');
	fprintf(fileID,'Variable\tTier\n');
	notiers = str2num(tiers{1});
	i = 1;
        for j = 1:notiers
		i = i + 1;
		tiername = tiers{i};
		i = i + 1;
		novars = str2num(tiers{i});
                if novars == 0
                   fprintf(fileID,'No variables\t%s\n',tiername);
                else
		   for k = 1:novars
		       i = i + 1;
		       fprintf(fileID,'%s\t%s\n',tiers{i},tiername);
                   end
                end
        end
fprintf(fileID,"\n");
end


descfile = strcat(pre,'tierdesc.txt');
%format of ???tierdesc.txt is:
%% Assume that there are n tiers:
%% For Tier i:
%%   within tier interactions allowed: 
%%       (i-1)*n+1 true/false for yes,
%%       (i-1)*n+2 true/false for no;  (not needed, opposite of above)
%%   Tier j (for 1 to n, with i != j)
%%       Can j be parent of Tier i
%%       Can j be child of Tier i
if exist(descfile,'file') == 2
        %%I am going to assume that if ???tierdesc.txt exists, then
        %%%  ???tiers.txt exists and notiers has been defined.
        within = cell(notiers,1);
        parents = cell(notiers,notiers);
        children = cell(notiers,notiers);
        within(:) = 0;
        parents(:) = 0;
        children(:) = 0;
	descopen = fopen(descfile,'r');
	desc = fscanf(descopen,'%s');
	desc = strsplit(",",desc);
	desc(size(desc,2)) = [];
        for i = 1:notiers
            %%Pull out the section of desc array for this tiers
            desc_current = desc((i-1)*notiers*2+1:i*notiers*2);
	    for j = 1:notiers
		if j < i
                   if strcmp(desc_current{j*2+1},'true')
                       parents{i,j} = j;
		   end
                   if strcmp(desc_current{j*2+2},'true')
                       children{i,j} = j;
		   end
		end
                if i == j
                   if strcmp(desc_current{1},'true')
                      within{i,1} = 1;
		   end
		end
		if j > i
                   if strcmp(desc_current{(j-1)*2+1},'true')
                       parents{i,j} = j;
		   end
                   if strcmp(desc_current{(j-1)*2+2},'true')
                       children{i,j} = j;
		   end
		end
	end
	end
        fprintf(fileID,"Within tier interactions\n");
        fprintf(fileID,"Tier\tAre within tier interaction allowed?\n");
        for i = 1:notiers
            i1 = sprintf("%i",i);
            if within{i,1} == 1
               fprintf(fileID,"Tier%s\tYes\n",i1);
            else
               fprintf(fileID,"Tier%s\tNo\n",i1);
            end
        end
        fprintf(fileID,'\n');          
	fprintf(fileID,'Allowed parents\n');
	fprintf(fileID,'Tier\tOther tiers containing potential parents\n');
        for i=1:notiers
	    lineout = '';
            for j=1:notiers
               if parents{i,j} != 0
                   j1 = sprintf("%i",parents{i,j});
                   lineout = strcat(lineout,j1,',',{' '});
               end
            end
	    col1 = sprintf("%i",i);
            col1 = strcat("Tier",col1);
            if size(lineout,1) > 0
	       fprintf(fileID,'%s\t%s\n',col1,lineout{1,1}(1:end-2));
            else 
	       fprintf(fileID,'%s\tNone\n',col1);
            end
        end
        fprintf(fileID,'\n');          
	fprintf(fileID,'Allowed children\n');
	fprintf(fileID,'Tier\tOther tiers containing potential children\n');
        for i=1:notiers
	    lineout = '';
            for j=1:notiers
               if children{i,j} != 0
                   j1 = sprintf("%i",children{i,j});
                   lineout = strcat(lineout,j1,',',{' '});
               end
            end
	    col1 = sprintf("%i",i);
            col1 = strcat("Tier",col1);
            if size(lineout,1) > 0
	       fprintf(fileID,'%s\t%s\n',col1,lineout{1,1}(1:end-2));
            else 
	       fprintf(fileID,'%s\tNone\n',col1);
            end
        end
end

banfile = strcat(pre,'ban.txt');
if exist(banfile,'file') == 2
	banopen = fopen(banfile,'r');
	ban = fscanf(banopen,'%s');
	ban = strsplit(",",ban);
        ban(end) = [];
        nban = size(ban,2)/2;
        if nban > 0
           fprintf(fileID,'\n');          
           fprintf(fileID,'Banned edges\n');
	   fprintf(fileID,'From\tTo\n');
           for j=1:nban
              j1 = (j-1)*2 + 1;
              j2 = (j-1)*2 + 2;
	      fprintf(fileID,"%s\t%s\n",ban{1,j1},ban{1,j2})
           end
        end
end



whitefile = strcat(pre,'white.txt');
if exist(whitefile,'file') == 2
	whiteopen = fopen(whitefile,'r');
	white = fscanf(whiteopen,'%s');
	white = strsplit(",",white);
        white(end) = [];
        nwhite = size(white,2)/2;
        if nwhite > 0
           fprintf(fileID,'\n');          
           fprintf(fileID,'Required edges\n');
	   fprintf(fileID,'From\tTo\n');
           for j=1:nwhite
              j1 = (j-1)*2 + 1;
              j2 = (j-1)*2 + 2;
	      fprintf(fileID,"%s\t%s\n",white{1,j1},white{1,j2})
           end
        end
end




fclose(fileID);

end