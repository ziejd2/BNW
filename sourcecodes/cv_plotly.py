#!/var/www/html/compbio/BNW_1.3/bnw-env/bin/python3
import os
import sys


import plotly
import plotly.graph_objs as go
import csv
import string

netID = sys.argv[-1]
outfile = netID+"loo_plotly.html"

 
filename=netID+"looCV.txt"
f=open(filename,"r")
lines=f.readlines()
#Read the last line to get the variable name
line = lines.pop()
#line = map(string.strip,line.strip().split(" "))
line = line.strip().split(" ")
varName = line[3][:-1]
plot_title = varName+" LOOCV"
#remove header line
header = lines.pop(0)

#Read type file to determine if it is continuous or discrete
typefile = netID+"type.txt"
tf=open(typefile,"r")
line=tf.readline()
#varnames = map(string.strip,line.strip().split("\t"))
varnames = line.strip().split("\t")
line=tf.readline()
#vartypes = map(string.strip,line.strip().split("\t"))
vartypes = line.strip().split("\t")
varindex = varnames.index(varName)
cd_type = int(vartypes[varindex])


if cd_type == 1:
    #Make scatterplot for continuous_data
    #Read introductory lines from file
#    for i in range(6):
#        line = f.readline()
    #Read the data
    x = []
    y = []
#    line = f.readline()
#    while line:
    for line in lines:
        #line = map(string.strip,line.strip().split("\t"))
        line = line.strip().split("\t")
        x.append(float(line[1]))
        y.append(float(line[2]))
#        line=f.readline()

    data = [go.Scatter(x=x,y=y,mode='markers')]
    layout = go.Layout(
        title=plot_title,
        titlefont=dict(
            family='Arial, sans-serif',
            size=24,
            color='black'
            ),
        title_xref="paper",
	title_x=0.5,
	title_xanchor="center",
	title_yanchor="middle",
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
            ),
        margin=dict(t=30,l=50,b=40)
        )
    fig = go.Figure(data=data, layout = layout)
    plotly.offline.plot(fig,filename=outfile)

else:
    #Make bar chart for discrete data
    #Get names of states
    #header = map(string.strip,header.strip().split("\t"))
    header = header.strip().split("\t")
    states = header[2:]
    #Read the data
    actual = []
    predicted = []
 #   line = f.readline()
 #   while line:
    for line in lines:
        #line = map(string.strip,line.strip().split("\t"))
        line = line.strip().split("\t")
        actual.append(line[1])
        predict_x = line[2:]
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
#        line=f.readline()
    
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
   # print tp_all
   # print fn_all
   #  print fp_all
    trace1 = go.Bar(x=states,y=tp_all,name="True Positives")
    trace2 = go.Bar(x=states,y=fn_all,name="False Negatives")
    trace3 = go.Bar(x=states,y=fp_all,name="False Positives")
    data = [trace1,trace2,trace3]
    layout = go.Layout(
        barmode='group',
        title=plot_title,
        titlefont=dict(
            family='Arial, sans-serif',
            size=24,
            color='black'
            ),
	title_xref="paper",
	title_x=0.5,
	title_xanchor="center",
	title_yanchor="middle",
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
            ),
        margin=dict(t=30,l=50,b=40),
    )
    fig = go.Figure(data=data, layout = layout)
    plotly.offline.plot(fig,filename=outfile)
