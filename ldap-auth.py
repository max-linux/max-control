import ldap

OU_USERS="cn=Users,dc=madrid,dc=local"
#OU_ADMINS="CN=Domain Admins,CN=Users,DC=madrid,DC=local"

def auth(username, password):
    DN="cn=%s,%s"%(username, OU_USERS) 
    l = ldap.initialize("ldap://127.0.0.1:389") 
    l.protocol_version = 3
    try:
        l.simple_bind_s(DN, password)
        return "OK"
    except ldap.INVALID_CREDENTIALS, err:
        print "Exception=%s" % err
        l.unbind_s()
        return "ERROR"
    
    # is admin ???
    # result=l.search_s("CN=Users,DC=madrid,DC=local",
    #                   ldap.SCOPE_SUBTREE,
    #                   '(&(objectclass=user)(CN=*))',
    #                   #['cn', 'member', 'name', 'memberOf', 'gidNumber', 'description']
    #                   ['cn', 'name', 'unicodePwd', 'supplementalCredentials']
    #                   )
    # l.unbind_s()
    # for entry in result:
    #     print entry
    #     if entry[1].has_key('member') and \
    #        username in entry[1]['member']:
    #         return True
    # return False


# example

print "admin      ", auth('Administrator', 'mario')
#print "bad passwd ", auth('test', 'test2')
#print "not admin  ", auth('prueba2', 'prueba2')
