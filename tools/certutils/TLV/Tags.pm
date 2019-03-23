#!/usr/bin/perl

package TLV::Tags;

use strict;
use parent qw/Exporter/;

our $VERSION = '1.0';

our %EXPORT_TAGS = (header   => [qw/HEADER_VERSION HEADER_LENGTH HEADER_SIGNER_ID HEADER_SIGNER_NAME HEADER_SERIAL_NUMBER
                                    HEADER_CA_NAME HEADER_SIGNATURE_INFO HEADER_DIGEST_ALGORITHM
                                    HEADER_SIGNATURE_ALGORITHM_INFO HEADER_SIGNATURE_ALGORITHM HEADER_SIGNATURE_MODULUS
                                    HEADER_SIGNATURE HEADER_PADDING HEADER_FILENAME HEADER_TIMESTAMP/],
                    record   => [qw/RECORD_LENGTH RECORD_DNS_NAME RECORD_SUBJECT_NAME RECORD_FUNCTION RECORD_ISSUER_NAME
                                    RECORD_SERIAL_NUMBER RECORD_PUBLIC_KEY RECORD_SIGNATURE RECORD_CERTIFICATE
                                    RECORD_IP_ADDRESS RECORD_CERTIFICATE_HASH RECORD_HASH_ALGORITHM/],
                    digest   => [qw/DIGEST_SHA1 DIGEST_SHA256 DIGEST_SHA384 DIGEST_SHA512/],
                    function => [qw/FUNCTION_SAST FUNCTION_CCM FUNCTION_CCM_TFTP FUNCTION_TFTP FUNCTION_HTTPS/]);

our @EXPORT_OK = (@{$EXPORT_TAGS{header}}, @{$EXPORT_TAGS{record}}, @{$EXPORT_TAGS{digest}}, @{$EXPORT_TAGS{function}});

use constant {
    HEADER_VERSION                  => 1,
    HEADER_LENGTH                   => 2,
    HEADER_SIGNER_ID                => 3,
    HEADER_SIGNER_NAME              => 4,
    HEADER_SERIAL_NUMBER            => 5,
    HEADER_CA_NAME                  => 6,
    HEADER_SIGNATURE_INFO           => 7,
    HEADER_DIGEST_ALGORITHM         => 8,
    HEADER_SIGNATURE_ALGORITHM_INFO => 9,
    HEADER_SIGNATURE_ALGORITHM      => 10,
    HEADER_SIGNATURE_MODULUS        => 11,
    HEADER_SIGNATURE                => 12,
    HEADER_PADDING                  => 13,
    HEADER_FILENAME                 => 14,
    HEADER_TIMESTAMP                => 15
};

use constant {
    RECORD_LENGTH           => 1,
    RECORD_DNS_NAME         => 2,
    RECORD_SUBJECT_NAME     => 3,
    RECORD_FUNCTION         => 4,
    RECORD_ISSUER_NAME      => 5,
    RECORD_SERIAL_NUMBER    => 6,
    RECORD_PUBLIC_KEY       => 7,
    RECORD_SIGNATURE        => 8,
    RECORD_CERTIFICATE      => 9,
    RECORD_IP_ADDRESS       => 10,
    RECORD_CERTIFICATE_HASH => 11,
    RECORD_HASH_ALGORITHM   => 12
};

use constant {
    DIGEST_SHA1   => 1,
    DIGEST_SHA256 => 2,
    DIGEST_SHA384 => 3,
    DIGEST_SHA512 => 4
};

use constant {
    FUNCTION_SAST     => 0,
    FUNCTION_CCM      => 1,
    FUNCTION_CCM_TFTP => 2,
    FUNCTION_TFTP     => 3,
    FUNCTION_CAPF     => 4,
    FUNCTION_SRST     => 5,
    FUNCTION_HTTPS    => 7,
    FUNCTION_TVS      => 21
};

1;
