<?php

namespace Abnermouke\EasyBuilder\Library\Currency;

use Illuminate\Support\Arr;

/**
 * Easy Builder Fake UserAgent Library Power By Abnermouke
 * Class FakeUserAgentLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class FakeUserAgentLibrary
{
    /**
     * 获取随机UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 14:08:19
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function random()
    {
        //获取UA信息
        $userAgents = self::getFakeUserAgent();
        //随机获取设备
        $browser = Arr::random(array_values(data_get($userAgents, 'randomize', [])));
        //获取指定浏览器UA
        return self::browser($browser);
    }

    /**
     * 指定Chrome访问UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 15:10:45
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function chrome()
    {
        //指定 Chrome UA
        return self::browser('chrome', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
    }

    /**
     * 指定Opera访问UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 15:13:06
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function opera()
    {
        //指定 Opera UA
        return self::browser('opera', 'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16');
    }

    /**
     * 指定Firefox访问UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 15:13:09
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function firefox()
    {
        //指定 Firefox UA
        return self::browser('firefox', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
    }

    /**
     * 指定Internetexplorer访问UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 15:13:13
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function internetexplorer()
    {
        //指定 Internetexplorer UA
        return self::browser('internetexplorer', 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko');
    }

    /**
     * 指定Safari访问UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 15:13:17
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function safari()
    {
        //指定 Safari UA
        return self::browser('safari', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A');
    }

    /**
     * 获取指定浏览器UA
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 14:08:26
     * @param string $browser 'chrome', 'opera', 'firefox', 'internetexplorer', 'safari'
     * @param string $default 默认返回UA
     * @return array|mixed|string
     * @throws \Exception
     */
    protected static function browser($browser = 'chrome', $default = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36')
    {
        //获取UA信息
        $userAgents = self::getFakeUserAgent();
        //获取全部UA
        $userAgents = data_get($userAgents, 'browsers.'.$browser, []);
        //判断是否存在UA
        if ($userAgents) {
            //获取一条并设为默认
            $default = Arr::random($userAgents);
        }
        //返回默认UA
        return $default;
    }

    /**
     * 获取伪造UA信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 14:05:43
     * @return mixed
     * @throws \Exception
     */
    private static function getFakeUserAgent()
    {
        //获取json信息
        $ua = file_get_contents(__DIR__.'/../../../data/fake_useragent.json');
        //初始化信息
        return json_decode($ua, true);
    }

}
