#!/usr/bin/perl
use Regexp::Assemble;
use utf8;

for($i = 0 ; $i < 10000 ; ++$i ) {
    $reg = Regexp::Assemble->new();
    $reg->add('神岸あかり');
    $reg->add('赤座あかり');
    $reg->add('黒座あかり');
    $str = $reg->re();

    $reg = Regexp::Assemble->new();
    $reg->add('スティーブ・ジョブズ');
    $reg->add('スティーブ・ウォズアニック');
    $str = $reg->re();

    $reg = Regexp::Assemble->new();
    $reg->add('お兄ちゃま');
    $reg->add('あにぃ');
    $reg->add('お兄様');
    $reg->add('おにいたま');
    $reg->add('兄上様');
    $reg->add('にいさま');
    $reg->add('アニキ');
    $reg->add('兄くん');
    $reg->add('兄君さま');
    $reg->add('兄チャマ');
    $reg->add('兄や');
    $str = $reg->re();
}
