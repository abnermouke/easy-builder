<?php

namespace Abnermouke\EasyBuilder\Library\Currency;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Easy Builder Picture Library Power By Abnermouke
 * Class PictureLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class PictureLibrary
{

    //图片实例
    private $image;

    //字体文件
    private $font_ttf;

    /**
     * 创建图片处理实例对象
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-09-13 14:28:15
     * @param $source_path
     * @param $ttf
     * @return PictureLibrary
     */
    public static function make($source_path, $ttf)
    {
        //实例化对象
        return new PictureLibrary($source_path, $ttf);
    }

    /**
     * 构造函数
     * PictureLibrary constructor.
     * @param $source_path
     */
    public function __construct($source_path, $ttf)
    {
        //生成图片实例
        $this->image = Image::make($source_path);
        //设置文字路径
        $this->ttf($ttf);
    }

    /**
     * 设置字体文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-09-13 13:48:13
     * @param $ttf_path
     * @return $this
     */
    public function ttf($ttf_path)
    {
        //设置字体文件路径
        $this->font_ttf = $ttf_path;
        //返回当前实例
        return $this;
    }

    /**
     * 写入水印图片
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-09-13 13:53:35
     * @param $picture 水印图片地址
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $x x坐标
     * @param int $y y坐标
     * @param string $position 写入位置
     * @return $this
     */
    public function drawl($picture, $width = 100, $height = 100, $x = 0, $y = 0, $position = 'top-left')
    {
        //整理水印图片
        $watermark = Image::make($picture)->resize($width, $height);
        //设置图片
        $this->image->insert($watermark, $position, $x, $y);
        //释放内存
        unset($watermark);
        //返回当前实例
        return $this;
    }

    /**
     * 写入文字水印
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-09-13 12:23:03
     * @param $string 文字内容
     * @param int $x x轴坐标
     * @param int $y y轴坐标
     * @param int $font_size 文字大小
     * @param string|bool $ttf 字体文件
     * @param string $color 颜色
     * @param string $align_position 水平位置
     * @param string $valigin_position 垂直位置
     * @return $this
     */
    public function text($string, $x = 0, $y = 0, $font_size = 20, $ttf = false, $color = '#000000', $align_position = 'left', $valigin_position = 'top')
    {
        //获取字体文件
        $ttf = $ttf ? $ttf : $this->font_ttf;
        //设置文字水印
        $this->image->text($string, $x, $y, function ($font) use ($font_size, $color, $ttf, $align_position, $valigin_position) {
            //设置文字属性
            $font->file($ttf);
            $font->size($font_size);
            $font->color($color);
            $font->align($align_position);
            $font->valign($valigin_position);
        });
        //返回当前实例
        return $this;
    }

    /**
     * 保存图片文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-09-13 13:59:50
     * @param $storage_name
     * @param int $quality
     * @param string $storage_disk
     * @return string
     */
    public function save($storage_name, $quality = 100, $storage_disk = 'public')
    {
        //存入文件
        $this->image->save(($storage_path = Storage::disk($storage_disk)->path($storage_name)), $quality);
        //释放内存
        $this->image = null;
        //返回成功
        return $storage_path;
    }



}
