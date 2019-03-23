#!/usr/bin/perl

package TLV::Parser;

use strict;
use parent qw/Exporter/;
use Carp qw/croak/;

our $VERSION = '1.0';

sub new {
    my ($class, $content);

    $class   = shift;
    $content = shift;

    croak 'No content' unless (length $content);

    my $self = {
        content        => $content,
        content_length => length $content,
        index          => 0,
        tag            => undef,
        length         => 0,
        value          => undef
    };

    return bless ($self, $class);
}

sub next_tag {
    my $self = shift;

    croak 'No space for tag' if ($self->{index} + 1 > $self->{content_length});

    $self->{tag} = unpack ('C', substr ($self->{content}, $self->{index}, 1));
    $self->{index} += 1;

    return $self->{tag};
}

sub next_length {
    my $self = shift;

    croak 'No space for length' if ($self->{index} + 2 > $self->{content_length});

    $self->{length} = unpack ('S>', substr ($self->{content}, $self->{index}, 2));
    $self->{index} += 2;

    croak 'Length is 0' unless ($self->{length});

    return $self->{length};
}

sub next_value {
    my $self = shift;

    croak 'No space for value' if ($self->{index} + $self->{length} > $self->{content_length});

    $self->{value} = substr ($self->{content}, $self->{index}, $self->{length});
    $self->{index} += $self->{length};

    return $self->{value};
}

sub index {
    my $self = shift;
    return $self->{index};
}

sub tag {
    my $self = shift;
    return $self->{tag};
}

sub length {
    my $self = shift;
    return $self->{length};
}

sub value {
    my $self = shift;
    return $self->{value};
}

sub content {
    my $self = shift;
    return $self->{content};
}

sub done {
    my $self = shift;
    return $self->{index} == $self->{content_length};
}

1;
