#!/usr/bin/env python

import sys

#f=open("./smb.conf", 'r')
#data=f.readlines()
#f.close()

#new=[]
#section=None
#for line in data:
#  if "[grupoprueba]" in line:
#    section="grupoprueba"

#  elif section == "grupoprueba" and line.startswith('['):
#    section=None
#    print "\n"

#  if section != 'grupoprueba':
#    #new.append(line)
#    #print "LINE=> ", line,
#    print line,

#  else:
#    print >> sys.stderr, line,

from configobj import ConfigObj


smb=ConfigObj('/etc/samba/smb.conf')
for section in smb:
    print "[%s]"%section
    for sub in smb[section]:
        if type(smb[section][sub]) == type( [] ):
            print "  %s = %s" %(sub, ", ".join(smb[section][sub]))
        else:
            print "  %s = %s" %(sub, smb[section][sub])
    
    print "\n\n"
