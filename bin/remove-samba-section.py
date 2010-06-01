#!/usr/bin/env python

import sys

f=open("./smb.conf", 'r')
data=f.readlines()
f.close()

new=[]
section=None
for line in data:
  if "[grupoprueba]" in line:
    section="grupoprueba"

  elif section == "grupoprueba" and line.startswith('['):
    section=None
    print "\n"

  if section != 'grupoprueba':
    #new.append(line)
    #print "LINE=> ", line,
    print line,

  else:
    print >> sys.stderr, line,


#for line in new:
#  print line,
