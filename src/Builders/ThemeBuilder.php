<?php


namespace Abnermouke\EasyBuilder\Builders;

use Abnermouke\EasyBuilder\Module\BaseModel;

/**
 * 构建器主题配置器
 * Class ThemeBuilder
 * @package Abnermouke\EasyBuilder\Builders
 */
class ThemeBuilder
{

    // 默认switch主题
    public const DEFAULT_SWITCH_THEME = [
        //开启 -> 成功
        BaseModel::SWITCH_ON => 'success',
        //关闭 -> 警告
        BaseModel::SWITCH_OFF => 'warning',
    ];

    // 默认status主题
    public const DEFAULT_STATUS_THEME = [
        // 正常启用 -> 成功
        BaseModel::STATUS_ENABLED => 'success',
        // 禁用中 -> 错误
        BaseModel::STATUS_DISABLED => 'danger',
        // 审核中 -> 一般
        BaseModel::STATUS_VERIFYING => 'primary',
        // 审核失败 ->  信息
        BaseModel::STATUS_VERIFY_FAILED => 'info',
        // 已删除 -> 其他
        BaseModel::STATUS_DELETED => 'dark',
    ];




}
