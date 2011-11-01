<?php
require_once("Regexp_Assemble.php");
require_once("testutil.php");

//そもそも PHP では正規表現のトラッキングは使えません。
die;



    $re = new Regexp_Assemble();
    $re->track(1)->add('dog');
    var_dump($re->match('dog'));
    ok( $re->match('dog'), 're pattern-0 dog match' );
//    is( $re->source(0), 'dog', 'source is dog' );
/*
# 08_track.t
#
# Test suite for Regexp::Assemble
# Tests to see that tracked patterns behave themselves
#
# copyright (C) 2004-2007 David Landgren

use strict;
use constant TESTS => 75;

eval qq{use Test::More tests => TESTS + 4};
if( $@ ) {
    warn "# Test::More not available, no tests performed\n";
    print "1..1\nok 1\n";
    exit 0;
}

my $PERL_VERSION_TOO_LOW = ($] < 5.007);
my $PERL_VERSION_5_005   = ($] < 5.006);

use Regexp::Assemble;

my $fixed = 'The scalar remains the same';
$_ = $fixed;
*/
$ra = new Regexp_Assemble();

is_deeply( $ra->__mbegin, [], 'mbegin is [] on non-tracked R::A object' );
is_deeply( $ra->__mend,   [], 'mend is [] on non-tracked R::A object' );

{
    $re = new Regexp_Assemble();
    $re
        ->add( 'cat' )
        ->add( 'dog' )
    ;
    $regexp = $re->re();
    ok( $re->match( 'cat' ), 'match without tracking' );
    ok( ! $re->match( 'eagle' ), 'match fail without tracking' );
}

{
    $re = new Regexp_Assemble();
    $re->track(1)->add('dog');
    ok( $re->match('dog'), 're pattern-0 dog match' );
    is( $re->source(0), 'dog', 'source is dog' );

    $re = new Regexp_Assemble(['track'=>1]);
    $re
        ->add( 'dog', 'dogged', 'fish', 'fetish', 'flash', 'fresh' );
    $re->add('foolish-\\d+');
    ok( $re->match('dog'), 're pattern-1 dog match' );

//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 1 ) if $PERL_VERSION_TOO_LOW;
        is( $re->matched,  'dog', 're pattern-1 dog matched' );
//    }
    ok( $re->match('dogged'), 're pattern-1 dogged match' );
//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 1 ) if $PERL_VERSION_TOO_LOW;
        is( $re->matched,  'dogged', 're pattern-1 dogged matched' );
//    }
    ok( $re->match('fetish'), 're pattern-1 fetish match' );
//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 1 ) if $PERL_VERSION_TOO_LOW;
        is( $re->matched,  'fetish', 're pattern-1 fetish matched' );
//    }
    ok( $re->match('foolish-245'), 're pattern-1 foolish-\\d+ match' );
//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 2 ) if $PERL_VERSION_TOO_LOW;
        is( $re->matched,  'foolish-\\d+', 're pattern-1 foolish-\\d+ matched' );
        is ($re->source, 'foolish-\\d+', 're pattern-1 foolish source');
//    }
    ok( !$re->match('foolish-'), 're pattern-1 foolish-\\d+ 4' );
    ok( !$re->source, 're pattern-1 foolish-\\d+ source' );

//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 1 ) if $PERL_VERSION_TOO_LOW;
        ok( !$re->matched, 're pattern-1 foolish-\\d+ 5' );
//    }
//    if ($] < 5.009005) {
//        ok( do {use re 'eval'; 'cat' !~ /$re/}, 're pattern-1 cat <5.10' );
//        ok( do {use re 'eval'; 'foolish-808' =~ /$re/}, 're pattern-1 foolish-808 <5.10' );
//    }
//    else {
        unlike( 'cat' , '/'.$re.'/', 're pattern-1 cat 5.10' );
        unlike(  'foolish-808' ,'/'.$re.'/', 're pattern-1 foolish-808 5.10' );
//    }
}

{
    $re = new Regexp_Assemble(['track'=>1]);
    $re
        ->add( '^a-\\d+$' )
        ->add( '^a-\\d+-\\d+$' );
    $str = $re->as_string();
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 2 ) if $PERL_VERSION_5_005;
        ok( ! $re->match('foo'), 'match pattern-2 foo' );
        ok( $re->match('a-22-44'), 'match pattern-2 a-22-44' );
//    }
//    SKIP: {
//           skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 1 ) if $PERL_VERSION_5_005;
        is( $re->match('a-22-55555'),  '^a-\\d+-\\d+$', 're pattern-2 a-22-55555' );
//    }
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 1 ) if $PERL_VERSION_5_005;
        ok( $re->match('a-000'), 're pattern-2 a-000 match' );
//    }
//    SKIP: {
//        skip( "matched() is not implemented in this version of perl ($])", 1 ) if $PERL_VERSION_TOO_LOW;
        is( $re->matched,  '^a-\\d+$', 're pattern-2 a-000 matched' );
//    }
}

{
    $re = new Regexp_Assemble(['track'=>1]);
    $re
        ->add( '^b-(\\d+)$' )
        ->add( '^b-(\\d+)-(\\d+)$' )
    ;
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 12 ) if $PERL_VERSION_5_005;
        ok( ! $re->match('foo'), 'match pattern-3 foo' );
        ok( $re->match('b-34-56'), 'match pattern-3 b-34-56' );
        is( $re->mvar(0),  'b-34-56', 'match pattern-3 capture 1' );
        is( $re->mvar(1),  34, 'match pattern-3 capture 2' );
        is( $re->mvar(2),  56, 'match pattern-3 capture 3' );
        is_deeply( $re->mvar, ['b-34-56', 34, 56], 'match pattern-3 mvar' );
        is_deeply( $re->mbegin, [0, 2, 5], 'match pattern-3 mbegin' );
        is_deeply( $re->mend, [7, 4, 7], 'match pattern-3 ' );
        ok( $re->match('b-789'), 'match pattern-3 b-789' );
        is( $re->mvar(0),  'b-789', 'match pattern-3 capture 4' );
        is( $re->mvar(1),  789, 'match pattern-3 capture 5' );
        ok( ! ($re->mvar(2)), 'match pattern-3 undef' );
//    }
}

{
    $re = new Regexp_Assemble(['track'=>1]);
    $re
        ->add( '^c-(\\d+)$' )
        ->add( '^c-(\\w+)$' )
        ->add( '^c-([aeiou])-(\\d+)$' )
    ;
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 12 ) if $PERL_VERSION_5_005;
        ok( ! $re->match('foo'), 'match pattern-4 foo' );
        ok( ! $re->mvar(2), 'match pattern-4 foo novar' );
        $target = 'c-u-350';
        ok( $re->match($target), "match pattern-4 $target" );
        ok( $re->mvar(0) , $target, 'match pattern-4 capture 1' );
        ok( $re->mvar(1) , 'u', 'match pattern-4 capture 2' );
        ok( $re->mvar(2) == 350, 'match pattern-4 capture 3' );
        $target = 'c-2048';
        ok( $re->match($target), "match pattern-4 $target" );
        ok( $re->mvar(0) , $target, 'match pattern-4 capture 4' );
        ok( $re->mvar(1) == 2048, 'match pattern-4 capture 5' );
        ok( ! $re->mvar(2), 'match pattern-4 undef' );
        is_deeply( $re->mbegin, [0, undef, undef, 2], 'match pattern-3 mbegin' );
        is_deeply( $re->mend, [6, undef, undef, 6, undef], 'match pattern-3 mend' );
//    }
}

{
    $re = new Regexp_Assemble(['track'=>1]);
    $re
        ->add( '^c-\\d+$' )
        ->add( '^c-\\w+$' )
        ->add( '^c-[aeiou]-\\d+$' )
    ;
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 6 ) if $PERL_VERSION_5_005;
        ok( ! $re->match('foo'), 'match pattern-5 foo' );
        ok( ! $re->mvar(2), 'match pattern-4 foo novar' );
        $target = 'c-u-350';
        ok( $re->match($target), "match pattern-5 $target" );
        ok( $re->mvar(0) , $target, 'match pattern-5' );
        ok( ! $re->mvar(1), 'match pattern-5 no capture 2' );
        ok( ! $re->mvar(2), 'match pattern-5 no capture 3' );
//    }
}

{
    $re = new Regexp_Assemble( ['track'=>1] );
    $re
        ->add( '^cat' )
        ->add( '^candle$' )
        ->flags( 'i' )
    ;
//    SKIP: {
//           skip( "match()/matched() return undef in this version of perl ($])", 8 ) if $PERL_VERSION_5_005;
        ok( ! $re->match('foo'), 'not match pattern-6 foo' );
        $target = 'cat';
        ok( $re->match($target), "match pattern-6 $target" );
        is( $re->matched,  '^cat', "match pattern-6 $target re" );
        $target = 'CATFOOD';
        ok( $re->match($target), "match pattern-6 $target" );
        is( $re->matched,  '^cat', "match pattern-6 $target re" );
        $target = 'candle';
        ok( $re->match($target), "match pattern-6 $target" );
        is( $re->matched,  '^candle$', "match pattern-6 $target re" );
        $target = 'Candlestick';
        ok( ! $re->match($target), "match pattern-6 $target" );
//    }
}

{
    $re = new Regexp_Assemble(['track'=>1 ]);
    $re
        ->add( '^ab-(\d+)-(\d+)' )
        ->add( '^ac-(\d+)' )
        ->add( '^nothing' )
        ->add( '^ad-((\d+)-(\d+))' )
    ;
//    SKIP: {
//        skip( "/?{...}/ and \\d+ cause a panic in this version of perl ($])", 15 ) if $PERL_VERSION_5_005;
        ok( ! $re->capture(), 'match p7 no prior capture' );

        ok( $re->match('nothing captured'), 'match p7-1' );
        is( count($re->capture()), 0, 'match p7-1 no capture' );

        ok( $re->match('ac-417 captured'), 'match p7-2' );
        $capture = $re->capture();
        is( count($capture), 1, 'match p7-2 capture' );
        is( $capture[0], 417, "match p7-2 value 0 ok" );

        ok( $re->match('ab-21-17 captured'), 'match p7-3' );
        $capture = $re->capture();
        is( count($capture), 2, 'match p7-3 capture' );
        is( $capture[0], 21, "match p7-3 value 0 ok" );
        is( $capture[1], 17, "match p7-3 value 1 ok" );

        ok( $re->match('ad-808-245 captured'), 'match p7-4' );
        $capture = $re->capture();
        is( count($capture), 3, 'match p7-4 capture' );
        is( $capture[0], '808-245', "match p7-4 value 0 ok" );
        is( $capture[1], 808, "match p7-4 value 1 ok" );
        is( $capture[2], 245, "match p7-4 value 2 ok" );
//    }
}
/*
is( $_, 'eq', $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
