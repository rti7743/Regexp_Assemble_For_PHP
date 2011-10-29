<?php
require_once("Assemble.pm.php");
require_once("testutil.php");

$rt = new Regexp_Assemble();
$context = [ 'debug' => 255, 'depth' => 0 ];

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

is( Regexp::Assemble->new->as_string, $Regexp::Assemble::Always_Fail, 'empty' );
*/

foreach( array(
    [ '\\d?',        ['\\d'], ['5'], [''] ],


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
/*
//    [ '\\d?',        ['\\d'], ['5'], [''] ],
//    [ '\\s',         ['\\s'], [' '] ],
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
*/
    [ '(?:b[ey])?',   ['b', 'e'], [''], ['b', 'y'] ],
    [ '(?:be|do)?',   ['b', 'e'], [''], ['d', 'o'] ],
    [ '(?:b[ey]|a)?', ['b', 'e'], [''], ['b', 'y'], ['a'] ],
    [ 'da[by]',       ['d', 'a', 'b'] , ['d', 'a', 'y'] ],
    [ 'da(?:ily|b)',  ['d', 'a', 'b'], ['d', 'a', 'i', 'l', 'y'] ],
//    [ '(?:night|day)',    ['n', 'i', 'g', 'h', 't'], ['d', 'a', 'y'] ],
    [ '(?:day|night)',    ['n', 'i', 'g', 'h', 't'], ['d', 'a', 'y'] ], //Ž«‘‡‚É‚µ‚½‚¢‚©‚È‚Ÿ‚Á‚ÄB
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

/*
my $xism = ($] < 5.013) ? '-xism' : '^';

for my $test (
    [ qq'(?$xism:(?:^|m)a)',    qw(^a ma) ],
    [ qq'(?$xism:(?:[mw]|^)a)', qw(^a ma wa) ],
    [ qq'(?$xism:(?:^|\\^)a)',  qw(^a), '\\^a' ],
    [ qq'(?$xism:(?:^|0)a)',    qw(^a 0a) ],
    [ qq'(?$xism:(?:[m^]|^)a)', qw(^a ma), '\\^a' ],
    [ qq'(?$xism:(?:ma|^)a)',   qw(^a maa) ],
    [ qq'(?$xism:a.+)',         qw(a.+) ],
    [ qq'(?$xism:b?)',          '[b]?' ],
    [ qq'(?$xism:\\.)',         '[.]' ],
    [ qq'(?$xism:\\.+)',        '[.]+' ],
    [ qq'(?$xism:\\.+)'  ,      '[\\.]+' ],
    [ qq'(?$xism:\\^+)',        '[\\^]+' ],
    [ qq'(?$xism:%)',           '[%]' ],
    [ qq'(?$xism:%)',           '[\\%]' ],
    [ qq'(?$xism:!)',           '[!]' ],
    [ qq'(?$xism:!)',           '[\\!]' ],
    [ qq'(?$xism:@)',           '[@]' ],
    [ qq'(?$xism:@)',           '[\\@]' ],
    [ qq'(?$xism:a|[bc])',      'a|[bc]' ],
    [ qq'(?$xism:ad?|[bc])',    'ad?|[bc]' ],
    [ qq'(?$xism:'.'b(?:$|e))',    qw(b$ be) ],
    [ qq'(?$xism:'.'b(?:[ae]|$))', qw(b$ be ba) ],
    [ qq'(?$xism:'.'b(?:$|\\$))',  qw(b$), 'b\\$' ],
    [ qq'(?$xism:(?:^a[bc]|de))',  qw(^ab ^ac de) ],
    [ qq'(?$xism:(?i:/))',              qw(/),          {flags => 'i'} ],
    [ qq'(?$xism:(?i:(?:^a[bc]|de)))',  qw(^ab ^ac de), {flags => 'i'} ],
    [ qq'(?$xism:(?im:(?:^a[bc]|de)))', qw(^ab ^ac de), {flags => 'im'} ],
    [ qq'(?$xism:a(?:%[de]|=[bc]))',
        quotemeta('a%d'), quotemeta('a=b'), quotemeta('a%e'), quotemeta('a=c') ],
    [ qq'(?$xism:\\^[,:])',     quotemeta('^:'), quotemeta('^,') ],
    [ qq'(?$xism:a[-*=])',      quotemeta('a='), quotemeta('a*'), quotemeta('a-') ],
    [ qq'(?$xism:l(?:im)?it)',  qw(lit limit) ],
    [ qq'(?$xism:a(?:(?:g[qr]|h)w|[de]n|m)z)', qw(amz adnz aenz agrwz agqwz ahwz) ],
    [ qq'(?$xism:a(?:(?:e(?:[gh]u|ft)|dkt|f)w|(?:(?:ij|g)m|hn)v)z)',
        qw(adktwz aeftwz aeguwz aehuwz afwz agmvz ahnvz aijmvz) ],
    [ qq'(?$xism:b(?:d(?:kt?|i)|ckt?)x)', qw(bcktx bckx bdix bdktx bdkx) ],
    [ qq'(?$xism:d(?:[ln]dr?t|x))',  qw(dldrt dndrt dldt dndt dx) ],
    [ qq'(?$xism:d(?:[ln][dp]t|x))', qw(dldt dndt dlpt dnpt dx) ],
    [ qq'(?$xism:d(?:[ln][dp][mr]t|x))', qw(dldrt dndrt dldmt dndmt dlprt dnprt dlpmt dnpmt dx) ],
    [ qq'(?$xism:'.'(?:\(scan|\*mens|\[mail))', '\\*mens', '\\(scan', '\\[mail'],
    [ qq'(?$xism:a\\[b\\[c)', '\\Qa[b[c' ],
    [ qq'(?$xism:a\\]b\\]c)', '\\Qa]b]c' ],
    [ qq'(?$xism:a\\(b\\(c)', '\\Qa(b(c' ],
    [ qq'(?$xism:a\\)b\\)c)', '\\Qa)b)c' ],
    [ qq'(?$xism:a[(+[]b)', '\\Qa(b', '\\Qa[b', '\\Qa+b' ],
    [ qq'(?$xism:a[-+^]b)', '\\Qa^b', '\\Qa-b', '\\Qa+b' ],
    [ qq'(?$xism:car(?:rot)?)', qw(car carrot), {lookahead => 1} ],
    [ qq'(?$xism:car[dpt]?)',   qw(car cart card carp), {lookahead => 1} ],
    [ qq'(?$xism:[bc]a[nr]e)',  qw(bane bare cane care), {lookahead => 1} ],
    [ qq'(?$xism:(?=[ru])(?:ref)?use)',       qw(refuse use), {lookahead => 1} ],
    [ qq'(?$xism:(?=[bcd])(?:bird|cat|dog))', qw(bird cat dog), {lookahead => 1} ],
    [ qq'(?$xism:sea(?=[hs])(?:horse|son))',  qw(seahorse season), {lookahead => 1} ],
    [ qq'(?$xism:car(?:(?=[dr])(?:rot|d))?)', qw(car card carrot), {lookahead => 1} ],
    [ qq'(?$xism:(?:(?:[hl]o|s?t|ch)o|[bf]a)ked)',
        qw(looked choked hooked stoked toked baked faked) ],
    [ qq'(?$xism:(?=[frt])(?:trans|re|f)action)',
        qw(faction reaction transaction), {lookahead => 1} ],
    [ qq'(?$xism:c(?=[ao])(?:or(?=[np])(?:pse|n)|ar(?=[de])(?:et|d)))',
        qw(card caret corn corpse), {lookahead => 1} ],
    [ qq'(?$xism:car(?:(?=[dipt])(?:[dpt]|i(?=[no])(?:ng|on)))?)',
        qw(car cart card carp carion caring), {lookahead => 1} ],
    [ qq'(?$xism:(?=[dfrst])(?:(?=[frt])(?:trans|re|f)a|(?=[ds])(?:dir|s)e)ction)',
        qw(faction reaction transaction direction section), {lookahead => 1} ],
    [ qq'(?$xism:car(?=[eir])(?:e(?=[flst])(?:(?=[ls])(?:le)?ss|ful|t)|i(?=[no])(?:ng|on)|r(?=[iy])(?:ied|y)))',
        qw(caret caress careful careless caring carion carry carried), {lookahead => 1} ],
    [ qq'(?$xism:(?=[uv])(?:u(?=[nr])(?:n(?=[iprs])(?:(?=[ip])(?:(?:p[or]|impr))?i|(?:sea)?|rea)|r)|v(?=[ei])(?:en(?=[it])(?:trime|i)|i))son)',
        qw(unimprison unison unpoison unprison unreason unseason unson urson venison ventrimeson vison), {lookahead => 1} ],
    [ qq'(?$xism:(?:a?bc?)?d)',         qw(abcd abd bcd bd d) ],
    [ qq'(?$xism:(?:a?bc?|c)d)',        qw(abcd abd bcd bd cd) ],
    [ qq'(?$xism:(?:(?:a?bc?)?d|c))',   qw(abcd abd bcd bd c d) ],
    [ qq'(?$xism:(?:(?:a?bc?)?d|cd?))', qw(abcd abd bcd bd c cd d) ],
    [ qq'(?$xism:(?:(?:ab?|b)c?)?d)',   qw(abcd abd acd ad bcd bd d) ],
    [ qq'(?$xism:(?:(?:ab)?cd?)?e)',          qw(abcde abce cde ce e) ],
    [ qq'(?$xism:(?:(?:(?:ab?|b)c?)?d|c))',   qw(abcd abd acd ad bcd bd c d) ],
    [ qq'(?$xism:(?:(?:(?:ab?|b)c?)?d|cd?))', qw(abcd abd acd ad bcd bd c cd d) ],
    [ qq'(?$xism:'.'^(?:b?cd?|ab)$)',          qw(^ab$ ^bc$ ^bcd$ ^c$ ^cd$) ],
    [ qq'(?$xism:'.'^(?:(?:ab?c|cd?)e?|e)$)',  qw(^abc$ ^abce$ ^ac$ ^ace$ ^c$ ^cd$ ^cde$ ^ce$ ^e$) ],
    [ qq'(?$xism:'.'^(?:abc|bcd)e?$)',         qw(^abc$ ^abce$ ^bcd$ ^bcde$) ],
    [ qq'(?$xism:'.'^(?:abcdef|bcdefg)h?$)',   qw(^abcdef$ ^abcdefh$ ^bcdefg$ ^bcdefgh$) ],
    [ qq'(?$xism:'.'^(?:bcdefg|abcd)h?$)',     qw(^abcd$ ^abcdh$ ^bcdefg$ ^bcdefgh$) ],
    [ qq'(?$xism:'.'^(?:abcdef|bcd)h?$)',      qw(^abcdef$ ^abcdefh$ ^bcd$ ^bcdh$) ],
    [ qq'(?$xism:'.'^(?:a(?:bcd|cd?)e?|e)$)',  qw(^abcd$ ^abcde$ ^ac$ ^acd$ ^acde$ ^ace$ ^e$) ],
    [ qq'(?$xism:'.'^(?:bcd|cd?)e?$)',         qw(^bcd$ ^bcde$ ^c$ ^cd$ ^cde$ ^ce$) ],
    [ qq'(?$xism:'.'^(?:abc|bc?)(?:de)?$)',    qw(^abc$ ^abcde$ ^b$ ^bc$ ^bcde$ ^bde$) ],
    [ qq'(?$xism:'.'^(?:b(?:cd)?|abd)e?$)',    qw(^abd$ ^abde$ ^b$ ^bcd$ ^bcde$ ^be$) ],
    [ qq'(?$xism:'.'^(?:ad?|bcd)e?$)',         qw(^a$ ^ad$ ^ade$ ^ae$ ^bcd$ ^bcde$) ],
    [ qq'(?$xism:'.'^(?:a(?:bcd|cd?)e?|de)$)', qw(^abcd$ ^abcde$ ^ac$ ^acd$ ^acde$ ^ace$ ^de$) ],
    [ qq'(?$xism:'.'^(?:a(?:bcde)?|bc?d?e)$)', qw(^a$ ^abcde$ ^bcde$ ^bce$ ^bde$ ^be$) ],
    [ qq'(?$xism:'.'^(?:a(?:b[cd]?)?|bd?e?f)$)', qw(^a$ ^ab$ ^abc$ ^abd$ ^bdef$ ^bdf$ ^bef$ ^bf$) ],
    [ qq'(?$xism:'.'^(?:a(?:bc?|dd)?|bd?e?f)$)', qw(^a$ ^ab$ ^abc$ ^add$ ^bdef$ ^bdf$ ^bef$ ^bf$) ],
    [ qq'(?$xism:'.'^(?:a(?:bc?|de)?|bc?d?f)$)', qw(^a$ ^ab$ ^abc$ ^ade$ ^bcdf$ ^bcf$ ^bdf$ ^bf$) ],
    [ qq'(?$xism:'.'^(?:a(?:bc?|de)?|cd?e?f)$)', qw(^a$ ^ab$ ^abc$ ^ade$ ^cdef$ ^cdf$ ^cef$ ^cf$) ],
    [ qq'(?$xism:'.'^(?:a(?:bc?|e)?|bc?de?f)$)', qw(^a$ ^ab$ ^abc$ ^ae$ ^bcdef$ ^bcdf$ ^bdef$ ^bdf$) ],
    [ qq'(?$xism:'.'^(?:a(?:bc?|e)?|b(?:cd)?e?f)$)', qw(^a$ ^ab$ ^abc$ ^ae$ ^bcdef$ ^bcdf$ ^bef$ ^bf$) ],
    [ qq'(?$xism:'.'^(?:b(?:cde?|d?e)f|a(?:bc?|e)?)$)',
        qw(^a$ ^ab$ ^abc$ ^ae$ ^bcdef$ ^bcdf$ ^bdef$ ^bef$) ],
    [ qq'(?$xism:\\b(?:c[de]|ab)\\b)', qw(ab cd ce), {anchor_word => 1} ],
    [ qq'(?$xism:\\b(?:c[de]|ab))',    qw(ab cd ce), {anchor_word_begin => 1} ],
    [ qq'(?$xism:'.'^(?:c[de]|ab)$)',     qw(ab cd ce), {anchor_line => 1} ],
    [ qq'(?$xism:(?:c[de]|ab))',       qw(ab cd ce), {anchor_line => 0} ],
    [ qq'(?$xism:'.'(?:c[de]|ab)$)',      qw(ab cd ce), {anchor_line_end => 1} ],
    [ qq'(?$xism:\\A(?:c[de]|ab)\\Z)', qw(ab cd ce), {anchor_string => 1} ],
    [ qq'(?$xism:(?:c[de]|ab))',       qw(ab cd ce), {anchor_string => 0} ],
    [ qq'(?$xism:x[[:punct:]][yz])',   qw(x[[:punct:]]y x[[:punct:]]z) ],
) {
    my $result = shift @$test;
    my $param = ref($test->[-1]) eq 'HASH' ? pop @$test : {};
    my $r = Regexp::Assemble->new(%$param)->add(@$test);
    my $args = '(' . join( ') (', @$test ) . ')';
    if (keys %$param) {
        $args .= ' {'
            . join( ', ', map {"$_ => $param->{$_}"} sort keys %$param)
            . '}';
    }
    is( $r->re, $result, "add $args")
}

{
    my $r = Regexp::Assemble->new->add( 'de' );
    my $re = $r->re;
    is( "$re", qq'(?$xism:de)', 'de' );
    my $re2 = $r->re;
    is( "$re2", qq'(?$xism:de)', 'de again' );
}

is( Regexp::Assemble->new->lookahead->add( qw/
    car cart card carp carion
    / )->as_string,
    'car(?:(?=[dipt])(?:[dpt]|ion))?', 'lookahead car carp cart card carion' );

is( Regexp::Assemble->new->anchor_word
    ->add(qw(ab cd ce))
    ->as_string, '\\b(?:c[de]|ab)\\b', 'implicit anchor word via method' );

is( Regexp::Assemble->new->anchor_word_end
    ->add(qw(ab cd ce))
    ->as_string, '(?:c[de]|ab)\\b', 'implicit anchor word end via method' );

is( Regexp::Assemble->new->anchor_word(0)
    ->add(qw(ab cd ce))
    ->as_string, '(?:c[de]|ab)', 'no implicit anchor word' );

is( Regexp::Assemble->new( anchor_word => 1 )->anchor_word_end(0)
    ->add(qw(ab cd ce))
    ->as_string, '\\b(?:c[de]|ab)', 'implicit anchor word, no anchor word end' );

is( Regexp::Assemble->new->anchor_word_begin(1)
    ->add(qw(ab cd ce))
    ->as_string, '\\b(?:c[de]|ab)', 'implicit anchor word begin' );

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_line
    ->as_string, '^(?:c[de]|ab)$', 'implicit anchor line via new' );

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_line_begin
    ->as_string, '^(?:c[de]|ab)', 'implicit anchor line via method' );

is( Regexp::Assemble->new->anchor_line_begin->anchor_line(0)
    ->add(qw(ab cd ce))
    ->as_string, '(?:c[de]|ab)', 'no implicit anchor line via method' );

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_string
    ->as_string, '\\A(?:c[de]|ab)\\Z', 'implicit anchor string via method' );

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_string_absolute
    ->as_string, '\\A(?:c[de]|ab)\\z', 'implicit anchor string absolute via method' );

is( Regexp::Assemble->new(anchor_string_absolute => 1)
    ->add(qw(de df fe))
    ->as_string, '\\A(?:d[ef]|fe)\\z', 'implicit anchor string absolute via new' );

is( Regexp::Assemble->new(anchor_string_absolute => 1, anchor_string_begin => 0)
    ->add(qw(de df))
    ->as_string, 'd[ef]\\z', 'anchor string absolute and no anchor_string_begin via new' );

is( Regexp::Assemble->new(anchor_word => 1, anchor_word_end => 0)
    ->add(qw(ze zf zg))
    ->as_string, '\bz[efg]', 'anchor word and no anchor_word_begin via new' );

is( Regexp::Assemble->new(anchor_string_absolute => 0)
    ->add(qw(de df fe))
    ->as_string, '(?:d[ef]|fe)', 'no implicit anchor string absolute via new' );

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_word_begin
    ->anchor_string_end_absolute
    ->as_string, '\\b(?:c[de]|ab)\\z',
        'implicit anchor word begin/string absolute end via method'
);

is( Regexp::Assemble->new
    ->add(qw(ab ad))
    ->anchor_string(1)
    ->anchor_string_end(0)
    ->as_string, '\\Aa[bd]',
        'explicit anchor string/no end via method'
);

is( Regexp::Assemble->new
    ->anchor_string_end
    ->add(qw(ab ad))
    ->as_string, 'a[bd]\\Z',
        'anchor string end via method'
);

is( Regexp::Assemble->new
    ->anchor_string_absolute(1)
    ->add(qw(ab ad))
    ->as_string, '\\Aa[bd]\\z',
        'anchor string end via method'
);

is( Regexp::Assemble->new(anchor_word_begin => 1, anchor_string_end_absolute => 1)
    ->add(qw(de ad be ef))
    ->as_string, '\\b(?:[bd]e|ad|ef)\\z',
        'implicit anchor word begin/string absolute end via new'
);

is( Regexp::Assemble->new
    ->add(qw(ab cd ce))
    ->anchor_word_begin
    ->anchor_string_begin
    ->as_string, '\\b(?:c[de]|ab)',
        'implicit anchor word beats string'
);

TODO: {
    use vars '$TODO';
    local $TODO = "\\d+ does not absorb digits";

    is( Regexp::Assemble->new->add( '5', '\\d+' )->as_string,
        '\\d+', '\\d+ absorbs single char',
    );

    is( Regexp::Assemble->new->add( '54321', '\\d+' )->as_string,
        '\\d+', '\\d+ absorbs multiple chars',
    );

    is( Regexp::Assemble->new
        ->add( qw/ abz acdez a5txz a7z /, 'a\\d+z', 'a\\d+-\\d+z' ) # 5.6.0 kluge
        ->as_string, 'a(?:b|(?:\d+-)?\d+|5tx|cde)z',
        'abz a\\d+z acdez a\\d+-\\d+z a5txz a7z'
    );
}

my $mute = Regexp::Assemble->new->mutable(1);

$mute->add( 'dog' );
is( $mute->as_string, 'dog', 'mute dog' );
is( $mute->as_string, 'dog', 'mute dog cached' );

$mute->add( 'dig' );
is( $mute->as_string, 'd(?:ig|og)', 'mute dog' );

my $red = Regexp::Assemble->new->reduce(0);

$red->add( 'dog' );
$red->add( 'dig' );
is( $red->as_string, 'd(?:ig|og)', 'mute dig dog' );

$red->add( 'dog' );
is( $red->as_string, 'dog', 'mute dog 2' );

$red->add( 'dig' );
is( $red->as_string, 'dig', 'mute dig 2' );

is( Regexp::Assemble->new->add(qw(ab cd))->as_string(indent => 0),
    '(?:ab|cd)', 'indent 0'
);

is( Regexp::Assemble->new
    ->add( qw/ dldrt dndrt dldt dndt dx / )
    ->as_string(indent => 3),
'd
(?:
   [ln]dr?t
   |x
)'
,  'dldrt dndrt dldt dndt dx (indent 3)' );

is( Regexp::Assemble->new( indent => 2 )
    ->add( qw/foo bar/ )
    ->as_string,
'(?:
  bar
  |foo
)'
, 'pretty foo bar' );

is( Regexp::Assemble->new
    ->indent(2)
    ->add( qw/food fool bar/ )
    ->as_string,
'(?:
  foo[dl]
  |bar
)'
, 'pretty food fool bar' );

is( Regexp::Assemble->new
    ->add( qw/afood afool abar/ )
    ->indent(2)
    ->as_string,
'a
(?:
  foo[dl]
  |bar
)'
, 'pretty afood afool abar' );

is( Regexp::Assemble->new
    ->add( qw/dab dam day/ )
    ->as_string(indent => 2),
'da[bmy]', 'pretty dab dam day' );

is( Regexp::Assemble->new(indent => 5)
    ->add( qw/be bed/ )
    ->as_string(indent => 2),
'bed?'
, 'pretty be bed' );

is( Regexp::Assemble->new(indent => 5)
    ->add( qw/b-d b\.d/ )
    ->as_string(indent => 2),
'b[-.]d'
, 'pretty b-d b\.d' );

is( Regexp::Assemble->new
    ->add( qw/be bed beg bet / )
    ->as_string(indent => 2),
'be[dgt]?'
, 'pretty be bed beg bet' );

is( Regexp::Assemble->new
    ->add( qw/afoodle afoole abarle/ )
    ->as_string(indent => 2),
'a
(?:
  food?
  |bar
)
le'
, 'pretty afoodle afoole abarle' );

is( Regexp::Assemble->new
    ->add( qw/afar afoul abate aback/ )
    ->as_string(indent => 2),
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


is( Regexp::Assemble->new
    ->add( qw/stormboy steamboy saltboy sockboy/ )
    ->as_string(indent => 5),
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

is( Regexp::Assemble->new
    ->add( qw/stormboy steamboy stormyboy steamyboy saltboy sockboy/ )
    ->as_string(indent => 4),
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

is( Regexp::Assemble->new
    ->add( qw/stormboy steamboy stormyboy steamyboy stormierboy steamierboy saltboy/ )
    ->as_string(indent => 1),
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

is( Regexp::Assemble->new
    ->add( qw/showerless showeriness showless showiness show shows/ )
    ->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
    showerless showeriness showdeless showdeiness showless showiness show shows
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
        convenient consort concert
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
        200.1 202.1 207.4 208.3 213.2
    / )->as_string(indent => 4),
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


is( Regexp::Assemble->new->add( qw/
        yammail\.com yanmail\.com yeah\.net yourhghorder\.com yourload\.com
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
        convenient containment consort concert
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
        sat sit bat bit sad sid bad bid
    / )->as_string(indent => 5),
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

is( Regexp::Assemble->new->add( qw/
        commercial\.net compuserve\.com compuserve\.net concentric\.net
        coolmail\.com coventry\.com cox\.net
    / )->as_string(indent => 5),
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

is( Regexp::Assemble->new->add( qw/
        ambient\.at agilent\.com americanexpress\.com amnestymail\.com
        amuromail\.com angelfire\.com anya\.com anyi\.com aol\.com
        aolmail\.com artfiles\.de arcada\.fi att\.net
    / )->as_string(indent => 5),
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

is( Regexp::Assemble->new->add( qw/
    looked choked hooked stoked toked baked faked
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
arson bison brickmason caisson comparison crimson diapason disimprison empoison
foison foreseason freemason godson grandson impoison imprison jettison lesson
liaison mason meson midseason nonperson outreason parson person poison postseason
precomparison preseason prison reason recomparison reimprison salesperson samson
season stepgrandson stepson stonemason tradesperson treason unison venison vison
whoreson
    / )->as_string(indent => 4),
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

is( Regexp::Assemble->new->add( qw/
    deathweed deerweed deeded detached debauched deboshed detailed
    defiled deviled defined declined determined declared deminatured
    debentured deceased decomposed demersed depressed dejected
    deflected delighted
/ )->as_string(indent => 2),
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

is( $_, $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
