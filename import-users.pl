#!/usr/bin/perl

use strict;
use warnings;

use EBox;
use EBox::Samba::User;
use File::Slurp;

my @lines = read_file('users.csv');
chomp (@lines);

EBox::init();

my $parent = EBox::Samba::User->defaultContainer();

for my $line (@lines) {
    my ($username, $givenname, $surname, $password) = split(',', $line);
    EBox::Samba::User->create(
        samAccountName => $username,
        parent => $parent,
        givenName => $givenname,
        sn => $surname,
        password => $password
    );
}

1;
