#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import os
import sys
import subprocess

#samba-tool dns query 127.0.0.1 madrid.local @ ALL -UAdministrator%mario

CONFIG_FILE="/etc/max-control/conf.inc.php"
if not os.path.isfile(CONFIG_FILE):
    print " * No existe el archivo %s"%(CONFIG_FILE)
    sys.exit(0)


def read_conf(varname):
    f=open(CONFIG_FILE, 'r')
    data=f.readlines()
    f.close()

    for line in data:
        if line.startswith('define') and varname in line:
            if len(line.split('"')) >= 3:
                return line.split('"')[3]
            if len(line.split("'")) >= 3:
                return line.split("'")[3]
    return ''


USER = "Administrator"
PASS = read_conf('LDAP_BINDPW')
DOMAIN = read_conf('LDAP_DOMAIN')

args = ['samba-tool', 'dns', 'query',
        '127.0.0.1', DOMAIN,
        '@', 'A', '-U' + USER + '%' + PASS
        ]

IGNORE = ['ForestDnsZones', 'DomainDnsZones']
#
  # Name=, Records=2, Children=0
  #   A: 192.168.0.44 (flags=600000f0, serial=3, ttl=259200)
  #   A: 192.168.1.1 (flags=600000f0, serial=3, ttl=259200)
  # Name=_msdcs, Records=0, Children=0
  # Name=_sites, Records=0, Children=1
  # Name=_tcp, Records=0, Children=4
  # Name=_udp, Records=0, Children=2
  # Name=DomainDnsZones, Records=0, Children=2
  # Name=ForestDnsZones, Records=0, Children=2
  # Name=winxpldap, Records=1, Children=0
  #   A: 192.168.1.2 (flags=f0, serial=5, ttl=1200)
  # Name=zentyal3, Records=2, Children=0
  #   A: 192.168.0.44 (flags=f0, serial=3, ttl=259200)
  #   A: 192.168.1.1 (flags=f0, serial=3, ttl=259200)

#print " ".join(args)
p = subprocess.Popen(" ".join(args), shell=True, stdout=subprocess.PIPE, stdin=subprocess.PIPE, stderr=subprocess.STDOUT)
name = None
dirs = []
data = {}
while True:
    output = p.stdout.readline()
    
    if "Name=" in output:
        name = output.split(',')[0].strip().replace('Name=', '')
        if name.startswith('_') or name in IGNORE:
            name = None
            dirs = []
        dirs = []

    if name:
        if " A: " in output:
            dirs.append( output.split()[1] )
        data[name] = dirs

    if output == '' and p.poll() != None:
        break
    #print output,

for computer in data:
    print computer, " ".join(data[computer])

