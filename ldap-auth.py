import ldap

OU_USERS="ou=Users,dc=max-server"
OU_ADMINS="cn=Administrators,ou=Groups,dc=max-server"

def auth(username, password):
    DN="uid=%s,%s"%(username, OU_USERS) 
    l = ldap.initialize("ldap://127.0.0.1:389") 
    l.protocol_version = 3 
    try:
        l.simple_bind_s(DN, password) 
    except ldap.INVALID_CREDENTIALS:
        l.unbind_s()
        return False
    
    # is admin ???
    result=l.search_s(OU_ADMINS,
                      ldap.SCOPE_SUBTREE,
                      '(cn=*)',
                      ['memberUid'])
    l.unbind_s()
    for entry in result:
        if entry[1].has_key('memberUid') and \
           username in entry[1]['memberUid']:
            return True
    return False


# example

print "admin      ", auth('test', 'test')
print "bad passwd ", auth('test', 'test2')
print "not admin  ", auth('profe1', 'profe1')
