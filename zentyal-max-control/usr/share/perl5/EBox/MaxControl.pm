package EBox::MaxControl;

use strict;
use warnings;

use base qw(EBox::Module::Service
            EBox::FirewallObserver
            );

use EBox::Exceptions::DataExists;
use EBox::Gettext;
use EBox::Service;
use EBox::Sudo qw ( :all );
use EBox::Validate qw ( :all );
use EBox::MaxControlFirewall;

sub _create
{
    my $class = shift;
    my $self = $class->SUPER::_create(name => 'maxcontrol',
                      printableName => 'MAX Control',
                      @_);
    bless($self, $class);
    return $self;
}

# Method: menu
#
#       Overrides EBox::Module method.
sub menu
{
    my ($self, $root) = @_;
    $root->add(new EBox::Menu::Item('url' => 'MaxControl/Index',
                                    'text' => $self->printableName(),
                                    'separator' => 'Infrastructure',
                                    'order' => 460));
}

#sub _daemons
#{
#    return [
#        {
#            'name' => 'backharddi-ng',
#            'type' => 'init.d',
#            'pidfiles' => ['/var/run/backharddi-ng.pid']
#        }
#    ];
#}

sub usesPort # (protocol, port, iface)
{
    my ($self, $protocol, $port, $iface) = @_;

    return undef unless($self->isEnabled());

    return 1 if ($port eq 80);
    return 1 if ($port eq 389);

    return undef;
}

sub firewallHelper
{
    my ($self) = @_;
    if ($self->isEnabled()){
        return new EBox::MaxControlFirewall();
    }
    return undef;
}

1;
