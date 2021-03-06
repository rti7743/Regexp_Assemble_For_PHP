<?php
require_once("Regexp_Assemble.php");
require_once("testutil.php");




$xism = '-xism:';

foreach (
[
    [ "(?{$xism}\\b(?:c[de]|ab)\\b)", 'ab', 'cd', 'ce', ['anchor_word' => 1] ],
] as $test) {
    $result = array_shift( $test );

    $param = is_array( $test[ count($test) - 1 ]  ) ? array_pop( $test ) : [];

    $r = new Regexp_Assemble($param);
    $r->add($test);
//    if (! isset($param['flags']) ) {
//        $r->__flags = 'xism';
//    }

    $args = '(' . join( ') (', $test ) . ')';
    if ( count( $param ) ) {
        
        $args .= '{';
        foreach($param as $key => $value) {
            $args .= "key => $value ,";
        }
        $args .= '}';
    }
    is( $r->re() , $result, "add $args");
}


/*
# 03_str.t
#
# Test suite for Regexp::Assemble
# Ensure the the generated patterns seem reasonable.
#
# copyright (C) 2004-2011 David Landgren

use strict;

eval qq{use Test::More tests => 210};
if( $@ ) {
    warn "# Test::More not available, no tests performed\n";
    print "1..1\nok 1\n";
    exit 0;
}

use Regexp::Assemble;

my $fixed = 'The scalar remains the same';
$_ = $fixed;

is( Regexp::Assemble->new->as_string(), $Regexp::Assemble::Always_Fail, 'empty' );
*/

foreach( array(
    [ '(?:)?',       [''] ],
    [ 'd',           ['d'] ],
    [ 'dot',         ['d', 'o', 't'] ],
    [ '[dot]',       ['d'], ['o'], ['t'] ],
    [ 'd?',          ['d'], [''] ],
    [ 'da',          ['d', 'a'] ],
    [ 'da?',         ['d', 'a'], ['d'] ],
    [ '(?:da)?',     ['d', 'a'], [''] ],
    [ '[ad]?',       ['d'], [''], ['a'] ],
    [ '(?:do|a)?',   ['d', 'o'], [''], ['a'] ],
    [ '.',           ['x'], ['.'] ],
    [ '.',           ['\033'], ['.'] ],
    [ '.',           ['\\d'], ['\\s'], ['.'] ],
    [ '.',           ['\\d'], ['\\D'] ],
    [ '.',           ['\\s'], ['\\S'] ],
    [ '.',           ['\\w'], ['\\W'] ],
    [ '.',           ['\\w'], ['\\W'], ["\t"] ],
    [ '\\d',         ['\\d'], ['5'] ],
    [ '\\d',         ['\\d'], [5], [7], [0] ],

    [ '\\d?',        ['\\d'], ['5'], [''] ],
    [ '\\s',         ['\\s'], [' '] ],
    [ '\\s?',        ['\\s'], [''] ],
    [ '[\\dx]',      ['\\d'], [5], [7], [0], ['x'] ],
    [ '[\\d\\s]',    ['\\d'], ['\\s'], [5], [7], [0], [' '] ],
    [ '[.p]',        ['\\.'], ['p'] ],
    [ '\\w',         ['\\w'], [5], [1], [0], ['a'], ['_'] ],
    [ '[*\\d]?',     ['\\d'], [''], ['\\*'] ],
    [ '[\\d^]?',     ['\\d'], [''], ['\\^'] ],
    [ 'a[?@]z',      ['a', '@', 'z'], ['a', "\?", 'z'] ],
    [ '\\+',         ['\\+'] ],
    [ '\\+',         [quotemeta('+')] ],
    [ '[*+]',        ['\\+'], ['\\*'] ],
    [ '[*+]',        [quotemeta('+')], [quotemeta('*')] ],
    [ '[-0z]',       ['-'], ['0'], ['z'] ],
    [ '[-.z]',       ['-'], ['\\.'], ['z'] ],
    [ '[-*+]',       ['\\+'], ['-'], ['\\*'] ],
    [ '[-.]',        ['\\.'], ['-'] ],
    [ '(?:[0z]|^)',  ['^'], ['0'], ['z'] ],
    [ '(?:[-0z]|^)', ['^'], ['0'], ['-'], ['z'] ],
    [ '(?:[-\\w]|^)', ['^'], ['0'], ['-'], ['z'], ['\\w'] ],
    [ '(?:[-0]|$)',   ['$'], ['0'], ['-'] ],
    [ '(?:[-0]|$|^)', ['$'], ['0'], ['-'], ['^'] ],
    [ '\\d',          [0], [1], [2], [3], [4], [5], [6], [7], [8], [9] ],
    [ '[\\dx]',       [0], [1], [2], [3], [4], [5], [6], [7], [8], [9], ['x'] ],

    [ '(?:b[ey])?',   ['b', 'e'], [''], ['b', 'y'] ],
    [ '(?:be|do)?',   ['b', 'e'], [''], ['d', 'o'] ],
    [ '(?:b[ey]|a)?', ['b', 'e'], [''], ['b', 'y'], ['a'] ],
    [ 'da[by]',       ['d', 'a', 'b'] , ['d', 'a', 'y'] ],
    [ 'da(?:ily|b)',  ['d', 'a', 'b'], ['d', 'a', 'i', 'l', 'y'] ],
    [ '(?:night|day)',    ['n', 'i', 'g', 'h', 't'], ['d', 'a', 'y'] ],
    [ 'da(?:(?:il)?y|b)', ['d', 'a', 'b'], ['d', 'a', 'y'], ['d', 'a', 'i', 'l', 'y'] ],
    [ 'dab(?:ble)?',      ['d', 'a', 'b'], ['d', 'a', 'b', 'b', 'l', 'e'] ],
    [ 'd(?:o(?:ne?)?)?',      ['d'], ['d', 'o'], ['d', 'o', 'n'], ['d', 'o', 'n', 'e'] ],
    [ '(?:d(?:o(?:ne?)?)?)?', ['d'], ['d', 'o'], ['d', 'o', 'n'], ['d', 'o', 'n', 'e'], [''] ],
    [ 'd(?:o[begnt]|u[bd])',
        ['d', 'o', 'b'], ['d', 'o', 'e'], ['d', 'o', 'g'], ['d', 'o', 'n'], ['d', 'o', 't'], ['d', 'u', 'b'], ['d', 'u', 'd'] ],
    [ 'da(?:m[ep]|r[kt])',
        ['d', 'a', 'm', 'p'], ['d', 'a', 'm', 'e'], ['d', 'a', 'r', 't'], ['d', 'a', 'r', 'k'] ],
) as $test) {
    $result = array_shift($test);
    $r = new Regexp_Assemble();
    foreach( $test as $_) {
        $r->insert($_);
    }

    $_temp_array = [];
    foreach( $test as $_) {
        $_temp_array[] = join('',$_);
    }
    $args = join( ') (', $_temp_array );

    is($r->as_string() , $result, "insert ($args)");
}


//my {$xism} = ($] < 5.013) ? '-xism' : '^:';
//$xism = '-xism:';

foreach (
[
    [ "(?{$xism}(?:^|m)a)",    '^a', 'ma' ],
    [ "(?{$xism}(?:[mw]|^)a)", '^a', 'ma', 'wa' ],
    [ "(?{$xism}(?:^|\\^)a)",  '^a', '\\^a' ],
    [ "(?{$xism}(?:^|0)a)",    '^a', '0a' ],
    [ "(?{$xism}(?:[m^]|^)a)", '^a', 'ma', '\\^a' ],
    [ "(?{$xism}(?:ma|^)a)",   '^a', 'maa' ],
    [ "(?{$xism}a.+)",         'a.+' ],
    [ "(?{$xism}b?)",          '[b]?' ],
    [ "(?{$xism}\\.)",         '[.]' ],
    [ "(?{$xism}\\.+)",        '[.]+' ],
    [ "(?{$xism}\\.+)"  ,      '[\\.]+' ],
    [ "(?{$xism}\\^+)",        '[\\^]+' ],
    [ "(?{$xism}%)",           '[%]' ],
    [ "(?{$xism}%)",           '[\\%]' ],
    [ "(?{$xism}!)",           '[!]' ],
    [ "(?{$xism}!)",           '[\\!]' ],
    [ "(?{$xism}@)",           '[@]' ],
    [ "(?{$xism}@)",           '[\\@]' ],
    [ "(?{$xism}a|[bc])",      'a|[bc]' ],
    [ "(?{$xism}ad?|[bc])",    'ad?|[bc]' ],
    [ "(?{$xism}"."b(?:$|e))",    'b$','be' ],
    [ "(?{$xism}"."b(?:[ae]|$))", 'b$','be', 'ba' ],
    [ "(?{$xism}"."b(?:$|\\$))",  'b$','b\\$' ],
    [ "(?{$xism}(?:^a[bc]|de))",  '^ab','^ac', 'de' ],
    [ "(?i:/)",              '/',          ['flags' => 'i'] ],
    [ "(?i:(?:^a[bc]|de))",  '^ab', '^ac', 'de', ['flags' => 'i'] ],
    [ "(?im:(?:^a[bc]|de))", '^ab', '^ac', 'de', ['flags' => 'im'] ],
    [ "(?{$xism}a(?:%[de]|=[bc]))",
        quotemeta('a%d'), quotemeta('a=b'), quotemeta('a%e'), quotemeta('a=c') ],
    [ "(?{$xism}\\^[,:])",     quotemeta('^:'), quotemeta('^,') ],
    [ "(?{$xism}a[-*=])",      quotemeta('a='), quotemeta('a*'), quotemeta('a-') ],
    [ "(?{$xism}l(?:im)?it)",  'lit', 'limit' ],
    [ "(?{$xism}a(?:(?:g[qr]|h)w|[de]n|m)z)", 'amz', 'adnz', 'aenz', 'agrwz', 'agqwz', 'ahwz' ],
    [ "(?{$xism}a(?:(?:e(?:[gh]u|ft)|dkt|f)w|(?:(?:ij|g)m|hn)v)z)",
        'adktwz', 'aeftwz', 'aeguwz', 'aehuwz', 'afwz', 'agmvz', 'ahnvz','aijmvz' ],
    [ "(?{$xism}b(?:d(?:kt?|i)|ckt?)x)", 'bcktx', 'bckx', 'bdix', 'bdktx', 'bdkx' ],
    [ "(?{$xism}d(?:[ln]dr?t|x))",  'dldrt', 'dndrt', 'dldt', 'dndt', 'dx' ],
    [ "(?{$xism}d(?:[ln][dp]t|x))", 'dldt', 'dndt', 'dlpt', 'dnpt', 'dx' ],
    [ "(?{$xism}d(?:[ln][dp][mr]t|x))", 'dldrt', 'dndrt', 'dldmt', 'dndmt', 'dlprt', 'dnprt', 'dlpmt', 'dnpmt', 'dx' ],
    [ "(?{$xism}"."(?:\(scan|\*mens|\[mail))", '\\*mens', '\\(scan', '\\[mail'],
    [ "(?{$xism}a\\[b\\[c)", '\\Qa[b[c' ],
    [ "(?{$xism}a\\]b\\]c)", '\\Qa]b]c' ],
    [ "(?{$xism}a\\(b\\(c)", '\\Qa(b(c' ],
    [ "(?{$xism}a\\)b\\)c)", '\\Qa)b)c' ],
    [ "(?{$xism}a[(+[]b)", '\\Qa(b', '\\Qa[b', '\\Qa+b' ],
    [ "(?{$xism}a[-+^]b)", '\\Qa^b', '\\Qa-b', '\\Qa+b' ],
    [ "(?{$xism}car(?:rot)?)", 'car', 'carrot', ['lookahead' => 1] ],
    [ "(?{$xism}car[dpt]?)",   'car', 'cart', 'card', 'carp', ['lookahead' => 1] ],
    [ "(?{$xism}[bc]a[nr]e)",  'bane', 'bare', 'cane', 'care', ['lookahead' => 1] ],
    [ "(?{$xism}(?=[ru])(?:ref)?use)",       'refuse', 'use', ['lookahead' => 1] ],
    [ "(?{$xism}(?=[bcd])(?:bird|cat|dog))", 'bird', 'cat', 'dog', ['lookahead' => 1] ],
    [ "(?{$xism}sea(?=[hs])(?:horse|son))",  'seahorse', 'season', ['lookahead' => 1] ],
    [ "(?{$xism}car(?:(?=[dr])(?:rot|d))?)", 'car', 'card', 'carrot', ['lookahead' => 1] ],
    [ "(?{$xism}(?:(?:[hl]o|s?t|ch)o|[bf]a)ked)",
        'looked', 'choked', 'hooked', 'stoked', 'toked', 'baked', 'faked' ],
    [ "(?{$xism}(?=[frt])(?:trans|re|f)action)",
        'faction', 'reaction', 'transaction', ['lookahead' => 1] ],
    [ "(?{$xism}c(?=[ao])(?:or(?=[np])(?:pse|n)|ar(?=[de])(?:et|d)))",
        'card', 'caret', 'corn', 'corpse', ['lookahead' => 1] ],
    [ "(?{$xism}car(?:(?=[dipt])(?:[dpt]|i(?=[no])(?:ng|on)))?)",
        'car', 'cart', 'card', 'carp', 'carion', 'caring', ['lookahead' => 1] ],
    [ "(?{$xism}(?=[dfrst])(?:(?=[frt])(?:trans|re|f)a|(?=[ds])(?:dir|s)e)ction)",
        'faction', 'reaction', 'transaction', 'direction', 'section', ['lookahead' => 1] ],
    [ "(?{$xism}car(?=[eir])(?:e(?=[flst])(?:(?=[ls])(?:le)?ss|ful|t)|i(?=[no])(?:ng|on)|r(?=[iy])(?:ied|y)))",
        'caret', 'caress', 'careful', 'careless', 'caring', 'carion', 'carry', 'carried', ['lookahead' => 1] ],
    [ "(?{$xism}(?=[uv])(?:u(?=[nr])(?:n(?=[iprs])(?:(?=[ip])(?:(?:p[or]|impr))?i|(?:sea)?|rea)|r)|v(?=[ei])(?:en(?=[it])(?:trime|i)|i))son)",
        'unimprison', 'unison', 'unpoison', 'unprison', 'unreason', 'unseason', 'unson', 'urson', 'venison', 'ventrimeson', 'vison', ['lookahead' => 1] ],
    [ "(?{$xism}(?:a?bc?)?d)",         'abcd', 'abd', 'bcd', 'bd', 'd' ],
    [ "(?{$xism}(?:a?bc?|c)d)",        'abcd', 'abd', 'bcd', 'bd', 'cd' ],
    [ "(?{$xism}(?:(?:a?bc?)?d|c))",   'abcd', 'abd', 'bcd', 'bd', 'c', 'd' ],
    [ "(?{$xism}(?:(?:a?bc?)?d|cd?))", 'abcd', 'abd', 'bcd', 'bd', 'c', 'cd', 'd' ],
    [ "(?{$xism}(?:(?:ab?|b)c?)?d)",   'abcd', 'abd', 'acd', 'ad', 'bcd', 'bd', 'd' ],
    [ "(?{$xism}(?:(?:ab)?cd?)?e)",          'abcde', 'abce', 'cde', 'ce', 'e' ],
    [ "(?{$xism}(?:(?:(?:ab?|b)c?)?d|c))",   'abcd', 'abd', 'acd', 'ad', 'bcd', 'bd', 'c', 'd' ],
    [ "(?{$xism}(?:(?:(?:ab?|b)c?)?d|cd?))", 'abcd', 'abd', 'acd', 'ad', 'bcd', 'bd', 'c', 'cd', 'd' ],
    [ "(?{$xism}"."^(?:b?cd?|ab)$)",          '^ab$', '^bc$', '^bcd$', '^c$', '^cd$'],
    [ "(?{$xism}"."^(?:(?:ab?c|cd?)e?|e)$)",  '^abc$', '^abce$', '^ac$', '^ace$', '^c$', '^cd$', '^cde$', '^ce$', '^e$' ],
    [ "(?{$xism}"."^(?:abc|bcd)e?$)",         '^abc$', '^abce$', '^bcd$', '^bcde$' ],
    [ "(?{$xism}"."^(?:abcdef|bcdefg)h?$)",   '^abcdef$', '^abcdefh$', '^bcdefg$', '^bcdefgh$' ],
    [ "(?{$xism}"."^(?:bcdefg|abcd)h?$)",     '^abcd$', '^abcdh$', '^bcdefg$', '^bcdefgh$' ],
    [ "(?{$xism}"."^(?:abcdef|bcd)h?$)",      '^abcdef$', '^abcdefh$', '^bcd$', '^bcdh$' ],
    [ "(?{$xism}"."^(?:a(?:bcd|cd?)e?|e)$)",  '^abcd$', '^abcde$', '^ac$', '^acd$', '^acde$', '^ace$', '^e$' ],
    [ "(?{$xism}"."^(?:bcd|cd?)e?$)",         '^bcd$', '^bcde$', '^c$', '^cd$', '^cde$', '^ce$' ],
    [ "(?{$xism}"."^(?:abc|bc?)(?:de)?$)",    '^abc$', '^abcde$', '^b$', '^bc$', '^bcde$', '^bde$' ],
    [ "(?{$xism}"."^(?:b(?:cd)?|abd)e?$)",    '^abd$', '^abde$', '^b$', '^bcd$', '^bcde$', '^be$' ],
    [ "(?{$xism}"."^(?:ad?|bcd)e?$)",         '^a$', '^ad$', '^ade$', '^ae$', '^bcd$', '^bcde$' ],
    [ "(?{$xism}"."^(?:a(?:bcd|cd?)e?|de)$)", '^abcd$', '^abcde$', '^ac$', '^acd$', '^acde$', '^ace$', '^de$' ],
    [ "(?{$xism}"."^(?:a(?:bcde)?|bc?d?e)$)", '^a$', '^abcde$', '^bcde$', '^bce$', '^bde$', '^be$' ],
    [ "(?{$xism}"."^(?:a(?:b[cd]?)?|bd?e?f)$)", '^a$', '^ab$', '^abc$', '^abd$', '^bdef$', '^bdf$', '^bef$', '^bf$' ],
    [ "(?{$xism}"."^(?:a(?:bc?|dd)?|bd?e?f)$)", '^a$', '^ab$', '^abc$', '^add$', '^bdef$', '^bdf$', '^bef$', '^bf$' ],
    [ "(?{$xism}"."^(?:a(?:bc?|de)?|bc?d?f)$)", '^a$', '^ab$', '^abc$', '^ade$', '^bcdf$', '^bcf$', '^bdf$', '^bf$' ],
    [ "(?{$xism}"."^(?:a(?:bc?|de)?|cd?e?f)$)", '^a$', '^ab$', '^abc$', '^ade$', '^cdef$', '^cdf$', '^cef$', '^cf$' ],
    [ "(?{$xism}"."^(?:a(?:bc?|e)?|bc?de?f)$)", '^a$', '^ab$', '^abc$', '^ae$', '^bcdef$', '^bcdf$', '^bdef$', '^bdf$' ],
    [ "(?{$xism}"."^(?:a(?:bc?|e)?|b(?:cd)?e?f)$)", '^a$', '^ab$', '^abc$', '^ae$', '^bcdef$', '^bcdf$', '^bef$', '^bf$' ],
    [ "(?{$xism}"."^(?:b(?:cde?|d?e)f|a(?:bc?|e)?)$)",
        '^a$', '^ab$', '^abc$', '^ae$', '^bcdef$', '^bcdf$', '^bdef$', '^bef$' ],
    [ "(?{$xism}\\b(?:c[de]|ab)\\b)", 'ab', 'cd', 'ce', ['anchor_word' => 1] ],
    [ "(?{$xism}\\b(?:c[de]|ab))",    'ab', 'cd', 'ce', ['anchor_word_begin' => 1] ],
    [ "(?{$xism}"."^(?:c[de]|ab)$)",     'ab', 'cd', 'ce', ['anchor_line' => 1] ],
    [ "(?{$xism}(?:c[de]|ab))",       'ab', 'cd', 'ce', ['anchor_line' => 0] ],
    [ "(?{$xism}"."(?:c[de]|ab)$)",      'ab', 'cd', 'ce', ['anchor_line_end' => 1] ],
    [ "(?{$xism}\\A(?:c[de]|ab)\\Z)", 'ab', 'cd', 'ce', ['anchor_string' => 1] ],
    [ "(?{$xism}(?:c[de]|ab))",       'ab', 'cd', 'ce', ['anchor_string' => 0] ],
    [ "(?{$xism}x[[:punct:]][yz])",   'x[[:punct:]]y', 'x[[:punct:]]z' ],
] as $test) {
    $result = array_shift( $test );

    $param = is_array( $test[ count($test) - 1 ]  ) ? array_pop( $test ) : [];

    $r = new Regexp_Assemble($param);
    $r->add($test);
    if (! isset($param['flags']) ) {
//        $r->__flags = '-xism';
    }

    $args = '(' . join( ') (', $test ) . ')';
    if ( count( $param ) ) {
        
        $args .= '{';
        foreach($param as $key => $value) {
            $args .= "$key => $value ,";
        }
        $args .= '}';
    }
    is( $r->re() , $result, "add $args");
}


{
    $r = new Regexp_Assemble();
    $r->__flags = '-xism';
    $r->add( 'de' );
    $re = $r->re();
    is( "$re", "(?{$xism}de)", 'de' );
    $re2 = $r->re();
    is( "$re2", "(?{$xism}de)", 'de again' );
}

$r = new Regexp_Assemble(['lookahead' => 1]);
is( $r->add( 
    'car', 'cart', 'card', 'carp', 'carion'
    )->as_string(),
    'car(?:(?=[dipt])(?:[dpt]|ion))?', 'lookahead car carp cart card carion' );

$r = new Regexp_Assemble(['anchor_word' => 1]);
is( $r
    ->add('ab', 'cd', 'ce')
    ->as_string(), '\\b(?:c[de]|ab)\\b', 'implicit anchor word via method' );

$r = new Regexp_Assemble(['anchor_word_end' => 1]);
is( $r
    ->add('ab' ,'cd', 'ce')
    ->as_string(), '(?:c[de]|ab)\\b', 'implicit anchor word end via method' );

$r = new Regexp_Assemble(['anchor_word' => 0]);
is( $r
    ->add('ab', 'cd', 'ce')
    ->as_string(), '(?:c[de]|ab)', 'no implicit anchor word' );

$r = new Regexp_Assemble(['anchor_word' => 1]);
is( $r->anchor_word_end(0)
    ->add('ab', 'cd', 'ce')
    ->as_string(), '\\b(?:c[de]|ab)', 'implicit anchor word, no anchor word end' );

$r = new Regexp_Assemble();
is( $r->anchor_word_begin(1)
    ->add('ab', 'cd', 'ce')
    ->as_string(), '\\b(?:c[de]|ab)', 'implicit anchor word begin' );

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_line()
    ->as_string(), '^(?:c[de]|ab)$', 'implicit anchor line via new' );


$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_line_begin()
    ->as_string(), '^(?:c[de]|ab)', 'implicit anchor line via method' );

$r = new Regexp_Assemble();
is( $r->anchor_line_begin()->anchor_line(0)
    ->add('ab', 'cd', 'ce')
    ->as_string(), '(?:c[de]|ab)', 'no implicit anchor line via method' );

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_string()
    ->as_string(), '\\A(?:c[de]|ab)\\Z', 'implicit anchor string via method' );

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_string_absolute()
    ->as_string(), '\\A(?:c[de]|ab)\\z', 'implicit anchor string absolute via method' );

$r = new Regexp_Assemble(['anchor_string_absolute' => 1]);
is( $r
    ->add('de', 'df', 'fe')
    ->as_string(), '\\A(?:d[ef]|fe)\\z', 'implicit anchor string absolute via new' );

$r = new Regexp_Assemble(['anchor_string_absolute' => 1 , 'anchor_string_begin' => 0 ]);
is( $r
    ->add('de', 'df')
    ->as_string(), 'd[ef]\\z', 'anchor string absolute and no anchor_string_begin via new' );

$r = new Regexp_Assemble(['anchor_word' => 1 , 'anchor_word_end' => 0 ]);
is( $r
    ->add('ze', 'zf', 'zg')
    ->as_string(), '\bz[efg]', 'anchor word and no anchor_word_begin via new' );

$r = new Regexp_Assemble(['anchor_string_absolute' => 0 ]);
is( $r
    ->add('de', 'df', 'fe')
    ->as_string(), '(?:d[ef]|fe)', 'no implicit anchor string absolute via new' );

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_word_begin()
    ->anchor_string_end_absolute()
    ->as_string(), '\\b(?:c[de]|ab)\\z',
        'implicit anchor word begin/string absolute end via method'
);

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'ad')
    ->anchor_string(1)
    ->anchor_string_end(0)
    ->as_string(), '\\Aa[bd]',
        'explicit anchor string/no end via method'
);

$r = new Regexp_Assemble();
is( $r
    ->anchor_string_end()
    ->add('ab', 'ad')
    ->as_string(), 'a[bd]\\Z',
        'anchor string end via method'
);

$r = new Regexp_Assemble();
is( $r
    ->anchor_string_absolute(1)
    ->add('ab', 'ad')
    ->as_string(), '\\Aa[bd]\\z',
        'anchor string end via method'
);

$r = new Regexp_Assemble(['anchor_word_begin' => 1 , 'anchor_string_end_absolute' => 1 ]);
is( $r
    ->add('de', 'ad', 'be', 'ef')
    ->as_string(), '\\b(?:[bd]e|ad|ef)\\z',
        'implicit anchor word begin/string absolute end via new'
);

$r = new Regexp_Assemble();
is( $r
    ->add('ab', 'cd', 'ce')
    ->anchor_word_begin()
    ->anchor_string_begin()
    ->as_string(), '\\b(?:c[de]|ab)',
        'implicit anchor word beats string'
);

/*
TODO: {
//    use vars '$TODO';
//    local $TODO = "\\d+ does not absorb digits";

    $r = new Regexp_Assemble();
    is( $r->add( '5', '\\d+' )->as_string(),
        '\\d+', '\\d+ absorbs single char'
    );

    $r = new Regexp_Assemble();
    is( $r->add( '54321', '\\d+' )->as_string(),
        '\\d+', '\\d+ absorbs multiple chars'
    );

    $r = new Regexp_Assemble();
    is( $r
        ->add( 'abz', 'acdez', 'a5txz', 'a7z', 'a\\d+z', 'a\\d+-\\d+z' ) # 5.6.0 kluge
        ->as_string(), 'a(?:b|(?:\d+-)?\d+|5tx|cde)z',
        'abz a\\d+z acdez a\\d+-\\d+z a5txz a7z'
    );
}
*/

$r = new Regexp_Assemble();
$mute = $r->mutable(1);

$mute->add( 'dog' );
is( $mute->as_string(), 'dog', 'mute dog' );
is( $mute->as_string(), 'dog', 'mute dog cached' );

$mute->add( 'dig' );
is( $mute->as_string(), 'd(?:ig|og)', 'mute dog' );

$r = new Regexp_Assemble();
$red = $r->reduce(0);

$red->add( 'dog' );
$red->add( 'dig' );
is( $red->as_string(), 'd(?:ig|og)', 'mute dig dog' );

$red->add( 'dog' );
is( $red->as_string(), 'dog', 'mute dog 2' );

$red->add( 'dig' );
is( $red->as_string(), 'dig', 'mute dig 2' );

$r = new Regexp_Assemble();
is( $r->add('ab', 'cd')->as_string(['indent' => 0]),
    '(?:ab|cd)', 'indent 0'
);

$r = new Regexp_Assemble();
is( $r
    ->add( 'dldrt', 'dndrt', 'dldt', 'dndt', 'dx' )
    ->as_string(['indent' => 3]),
'd
(?:
   [ln]dr?t
   |x
)'
,  'dldrt dndrt dldt dndt dx (indent 3)' );

$r = new Regexp_Assemble(['indent' => 2]);
is( $r
    ->add( 'foo', 'bar' )
    ->as_string(),
'(?:
  bar
  |foo
)'
, 'pretty foo bar' );

$r = new Regexp_Assemble();
is( $r
    ->indent(2)
    ->add( 'food', 'fool', 'bar' )
    ->as_string(),
'(?:
  foo[dl]
  |bar
)'
, 'pretty food fool bar' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'afood', 'afool', 'abar' )
    ->indent(2)
    ->as_string(),
'a
(?:
  foo[dl]
  |bar
)'
, 'pretty afood afool abar' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'dab', 'dam', 'day' )
    ->as_string(['indent' => 2]),
'da[bmy]', 'pretty dab dam day' );

$r = new Regexp_Assemble(['indent' => 5]);
is( $r
    ->add( 'be', 'bed' )
    ->as_string(['indent' => 2]),
'bed?'
, 'pretty be bed' );

$r = new Regexp_Assemble(['indent' => 5]);
is( $r
    ->add( 'b-d', 'b\.d' )
    ->as_string(['indent' => 2]),
'b[-.]d'
, 'pretty b-d b\.d' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'be', 'bed', 'beg', 'bet' )
    ->as_string(['indent' => 2]),
'be[dgt]?'
, 'pretty be bed beg bet' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'afoodle', 'afoole', 'abarle' )
    ->as_string(['indent' => 2]),
'a
(?:
  food?
  |bar
)
le'
, 'pretty afoodle afoole abarle' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'afar', 'afoul', 'abate', 'aback' )
    ->as_string(['indent' => 2]),
'a
(?:
  ba
  (?:
    ck
    |te
  )
  |f
  (?:
    oul
    |ar
  )
)'
, 'pretty pretty afar afoul abate aback' );


$r = new Regexp_Assemble();
is( $r
    ->add( 'stormboy', 'steamboy', 'saltboy', 'sockboy' )
    ->as_string(['indent' => 5]),
's
(?:
     t
     (?:
          ea
          |or
     )
     m
     |alt
     |ock
)
boy'
, 'pretty stormboy steamboy saltboy sockboy' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'stormboy', 'steamboy', 'stormyboy', 'steamyboy', 'saltboy', 'sockboy' )
    ->as_string(['indent' => 4]),
's
(?:
    t
    (?:
        ea
        |or
    )
    my?
    |alt
    |ock
)
boy'
, 'pretty stormboy steamboy stormyboy steamyboy saltboy sockboy' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'stormboy', 'steamboy', 'stormyboy', 'steamyboy', 'stormierboy', 'steamierboy', 'saltboy' )
    ->as_string(['indent' => 1]),
's
(?:
 t
 (?:
  ea
  |or
 )
 m
 (?:
  ier
  |y
 )
 ?
 |alt
)
boy'
, 'pretty stormboy steamboy stormyboy steamyboy stormierboy steamierboy saltboy' );

$r = new Regexp_Assemble();
is( $r
    ->add( 'showerless', 'showeriness', 'showless', 'showiness', 'show', 'shows' )
    ->as_string(['indent' => 4]),
'show
(?:
    (?:
        (?:
            er
        )
        ?
        (?:
            in
            |l
        )
        es
    )
    ?s
)
?' , 'pretty showerless showeriness showless showiness show shows' );

$r = new Regexp_Assemble();
is( $r->add( 'showerless', 'showeriness', 'showdeless', 'showdeiness', 'showless', 'showiness', 'show', 'shows'
    )->as_string(['indent' => 4]),
'show
(?:
    (?:
        (?:
            de
            |er
        )
        ?
        (?:
            in
            |l
        )
        es
    )
    ?s
)
?' , 'pretty showerless showeriness showdeless showdeiness showless showiness show shows' );

$r = new Regexp_Assemble();
is( $r->add( 'convenient', 'consort', 'concert'
    )->as_string(['indent' => 4]),
'con
(?:
    (?:
        ce
        |so
    )
    r
    |venien
)
t', 'pretty convenient consort concert' );

$r = new Regexp_Assemble();
is( $r->add( '200.1', '202.1', '207.4', '208.3', '213.2')->as_string(['indent' => 4]),
'2
(?:
    0
    (?:
        [02].1
        |7.4
        |8.3
    )
    |13.2
)', 'pretty 200.1 202.1 207.4 208.3 213.2' );


$r = new Regexp_Assemble();
is( $r->add( 'yammail\.com', 'yanmail\.com', 'yeah\.net', 'yourhghorder\.com', 'yourload\.com')->as_string(['indent' => 4]),
'y
(?:
    (?:
        our
        (?:
            hghorder
            |load
        )
        |a[mn]mail
    )
    \.com
    |eah\.net
)'
, 'pretty yammail.com yanmail.com yeah.net yourhghorder.com yourload.com' );

$r = new Regexp_Assemble();
is( $r->add( 'convenient', 'containment', 'consort', 'concert')->as_string(['indent' => 4]),
'con
(?:
    (?:
        tainm
        |veni
    )
    en
    |
    (?:
        ce
        |so
    )
    r
)
t'
, 'pretty convenient containment consort concert' );

$r = new Regexp_Assemble();
is( $r->add( 'sat', 'sit', 'bat', 'bit', 'sad', 'sid', 'bad', 'bid')->as_string(['indent' => 5]),
'(?:
     b
     (?:
          a[dt]
          |i[dt]
     )
     |s
     (?:
          a[dt]
          |i[dt]
     )
)'
, 'pretty sat sit bat bit sad sid bad bid' );

$r = new Regexp_Assemble();
is( $r->add( 'commercial\.net', 'compuserve\.com', 'compuserve\.net', 'concentric\.net',
        'coolmail\.com', 'coventry\.com', 'cox\.net'
     )->as_string(['indent' => 5]),
'co
(?:
     m
     (?:
          puserve\.
          (?:
               com
               |net
          )
          |mercial\.net
     )
     |
     (?:
          olmail
          |ventry
     )
     \.com
     |
     (?:
          ncentric
          |x
     )
     \.net
)'
, 'pretty c*.*' );

$r = new Regexp_Assemble();
is( $r->add( 
        'ambient\.at', 'agilent\.com', 'americanexpress\.com', 'amnestymail\.com',
        'amuromail\.com', 'angelfire\.com', 'anya\.com', 'anyi\.com', 'aol\.com',
        'aolmail\.com', 'artfiles\.de', 'arcada\.fi', 'att\.net'
     )->as_string(['indent' => 5]),
'a
(?:
     m
     (?:
          (?:
               (?:
                    nesty
                    |uro
               )
               mail
               |ericanexpress
          )
          \.com
          |bient\.at
     )
     |
     (?:
          n
          (?:
               gelfire
               |y[ai]
          )
          |o
          (?:
               lmai
          )
          ?l
          |gilent
     )
     \.com
     |r
     (?:
          tfiles\.de
          |cada\.fi
     )
     |tt\.net
)' , 'pretty a*.*' );

$r = new Regexp_Assemble();
is( $r->add( 
    'looked', 'choked', 'hooked', 'stoked', 'toked', 'baked', 'faked'
     )->as_string(['indent' => 4]),
'(?:
    (?:
        [hl]o
        |s?t
        |ch
    )
    o
    |[bf]a
)
ked' , 'looked choked hooked stoked toked baked faked' );

$r = new Regexp_Assemble();
is( $r->add( 
'arson','bison','brickmason','caisson','comparison','crimson','diapason','disimprison','empoison',
'foison','foreseason','freemason','godson','grandson','impoison','imprison','jettison','lesson',
'liaison','mason','meson','midseason','nonperson','outreason','parson','person','poison','postseason',
'precomparison','preseason','prison','reason','recomparison','reimprison','salesperson','samson',
'season','stepgrandson','stepson','stonemason','tradesperson','treason','unison','venison','vison',
'whoreson'
     )->as_string(['indent' => 4]),
'(?:
    p
    (?:
        r
        (?:
            e
            (?:
                compari
                |sea
            )
            |i
        )
        |o
        (?:
            stsea
            |i
        )
        |[ae]r
    )
    |s
    (?:
        t
        (?:
            ep
            (?:
                grand
            )
            ?
            |onema
        )
        |a
        (?:
            lesper
            |m
        )
        |ea
    )
    |
    (?:
        v
        (?:
            en
        )
        ?
        |imp[or]
        |empo
        |jett
        |un
    )
    i
    |f
    (?:
        o
        (?:
            resea
            |i
        )
        |reema
    )
    |re
    (?:
        (?:
            compa
            |imp
        )
        ri
        |a
    )
    |m
    (?:
        (?:
            idse
        )
        ?a
        |e
    )
    |c
    (?:
        ompari
        |ais
        |rim
    )
    |di
    (?:
        simpri
        |apa
    )
    |g
    (?:
        ran
        |o
    )
    d
    |tr
    (?:
        adesper
        |ea
    )
    |b
    (?:
        rickma
        |i
    )
    |
    (?:
        nonpe
        |a
    )
    r
    |l
    (?:
        iai
        |es
    )
    |outrea
    |whore
)
son' , '.*son' );

$r = new Regexp_Assemble();
is( $r->add( 
    'deathweed','deerweed','deeded','detached','debauched','deboshed','detailed',
    'defiled','deviled','defined','declined','determined','declared','deminatured',
    'debentured','deceased','decomposed','demersed','depressed','dejected',
    'deflected','delighted'
 )->as_string(['indent' => 2]),
'de
(?:
  c
  (?:
    (?:
      ompo
      |ea
    )
    s
    |l
    (?:
      ar
      |in
    )
  )
  |b
  (?:
    (?:
      auc
      |os
    )
    h
    |entur
  )
  |t
  (?:
    a
    (?:
      ch
      |il
    )
    |ermin
  )
  |f
  (?:
    i[ln]
    |lect
  )
  |m
  (?:
    inatur
    |ers
  )
  |
  (?:
    ligh
    |jec
  )
  t
  |e
  (?:
    rwe
    |d
  )
  |athwe
  |press
  |vil
)
ed', 'indent de.*ed' );


//追加
$r = new Regexp_Assemble();
is( $r->add( 'unimped','unimpeded','unimpelled' )->as_string(),
    'unimpe(?:(?:de)?|lle)d', 'unimped unimpeded unimpelled'
);

$r = new Regexp_Assemble();
is( $r->add( 'tiao','tie','tien','tin','tine','tinea','tinean','tineine',
    'tininess','tinnet','tinniness','tinosa','tinstone','tint','tinta','tintie','tintiness',
    'tintist','tisane','tit','titanate','titania','titanite','titano','tite','titi','titian',
    'titien','tittie' )->as_string(),
    'ti(?:n(?:t(?:i(?:ness|st|e)|a)?|e(?:an?|ine)?|n(?:iness|et)|iness|stone|osa)?|t(?:an(?:i(?:te|a)|ate|o)|i(?:[ae]n)?|(?:ti)?e)?|sane|en?|ao)', 'tiao tie ....  titien tittie'
);



/*
is( $_, $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
