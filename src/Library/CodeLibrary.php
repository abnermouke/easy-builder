<?php

namespace Abnermouke\EasyBuilder\Library;

/**
 * Easy Builder Code Library Power By Abnermouke
 * Class CodeLibrary
 * @package Abnermouke\EasyBuilder\Library
 */
class CodeLibrary
{

    # 核心编码库 0 - 499

    // 处理失败
    public const CODE_ERROR = 0;
    //处理成功
    public const CODE_SUCCESS = 200;
    //表单验证失败
    public const VALIDATE_FAILED = 403;
    //未知错误
    public const UNKNOWN = 404;

    # 数据错误编码

    //数据不存在
    public const DATA_MISSING = 300;
    //数据不允许被创建
    public const DATA_CAN_NOT_BE_CREATE = 301;
    //数据不允许被更新
    public const DATA_CAN_NOT_BE_UPDATE = 302;
    //数据不允许被删除
    public const DATA_CAN_NOT_BE_DELETE = 303;
    //网络错误，创建失败
    public const DATA_CREATE_FAIL = 304;
    //网络错误，更新失败
    public const DATA_UPDATE_FAIL = 305;
    //网络错误，删除失败
    public const DATA_DELETE_FAIL = 306;
    //数据已存在
    public const DATA_EXISTS = 307;
    //缺失必要参数
    public const MISSING_REQUIRE_PARAM = 308;
    //请求参数验证失败
    public const REQUEST_PARAM_VERIFY_FAILED = 309;

    # 权限编码

    //暂无相关权限
    public const MISSING_PERMISSION = 400;
    //不允许的处理状态
    public const WITH_DO_NOT_ALLOW_STATE = 401;
    //权限超时
    public const PERMISSION_EXPIRED = 402;

    # 逻辑处理错误

    //逻辑错误
    public const CODE_LOGIC_ERROR = 500;
    //网络错误
    public const NETWORK_ERROR = 501;
    //系统繁忙
    public const TOO_MANY_REQUEST = 502;

    # 数据请求错误

    //请求失败
    public const GUZZLE_HTTP_REQUEST_FAIL = 700;
    //请求错误
    public const GUZZLE_HTTP_REQUEST_ERROR = 701;
    //无效请求签名
    public const INVALID_REQUEST_SIGNATURE = 702;
    //请求超时
    public const REQUEST_EXPIRED = 703;
    //非法请求参数
    public const ILLEGAL_REQUEST_PARAMETER = 704;

    # 文件错误

    //文件不存在
    public const FILE_MISSING = 900;
    //文件已存在
    public const FILE_EXISTS = 901;
    //文件移动失败
    public const FILE_MOVE_FAILED = 902;
    //文件目录不存在
    public const FILE_DICTIONARY_MISSING = 903;
    //文件目录已存在
    public const FILE_DICTIONARY_EXISTS = 904;
    //文件不允许写入
    public const FILE_CAN_NOT_BE_WRITE = 905;
    //文件无操作权限
    public const FILE_WITHOUT_PERMISSION = 906;
    //文件资源不存在
    public const FILE_RESOURCE_UNDEFINED = 907;

}
