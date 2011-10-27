<?php
require_once("Assemble.pm.php");
require_once("testutil.php");



/*
# 01_insert.t
#
# Test suite for Regexp::Assemble
#
# When a series of paths are inserted in an R::A object, they are
# stored into tree structure using a crafty blend of arrays and hashes.
# 
# These tests verify that the tokens that are added to the
# Regexp::Assemble object are stored correctly.
#
# The tests here verify to a much greater extent that the tree/hash structure
# built up from repeated add() calls produce a structure that the
# subsequent coalescing and reduction routines can operate upon correctly.
#
# copyright (C) 2004-2006 David Landgren

use strict;
use Regexp::Assemble;

use constant permute_testcount => 120 * 5; # permute() has 120 (5!) variants

eval qq{use Test::More tests => 50 + permute_testcount};
if( $@ ) {
    warn "# Test::More not available, no tests performed\n";
    print "1..1\nok 1\n";
    exit 0;
}
my $fixed = 'The scalar remains the same';
$_ = $fixed;
*/

//{
    $ra = new Regexp_Assemble();
    $ra->insert( '' );
    $r = $ra->path[0];
    is( is_array($r), true,  "insert('') => first element is a HASH" );
    is( count($r), 1,      "...and contains one key" );
    ok( isset($r['__@UNDEF@__']),    "...which is an empty string" );
    ok( $r['__@UNDEF@__'] === 0, "...and points to undef" );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( 'a' );
    $r = $ra->path;
    is( count($r) , 1,  "'a' => path of length 1" );
    is( $r[0], 'a',   "'a' => ...and is an 'a'" );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert();
    $ra->insert('a');
    is_deeply( $ra->path, [['__@UNDEF@__' => 0, 'a' => ['a']]], "insert(), insert('a')" );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( 'a', 'b' );
    $r = $ra->path;
    is( count($r), 2,  "'ab' => path of length 2" );
    is( join( '' , $r ), 'ab', "'ab' => ...and is 'a', 'b'" );
    is( $ra->dump(), '[a b]', 'dump([a b])' );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( 'a', 'b' );
    $ra->insert( 'a', 'c' );
    is( $ra->dump(), '[a {b=>[b] c=>[c]}]', 'dump([a {b c}])' );
    $r = $ra->path;
    is( count($r), 2,         "'ab,ac' => path of length 2" );
    is( $r[0], 'a',         "'ab,ac' => ...and first atom is 'a'" );
    is( is_array($r[1]), true, "'ab,ac' => ...and second is a node" );
    $r = $r[1];
    is( count($r), 2,  "'ab,ac' => ...node has two keys" );
    is( join( '' , perl_sort( array_keys( $r) )  ), 'bc',
        "'ab,ac' => ...keys are 'b','c'" );
    ok( isset($r['b']) , "'ab,ac' => ... key 'b' exists" );
    is( is_array($r['b']), true, "'ab,ac' => ... and points to a path" );
    ok( isset($r['c']), "'ab,ac' => ... key 'c' exists" );
    is( is_array($r['c']), true, "'ab,ac' => ... and points to a path" );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( NULL );
    is_deeply( $ra->path, [['__@UNDEF@__' => 0]], 'insert(undef)' );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( '' );
    is_deeply( $ra->path, [['__@UNDEF@__' => 0]], "insert('')" );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert();
    is_deeply( $ra->path, [['__@UNDEF@__' => 0]], 'insert()' );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ['0'] );
    is_deeply( $ra->path,
        ['0'],
        "/0/"
    );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ["d"] );
    is_deeply( $ra->path,
        ['d'],
        "/d/"
    );
//}

//{
    $r = new Regexp_Assemble();
    $r->lex = '\([^(]*(?:\([^)]*\))?[^)]*\)|.';

    $r->reset()->add( 'ab(cd)ef' );
    is_deeply( $r->path,
        [ 'a', 'b', '(cd)', 'e', 'f' ],
        'ab(cd)ef (with parenthetical lexer)'
    );

    $r->reset()->add( 'ab(cd(ef)gh)ij' );
    is_deeply( $r->path,
        [ 'a', 'b', '(cd(ef)gh)', 'i', 'j' ],
        'ab(cd(ef)gh)ij (with parenthetical lexer)'
    );

    $r->reset()->add( 'ab((ef)gh)ij' );
    is_deeply( $r->path,
        [ 'a', 'b', '((ef)gh)', 'i', 'j' ],
        'ab((ef)gh)ij (with parenthetical lexer)'
    );

    $r->reset()->add( 'ab(cd(ef))ij' );
    is_deeply( $r->path,
        [ 'a', 'b', '(cd(ef))', 'i', 'j' ],
        'ab(cd(ef))ij (with parenthetical lexer)'
    );

    $r->reset()->add( 'ab((ef))ij' );
    is_deeply( $r->path,
        [ 'a', 'b', '((ef))', 'i', 'j' ],
        'ab((ef))ij (with parenthetical lexer)'
    );
//}

//{
    $r = new Regexp_Assemble(['lex' => '\\d']);
    is_deeply( $r->add( '0\Q0C,+' )->path,
        [ '0', '0', 'C', ',', '\\+' ],
        '0\\Q0C,+ with \\d lexer'
    );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','a','b'] );
    is_deeply( $ra->path,
        ['d','a','b'],
        '/dab/'
    );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ['0','1'] );
    $ra->insert( ['0','2'] );
    is_deeply( $ra->path,
        [
            '0',
            [
                '1' => ['1'],
                '2' => ['2']
            ]
        ],
        '/01/ /02/'
    );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ['0'] );
    $ra->insert( ['0','1'] );
    $ra->insert( ['0','2'] );
    is_deeply( $ra->path,
        [
            '0',
            [
                '1' => ['1'],
                '2' => ['2'],
                '__@UNDEF@__' => 0
            ]
        ],
        '/0/ /01/ /02/'
    );
//}

//{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','a','m'] );
    $ra->insert( ['d','a','m'] );
    is_deeply( $ra->path,
        [
            'd', 'a', 'm'
        ],
        '/dam/ x 2'
    );
//}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','a','y'] );
    $ra->insert( ['d','a'] );
    $ra->insert( ['d','a'] );
    is_deeply( $ra->path,
        [
            'd', 'a',
            [
                'y' => ['y'],
                '__@UNDEF@__' => 0
            ]
        ],
        '/day/, /da/ x 2'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','o','t'] );
    $ra->insert( ['d','o'] );
    $ra->insert( ['d'] );
    is_deeply( $ra->path,
        [
            'd',
            [
                'o' => [
                    'o',
                    [
                        't' => ['t'],
                        '__@UNDEF@__' => 0
                    ]
                ],
                '__@UNDEF@__' => 0
            ]
        ],
        '/dot/ /do/ /d/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['b','i','g'] );
    $ra->insert( ['b','i','d'] );
    is_deeply( $ra->path,
        [
            'b', 'i',
            [
                'd' => ['d'],
                'g' => ['g']
            ]
        ],
        '/big/ /bid/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','a','r','t'] );
    $ra->insert( ['d','a','m','p'] );
    is_deeply( $ra->path,
        [
            'd', 'a',
            [
                'r' => ['r', 't'],
                'm' => ['m', 'p']
            ]
        ],
        '/dart/ /damp/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['a','m','b','l','e'] );
    $ra->insert( ['i','d','l','e'] );
    is_deeply( $ra->path,
        [
            [
                'a' => ['a', 'm', 'b', 'l', 'e'],
                'i' => ['i', 'd', 'l', 'e']
            ]
        ],
        '/amble/ /idle/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['a','m','b','l','e'] );
    $ra->insert( ['a','m','p','l','e'] );
    $ra->insert( ['i','d','l','e'] );
    is_deeply( $ra->path,
        [
            [
                'a' => [
                    'a', 'm',
                    [
                        'b' => [ 'b', 'l', 'e' ],
                        'p' => [ 'p', 'l', 'e' ]
                    ]
                ],
                'i' => ['i', 'd', 'l', 'e'],
            ]
        ],
        '/amble/ /ample/ /idle/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra->insert( ['d','a','m'] );
    $ra->insert( ['d','a','r','e'] );
    is_deeply( $ra->path,
        [
            'd', 'a',
            [
                'm' => ['m'],
                'r' => ['r', 'e']
            ]
        ],
        '/dam/ /dare/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra
        ->insert(['d','a'])
        ->insert(['d','b'])
        ->insert(['d','c'])
    ;
    is_deeply( $ra->path,
        [
            'd',
            [
                'a' => ['a'],
                'b' => ['b'],
                'c' => ['c']
            ]
        ],
        '/da/ /db/ /dc/'
    );
}

{
    $ra = new Regexp_Assemble();
    $ra
        ->insert(['d','a'])
        ->insert(['d','b','c','d'])
        ->insert(['d','c'])
    ;
    is_deeply( $ra->path,
        [
            'd',
            [
                'a' => ['a'],
                'b' => ['b', 'c', 'd'],
                'c' => ['c']
            ]
        ],
        '/da/ /dbcd/ /dc/'
    );
}

function permute($target , $path) {
    foreach( range(0,4) as $x1 ){
        foreach( range(0,4) as $x2 ){
            if ($x2 == $x1)   continue;
            foreach( range(0,4) as $x3 ){
                if ( count(array_filter( [$x1, $x2] , function($_) use($x3){ return $_ == $x3; } )) ) continue;
                foreach( range(0,4) as $x4 ){
                    if ( count(array_filter( [$x1, $x2, $x3] , function($_) use($x4){ return $_ == $x4; } )) ) continue;
                    foreach( range(0,4) as $x5 ){
                        if ( count(array_filter( [$x1, $x2, $x3, $x4] , function($_) use($x5){ return $_ == $x5; } )) ) continue;
                        $ra = new Regexp_Assemble();
                        $ra
                            ->insert( $path[$x1] )
                            ->insert( $path[$x2] )
                            ->insert( $path[$x3] )
                            ->insert( $path[$x4] )
                            ->insert( $path[$x5] )
                        ;
                        is_deeply( $ra->path, $target,
                            'join: /' . join( '/ /', 
                                array(
                                   join( '' , $path[$x1]),
                                   join( '' , $path[$x2]),
                                   join( '' , $path[$x3]),
                                   join( '' , $path[$x4]),
                                   join( '' , $path[$x5])
                                )
                            ) . '/\n'
                            .
                            $ra->dump() . ' versus ' . $ra->_dump($target) . "\n"
                        );
                    }
                }
            }
        }
    }
}

permute(
    [
        'a', [
            '__@UNDEF@__' => 0, 'b' => [
                'b', [
                    '__@UNDEF@__' => 0, 'c' => [
                        'c', [
                            '__@UNDEF@__' => 0, 'd' => [
                                'd', [
                                    '__@UNDEF@__' => 0, 'e' => [
                                        'e'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        [ 'a'                     ],
        [ 'a', 'b'                ],
        [ 'a', 'b', 'c'           ],
        [ 'a', 'b', 'c', 'd'      ],
        [ 'a', 'b', 'c', 'd', 'e' ]
    ]
);

permute(
    [
        [
            '__@UNDEF@__' => 0, 'a' => [
                'a', [
                    '__@UNDEF@__' => 0, 'b' => [
                        'b', [
                            '__@UNDEF@__' => 0, 'c' => [
                                'c', [
                                    '__@UNDEF@__' => 0, 'd' => [
                                        'd',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        [ '',                ],
        [ 'a',               ],
        [ 'a', 'b'           ],
        [ 'a', 'b', 'c'      ],
        [ 'a', 'b', 'c', 'd' ]
    ]
);

permute(
    [ 'd', 'o',
    [
        'n' => [
            'n', 'a', 't',
            [
                'e' => ['e'],
                'i' => ['i', 'o', 'n']
            ]
        ]
        ,
        't' => [
            't',
            [
                'a' => ['a', 't', 'e'],
                'i' => ['i', 'n', 'g']
            ]
        ]
        ,
        '__@UNDEF@__' => 0,
    ]],
    [
        [ 'd','o'       ],
        [ 'd','o','n','a','t','e'   ],
        [ 'd','o','n','a','t','i','o','n' ],
        [ 'd','o','t','a','t','e'   ],
        [ 'd','o','t','i','n','g'   ]
    ]
);

permute(
    [
        'o',
        [
            '__@UNDEF@__' => 0,
            'n' => [
                'n', [
                    '__@UNDEF@__' => 0,
                    'l' => ['l', 'y'],
                    'e' => [
                        'e', [
                            '__@UNDEF@__' => 0,
                            'r' => ['r']
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
         ['o']    ,
         ['o','n']   ,
         ['o','n','e']  ,
         ['o','n','l','y' ] ,
         ['o','n','e','r' ] 
    ]
);

permute(
    [
        'a', 'm',
        [
            'a' => [ 'a',
                [
                    's' => ['s', 's'],
                    'z' => ['z', 'e']
                ]
            ],
            'u' => [ 'u',
                [
                    'c' => ['c', 'k'],
                    's' => ['s', 'e']
                ]
            ],
            'b' => [ 'b', 'l', 'e' ]
        ]
    ],
    [
        [ 'a','m','a','s','s' ],
        [ 'a','m','a','z','e' ],
        [ 'a','m','b','l','e' ],
        [ 'a','m','u','c','k' ],
        [ 'a','m','u','s','e' ]
    ]
);
/*
Regexp::Assemble::Default_Lexer( '\([^(]*(?:\([^)]*\))?[^)]*\)|.' );

{
    my $r = Regexp::Assemble->new;

    $r->reset->add( 'ab(cd)ef' );
    is_deeply( $r->_path,
        [ 'a', 'b', '(cd)', 'e', 'f' ],
        'ab(cd)ef (with Default parenthetical lexer)'
    ) or diag("lex = $r->{lex}");

    $r->reset->add( 'ab((ef)gh)ij' );
    is_deeply( $r->_path,
        [ 'a', 'b', '((ef)gh)', 'i', 'j' ],
        'ab((ef)gh)ij (with Default parenthetical lexer)'
    );

    $r->reset->add( 'ab(ef(gh))ij' );
    is_deeply( $r->_path,
        [ 'a', 'b', '(ef(gh))', 'i', 'j' ],
        'ab(ef(gh))ij (with Default parenthetical lexer)'
    );

    eval { $r->filter('choke') };
    ok( $@, 'die on non-CODE filter' );

    eval { $r->pre_filter('choke') };
    ok( $@, 'die on non-CODE pre_filter' );
}

is( $_, $fixed, '$_ has not been altered' );
*/
echo "===OK===\n";
