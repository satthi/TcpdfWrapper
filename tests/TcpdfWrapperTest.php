<?php
namespace TcpdfWrapper\Test;

use PHPUnit\Framework\TestCase;
use TcpdfWrapper\TcpdfWrapper;

require_once('./vendor/autoload.php');

class TcpdfWrapperTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        //ディレクトリの作成
        $this->__tmpDir = dirname(dirname(__FILE__)) . '/tmp';
        if (!is_dir($this->__tmpDir)) {
            mkdir($this->__tmpDir);
        }

        //出力ファイルがいたら削除
        $this->__exportFile = $this->__tmpDir . '/export.pdf';
        if (file_exists($this->__exportFile)) {
            unlink($this->__exportFile);
        }
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test test_makePDF
     * 値がうまく設置されたかなどのテストは難しいがとりあえずファイルが生成されるところまでテスト
     *
     * @return void
     */
    public function test_makepdf()
    {
        $this->assertFalse(file_exists($this->__exportFile));

        $setImagePath = dirname(__FILE__) . '/file/test.png';
        $templateFile = dirname(__FILE__) . '/file/template.pdf';

        $TcpdfWrapper = new TcpdfWrapper();
        $TcpdfWrapper->setPrintHeader(false);
        $TcpdfWrapper->setPrintFooter(false);

        //1ページ目
        $TcpdfWrapper->addPage($templateFile, 1);
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
            'font' => 'kozgopromedium',
            'size' => 30,
        ];
        $TcpdfWrapper->setVal('山田　太郎', $option);

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

        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'R',
            'fill' => false,
            'link' => '',
            'x' => 40,
            'y' => 89,
            'color' => '000000',
            'font' => 'kozgopromedium',
            'size' => 11,
        ];
        $TcpdfWrapper->setVal('090-0000-0000', $option);
        $option = [
            'x' => 55,
            'y' => 110,
            'w' => 30,
            'link' => 'http://fusic.co.jp',
            'resize' => true,
            'dpi' => '300',
        ];
        $TcpdfWrapper->setImage($setImagePath, $option);
        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'L',
            'fill' => false,
            'x' => 40,
            'y' => 160,
            'color' => '000000',
            'font' => 'kozgopromedium',
            'size' => 11,
        ];
        $TcpdfWrapper->setHtml('<p>備考備考hogehoge</p>', $option);

        //2ページ目
        $TcpdfWrapper->addPage($templateFile, 1);
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
            'font' => 'kozgopromedium',
            'size' => 30,
        ];
        $TcpdfWrapper->setVal('山田　次郎', $option);

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

        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'R',
            'fill' => false,
            'link' => '',
            'x' => 40,
            'y' => 89,
            'color' => '000000',
            'font' => 'kozgopromedium',
            'size' => 11,
        ];
        $TcpdfWrapper->setVal('090-0000-0000', $option);
        $option = [
            'x' => 55,
            'y' => 110,
            'w' => 30,
            'link' => 'http://fusic.co.jp',
            'resize' => true,
            'dpi' => '300',
        ];
        $TcpdfWrapper->setImage($setImagePath, $option);
        
        $option = [
            'w' => 200,
            'h' => 0,
            'border' => 0,
            'align' => 'L',
            'fill' => false,
            'x' => 40,
            'y' => 160,
            'color' => '000000',
            'font' => 'kozgopromedium',
            'size' => 11,
        ];
        $TcpdfWrapper->setHtml('<p>備考備考hogehoge222</p>', $option);
        $TcpdfWrapper->write($this->__exportFile);
        

        $this->assertTrue(file_exists($this->__exportFile));
    }

}
