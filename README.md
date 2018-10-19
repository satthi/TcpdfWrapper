# TcpdfWrapper

[![Build Status](https://travis-ci.org/satthi/TcpdfWrapper.svg?branch=master)](https://travis-ci.org/satthi/TcpdfWrapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/satthi/TcpdfWrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/satthi/TcpdfWrapper/?branch=master)

このプロジェクトは[TCPDF](https://github.com/tecnickcom/TCPDF)を自分が使いやすいように対応したものになります。

## インストール
composer.json
```
{
	"require": {
		"satthi/tcpdfwrapper": "~2.0.0"
	}
}
```

`composer install`

## 使い方(基本)

```php
<?php
require('./vendor/autoload.php');
use TcpdfWrapper\TcpdfWrapper;

class hoge{

    public function fuga(){
        $TcpdfWrapper = new TcpdfWrapper();
        $TcpdfWrapper->setPrintHeader(false);
        $TcpdfWrapper->setPrintFooter(false);

        //独自フォント利用の場合
        $TcpdfWrapper->setFont('testfont', dirname(__FILE__) . '/fonts/○○.ttf');

        //1ページ目
        $TcpdfWrapper->addPage(dirname(__FILE__) . '/template.pdf', 1);
        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'C',
            'fill' => false,
            'link' => '',
            'x' => 40,
            'y' => 37,
            'color' => '000000',
            //独自フォント利用
            'font' => 'testfont',
            'size' => 30,
        ];
        $TcpdfWrapper->setVal('山田　太郎', $option);

        $option = [
            'x' => 55,
            'y' => 110,
            'w' => 30,
            'link' => 'http://fusic.co.jp',
            'resize' => true,
            'dpi' => '300',
        ];
        $TcpdfWrapper->setImage(dirname(__FILE__) . '/test.png', $option);

        //2ページ目
        $TcpdfWrapper->addPage(dirname(__FILE__) . '/template.pdf', 2);

        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'L',
            'fill' => false,
            'link' => '',
            'x' => 40,
            'y' => 68,
            'color' => '000000',
            'font' => 'kozgopromedium',
            'size' => 11,
        ];
        $TcpdfWrapper->setVal('福岡県テストテスト○○-○○', $option);

        $TcpdfWrapper->write(dirname(__FILE__) . '/export.pdf');
    }
}

$hoge = new hoge();
$hoge->fuga();
```

## 準備している関数

```php
/**
* setPrintHeader
* ヘッダー出力のon/off
* @param boolean $pring 出力フラグ
* @author hagiwara
*/
$TcpdfWrapper->setPrintHeader(false);

/**
* setPrintFooter
* フッター出力のon/off
* @param boolean $pring 出力フラグ
* @author hagiwara
*/
$TcpdfWrapper->setPrintFooter(false);

/**
* setFont
* 独自フォントのセット
* @param string $name フォント名
* @param string $path フォントパス nullでデフォルトセット
* @author hagiwara
*/
$TcpdfWrapper->setFont('testfont', dirname(__FILE__) . '/fonts/○○.ttf');

/**
* addPage
* ページの追加
* @param string $template テンプレートパス
* @param integer $templateIndex テンプレートページ
* @author hagiwara
*/
$TcpdfWrapper->addPage(dirname(__FILE__) . '/template.pdf', 1);

/**
* setVal
*
* @param string $text テキスト
* @param array $option オプション
* @author hagiwara
*/
$option = [
    //テキストを入れるブロックのサイズ
    'w' => 200,
    'h' => 0,
    /*
    //ブロックに対する罫線
    0: 罫線を引かない(デフォルト)
    1: 罫線を引く
    L: 左
    T: 上
    R: 右
    B: 下
    */
    'border' => 0,
    /*
    L or 空文字: 左揃え(既定)
    C: 中央揃え
    R: 右揃え
    J: 両端揃え
    */
    'align' => 'C',
    //ブロック塗りつぶすか
    'fill' => false,
    //テキストにリンクを設定
    'link' => '',
    //ブロックの配置
    'x' => 40,
    'y' => 37,
    //文字色
    'color' => '000000',
    //フォントの種類
    'font' => 'kozgopromedium',
    //文字サイズ
    'size' => 30,
];
$TcpdfWrapper->setVal('山田　太郎', $option);

/**
* setHtml
*
* @param string $html HTML
* @param array $option オプション
* @author hagiwara
*/
$option = [
    //テキストを入れるブロックのサイズ
    'w' => 200,
    'h' => 0,
    /*
    //ブロックに対する罫線
    0: 罫線を引かない(デフォルト)
    1: 罫線を引く
    L: 左
    T: 上
    R: 右
    B: 下
    */
    'border' => 0,
    /*
    L or 空文字: 左揃え(既定)
    C: 中央揃え
    R: 右揃え
    J: 両端揃え
    */
    'align' => 'C',
    //ブロック塗りつぶすか
    'fill' => false,
    //テキストにリンクを設定
    'link' => '',
    //ブロックの配置
    'x' => 40,
    'y' => 37,
    //文字色
    'color' => '000000',
    //フォントの種類
    'font' => 'kozgopromedium',
    //文字サイズ
    'size' => 30,
];
$TcpdfWrapper->setVal('山田　太郎', $option);

/**
* setImage
*
* @param string $image 画像パス
* @param array $option オプション
* @author hagiwara
*/
$option = [
    //画像の配置
    'x' => 55,
    'y' => 110,
    //画像のサイズ。片方だけ指定で縦横比を維持してリサイズ
    'w' => 30,
    'h' => 0,
    'link' => 'http://fusic.co.jp',
    //画像自体をリサイズするか
    'resize' => true,
    //解像度
    'dpi' => '300',
];
$TcpdfWrapper->setImage($setImagePath, $option);

/**
* write
* PDFファイルの出力
* @param string $file 出力ファイル
* @author hagiwara
*/
$TcpdfWrapper->write(dirname(__FILE__) . '/hoge.pdf');
```
```

## License ##

The MIT Lisence

Copyright (c) 2016 Fusic Co., Ltd. (http://fusic.co.jp)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Author ##

Satoru Hagiwara
