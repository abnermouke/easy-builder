<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: Abnermouke <abnermouke>
 * Originate in YunniTec.
 */

use Abnermouke\LaravelBuilder\Library\CodeLibrary;

if (!function_exists('responseService')) {
    /**
     * 响应逻辑服务结果
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-06-03 11:27:37
     * @param $service \Abnermouke\LaravelBuilder\Module\BaseService 逻辑服务对象
     * @param string $format 返回类型
     * @param string $callback 回调信息
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function responseService($service, $format = 'json', $callback = '')
    {
        //判断信息
        if ($service->getState()) {
            //响应成功
            return responseSuccess($service->getResult(), $service->getMessage(), $service->getExtra(), $format, $callback);
        }
        //响应失败
        return responseError($service->getCode(), [], $service->getMessage(), $service->getExtra(), $format, $callback);
    }
}

if (!function_exists('responseError')) {
    /**
     * 响应失败
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-06-03 11:21:52
     * @param int $code 响应状态码
     * @param array $data 相应数据
     * @param string $msg 提示信息
     * @param array $extra 额外参数
     * @param string $format 返回类型
     * @param string $callback 回调信息
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function responseError($code, $data = [], $msg = '', $extra = [], $format = 'json', $callback = '')
    {
        //判断提示信息
        if (!$msg || empty($msg)) {
            //根据状态码获取提示信息
            $msg = '[ERROR:'.(int)$code.']';
        }
        //判断是否存在验证错误
        if (data_get($extra, 'validations', false)) {
            //设置错误
            $code = \Abnermouke\LaravelBuilder\Library\CodeLibrary::VALIDATE_FAILED;
        }
        //响应返回信息
        return responseReturn((int)$code, $msg, $data, $extra, $format, $callback);
    }
}

if (!function_exists('responseSuccess')) {
    /**
     * 响应成功
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-06-03 11:21:52
     * @param array $data 相应数据
     * @param string $msg 提示信息
     * @param array $extra 额外参数
     * @param string $format 返回类型
     * @param string $callback 回调信息
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function responseSuccess($data = [], $msg = '', $extra = [], $format = 'json', $callback = '')
    {
        //响应返回信息
        return responseReturn(\Abnermouke\LaravelBuilder\Library\CodeLibrary::CODE_SUCCESS, $msg, $data, $extra, $format, $callback);
    }
}

if (!function_exists('responseReturn')) {
    /**
     * 相应返回信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-06-03 11:14:23
     * @param $code int 状态码
     * @param $msg string 提示信息
     * @param $data array|mixed 返回数据
     * @param array $extra 额外参数
     * @param string $format 返回类型
     * @param string $callback 回调信息
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function responseReturn($code, $msg, $data, $extra = [], $format = 'json', $callback = '')
    {
        //默认处理状态
        $state = (int)$code === CodeLibrary::CODE_SUCCESS;
        //整理基础返回数据
        $result = compact('state', 'code', 'msg', 'data');
        //判断额外的数据返回
        if ($extra && is_array($extra)) {
            //整理返回数据
            $result = array_merge($result, $extra);
        }
        //添加基础数据
        $result['locale'] = config('app.locale');
        //获取逻辑请求记录时间
        $logic_request_log_time = (int)request()->logic_request_log_time;
        //判断信息
        if ((int)$logic_request_log_time > 0) {
            //设置基础数据
            $result['consuming'] = (((int)(microtime(true)*1000) - (int)$logic_request_log_time)).' ms';
        }
        //根据返回类型处理信息
        switch ($format) {
            case 'jsonp':
                //jsonp返回信息
                $response = response()->jsonp($callback, $result);
                break;
            default:
                //初始化返回信息
                $response = response()->json($result);
                break;
        }
        //返回响应结果
        return $response;
    }
}
