<?php


namespace Abnermouke\EasyBuilder\Library\Currency;

use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Easy Builder Qr Library Power By Abnermouke
 * Class QrLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class QrLibrary
{

    /**
     * 创建二维码
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-15 02:39:13
     * @param $content string 二维码内容
     * @param $storage_name_with_direct string 储存路径
     * @param int $size 大小
     * @param false $force_merge 是否强制覆盖
     * @return array
     */
    public static function create($content, $storage_name_with_direct, $size = 200, $force_merge = false)
    {
        //检测路径状态
        $storage_info = StorageFileLibrary::check($storage_name_with_direct, 'public', $force_merge);
        //判断图片是否存在
        if (!File::exists($storage_info['storage_path']) || $force_merge) {
            //创建二维码
            QrCode::format('png')->size((int)$size)->encoding('UTF-8')->generate($content, $storage_info['storage_path']);
        }
        //返回信息
        return $storage_info;
    }

}
