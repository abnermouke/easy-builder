<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

namespace App\Model{__DICTIONARY__};

use Abnermouke\LaravelBuilder\Module\BaseModel;

/**
 * {__TABLE_NAME__}
 * Class {__CASE_NAME__}
 * @package App\Model{__DICTIONARY__}
*/
class {__CASE_NAME__} extends BaseModel
{
    //设置表名
    protected $table = self::TABLE_NAME;

    //定义表链接信息
    protected $connection = '{__DB_CONNECTION__}';

    //定义表名
    public const TABLE_NAME = '{__DB_PREFIX__}{__NAME__}';

    //定义表链接信息
    public const DB_CONNECTION = '{__DB_CONNECTION__}';

    //类型分组解释信息
    public const TYPE_GROUPS = [
        //是否选择
        '__switch__' => [self::SWITCH_ON => '是', self::SWITCH_OFF => '不是'],
        //默认状态
        '__status__' => [self::STATUS_ENABLED => '正常启用', self::STATUS_DISABLED => '禁用中', self::STATUS_VERIFYING => '审核中', self::STATUS_VERIFY_FAILED => '审核失败', self::STATUS_DELETED => '已删除'],

        //

    ];
}
