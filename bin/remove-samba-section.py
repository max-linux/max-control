#!/usr/bin/env python

import os
import sys
from configobj import ConfigObj
import ldap
import commands
from pprint import pprint
import pyinotify

CONF="/etc/max-control/conf.inc.php"
SHARED_DIR="/home/samba/groups/"

TO_ADD={'isos': {'comment': 'Archivos ISO', 
               'path': '/home/samba/shares/isos',
               'valid users': ['@"Teachers"', '@"__USERS__"'],
               'read list': ['@"Teachers"', '@"__USERS__"'],
               'write list': '@"Teachers"',
               'admin users': '@"Domain Admins"',
               'read only': 'No', 
               'browseable': 'Yes', 
               'force create mode': '0664', 
               'force directory mode': '0664', 
               }
        }

NOT_VALID_KEYS=['global', 'netlogon', 'profiles', 'homes', 'ebox-internal-backups', 'ebox-quarantine', 'print$']

def read_conf(varname):
    f=open(CONF, 'r')
    data=f.readlines()
    f.close()
    
    for line in data:
        if line.startswith('define') and varname in line:
            if len(line.split('"')) >= 3:
                return line.split('"')[3]
            if len(line.split("'")) >= 3:
                return line.split("'")[3]
    return ''


GROUPS=read_conf('LDAP_OU_GROUPS')
DOMAIN=read_conf('LDAP_DOMAIN')


def getGoupsShares():
    sharedgroups=[]
    l = ldap.initialize('ldap://localhost:389')
    results = l.search_s(GROUPS,ldap.SCOPE_SUBTREE,'(cn=*)',['cn', 'memberUid', 'sambaGroupType', 'gidNumber'])
    sharedgroups=[]

    for group in results:
        if "Teachers" in group[0]:
            continue
        
        if int(group[1]['gidNumber'][0]) < 2000:
            continue
        
        if int(group[1]['sambaGroupType'][0]) != 2:
            continue
        
        
        groupname=group[1]['cn'][0]
        
#        if not os.path.isdir( os.path.join(SHARED_DIR, groupname) ):
#            continue
        
        sharedgroups.append(groupname)
    return sharedgroups


print getGoupsShares()
sys.exit(0)

def waitForChanges(fname):
    print "waitForChanges() fname=%s"%fname
    res=commands.getoutput("inotifywait -e modify %s"%fname)
    #print res


def loadFile(fname):
    smb=ConfigObj('smb.conf')
    return smb

def printConf(smb):
    for section in smb:
        print "[%s]"%section
        if not smb[section]:
            continue
        for sub in smb[section]:
            if type(smb[section][sub]) == type( [] ):
                print "  %s = %s" %(sub, ", ".join(smb[section][sub]))
            else:
                print "  %s = %s" %(sub, smb[section][sub])
        
        print "\n\n"


def diffConfs(old, new):
    deleted={}
    added={}
    for section in old:
        if section in new:
            continue
        else:
            deleted[section]=old[section]
    
    for section in new:
        if section in old:
            continue
        else:
            added[section]=new[section]
    
    print "DELETED  ",
    pprint(deleted)
    print "ADDED  ",
    pprint(added)

def callback(event):
    if event.pathname != '/etc/samba/smb.conf':
        return
    
    if event.maskname != 'IN_MOVED_TO':
        return
    
    print event


wm = pyinotify.WatchManager()
notifier = pyinotify.Notifier(wm, default_proc_fun=callback)
wm.add_watch('/etc/samba', pyinotify.ALL_EVENTS, rec=True, auto_add=True)
notifier.loop()
#notifier.loop(daemonize=True,
#              pid_file='/var/run/max-control-samba.pid', 
#              force_kill=True,
#              stdout='/var/log/max-control-samba.log')


#old=loadFile('smb.conf')
#print old.keys()
#waitForChanges('smb.conf')
#new=loadFile('smb.conf')

#diffConfs(old, new)

