<?php

require_once("Regexp_Assemble.php");
require_once("testutil.php");
{
    $r = new Regexp_Assemble();
    $r->add( 'a b' );

    is( $r->dump(),  "[a ' ' b]", 'dump path with space' );
    $r->insert( 'a', ' ', 'b', 'c', 'd' );
    is( $r->dump(), "[a ' ' b {* c=>[c d]}]",
        'dump path with space 2' );
}

/*
# 06_general.t
#
# Test suite for Regexp::Assemble
# Check out the general functionality, now that all the subsystems have been exercised
#
# copyright (C) 2004-2007 David Landgren

use strict;
use Regexp::Assemble;

eval qq{use Test::More tests => 142 };
if( $@ ) {
    warn "# Test::More not available, no tests performed\n";
    print "1..1\nok 1\n";
    exit 0;
}

use constant NR_GOOD  => 45;
use constant NR_BAD   => 529;
use constant NR_ERROR => 0;

my $fixed = 'The scalar remains the same';
$_ = $fixed;
*/

$ra = new Regexp_Assemble();
$ra->add( 'foo' ,'bar', 'rat' );

foreach(array('unfooled', 'disembark', 'vibration') as $target){
    like( $target, "/$ra/", "match ok $target" );
}

ok( ! $ra->source(), 'source() undefined' );

foreach(array('unfooled', 'disembark', 'vibration') as $target){
    unlike( $target, "/^$ra/", "anchored match not ok $target" );
}

$ra->reset();

foreach(array('unfooled', 'disembark', 'vibration') as $target){
    unlike( $target, "/$ra/", "fail after reset $target" );
}

$ra->add( 'who', 'what', 'where', 'why', 'when' );

foreach(array('unfooled', 'disembark', 'vibration') as $target){
    unlike( $target, "/$ra/", "fail ok $target" );
}

foreach(array('snowhouse', 'somewhat', 'nowhereness', 'whyever', 'nowhence') as $target){
    like( $target, "/$ra/", "new match ok $target" );
}

$ra->reset()->mutable(1);

unlike( 'nothing', "/$ra/", "match nothing after reset" );

$ra->add( '^foo\\d+' );

like( 'foo12', "/$ra/", "match 1 ok foo12" );
unlike( 'nfoo12', "/$ra/", "match 1 nok nfoo12" );
unlike( 'bar6', "/$ra/", "match 1 nok bar6" );

ok( ! $ra->mvar(), 'mvar() undefined' );

$ra->add( 'bar\\d+' );

like( 'foo12', "/$ra/", "match 2 ok foo12" );
unlike( 'nfoo12', "/$ra/", "match 2 nok nfoo12" );
like( 'bar6', "/$ra/", "match 2 ok bar6" );

$ra->reset()->filter( function($arr) {
   $r = [];
   foreach($arr as $_) {
      if ( preg_match("/\d /" , $_) ) {
         $r[] = $_;
      }
   }
   return ! count($r);
} );

$ra->add( '1 2 4' );
$ra->insert( '1', '2', '8*' );

unlike( '3 4 1 2', "/$ra/", 'filter nok 3 4 1 2' );
like( '3 1 2 4', "/$ra/", 'filter ok 3 1 2 4' );
unlike( '5 2 3 4', "/$ra/", 'filter ok 5 2 3 4' );

$ra->add( '2 3 a+' );
$ra->insert( '2', ' ', '3', ' ', 'a+' );

unlike( '5 2 3 4', "/$ra/", 'filter ok 5 2 3 4 (2)' );
//unlike( '5 2 3 aaa', "/$ra/", 'filter nok 5 2 3 a+' );

$ra->reset()->filter( NULL );

$ra->add( '1 2 a+' );
like( '5 1 2 aaaa', "/$ra/", 'filter now ok 5 1 2 a+' );

$ra->reset()->pre_filter( function ($p1){
   $r = [];
  if ( preg_match("/^#/" , $p1) ) {
     return false;
  }
  return true;
} );
$ra->add( '#de' );
$ra->add( 'abc' );

unlike( '#de', "/^$ra$/", '#de not matched by comment-filtered assembly' );
like(   'abc', "/^$ra$/", 'abc matched by comment-filtered assembly' );

/* PHP CLONE するから意味はない
SKIP: {
    skip( "is_deeply is broken in this version of Test::More (v$Test::More::VERSION)", 5 )
        unless $Test::More::VERSION > 0.47;

    {
        my $orig = Regexp::Assemble->new;
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone empty' );
    }

    {
        my $orig = Regexp::Assemble->new->add( qw/ dig dug dog / );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone path' );
    }

    {
        my $orig = Regexp::Assemble->new->add( qw/ dig dug dog / );
        my $clone = $orig->clone;
        $orig->add( 'digger' );
        $clone->add( 'digger' );
        is_deeply( $orig, $clone, 'clone then add' );
    }

    {
        my $orig = Regexp::Assemble->new
            ->add( qw/ bird cat dog elephant fox/ );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone node' );
    }

    {
        my $orig = Regexp::Assemble->new
            ->add( qw/ after alter amber cheer steer / );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone more' );
    }
}
SKIP: {
    # If the Storable module is available, we will have used
    # that above, however, we will not have tested the pure-Perl
    # fallback routines.
    skip( 'Pure-Perl clone() already tested', 5 )
        unless $Regexp::Assemble::have_Storable;

    skip( "is_deeply is broken in this version of Test::More (v$Test::More::VERSION)", 5 )
        unless $Test::More::VERSION > 0.47;

    local $Regexp::Assemble::have_Storable = 0;
    {
        my $orig = Regexp::Assemble->new;
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone empty' );
    }

    {
        my $orig = Regexp::Assemble->new->add( qw/ dig dug dog / );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone path' );
    }

    {
        my $orig = Regexp::Assemble->new->add( qw/ dig dug dog / );
        my $clone = $orig->clone;
        $orig->add( 'digger' );
        $clone->add( 'digger' );
        is_deeply( $orig, $clone, 'clone then add' );
    }

    {
        my $orig = Regexp::Assemble->new
            ->add( qw/ bird cat dog elephant fox/ );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone node' );
    }

    {
        my $orig = Regexp::Assemble->new
            ->add( qw/ after alter amber cheer steer / );
        my $clone = $orig->clone;
        is_deeply( $orig, $clone, 'clone more' );
    }
}
*/

{
    $r = new Regexp_Assemble();
    $r->add( 'dig', 'dug' );
    is( $r->dump(), '[d {i=>[i g] u=>[u g]}]', 'dump path' );
}

{
    $r = new Regexp_Assemble();
    $r->add( 'a b' );

    is( $r->dump(),  "[a ' ' b]", 'dump path with space' );
    $r->insert( 'a', ' ', 'b', 'c', 'd' );
    is( $r->dump(), "[a ' ' b {* c=>[c d]}]",
        'dump path with space 2' );
}

{
    $r = new Regexp_Assemble();
    $r->add( 'dog', 'cat' );
    is( $r->dump(),  '[{c=>[c a t] d=>[d o g]}]', 'dump node' );
}

{
    $r = new Regexp_Assemble();
    $r->add( 'house', 'home' );
    $r->insert();
    is( $r->dump(), '[{* h=>[h o {m=>[m e] u=>[u s e]}]}]',
        'add opt to path' );
}

{
    $r = new Regexp_Assemble();
    $r->add('dog', 'cat');
    $r->insert();
    is( $r->dump(), '[{* c=>[c a t] d=>[d o g]}]',
        'add opt to node' );
}

{
    $slide = new Regexp_Assemble();
    is( $slide->add( 'schoolkids', 'acids', 'acidoids' )->as_string(),
         '(?:ac(?:ido)?|schoolk)ids', 'schoolkids acids acidoids' );

    is( $slide->add( 'schoolkids', 'acidoids' )->as_string(),
         '(?:schoolk|acido)ids', 'schoolkids acidoids' );

    is( $slide->add( 'nonschoolkids', 'nonacidoids' )->as_string(),
         'non(?:schoolk|acido)ids', 'nonschoolkids nonacidoids' );
}

{
    $r = new Regexp_Assemble();
    is( $r
        ->add( 'sing', 'singing')
        ->as_string(),  'sing(?:ing)?', 'super slide sing singing' # no sliding done
    );

    $r = new Regexp_Assemble();
    is( $r
        ->add( 'sing', 'singing', 'sling')
        ->as_string(),  's(?:(?:ing)?|l)ing',
        'super slide sing singing sling'
    );

    $r = new Regexp_Assemble();
    is( $r
        ->add( 'sing', 'singing', 'sling', 'slinging')
        ->as_string(), 'sl?(?:ing)?ing',
        'super slide sing singing sling slinging'
    );

    $r = new Regexp_Assemble();
    is( $r
        ->add( 'sing', 'singing', 'sling', 'slinging', 'sting', 'stinging')
        ->as_string(), 's[lt]?(?:ing)?ing',
        'super slide sing singing sling slinging sting stinging'
    );

    $r = new Regexp_Assemble();
    is( $r
        ->add( 'sing','singing','sling','slinging','sting','stinging','string','stringing','swing','swinging' )
        ->as_string(), 's(?:[lw]|tr?)?(?:ing)?ing',
        'super slide sing singing sling slinging sting stinging string stringing swing swinging'
    );
}
{
    $re = new Regexp_Assemble([ 'flags' => 'i' ]);
    $re->add( '^ab', '^are', 'de' );
    like( 'able', "/$re/", '{^ab ^are de} /i matches able' );
    like( 'About', "/$re/", '{^ab ^are de} /i matches About' );
    unlike( 'bare', "/$re/", '{^ab ^are de} /i fails bare' );
    like( 'death', "/$re/", '{^ab ^are de} /i matches death' );
    like( 'DEEP', "/$re/", '{^ab ^are de} /i matches DEEP' );
}

{
    $re = new Regexp_Assemble();
    $re->add( 'abc', 'def', 'ghi' );

    is( $re->stats_add(),     3, "stats add 3x3" );
    is( $re->stats_raw(),     9, "stats raw 3x3" );
    is( $re->stats_cooked(),  9, "stats cooked 3x3" );
    ok( ! $re->stats_dup()    ,  "stats dup 3x3" );

    $re->add( 'de' );
    is( $re->stats_add(),      4, "stats add 3x3 +1" );
    is( $re->stats_raw(),     11, "stats raw 3x3 +1" );
    is( $re->stats_cooked(),  11, "stats cooked 3x3 +1" );
}

{
    $re = new Regexp_Assemble();
    $re->add( '\\Qabc.def.ghi\\E' );
    is( $re->stats_add(),     1, "stats add qm" );
    is( $re->stats_raw(),     15, "stats raw qm" );
    is( $re->stats_cooked(),  13, "stats cooked qm" );
    ok( ! $re->stats_dup() , "stats dup qm" );
}

{
    $re = new Regexp_Assemble();
    $re->add( 'abc\\,def', 'abc\\,def' );
    is( $re->stats_add(),      1, "stats add unqm dup" );
    is( $re->stats_raw(),     16, "stats raw unqm dup" );
    is( $re->stats_cooked(),   7, "stats cooked unqm dup" );
    is( $re->stats_dup(),      1, "stats dup unqm dup" );
    is( $re->stats_length(),     0, "stats_length unqm dup" );

    $str = $re->as_string();
    is( $str,  'abc,def', "stats str unqm dup" );
    is( $re->stats_length(),  7, "stats len unqm dup" );
}

{
    $re = new Regexp_Assemble();
    $re->add( '' );
    is( $re->stats_add(),  1, "stats add empty" );
    is( $re->stats_raw(),  0, "stats raw empty" );
    ok( ! $re->stats_cooked() , "stats cooked empty" );
    ok( ! $re->stats_dup() ,    "stats dup empty" );
}

{
    $re = new Regexp_Assemble();
    is( $re->stats_add(),     0, "stats_add empty" );
    is( $re->stats_raw(),     0, "stats_raw empty" );
    is( $re->stats_cooked(),  0, "stats_cooked empty" );
    is( $re->stats_dup(),     0, "stats_dup empty" );
    is( $re->stats_length(),  0, "stats_length empty" );

    $str = $re->as_string();
    is( $str,  Regexp_Assemble::Always_Fail, "stats str empty" ); # tricky!
    is( $re->stats_length(),  0, "stats len empty" );
}

{
    $re = new Regexp_Assemble();
    $re->add( '\\Q.+\\E', '\\Q.+\\E', '\\Q.*\\E' );
    
    is( $re->stats_add(),      2, "stats_add 2" );
    is( $re->stats_raw(),     18, "stats_raw 2" );
    is( $re->stats_cooked(),   8, "stats_cooked 2" );
    is( $re->stats_dup(),      1, "stats_dup 2" );
    is( $re->stats_length(),   0, "stats_length 2" );

    $str = $re->as_string();
    is( $str,  '\\.[*+]', "stats str 2" );
    is( $re->stats_length(),  6, "stats len 2 <$str>" );
}


{
    # CPAN bug #24171
    # given a list of strings
    $str = [ 'a b', 'awb', 'a1b', 'bar', "a\nb" ];

    foreach( array('s', 'w', 'd') as $meta) {
        # given a list of patterns
        $re_list = [ "a\\{$meta}b", "a\\@{[uc{$meta}]}b" ];

        # produce an assembled pattern
        $r = new Regexp_Assemble();
        $re = $r->add($re_list)->re();

        $r2 = new Regexp_Assemble();
        $re_fold = $r2->fold_meta_pairs(0)->add($re_list)->re();

        # test it against the strings
        foreach($str as $s) {
            # any match?
            $ok = 0;
            foreach($re_list as $_) {
                if ( preg_match("/$_/" , $s) ) {
                    $ok = 1;
                }
            }

            # does the assemble regexp match as well?
            $ptr = $s;
//            $ptr =~ s/\\/\\\\/;
            $ptr = preg_replace('/\\\\/' , '/\\\\\\\\/' , $ptr);
//            $ptr =~ s/\n/\\n/;
            $ptr = preg_replace('/\n/' , '/\\\\n/' , $ptr);

//            my $bug_success = ($s =~ /\n/) ? 0 : 1;
            $bug_success = (preg_match("/\n/" , $s)) ? 0 : 1;
            $bug_fail    = 1 - $bug_success;


////保留
//            is( preg_match("/$re/" , $s ) ? $bug_success : $bug_fail, $ok,
//                "Folded meta pairs behave as list for \\$meta ($ptr,ok=$ok/$bug_success/$bug_fail)"
//	            );

            is( ( preg_match("/$re_fold/",$s)) ? 1 : 0, $ok,
                "Unfolded meta pairs behave as list for \\$meta ($ptr,ok=$ok)"
            );

        }
    }
}

{
    $u = new Regexp_Assemble(['unroll_plus' => 1]);

    $u->add( "a+b", 'ac' );
    $str = $u->as_string();
    is( $str, 'a(?:a*b|c)', 'unroll plus a+b ac' );

    $u->add( "\\LA+B", "ac" );
    $str = $u->as_string();
    is( $str, 'a(?:a*b|c)', 'unroll plus \\LA+B ac' );

    $u->add( "\\Ua+?b", "AC" );
    $str = $u->as_string();
    is( $str, 'A(?:A*?B|C)', 'unroll plus \\Ua+?b AC' );

    $u->add( '\\d+d', '\\de', '\\w+?x', '\\wy');
    $str = $u->as_string();
    is( $str, '(?:\\w(?:\\w*?x|y)|\\d(?:\d*d|e))', 'unroll plus \\d and \\w' );
//
//PHPではUTF-8以外のマルチバイトは扱えません。
//    $u->add( '\\xab+f', '\\xabg', '\\xcd+?h', '\\xcdi');
//    $str = $u->as_string();
//    is( $str, "(?:\xcd(?:\xcd*?h|i)|\xab(?:\xab*f|g))", 'unroll plus meta x' );

    $u->add( '[a-e]+h', '[a-e]i', '[f-j]+?k', '[f-j]m');
    $str = $u->as_string();
    is( $str, "(?:[f-j](?:[f-j]*?k|m)|[a-e](?:[a-e]*h|i))", 'unroll plus class' );

    $u->add( "a+b" );
    $str = $u->as_string();
    is( $str, "a+b", 'reroll a+b' );

    $u->add( "a+b", "a+" );
    $str = $u->as_string();
    is( $str, "a+b?", 'reroll a+b?' );

    $u->add( "a+?b", "a+?" );
    $str = $u->as_string();
    is( $str, "a+?b?", 'reroll a+?b?' );

    $u->unroll_plus(0)->add( '1+2', '13' );
    $str = $u->as_string();
    is( $str, "(?:1+2|13)", 'no unrolling' );

    $u->unroll_plus()->add( '1+2', '13' );
    $str = $u->as_string();
    is( $str, "1(?:1*2|3)", 'unrolling again via implicit' );

    $u->add('d+ldrt', 'd+ndrt', 'd+ldt', 'd+ndt', 'd+x');
    $str = $u->as_string();
    is( $str, 'd+(?:[ln]dr?t|x)', 'visit ARRAY codepath' );
}
/*
is( $_, 'eq', $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
