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
    // 読み込みフォントファイルのパス
    private $fontFilePath;
    // フォントの設定キャッシュファイル出力先ディレクトリ
    private $fontSettingCacheFileOutDir = '';

    const TATEGAKI_TYPE_NORMAL = 1;
    const TATEGAKI_TYPE_ROUND = 2;
    const TATEGAKI_TYPE_RIGHT = 3;

    // publicにしておくので必要に応じて設定
    public $setTategakiType = [
        self::TATEGAKI_TYPE_ROUND => [
            'ー',
            '-',
            '＝',
            '=',
            '(',
            ')',
            '（',
            '）',
            '>',
            '<',
            '＞',
            '＜',
            '》',
            '《',
            '≫',
            '≪',
            '{',
            '｛',
            '}',
            '｝',
            '[',
            ']',
            '［',
            '］',
            '「',
            '」',
            '～',
            '~',
            '|',
            '｜',
            '『',
            '』',
            '【',
            '】',
            '〔',
            '〕',
            '‹',
            '›',
            '〖',
            '〗',
            '〚',
            '〛',
            '〘',
            '〙',
        ],
        self::TATEGAKI_TYPE_RIGHT => [
            'ぁ',
            'ぃ',
            'ぅ',
            'ぇ',
            'ぉ',
            'ゃ',
            'ゅ',
            'ょ',
            'っ',
            'ァ',
            'ィ',
            'ぅ',
            'ェ',
            'ォ',
            'ャ',
            'ュ',
            'ョ',
            'ッ',
            'ｧ',
            'ｨ',
            'ｩ',
            'ｪ',
            'ｫ',
            'ｬ',
            'ｭ',
            'ｮ',
            'ｯ',
            '、',
            '。',
            '.',
            ',',
        ],
    ];

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
     * setfontSettingCacheFileOutDir
     *
     * @param string $fontSettingCacheFileOutDir
     * @return void
     * @author kawano
     */
    public function setFontSettingCacheFileOutDir($fontSettingCacheFileOutDir)
    {
        $this->fontSettingCacheFileOutDir = $fontSettingCacheFileOutDir;
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
     * setFontFilePath
     * setFont($name, $path)で指定された$pathをプロパティにセット
     *
     * @param string $fontFilePath 読み込みフォントファイルのパス
     * @return void
     */
    private function setFontFilePath($fontFilePath)
    {
        $this->fontFilePath = $fontFilePath;
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
        $this->__fonts[$name] = $this->__tcpdfFonts->addTTFfont($path, '', '', 32, $this->fontSettingCacheFileOutDir);
        // 読み込むフォントファイルのパスを設定
        $this->setFontFilePath($path);
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
    * @param array $rotateOption 回転オプション(縦書き対応用)
    * @author hagiwara
    */
    public function setVal($text, $option, $rotateOption = [])
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
            'fstroke' => 0,
        ];
        $option = array_merge($default_option ,$option);
        
        // 太字のセット
        $concertColor = $this->colorCodeConvert($option['color']);
        $this->__pdf->SetDrawColor($concertColor['r'], $concertColor['g'], $concertColor['b']);
        $this->__pdf->setTextRenderingMode($option['fstroke']);

        // 自動で枠に収めるかどうかのチェック
        if ($option['auto_size'] == true) {
            $fontDefaultWidth = $this->getStringWidth($text, $option['font'], '', $option['size']);
            if ($fontDefaultWidth > $option['w']) {
                $option['align'] ='J';
                $option['stretch'] =1;
            }
        }

        // 書き込む文字列のフォントを指定（フォントの設定キャッシュファイルの出力先がセットされていない場合はデフォルト値）
        $fontSettingCacheFilePath = $this->generateFontSettingCacheFilePath($option['font']);
        $this->__pdf->SetFont($this->getFont($option['font']), '', $option['size'], $fontSettingCacheFilePath);
        // 書き込む文字列の文字色を指定
        $this->__pdf->SetTextColor($concertColor['r'], $concertColor['g'], $concertColor['b']);

        $this->__pdf->SetXY($option['x'], $option['y']);
        if (!empty($rotateOption)) {
            $default_rotate_option = [
                'angle' => 0,
                'x' => '',
                'y' => '',
            ];
            $rotateOption = array_merge($default_rotate_option ,$rotateOption);
            $this->__pdf->Rotate($rotateOption['angle'], $rotateOption['x'], $rotateOption['y']);
        }
        // 文字列を書き込む
        $this->__pdf->Cell($option['w'], $option['h'], $text, $option['border'], 0, $option['align'], $option['fill'], $option['link'], $option['stretch']);
        // 元に戻しておく
        if (!empty($rotateOption)) {
            $this->__pdf->Rotate($rotateOption['angle'] * -1, $rotateOption['x'], $rotateOption['y']);
        }
        // 太字も元に戻しておく
        $this->__pdf->setTextRenderingMode();
    }

    /**
    * setValTategaki
    * 縦書き対応/改行は対応しきれない。折り返しもしない
    *
    * @param string $text テキスト
    * @param array $option オプション
    * @author hagiwara
    */
    public function setValTategaki($text, $option)
    {
        $default_option = [
            'h' => 0,
            'border' => 0,
            'fill' => false,
            'link' => '',
            'x' => 0,
            'y' => 0,
            'color' => '000000',
            'font' => '',
            'size' => 11,
            'fstroke' => 0,
        ];
        $option = array_merge($default_option ,$option);

        $concertColor = $this->colorCodeConvert($option['color']);
        $this->__pdf->SetDrawColor($concertColor['r'], $concertColor['g'], $concertColor['b']);
        $this->__pdf->setTextRenderingMode($option['fstroke']);

        // 設定している固定の高さとする
        $wordHeight = $option['h'];
        // 文字の幅は対応する文字の一番幅の大きい文字とする
        $wordWidth = max($this->getStringWidth($text, $option['font'], '', $option['size'], true));
        $splitWord = preg_split("//u", $text, -1, PREG_SPLIT_NO_EMPTY);
        $top = $option['y'];
        foreach ($splitWord as $word) {
            // 一文字ことにオプションを設定
            $partsOption = $option;
            $partsOption['w'] = $wordWidth;
            $partsOption['h'] = $wordHeight;
            $partsOption['auto_size'] = false;
            $partsOption['align'] = 'C';
            $partsOption['stretch'] = '0';
            $partsOption['y'] = $top;

            // 縦書き対応
            $rotateOption = [];
            switch ($this->getTategakiWordType($word)) {
                // 回転が必要な文字
                case self::TATEGAKI_TYPE_ROUND:
                    $rotateOption = [
                        'angle' => -90,
                        'x' => $partsOption['x'] + ($partsOption['w'] * 0.5),
                        'y' => $partsOption['y'] + ($partsOption['h'] * 0.5),
                    ];
                    break;
                // 小さいゃゅょ、句読点を少し右寄せする
                case self::TATEGAKI_TYPE_RIGHT:
                    $partsOption['x'] += $partsOption['size'] * 0.05;
                    break;

                default:
                    break;
            }

            $this->setVal($word, $partsOption, $rotateOption);

            // 固定の高さ分文字幅を取る
            $top += $wordHeight;
        }
        // 太字も元に戻しておく
        $this->__pdf->setTextRenderingMode();
    }

    /**
    * getTategakiWordType
    * 縦書きに必要な種別の取得
    *
    * @param string $word テキスト
    * @return int
    * @author hagiwara
    */
    private function getTategakiWordType($word)
    {
        if (in_array($word, $this->setTategakiType[self::TATEGAKI_TYPE_ROUND], true)) {
            return self::TATEGAKI_TYPE_ROUND;
        } elseif (in_array($word, $this->setTategakiType[self::TATEGAKI_TYPE_RIGHT], true)) {
            return self::TATEGAKI_TYPE_RIGHT;
        } else {
            return self::TATEGAKI_TYPE_NORMAL;
        }
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

        // 書き込む文字列のフォントを指定（フォントの設定キャッシュファイルの出力先がセットされていない場合はデフォルト値）
        $fontSettingCacheFilePath = $this->generateFontSettingCacheFilePath($option['font']);
        $this->__pdf->SetFont($this->getFont($option['font']), '', $option['size'], $fontSettingCacheFilePath);
        
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
    * setImageSvg
    *
    * @param string $image 画像パス
    * @param array $option オプション
    * @author hagiwara
    */
    public function setImageSvg($image, $option)
    {
        $default_option = [
            'x' => 0,
            'y' => 0,
            'w' => 0,
            'h' => 0,
            'link' => '',
            'align' => '',
            'palign' => '',
            'border' => 0,
        ];
        $option = array_merge($default_option ,$option);
        $this->__pdf->ImageSVG($image, $option['x'], $option['y'], $option['w'], $option['h'], $option['link'], $option['align'], $option['palign'], $option['border']);
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
     * setAutoPageBreak
     * page brackeを自動で行うかどうか。画像を下部に埋め込む際には切っておいたほうが良さげ
     * @param int $auto
     * @param int $margin
     */
    public function setAutoPageBreak($auto, $margin = 0)
    {
        $this->__pdf->SetAutoPageBreak($auto, $margin);
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

    /**
     * フォント設定キャッシュファイルのパスを返す
     * $this->fontSettingCacheFileOutDir (上記ファイルの出力先ディレクトリ) を指定した場合のみ呼ばれる
     * [関数中のアルゴリズムで生成されたフォント名].php が作られているので、そのpathを返す
     * @param string $font フォント名
     * @return string
     * @author kawano
     */
    private function generateFontSettingCacheFilePath($font)
    {
        // フォントの設定キャッシュファイル出力先ディレクトリが未指定の場合
        if (empty($this->fontSettingCacheFileOutDir)) {
            return '';
        }

        // TCPDFの処理の互換性のために、設定キャッシュファイルには、以下（参照）のアルゴリズムで、元の名称から改めて名称が付け直されている
        // 参照： https://github.com/tecnickcom/TCPDF/blob/master/include/tcpdf_fonts.php#L79 〜 https://github.com/tecnickcom/TCPDF/blob/master/include/tcpdf_fonts.php#L92
        // そのため、設定キャッシュファイルは、同様のアルゴリズムで生成した上で、そのパスを指定する必要がある
        $fontPathParts = pathinfo($this->fontFilePath);
        if (!isset($fontPathParts['filename'])) {
            $fontPathParts['filename'] = substr($fontPathParts['basename'], 0, -(strlen($fontPathParts['extension']) + 1));
        }
        // ファイル名を生成
        $fontName = strtolower($fontPathParts['filename']);
        $fontName = preg_replace('/[^a-z0-9_]/', '', $fontName);
        $search  = array('bold', 'oblique', 'italic', 'regular');
        $replace = array('b', 'i', 'i', '');
        $fontName = str_replace($search, $replace, $fontName);
        // $fontNameが空であった場合、 "tcpdffont" という名称が設定キャッシュファイルに付けられている
        if (empty($fontName)) {
            return $this->fontSettingCacheFileOutDir . 'tcpdffont' . '.php';
        }

        return $this->fontSettingCacheFileOutDir . $fontName . '.php';
    }
}
