# Copyright (C) 2011 Mario Izquierdo (mariodebian) for Comunidad de Madrid
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

use strict;
use warnings;

package EBox::MaxControlFirewall;
use base 'EBox::FirewallHelper';

use EBox::Global;
use EBox::Config;
use EBox::Firewall;
use EBox::Gettext;

# open apache
use constant TCPPORTS => qw(80);

# open UDP 7 and 9 for wakeonlan in input and output
use constant UDPPORTS => qw(9);

sub new
{
        my $class = shift;
        my %opts = @_;
        my $self = $class->SUPER::new(@_);
        bless($self, $class);
        return $self;
}

sub input
{
    my $self = shift;
    my @rules = ();

    my $net = EBox::Global->modInstance('network');
    my @ifaces = @{$net->InternalIfaces()};

    foreach my $port (TCPPORTS){
        foreach my $ifc (@ifaces) {
            my $r = "-m state --state NEW -i $ifc  ".
                    "-p tcp --dport $port -j ACCEPT";
            push(@rules, $r);
        }
    }
    return \@rules;
    }


sub output
{
    my $self = shift;
    my @rules = ();

    my $net = EBox::Global->modInstance('network');
    my @ifaces = @{$net->InternalIfaces()};

    foreach my $port (UDPPORTS) {
        foreach my $ifc (@ifaces) {
            my $r = "-m state --state NEW -o $ifc  ".
                    "-p udp --dport $port -j ACCEPT";
            push(@rules, $r);
        }
    }

    return \@rules;
}

# Method: restartOnTemporaryStop
#
# Overrides:
#
#   <EBox::FirewallHelper::restartOnTemporaryStop>
#
sub restartOnTemporaryStop
{
    return 1;
}

1;
