package EBox::CGI::MaxControl::Index;

use strict;
use warnings;

use base 'EBox::CGI::ClientBase';

use EBox::Global;
use EBox::Gettext;

## arguments:
## 	title [required]
sub new {
	my $class = shift;
	my $self = $class->SUPER::new('title'    => __('MAX Control'),
				      'template' => 'maxcontrol/index.mas',
				      @_);
	$self->{domain} = "zentyal-maxcontrol";
	bless($self, $class);
	return $self;
}

sub _process($) {
	my $self = shift;
	$self->{title} = __('MAX Control');
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
