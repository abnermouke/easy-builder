<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

namespace App\Handler\Cache\Data{__DICTIONARY__};

use Abnermouke\LaravelBuilder\Module\BaseCacheHandler;
use App\Repository{__DICTIONARY__}\{__LOWER_CASE_NAME__}Repository;

/**
 * {__DATA_NAME__}数据缓存处理器
 * Class {__LOWER_CASE_NAME__}CacheHandler
 * @package App\Handler\Cache\Data{__DICTIONARY__}
 */
class {__LOWER_CASE_NAME__}CacheHandler extends BaseCacheHandler
{
    /**
     * 构造函数
     * {__LOWER_CASE_NAME__}CacheHandler constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        //引入父级构造
        parent::__construct('{__DATA_CACHE_NAME__}', {__DATA_CACHE_EXPIRE_SECOND__}, '{__DATA_CACHE_DRIVER__}');
        //初始化缓存
        $this->init();
    }

    /**
     * 刷新当前缓存
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @return array
     * @throws \Exception
    */
    public function refresh()
    {
        //删除缓存
        $this->clear();
        //初始化缓存
        return $this->init();
    }

    /**
     * 初始化缓存
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @return array
     * @throws \Exception
    */
    private function init()
    {
        //获取缓存
        $cache = $this->cache;
        //判断缓存信息
        if (!$cache || empty($this->cache)) {
            //引入Repository
            $repository = new {__LOWER_CASE_NAME__}Repository();

            //TODO : 初始化缓存数据

        }
        //返回缓存信息
        return $cache;
    }

}
