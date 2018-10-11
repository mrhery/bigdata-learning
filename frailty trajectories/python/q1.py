# -*- coding: utf-8 -*-
"""
Created on Thu Oct 11 17:01:23 2018

@author: mrhery
"""
import csv
import datetime
import collections, numpy

filename = "sample.csv"
subjects = []
datas = []
pdata = []

def recursive(sets, total, index = 0, data = []):
    if(len(sets) > 0 and total != index):
        for a in sets[index]:
            loop = total - 1
            void = []
            
            if(index > 0):
                for ix in range(index):
                    void += sets[ix]
            
            for i in range(loop):
                try:
                    set = sets[index + i]
                    for b in set:
                        m = a + "," + b
                        if((m not in data) and (a != b) and (b not in sets[index]) and (b not in void)):
                            data.append(m)
                except:
                    pass
        index += 1
        return recursive(sets, total, index, data)
    else:
        return data

with open(filename, newline='') as csvfile:
    spamreader = csv.reader(csvfile, quotechar='|')
    n = 0;
    for row in spamreader:
        if (n > 0):
                    #0       1       2      3
            data = [row[0], row[1], row[2], datetime.datetime.strptime(row[2], '%d/%m/%Y %H:%M:%S').strftime('%d-%b-%Y')]
            datas.append(data)
            subjects.append(row[0])
        n += 1
    subjects = set(subjects)
    for sub in subjects:
        idates = []
        tdate = []
        
        for data in datas:
            if (data[0] == sub):
                if(data[3] not in idates):
                    idates.append(data[3])
                
        for idate in idates:
            item = []
            for data in datas:
                if(data[0] == sub and data[3] == idate ):
                    item.append(data[1])
                
            if(item not in tdate):
                 tdate.append(item)
                 
        pdata += recursive(tdate, len(tdate))
        
    a = (collections.Counter(numpy.array(pdata)))
    final = (a.most_common())
    string = "From, To, Occurances \n"
    
    for dt in final:
        word = dt[0].split(',')
        words = word[0] + "," + word[1] + "," + str(dt[1]) + "\n"
        string = string + words
    
    f = open("output.csv", "w")
    f.write(string);
