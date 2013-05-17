#!/usr/bin/perl

use strict;
use warnings;

use EBox;
use EBox::UsersAndGroups::User;

EBox::init();


print EBox::UsersAndGroups::User->defaultQuota();

1;
