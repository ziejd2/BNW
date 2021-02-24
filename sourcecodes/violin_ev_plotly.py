#!/home/jziebart/python/Python-2.7.15/python
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
line = map(string.strip,line.strip().split("\t"))
varNames = line
line=f.readline()
line = map(string.strip,line.strip().split("\t"))
for i in range(len(line)):
    line[i] = int(line[i])
cd_types = line
cNames = []
for i in range(len(varNames)):
    if cd_types[i] == 1:
        cNames.append(varNames[i])

#Get the variables with evidence
evfile=netID+"varname.txt"
evf=open(evfile,"r")
line=evf.readline()
ev_vars = map(string.strip,line.strip().split("\t"))
keepVars = []
for i in cNames:
    if i not in ev_vars:
        keepVars.append(i)

#Read in original data
data = []
line = f.readline()
while line:
    line = map(string.strip,line.strip().split("\t"))
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
    title="<br>Distributions considering evidence",
    titlefont=dict(
        family='Arial, sans-serif',
        size=24,
        color='black'
        ),

    yaxis=dict(title="Distributions of standardized data"),
    legend=dict(orientation='h'),
    margin=dict(t=10,l=70,b=40)
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
        fig.add_trace(go.Violin(y=pdf_data[ipdf],x0=cNames[idata],side='positive',legendgroup='Network parameters considering evidence',name='Network parameters considering evidence',fillcolor='orange',line=dict(color='orange'),hoverinfo='none',points=False,scalegroup=cNames[idata]))
        icount = 1
    else:
        fig.add_trace(go.Violin(y=y_stand,x0=cNames[idata],side='negative',legendgroup='Data',name='Data',fillcolor='blue',line=dict(color='blue'),showlegend=False,hoverinfo='none',points='all',scalegroup=cNames[idata]))
        fig.add_trace(go.Violin(y=pdf_data[ipdf],x0=cNames[idata],side='positive',legendgroup='Network parameters considering evidence',name='Network parameters considering evidence',fillcolor='orange',line=dict(color='orange'),showlegend=False,hoverinfo='none',points=False,scalegroup=cNames[idata]))


plotly.offline.plot(fig,filename=outfile)

