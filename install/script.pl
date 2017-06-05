#!/usr/bin/env perl
use Cwd;
my $dir = getcwd;


my %VAR = 
(
    downloadperl => 'wget http://www.cpan.org/src/5.0/perl-5.24.0.tar.gz',
);
print "cwd: $dir\n";
$dir .= "/mydan" unless $dir =~ /\/mydan$/;

mkdir $dir unless -d $dir;
chdir $dir;
print "dir = $dir\n";
unless( -f 'perl.source.tar.gz' )
{
    system "wget $VAR{downloadperl} -O perl.tar.gz.tmp";
    system "mv perl.tar.gz.tmp perl.source.tar.gz";
}


system "tar -zxvf perl.source.tar.gz";

chdir 'perl-5.24.0';

system "./Configure -des -Dprefix=$dir/perl  -Dusethreads -Uinstalluserbinperl";
system "make";
system "make test";
system "make install";
