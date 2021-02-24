#!/var/www/html/compbio/BNW_1.3/bnw-env/bin/python3
import os
import sys
import math
#sys.path.append('/home/jziebart/.local/bin')
#sys.path.append('/home/jziebart/.local/lib')

import plotly
import plotly.graph_objs as go
import csv
import string

def mean_stdev(y):
    mean = sum(y)
    mean = sum(y)/len(y)
    stdev = 0
    for yi in y:
        stdev = stdev + (yi - mean)**2
    stdev = stdev/(len(y)-1)
    stdev = math.sqrt(stdev)
    return mean,stdev;

def standardize(y):
    mean,stdev = mean_stdev(y)
    ystd = []
    for yi in y:
        yi = (yi - mean)/stdev
        ystd.append(yi)
    return ystd


netID = sys.argv[-1]
outfile = netID+"violin_plotly_evidence.html"

 
filename=netID+"continuous_input.txt"
f=open(filename,"r")
#Read the first line to get the variable names
line=f.readline()
#line = map(string.strip,line.strip().split("\t"))
varNames = line.strip().split("\t")
#varNames = line
line=f.readline()
#line = map(string.strip,line.strip().split("\t"))
line = line.strip().split("\t")
for i in range(len(line)):
    line[i] = int(line[i])
cd_types = line
cNames = []
for i in range(len(varNames)):
    if cd_types[i] == 1:
        cNames.append(varNames[i])

#Get the variables impacted by intervention
param_file = []
intfile=netID+"parameters_ev.txt"
intf=open(intfile,"r")
line=intf.readline()
while line:
    #line = map(string.strip,line.strip().split("\t"))
    line = line.strip().split("\t")
    param_file.append(line)
    line=intf.readline()
keepVars = []
temp=[]
for i in range(len(param_file)):
    if len(param_file[i][0]) == 0:
        desc = temp[1][0].split()
        if desc[0] == 'Mean':
            temp2 = temp[0][0].split()
            keepVars.append(temp2[0])
        temp=[]
    else:
        temp.append(param_file[i])

#Read in original data
data = []
line = f.readline()
while line:
    #line = map(string.strip,line.strip().split("\t"))
    line = line.strip().split("\t")
    temp = []
    for i in range(len(varNames)):
        if cd_types[i] == 1:
            temp.append(float(line[i]))
    data.append(temp)
    line = f.readline()

#Read in data for violin plots
filename2=netID+"violin_evidence.txt"
f2=open(filename2,"r")
line = f2.readline()
pdf_varName = []
pdf_data = []
while line:
    line = line.strip()
    if line == "Data for new node":
        line = f2.readline()
        pdf_varName.append(line.strip())
        line = f2.readline()
        pdf_data.append([line.strip()])
    else:
        pdf_data[-1].append(float(line.strip()))
    line = f2.readline()


layout = go.Layout(
    title="Distributions after intervention",
    titlefont=dict(
        family='Arial, sans-serif',
        size=24,
        color='black'
        ),
    title_xref="paper",
    title_x=0.5,
    title_xanchor="center",
    title_yanchor="middle",
    yaxis=dict(title="Distributions of standardized data"),
    legend=dict(orientation='h'),
    margin=dict(t=40,l=70,b=40)
)
fig = go.Figure(layout=layout)

icount = 0
for i in range(len(keepVars)):
    for j in range(len(cNames)):
        if keepVars[i] == cNames[j]:
            idata = j
            break
    for j in range(len(pdf_varName)):
        if keepVars[i] == pdf_varName[j]:
            ipdf = j
            break
    y = []
    for j in range(len(data)):
        y.append(data[j][idata])
    y_stand = standardize(y)
    #Add traces to violin plot
    if icount == 0:
        fig.add_trace(go.Violin(y=y_stand,x0=cNames[idata],side='negative',legendgroup='Data',name='Fit to original data',fillcolor='blue',line=dict(color='blue'),hoverinfo='none',points='all',scalegroup=cNames[idata]))
        fig.add_trace(go.Violin(y=pdf_data[ipdf],x0=cNames[idata],side='positive',legendgroup='Network parameters after intervention',name='Network parameters after intervention',fillcolor='orange',line=dict(color='orange'),hoverinfo='none',points=False,scalegroup=cNames[idata]))
        icount = 1
    else:
        fig.add_trace(go.Violin(y=y_stand,x0=cNames[idata],side='negative',legendgroup='Data',name='Data',fillcolor='blue',line=dict(color='blue'),showlegend=False,hoverinfo='none',points='all',scalegroup=cNames[idata]))
        fig.add_trace(go.Violin(y=pdf_data[ipdf],x0=cNames[idata],side='positive',legendgroup='Network parameters after invervention',name='Network parameters after intervention',fillcolor='orange',line=dict(color='orange'),showlegend=False,hoverinfo='none',points=False,scalegroup=cNames[idata]))


plotly.offline.plot(fig,filename=outfile)

