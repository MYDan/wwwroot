#!/usr/bin/env perl

my $tpl = `cat page.tpl`;

for my $file ( grep{ -f $_ }grep{/^[\w\d_]+$/}glob '*' )
{
    print "build $file\n";
    open $H, ">$file.html" or die "make $file fail";
    my $t = `cat $file`;
    my $tmp = $tpl;
    $tmp =~ s/PAGE_WRAPPER_CONTENT/$t/;
    print $H $tmp;
}
