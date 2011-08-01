# Copyright (C) 2011 eBox Technologies S.L.
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License, version 2, as
# published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

use base 'EBox::Network';

use strict;
use warnings;

use EBox::Global;

sub InternalIPAddress
{
    my ($self, $ip) = @_;

    my $global = EBox::Global->getInstance();
    my $net = $global->modInstance('network');

    #my @ifaces = @{$net->allIfaces()};
    my @ifaces = @{$net->InternalIfaces()};

    foreach my $iface (@ifaces) {
        unless ($net->ifaceMethod($iface) eq 'static') {
            next;
        }
        #print "$iface ";
        print $net->ifaceAddress($iface);
        print "\n";
    }
    return;
}


InternalIPAddress();

1;

