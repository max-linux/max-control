package EBox::MaxControlFirewall;
use strict;
use warnings;

use base 'EBox::FirewallHelper';

use EBox::Objects;
use EBox::Global;
use EBox::Config;
use EBox::Firewall;
use EBox::Gettext;

# open apache and ldap
use constant TCPPORTS => qw(80 389);

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

1;
