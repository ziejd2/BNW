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
outfile = netID+"violin_plotly.html"

 
filename=netID+"continuous_input.txt"
f=open(filename,"r")
#Read the first line to get the variable names
line=f.readline()
#line = map(string.strip,line.strip().split("\t"))
varNames = line.strip().split("\t")
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
filename2=netID+"violin_orig.txt"
f2=open(filename2,"r")
line = f2.readline()
pdf_data = []
while line:
    line = line.strip()
    if line == "Data for new node":
        line = f2.readline()
        pdf_data.append([line.strip()])
    else:
        pdf_data[-1].append(float(line.strip()))
    line = f2.readline()

pdf_data2 = []
for i in range(len(cNames)):
    for j in range(len(pdf_data)):
        if cNames[i] == pdf_data[j][0]:
            pdf_data2.append(pdf_data[j][1:])

del pdf_data

layout = go.Layout(
    title="Original distributions",
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

for i in range(len(cNames)):
    y = []
    for j in range(len(data)):
        y.append(data[j][i])
    y_stand = standardize(y)
    #Add traces to violin plot
    if i == 1:
        fig.add_trace(go.Violin(y=y_stand,x0=cNames[i],side='negative',legendgroup='Data',name='Fit to data',fillcolor='blue',line=dict(color='blue'),hoverinfo='none',points='all',scalegroup=cNames[i]))
        fig.add_trace(go.Violin(y=pdf_data2[i],x0=cNames[i],side='positive',legendgroup='Network parameters',name='Network parameters',fillcolor='orange',line=dict(color='orange'),hoverinfo='none',points=False,scalegroup=cNames[i]))
    else:
        fig.add_trace(go.Violin(y=y_stand,x0=cNames[i],side='negative',legendgroup='Data',name='Data',fillcolor='blue',line=dict(color='blue'),showlegend=False,hoverinfo='none',points='all',scalegroup=cNames[i]))
        fig.add_trace(go.Violin(y=pdf_data2[i],x0=cNames[i],side='positive',legendgroup='Network parameters',name='Network parameters',fillcolor='orange',line=dict(color='orange'),showlegend=False,hoverinfo='none',points=False,scalegroup=cNames[i]))


fig.update_layout(title_xanchor="center")

plotly.offline.plot(fig,filename=outfile)

