<?php

namespace TcpdfWrapper;

use setasign\Fpdi\Tcpdf\Fpdi;
use \TCPDF_FONTS;
/**
* TcpdfWrapper
* TcpdfWrapperを記載しやすくするためのラッパー
*/
class TcpdfWrapper
{
    private $__pdf;
    private $__fonts = [];
    private $__tcpdfFonts;

    /**
    * __construct
    *
    * @author hagiwara
    */
    public function __construct()
    {
        $this->__pdf = new Fpdi();
        $this->__tcpdfFonts = new TCPDF_FONTS();
    }

    /**
    * setPrintHeader
    *
    * @param boolean $print 出力フラグ
    * @author hagiwara
    */
    public function setPrintHeader($print)
    {
        $this->__pdf->setPrintHeader($print);
    }

    /**
    * setPrintFooter
    *
    * @param boolean $print 出力フラグ
    * @author hagiwara
    */
    public function setPrintFooter($print)
    {
        $this->__pdf->setPrintFooter($print);
    }

    /**
    * setFont
    *
    * @param string $name フォント名
    * @param string $path フォントパス nullでデフォルトセット
    * @author hagiwara
    */
    public function setFont($name, $path)
    {
        $this->__fonts[$name] = $this->__tcpdfFonts->addTTFfont($path);
    }

    /**
    * addPage
    *
    * @param string $template テンプレートパス
    * @param integer $templateIndex テンプレートページ
    * @author hagiwara
    */
    public function addPage($template, $templateIndex)
    {
        // ページを追加
        $this->__pdf->AddPage();

        // テンプレートを読み込み
        $this->__pdf->setSourceFile($template);

        // 読み込んだPDFの1ページ目のインデックスを取得
        $tplIdx = $this->__pdf->importPage($templateIndex);

        // 読み込んだPDFの1ページ目をテンプレートとして使用
        $this->__pdf->useTemplate($tplIdx, null, null, null, null, true);
    }

    /**
    * setVal
    *
    * @param string $text テキスト
    * @param array $option オプション
    * @author hagiwara
    */
    public function setVal($text, $option)
    {
        $default_option = [
            'w' => 0,
            'h' => 0,
            'border' => 0,
            'align' => '',
            'fill' => false,
            'link' => '',
            'x' => 0,
            'y' => 0,
            'color' => '000000',
            'font' => '',
            'size' => 11,
            'stretch' => 0,
            'auto_size' => false,
        ];
        $option = array_merge($default_option ,$option);
        
        // 自動で枠に収めるかどうかのチェック
        if ($option['auto_size'] == true) {
            $fontDefaultWidth = $this->getStringWidth($text, $option['font'], '', $option['size']);
            if ($fontDefaultWidth > $option['w']) {
                $option['align'] ='J';
                $option['stretch'] =1;
            }
        }
        
        // 書き込む文字列のフォントを指定
        $this->__pdf->SetFont($this->getFont($option['font']), '', $option['size']);
        // 書き込む文字列の文字色を指定
        $concertColor = $this->colorCodeConvert($option['color']);
        $this->__pdf->SetTextColor($concertColor['r'], $concertColor['g'], $concertColor['b']);

        $this->__pdf->SetXY($option['x'], $option['y']);
        // 文字列を書き込む
        $this->__pdf->Cell($option['w'], $option['h'], $text, $option['border'], 0, $option['align'], $option['fill'], $option['link'], $option['stretch']);
    }

    /**
    * setHtml
    *
    * @param string $html HTML
    * @param array $option オプション
    * @author hagiwara
    */
    public function setHtml($html, $option)
    {
        $default_option = [
            'w' => 0,
            'h' => 0,
            'border' => 0,
            'align' => '',
            'fill' => false,
            'link' => '',
            'x' => 0,
            'y' => 0,
            'color' => '000000',
            'font' => '',
            'size' => '',
            'reseth' => true,
            'autopadding' => false,
        ];
        $option = array_merge($default_option ,$option);
        // 書き込む文字列の文字色を指定
        //$concertColor = $this->colorCodeConvert($option['color']);
        //var_dump($concertColor);
        //$this->__pdf->SetTextColor($concertColor['r'], $concertColor['g'], $concertColor['b']);

        // 書き込む文字列のフォントを指定
        $this->__pdf->SetFont($this->getFont($option['font']), '', $option['size']);
        
        $this->__pdf->writeHTMLCell( $option['w'], $option['h'], $option['x'], $option['y'], $html, $option['border'], 0, $option['fill'], $option['reseth'], $option['align'], $option['autopadding']);
    }

    /**
    * getFont
    *
    * @param string $font フォント名
    * @author hagiwara
    */
    private function getFont($font)
    {
        if (array_key_exists($font, $this->__fonts)) {
            return $this->__fonts[$font];
        } else {
            return $font;
        }
    }

    /**
    * setImage
    *
    * @param string $image 画像パス
    * @param array $option オプション
    * @author hagiwara
    */
    public function setImage($image, $option)
    {
        $default_option = [
            'x' => 0,
            'y' => 0,
            'w' => 0,
            'h' => 0,
            'link' => '',
            'resize' => true,
            'dpi' => '300',
        ];
        $option = array_merge($default_option ,$option);
        $this->__pdf->Image($image, $option['x'], $option['y'], $option['w'], $option['h'], '', $option['link'], '', $option['resize'], $option['dpi']);
    }


    /**
    * colorCodeConvert
    *
    * @param string $color カラーコード(16進数)
    * @author hagiwara
    */
    private function colorCodeConvert($color)
    {
        if (
            preg_match('/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/', $color, $colorCheck)
        ) {
            return [
                'r' => hexdec($colorCheck[1]),
                'g' => hexdec($colorCheck[2]),
                'b' => hexdec($colorCheck[3]),
            ];
        } else {
            return [
                'r' => 0,
                'g' => 0,
                'b' => 0,
            ];
        }
    }
    
   /**
    * getStringWidth
    *
    * @param string $text テキスト
    * @param string $font フォント名
    * @param string $fontstyle フォントスタイル
    * @param integer $fontsize サイズ
    * @param bool $getarray 結果を1文字ずつ配列で返すか
    * @author hagiwara
    */
    public function getStringWidth($text, $font, $fontstyle, $fontsize, $getarray = false) {
        return $this->__pdf->GetStringWidth( $text, $font, $fontstyle, $fontsize, $getarray);
    }

    /**
    * write
    *
    * @param string $file 出力ファイル
    * @author hagiwara
    */
    public function write($file)
    {
        $pdf_info = $this->__pdf->Output(null, 'S');

        $fp = fopen($file, 'w');
        fwrite($fp ,$pdf_info);
        fclose($fp);
    }

}
