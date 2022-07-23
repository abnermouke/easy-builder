<?php


namespace Abnermouke\EasyBuilder\Library\Currency;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

/**
 * Easy Builder Storage File Library Power By Abnermouke
 * Class FileLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class StorageFileLibrary
{
    /**
     * 文件状态检测
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-15 02:35:43
     * @param $storage_name string 文件路径
     * @param string $storage_disk 驱动
     * @param false $force_clear 是否清除可能已存在项
     * @return array
     */
    public static function check($storage_name, $storage_disk = 'public', $force_clear = false)
    {
        //获取目录信息
        $storage_dir = dirname($storage_name);
        //判断目录是否存在
        if (!File::isDirectory(($storage_dir = Storage::disk($storage_disk)->path($storage_dir)))) {
            //创建目录
            File::makeDirectory($storage_dir, 0777, true);
        }
        //整理地址信息
        $storage_path = Storage::disk($storage_disk)->path($storage_name);
        //判断文件是否存在
        if ($force_clear && File::exists($storage_path)) {
            //删除源文件
            File::delete($storage_path);
        }
        //返回信息
        return compact('storage_name', 'storage_disk', 'storage_dir', 'storage_path');
    }

    /**
     * 移动文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-30 13:51:20
     * @param $form_storage_path string 原文件完整地址（需要移动的文件）
     * @param $to_storage_name string 目标文件storage名称
     * @param string $to_storage_disk 目标文件储存驱动
     * @param bool $force_merge 是否强制覆盖
     * @return bool
     */
    public static function move($form_storage_path, $to_storage_name, $to_storage_disk = 'public', $force_merge = true)
    {
        //整理地址信息
        $to_storage_path = Storage::disk($to_storage_disk)->path($to_storage_name);
        //判断原文件是否存在
        if (File::exists($form_storage_path)) {
            //检测文件状态
            self::check($to_storage_name, $to_storage_disk, $force_merge);
            //判断文件是否存在
            if (!File::exists($to_storage_path)) {
                //移动文件
                File::move($form_storage_path, $to_storage_path);
            }
        }
        //判断新文件是否存在
        return File::exists($to_storage_path);
    }

    /**
     * 移除文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-15 02:54:13
     * @param $storage_name string 文件路径
     * @param string $storage_disk 驱动
     * @return array
     */
    public static function remove($storage_name, $storage_disk = 'public')
    {
        //整理地址信息
        $storage_path = Storage::disk($storage_disk)->path($storage_name);
        //判断文件是否存在
        if (File::exists($storage_path)) {
            //删除源文件
            File::delete($storage_path);
        }
        //返回信息
        return compact('storage_name', 'storage_disk', 'storage_path');
    }

    /**
     * 同步至七牛
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-04-04 21:22:23
     * @param $storage_name string storage文件名称（含目录）
     * @param string $storage_disk 储存驱动
     * @param false $qiniu_config 七牛云配置
     * @param false $clear_location_file 上传成功后是否清除本地文件
     * @return false|string
     */
    public static function sync_to_qiniu($storage_name, $storage_disk = 'public', $qiniu_config = false, $clear_location_file = false)
    {
        //获取文件地址
        $file_path = Storage::disk($storage_disk)->path($storage_name);
        //生成链接
        $link = Storage::disk($storage_disk)->url($storage_name);
        //判断本地文件是否存在
        if (File::exists($file_path) && data_get($qiniu_config, 'access_key', '') && data_get($qiniu_config, 'access_secret', '') && data_get($qiniu_config, 'bucket', '') && data_get($qiniu_config, 'domain', '')) {
            //配置七牛信息
            $auth = new Auth(data_get($qiniu_config, 'access_key', ''), data_get($qiniu_config, 'access_secret', ''));
            //上传保存名
            $key = ($storage_disk !== 'public' ? ($storage_disk.'/') : '').$storage_name;
            //创建覆盖上传凭证
            $upToken = $auth->uploadToken(data_get($qiniu_config, 'bucket', 'abnermouke'), $key);
            //初始化上传
            $uploadMgr = new UploadManager();
            //上传信息
            list($ret, $err) = $uploadMgr->putFile($upToken, $key, $file_path);
            //判断是否上传成功
            if ($err !== null) {
                //返回失败
                return false;
            }
            //判断是否删除本地文件
            if ($clear_location_file) {
                //删除本地文件（可节约空间）
                File::delete($file_path);
            }
            //返回链接
            return $qiniu_config['domain'].'/'.$ret['key'];
        }
        //返回链接
        return $link;
    }

    /**
     * 删除七牛云文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-04-04 21:32:31
     * @param $storage_name string storage文件名称（含目录）
     * @param string $storage_disk 储存驱动
     * @param false $qiniu_config 七牛云配置
     * @param false $clear_location_file 上传成功后是否清除本地文件
     * @return bool
     */
    public static function deleteQiniu($storage_name, $storage_disk = 'public', $qiniu_config = false, $clear_location_file = false)
    {
        //获取文件地址
        $file_path = Storage::disk($storage_disk)->path($storage_name);
        //判断本地文件是否存在
        if (data_get($qiniu_config, 'access_key', '') && data_get($qiniu_config, 'access_secret', '') && data_get($qiniu_config, 'bucket', '') && data_get($qiniu_config, 'domain', '')) {
            //配置七牛信息
            $auth = new Auth(data_get($qiniu_config, 'access_key', ''), data_get($qiniu_config, 'access_secret', ''));
            //处理bucket
            $bucketManager = new BucketManager($auth, new Config());
            //上传保存名
            $key = ($storage_disk !== 'public' ? ($storage_disk.'/') : '').$storage_name;
            //删除文件
            if ($err = $bucketManager->delete(data_get($qiniu_config, 'bucket', 'abnermouke'), $key)) {
                //返回失败
                return false;
            }
            //判断是否删除本地文件
            if (File::exists($file_path) && $clear_location_file) {
                //删除本地文件（可节约空间）
                File::delete($file_path);
            }
            //返回成功
            return true;
        }
        //返回失败
        return false;
    }

}
