#!/usr/bin/perl

use strict;
use warnings;

use EBox;
use EBox::Samba::Group;
EBox::init();



my $parent = EBox::Samba::Group->defaultContainer();


# Method: create
#
#   Adds a new Samba group.
#
# Parameters:
#
#   args - Named parameters:
#       name            - Group name.
#       parent          - Parent container that will hold this new Group.
#       description     - Group's description.
#       mail            - Group's mail.
#       isSecurityGroup - If true it creates a security group, otherwise creates a distribution group. By default true.
#       isSystemGroup   - If true it adds the group as system group, otherwise as normal group.
#       gidNumber       - The gid number to use for this group. If not defined it will auto assigned by the system.
#



EBox::Samba::Group->create(
    name => 'Group7',
    parent => $parent,
    description => 'esto es grupo 7',
    mail => '',
    isSecurityGroup => 1,
    isSystemGroup => 1,
    gidNumber => 1500
);


1;
