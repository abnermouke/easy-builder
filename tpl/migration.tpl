<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

use App\Model{__DICTIONARY__}\{__CASE_NAME__};
use App\Repository{__DICTIONARY__}\{__LOWER_CASE_NAME__}Repository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
* {__DATA_NAME__}数据迁移处理器
* Class Create{__MIGRATION_CASE_NAME_}Table
*/
class Create{__MIGRATION_CASE_NAME_}Table extends Migration
{
    /**
      * 开始{__DATA_NAME__}数据迁移操作
      * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
      * @Originate in {__ORIGINATE__}
      * @Time {__DATE__} {__TIME__}
      * @throws \Exception
    */
    public function up()
    {
        Schema::connection({__CASE_NAME__}::DB_CONNECTION)->create('{__DB_PREFIX__}{__NAME__}', function (Blueprint $table) {
            //设置字符集
            $table->charset = '{__CHARSET__}';
            $table->collation = '{__CHARSET__}_general_ci';
            //设置引擎
            $table->engine = '{__ENGINE__}';
            //配置字段
            $table->increments('id')->comment('表ID');

            //TODO : 其他字段配置

            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');

            //TODO : 索引配置

        });
        //添加表自增长值
        (new {__LOWER_CASE_NAME__}Repository())->setIncrementId(1, {__CASE_NAME__}::DB_CONNECTION);
        //修改表注释
        (new {__LOWER_CASE_NAME__}Repository())->setTableComment('{__TABLE_NAME__}', {__CASE_NAME__}::DB_CONNECTION);
        //设置默认数据
        $this->defaultData();
    }

    /**
      * 设置默认数据
      * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
      * @Originate in {__ORIGINATE__}
      * @Time {__DATE__} {__TIME__}
      * @return mixed
      * @throws \Exception
    */
    private function defaultData()
    {
        //引入{__LOWER_CASE_NAME__}Repository
        $repository = new {__LOWER_CASE_NAME__}Repository();

        // TODO : 默认数据处理逻辑

        //返回数据
        return true;
    }

    /**
      * 回滚{__DATA_NAME__}数据迁移操作
      * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
      * @Originate in {__ORIGINATE__}
      * @Time {__DATE__} {__TIME__}
      * @throws \Exception
    */
    public function down()
    {
        Schema::connection({__CASE_NAME__}::DB_CONNECTION)->dropIfExists('{__DB_PREFIX__}{__NAME__}');
    }
}
