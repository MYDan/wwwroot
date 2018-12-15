#!/usr/bin/env perl

my $tpl = `cat page.tpl`;

system "rm -rf _book;cd ../mydan-book && rm -rf _book&& gitbook build && cp -r _book ../wwwroot/";
system 'cp _book/assets/images/favicon.ico _book/gitbook/images/favicon.ico';
for my $file ( grep{ -f $_ }grep{/^[\w\d_]+$/}glob '*' )
{
    print "build $file\n";
    open $H, ">$file.html" or die "make $file fail";
    my $t = `cat $file`;
    my $tmp = $tpl;
    $tmp =~ s/PAGE_WRAPPER_CONTENT/$t/;
    print $H $tmp;
}

system 'zip wwwroot.zip * -r && python -m SimpleHTTPServer';
