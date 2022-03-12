<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

namespace App\Repository{__DICTIONARY__};

use App\Model{__DICTIONARY__}\{__CASE_NAME__};
use Abnermouke\LaravelBuilder\Module\BaseRepository;

/**
 * {__DATA_NAME__}信息数据仓库 for table [{__DB_CONNECTION__}:{__DB_PREFIX__}{__NAME__}]
 * Class {__LOWER_CASE_NAME__}Repository
 * @package App\Repository
*/
class {__LOWER_CASE_NAME__}Repository extends BaseRepository
{
    /**
     * 构造函数
     * {__LOWER_CASE_NAME__}Repository constructor.
     * @throws \Exception
    */
    public function __construct()
    {
        //实例化模型
        $model = new {__CASE_NAME__}();
        //引入父级构造函数
        parent::__construct($model, {__CASE_NAME__}::DB_CONNECTION);
    }

}
