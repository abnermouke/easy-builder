<?php

namespace Abnermouke\EasyBuilder\Library\Currency;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Easy Builder Logger Library Power By Abnermouke
 * Class LoggerLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class LoggerLibrary
{
    /**
     * 日志记录
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-09-05 12:57:58
     * @param $alias_title string 处理标示
     * @param $message mixed 处理信息｜提示
     * @param string $path 日志路径
     * @param int $level 日志记录级别
     * @return bool
     * @throws \Exception
     */
    public static function logger($alias_title, $message, $path = '', $level = Logger::INFO)
    {
        //重命名日志记录
        $log = new Logger(strtoupper($alias_title));
        //初始化日志路径
        $path = $path ? $path : ('logs/logger/'.str_replace(' ', '-', strtolower($alias_title)).'/'.date('Y-m').'/'.date('d').'.log');
        //设置日志记录目录
        $log->pushHandler((new StreamHandler(storage_path($path))), $level);
        //写入分隔符
        $log->debug('============================BEGIN '.strtoupper($alias_title).'=========================');
        //记录请求参数
        $log->info(is_array($message) ? json_encode($message) : $message);
        //写入分隔符
        $log->debug('============================END '.strtoupper($alias_title).'=========================');
        //返回成功
        return true;
    }

    /**
     * 记录日志信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-09-05 13:05:02
     * @param string $alias_title 处理标示
     * @param mixed $message 处理信息｜提示
     * @param string $path 日志路径
     * @param int $length_limit 限制每行记录长度
     * @return bool
     * @throws \Exception
     */
    public static function record($alias_title, $message, $path = '', $length_limit = 250)
    {
        //初始化日志目录
        $path = storage_path($path ? $path : ('logs/records/'.str_replace(' ', '-', strtolower($alias_title)).'/'.date('Y-m').'/'.date('d').'.log'));
        //判断目录是否存在
        if (!File::isDirectory(dirname($path))) File::makeDirectory(dirname($path), 0777, true);
        //判断文件是否存在
        if (!File::exists($path)) File::put($path, '');
        //判断文件内容
        if (!is_string($message)) $message = json_encode($message);
        //取消根目录信息
        $message = str_replace(storage_path(), 'PROJECT_PATH', $message);
        //判断长度
        $message = (int)$length_limit > 0 ? Str::limit($message, (int)$length_limit, '...') : $message;
        //打开文件
        $record = fopen($path, 'a+');
        //写入文件
        fwrite($record, date('Y-m-d H:i:s').'  '.$message."\n");
        //关闭处理
        fclose($record);
        //返回成功
        return true;
    }

}
