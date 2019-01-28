#!/home/jziebart/python/Python-2.7.15/python
import os
import sys

#sys.path.append('/home/jziebart/.local/bin')
#sys.path.append('/home/jziebart/.local/lib')

import plotly
import plotly.graph_objs as go
import csv
import string

netID = sys.argv[-1]

outfile = netID+"kfold_plotly.html"
 
filename=netID+"kfoldCV.txt"
f=open(filename,"r")
#Read the first line to get the variable name
line=f.readline()
line = map(string.strip,line.strip().split(" "))
print line
varName = line[-1]
print varName

#Read type file to determine if it is continuous or discrete
typefile = netID+"type.txt"
tf=open(typefile,"r")
line=tf.readline()
varnames = map(string.strip,line.strip().split("\t"))
line=tf.readline()
vartypes = map(string.strip,line.strip().split("\t"))
varindex = varnames.index(varName)
cd_type = int(vartypes[varindex])


if cd_type == 1:
    #Make scatterplot for continuous_data
    #Read introductory lines from file
    for i in range(6):
        line = f.readline()
    #Read the data
    x = []
    y = []
    line = f.readline()
    while line:
        line = map(string.strip,line.strip().split("\t"))
        x.append(float(line[2]))
        y.append(float(line[3]))
        line=f.readline()

    data = [go.Scatter(x=x,y=y,mode='markers')]
    layout = go.Layout(
        xaxis=dict(
            autorange=True,
            title='Actual values',
            titlefont=dict(
                family='Arial, sans-serif',
                size=18,
                color='black'
                ),
            ),
        yaxis=dict(
            autorange=True,
            title='Predicted values',
            titlefont=dict(
                family='Arial, sans-serif',
                size=18,
                color='black'
                ),
            )
        )
    fig = go.Figure(data=data, layout = layout)
    plotly.offline.plot(fig,filename=outfile)

else:
    #Make bar chart for discrete data
    #Read introductory lines from file
    for i in range(5):
        line = f.readline()
    #Get names of states
    line = map(string.strip,line.strip().split("\t"))
    states = line[3:]
    #Read the data
    actual = []
    predicted = []
    line = f.readline()
    while line:
        line = map(string.strip,line.strip().split("\t"))
        actual.append(line[2])
        predict_x = line[3:]
        predict_x = [float(x) for x in predict_x]
        max_value = max(predict_x)
        max_index = predict_x.index(max_value)
        predicted.append(states[max_index])
        #check if multiple states are equally likely to be predicted states
        #I am not going to count these as being predicted here
        max_items = [x for x in predict_x if (abs(x-max_value) < 0.000001)]
        if len(max_items) > 1:
            predicted.pop()
            actual.pop()
        line=f.readline()
    
    #Go through states and get number of true positives, false positives, and false negatives
    tp_all = []
    fp_all = []
    fn_all = []
    for state in states:
        tp = 0
        fp = 0
        fn = 0
        for i in range(len(actual)):
            actual_i = actual[i]
            predicted_i = predicted[i]
            if actual_i == state:
                if predicted_i == state:
                    tp = tp + 1
                else:
                    fn = fn + 1
            elif predicted_i == state:
                fp = fp + 1
        tp_all.append(tp)
        fn_all.append(fn)
        fp_all.append(fp)
    trace1 = go.Bar(x=states,y=tp_all,name="True Positives")
    trace2 = go.Bar(x=states,y=fn_all,name="False Negatives")
    trace3 = go.Bar(x=states,y=fp_all,name="False Positives")
    data = [trace1,trace2,trace3]
    layout = go.Layout(
        barmode='group',
        xaxis=dict(
            autorange=True,
            title='State',
            titlefont=dict(
                family='Arial, sans-serif',
                size=18,
                color='black'
                ),
            ),
        yaxis=dict(
            autorange=True,
            title='Number of cases',
            titlefont=dict(
                family='Arial, sans-serif',
                size=18,
                color='black'
                ),
            )
    )
    fig = go.Figure(data=data, layout = layout)
    plotly.offline.plot(fig,filename=outfile)
