<?php

namespace Abnermouke\EasyBuilder\Module;

use Illuminate\Support\Facades\Cache;

/**
 * Easy Builder Basic Cache Handler Module Power By Abnermouke
 * Class BaseCacheHandler
 * @package Abnermouke\EasyBuilder\Module
 */
class BaseCacheHandler extends BaseHandler
{

    // cache name
    protected $cache_name = '';

    // cache expire seconds
    protected $expire_seconds = 0;

    //cache result data
    protected $cache = [];

    //cache driver
    protected $driver = 'file';

    //cache env
    protected $env = false;

    //cache locale
    protected $locale = false;

    /**
     * Easy Builder Basic Cache Handler Construct
     * BaseCacheHandler constructor.
     * @param $cache_name string Cache name
     * @param $expire_seconds int Cache expire seconds
     * @param $driver string Cache driver
     * @param $env mixed Cache env
     * @param $locale mixed Cache Locale
     */
    public function __construct($cache_name, $expire_seconds, $driver, $env = false, $locale = false)
    {
        //init cache env
        $this->env = $env ? $env : config('app.env', 'production');
        //init cache locale
        $this->locale = $locale ? $locale : config('app.locale', 'zh-cn');
        // init cache name
        $this->cache_name = $this->env.':'.$this->locale.':'.$cache_name;
        //init cache expire seconds
        $this->expire_seconds = (int)$expire_seconds;
        //init cache driver
        $this->driver = $driver && !empty($driver) ? $driver : config('cache.default');
        //init read cache
        $this->read();
    }

    /**
     * Easy Builder Basic Cache Handler to save cache data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:15:43
     * @return bool
     * @throws \Exception
     */
    protected function save()
    {
        //init cache
        $cache = [
            'expire_time' => (int)$this->expire_seconds > 0 ? (time() + (int)$this->expire_seconds) : 0,
            'data' => $this->cache
        ];
        //save cache
        return Cache::store($this->driver)->forever($this->cache_name, $cache);
    }

    /**
     * Easy Builder Basic Cache Handler to clear cache data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:15:49
     * @return bool
     * @throws \Exception
     */
    public function clear()
    {
        //clear current cache
        $this->cache = [];
        //store current cache
        return $this->save();
    }

    /**
     * Easy Builder Basic Cache Handler to get cache data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:15:59
     * @param string $index
     * @param bool $default
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function get($index = '', $default = false)
    {
        // without cache
        if (!$this->cache) {
            //return default value
            return $default;
        }
        //check cache format
        if (!is_array($this->cache) || empty($index)) {
            //return cache
            return $this->cache;
        }
        //get cache
        return !empty($this->cache) && !empty($index) && isset($this->cache[$index]) ? $this->cache[$index] : $default;
    }

    /**
     * Easy Builder Basic Cache Handler to forget cache data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:16:08
     * @return bool
     * @throws \Exception
     */
    public function forget()
    {
        //delete cache
        return Cache::store($this->driver)->forget($this->cache_name);
    }

    /**
     * Easy Builder Basic Cache Handler to read cache data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:16:17
     * @return array|mixed
     * @throws \Exception|\Psr\SimpleCache\InvalidArgumentException
     */
    public function read()
    {
        // set default cache
        $this->cache = [];
        //isset cache
        if (Cache::store($this->driver)->has($this->cache_name)) {
            //get cache
            $cache = Cache::store($this->driver)->get($this->cache_name);
            //check cache expired
            if ((int)$cache['expire_time'] > 0 && (int)$cache['expire_time'] <= time()) {
                //set cache to null
                $cache['data'] = [];
            }
            //set cache value
            $this->cache = $cache['data'];
        }
        //return cache
        return $this->cache;
    }
}
