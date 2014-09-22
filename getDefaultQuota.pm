#!/usr/bin/perl

use strict;
use warnings;

use EBox;
#use EBox::UsersAndGroups::User;
use EBox::Samba::User;

EBox::init();


#print EBox::UsersAndGroups::User->defaultQuota();
print EBox::Samba::User->defaultQuota();

1;
