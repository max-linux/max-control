
use strict;
use warnings;

package EBox::MaxControl::CGI::Index;

use base 'EBox::CGI::ClientBase';

use EBox;
use EBox::Global;
use EBox::Gettext;


# ## arguments:
# ## 	title [required]
sub new {
    my $class = shift;
    my $self = $class->SUPER::new('title'    => 'MAX Control',
                                  'template' => 'maxcontrol/index.mas',
                                  @_);
    $self->{domain} = "zentyal-maxcontrol";
    bless($self, $class);
    return $self;
}

sub _process($) {
    my $self = shift;
    $self->{title} = 'MAX Control';
    my $maxcontrol = EBox::Global->modInstance('maxcontrol');

    my @array = ();
    my $active = 'no';
    if ($maxcontrol->isEnabled()) {
        $active = 'yes';
    }

    push (@array, 'active' => $active);

    $self->{params} = \@array;
}


1;
