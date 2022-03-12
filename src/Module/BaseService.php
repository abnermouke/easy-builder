<?php

namespace Abnermouke\EasyBuilder\Module;

use Abnermouke\EasyBuilder\Library\CodeLibrary;

/**
 * Easy Builder Basic Service Module Power By Abnermouke
 * Class BaseService
 * @package Abnermouke\EasyBuilder\Module
 */
class BaseService
{

    // Laravel builder base service logic state
    private $state = false;

    // Laravel builder base service logic result data
    private $result = [];

    // Laravel builder base service logic message
    private $msg = '';

    // Laravel builder base service logic code
    private $code = 0;

    // Laravel builder base service logic extra data
    private $extra = [];

    // Laravel builder base service logic pass result
    private $pass = false;

    /**
     * Laravel builder base service construct
     * BaseService constructor.
     * @param bool $pass
     */
    public function __construct($pass = false)
    {
        //配置是否直接返回
        $this->pass = (bool)$pass;
    }

    /**
     * Laravel builder base service set return a logic result strict
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-06 11:04:16
     * @param bool $pass
     * @return $this
     * @throws \Exception
     */
    public function pass($pass = false)
    {
        //设置是否直接返回
        $this->pass = (bool)$pass;
        //返回当前实例
        return $this;
    }

    /**
     * Laravel builder base service return a logic result
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:54:47
     * @return array
     * @throws \Exception
     */
    public function getResult()
    {
        //返回结果集
        return $this->result;
    }

    /**
     * Laravel builder base service return a logic code
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:54:40
     * @return int
     * @throws \Exception
     */
    public function getCode()
    {
        //返回操作编码
        return (int)$this->code;
    }

    /**
     * Laravel builder base service return a logic message
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:54:25
     * @return string
     * @throws \Exception
     */
    public function getMessage()
    {
        //返回错误信息
        return $this->msg;
    }

    /**
     * Laravel builder base service return a extra data
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:54:15
     * @return array
     * @throws \Exception
     */
    public function getExtra()
    {
        //返回额外参数
        return $this->extra;
    }

    /**
     * Laravel builder base service return a logic state
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:53:58
     * @return bool
     * @throws \Exception
     */
    public function getState()
    {
        //返回处理状态
        return (bool)$this->state;
    }

    /**
     * Laravel builder base service return a success response
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:53:19
     * @param array $result Result data
     * @param array $extra Extra data
     * @return array|bool
     * @throws \Exception
     */
    protected function success($result = [], $extra = [])
    {
        //设置成功
        $this->code = CodeLibrary::CODE_SUCCESS;
        //设置处理状态
        $this->state = true;
        //设置结果集
        $this->result = $result;
        //设置额外参数
        $this->extra = $extra;
        //返回结果
        return $this->pass ? $this->getResult() : true;
    }

    /**
     * Laravel builder base service return a failed response
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 15:51:16
     * @param int $code Logic code
     * @param string $message Error message
     * @param array $extra Extra data
     * @return bool
     * @throws \Exception
     */
    protected function fail($code = CodeLibrary::CODE_ERROR, $message = '', $extra = [])
    {
        //设置处理状态
        $this->state = false;
        //设置处理编码
        $this->code = (int)($code);
        //设置提示信息
        $this->msg = $message;
        //设置额外参数
        $this->extra = $extra;
        //返回结果
        return false;
    }

}
