#!/usr/bin/perl

package TLV::Builder;

use strict;
use parent qw/Exporter/;
use Carp qw/croak/;

our $VERSION = '1.0';

sub new {
    my $class = shift;

    my $self = {
        content => undef,
        index   => 0
    };

    return bless ($self, $class);
}

sub next_tag {
    my ($self, $tag);

    $self = shift;
    $tag  = shift;

    $self->{content} .= pack ('C', $tag);
    $self->{index} += 1;

    return $self->{tag};
}

sub next_length {
    my ($self, $length);

    $self   = shift;
    $length = shift;

    croak 'Length is 0' unless ($length);

    $self->{content} .= pack ('S>', $length);
    $self->{index} += 2;
}

sub next_value {
    my ($self, $value);

    $self  = shift;
    $value = shift;

    $self->{content} .= $value;
    $self->{index} += length $value;
}

sub index {
    my $self = shift;
    return $self->{index};
}

sub length {
    my ($self, $index, $length);

    $self   = shift;
    $index  = shift;
    $length = shift;

    substr ($self->{content}, $index, 2, pack ('S>', $length));
}

sub content {
    my $self = shift;
    return $self->{content};
}

1;
