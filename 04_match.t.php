<?php
require_once("Assemble.pm.php");
require_once("testutil.php");
//match_list( 'POSIX', ['X[0[:alpha:]%]','Y[1-4[:punct:]a-c]'] , ['X0','X%','Xa','Xf','Y1','Y;','Y!','yc'] );

//match_list( 'c.z', ['c^z','c-z','c5z','cmz'] , ['c^z','c-z','c5z','cmz'] );

/*
# 04_match.t
#
# Test suite for Regexp::Assemble
# Tests to see than an assembled regexp matches all that it is supposed to
#
# copyright (C) 2004-2006 David Landgren

use strict;
eval qq{
    use Test::More tests => 1381;
};
if( $@ ) {
    warn "# Test::More not available, no tests performed\n";
    print "1..1\nok 1\n";
    exit 0;
}

use Regexp::Assemble;

my $fixed = 'The scalar remains the same';
$_ = $fixed;
# Bug #17507 as noted by barbie
#
# There appears to be a problem with the substitute key on Windows, for
# at least Perl 5.6.1, which causes this test script to terminate
# immediately on encountering the character.
my $subchr    = 0x1a;
my $win32_56x = ($^O eq 'MSWin32' && $] < 5.008) ? 1 : 0;
diag("enabling defensive workaround for $] on $^O") if $win32_56x;

{
    for my $outer ( 0 .. 15 ) {
        my $re = Regexp::Assemble->new->anchor_string->chomp(0);
        for my $inner ( 0 .. 15 ) {
            next if $win32_56x and $subchr == ($outer*16 + $inner);
            $re->add( quotemeta( chr( $outer*16 + $inner )));
        }
        for my $inner ( 0 .. 15 ) {
            if( $win32_56x and $subchr == ($outer*16 + $inner)) {
                 ok( 1, "faking $subchr for 5.6 on Win32" );
            }
            else {
                my $ch = chr($outer*16 + $inner);
                like( $ch, qr/$re/, "run $ch ($outer:$inner) $re" );
            }
        }
    }
}
*/
/*
for( 0 .. 255 ) {
    if( $win32_56x and $subchr == $_) {
        pass("Fake a single for 5.6 on Win32");
        next;
    }
    my $ch = chr($_);
    my $qm = Regexp::Assemble->new(chomp=>0)->anchor_string->add(quotemeta($ch));
    like( $ch, qr/$qm/, "quotemeta(chr($_))" );
}

for( 0 .. 127 ) {
    if( $win32_56x and $subchr == $_) {
        pass( "Fake a hi for 5.6 on Win32");
        pass( "Fake a lo for 5.6 on Win32");
        next;
    }
    my $lo = chr($_);
    my $hi = chr($_+128);
    my $qm = Regexp::Assemble->new(chomp => 0, anchor_string => 1)->add(
        quotemeta($lo),
        quotemeta($hi),
    );
    like( $lo, qr/$qm/, "$_: quotemeta($lo) lo" );
    like( $hi, qr/$qm/, "$_: quotemeta($hi) hi" );
}
*/

function match($tag) {
    $re   = new Regexp_Assemble();
    $rela = new Regexp_Assemble(['lookahead' => 1]);
    
    $args = func_get_args();
    array_shift($args); //$tag‚ðÁ‚·.
    foreach($args as $_) {
    var_dump($_);
       $re->add($_);
       $rela->add($_);
    }

    $reind = clone $re;
    $reind = $reind->flags('x')->re(['indent' => 3]);
    $rered = clone $re;
    $rered->reduce(0);

    foreach($args as $str ) {
        like( $str, '/^'.$re.'$/',     "-- $tag: $str" );
        like( $str, '/^'.$rela.'$/',   "LA $tag: $str" );
        like( $str, '/^'.$reind.'$/x', "IN $tag: $str" );
        like( $str, '/^'.$rered.'$/',  "RD $tag: $str" );
    }
}

function match_list($tag, $patt, $test) {
    $re   = new Regexp_Assemble(); $re->add($patt);
    $rela = new Regexp_Assemble(); $rela->lookahead(1)->add($patt);

    foreach($test as $str) {
        like( $str , '/^'.$re.'$/', "re $tag: $str" );
        like( $str , '/^'.$rela.'$/', "rela $tag: $str" );
    }
}

{
    $r   = new Regexp_Assemble(['flags' => 'i']);
    $re = $r
        ->add( '^fg' )
        ->re();
    like( 'fgx', '/'.$re.'/', 'fgx/i' );
    like( 'Fgx', '/'.$re.'/', 'Fgx/i' );
    like( 'FGx', '/'.$re.'/', 'FGx/i' );
    like( 'fGx', '/'.$re.'/', 'fGx/i' );
    unlike( 'F', '/'.$re.'/', 'F/i' );
}

{
    $r   = new Regexp_Assemble(['flags' => 'x']);
    $re = $r
        ->add( '^fish' )
        ->add( '^flash' )
        ->add( '^fetish' )
        ->add( '^foolish' )
        ->re([ 'indent' => 2 ]);
    like( 'fish', '/'.$re.'/', 'fish/x' );
    like( 'flash', '/'.$re.'/', 'flash/x' );
    like( 'fetish', '/'.$re.'/', 'fetish/x' );
    like( 'foolish', '/'.$re.'/', 'foolish/x' );
    unlike( 'fetch', '/'.$re.'/', 'fetch/x' );
}

match_list( 'lookahead car.*',
    ['caret','caress','careful','careless','caring','carion','carry','carried'],
    ['caret','caress','careful','careless','caring','carion','carry','carried']
);

match_list( 'a.x', ['abx', 'adx', 'a.x' ] , ['aax', 'abx', 'acx', 'azx', 'a4x', 'a%x', 'a+x', 'a?x' ] );

/*
//•Û—¯
match_list( 'POSIX', ['X[0[:alpha:]%]','Y[1-4[:punct:]a-c]'] , ['X0','X%','Xa','Xf','Y1','Y;','Y!','yc'] );

match_list( 'c.z', ['c^z','c-z','c5z','cmz'] , ['c^z','c-z','c5z','cmz'] );
*/

match_list( '\d, \D', [ 'b\\d', 'b\\D' ] , ['b4','bX','b%','b.','b?'] );

match_list( 'abcd',
    ['abc','abcd','ac','acd','b','bc','bcd','bd'],
    ['abc','abcd','ac','acd','b','bc','bcd','bd']
);

match( 'foo', 'foo','bar','rat','quux');

match( '.[ar]it 1', 'bait','brit','frit','gait','grit','tait','wait','writ');

match( '.[ar]it 2', 'bait','brit','gait','grit');

match( '.[ar]it 3', 'bit','bait','brit','gait','grit');

match( '.[ar]it 4', 'barit','bait','brit','gait','grit');

match( 't.*ough', 'tough','though','trough','through','thorough');

match( 'g.*it', 'gait','git','grapefruit','grassquit','grit','guitguit');

match( 'show.*ess', 'showeriness','showerless','showiness','showless');

match( 'd*', 'den-at','dot-at','den-pt','dot-pt','dx');

match( 'd*', 'den-at','dot-at','den-pt','dot-pt','d-at','d-pt','dx');

match( 'un*ed', 'unimped','unimpeded','unimpelled');

match( '(un)?*(ing)?ing', 
    'sing','swing','sting','sling',
    'singing','swinging','stinging','slinging',
    'unsing','unswing','unsting','unsling',
    'unsinging','unswinging','unstinging','unslinging'
);

match( 's.*at 1', 'sat','sweat','sailbat');

match( 'm[eant]+', 'ma','mae','man','mana','manatee','mane','manent','manna','mannan','mant',
    'manta','mat','mate','matta','matte','me','mean','meant','meat','meet','meeten','men','met','meta',
    'metate','mete');

match( 'ti[aeinost]+', 'tiao','tie','tien','tin','tine','tinea','tinean','tineine',
    'tininess','tinnet','tinniness','tinosa','tinstone','tint','tinta','tintie','tintiness',
    'tintist','tisane','tit','titanate','titania','titanite','titano','tite','titi','titian',
    'titien','tittie');

/*
is( $_, $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
