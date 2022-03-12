<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CreateFillingsTable extends Migration
{
    /**
     * 执行迁移
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @throws \Exception
     */
    public function up()
    {

        // TODO : 执行指定模块迁移
        //$this->migrate('dictionary_name', 'module_name');

    }

    /**
     * 执行表迁移操作
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $lower_alias string 小写标示（创建migration文件）
     * @param $module_alias string 模块标示（创建模块目录名称）
     * @throws \Exception
     */
    public function migrate($lower_alias, $module_alias)
    {
        //查询迁移文件
        $migrations = File::files(database_path('migrations/' . $lower_alias));
        //判断文件信息
        if (!empty($migrations)) {
            //循环迁移文案信息
            foreach ($migrations as $k => $migration) {
                //获取表名称
                if (preg_match('~create\_' . $lower_alias . '\_(.*)\_table\.php~Uuis',
                        $migration_file_name = $migration->getRelativePathname(), $matched) >= 1) {
                    //获取表名
                    $table_name = trim($matched[1]);
                    //获取Model
                    $model_class_name = 'App\\Model\\' . $module_alias . '\\' . (strstr($table_name, '___') ? implode('\\', array_map(function ($value) {
                        return Str::studly($value);
                    }, explode('___', $table_name))) : Str::studly($table_name));
                    //判断model是否存在
                    if (class_exists($model_class_name)) {
                        //引入model
                        $table_name_with_prefix = $model_class_name::TABLE_NAME;
                        //查询表是否存在
                        if (!Schema::connection($model_class_name::DB_CONNECTION)->hasTable($table_name_with_prefix)) {
                            //获取类名
                            $class_name = 'Create' . $module_alias . Str::studly($table_name) . 'Table';
                            //引入文件
                            require_once database_path('migrations/' . $lower_alias . '/' . $migration_file_name);
                            //判断是否存在类信息
                            if (class_exists($class_name)) {
                                //引入class
                                $class = new $class_name;
                                //迁移信息
                                $class->up();
                            }
                        }
                    }
                }
                //释放内存
                unset($migrations[$k]);
            }
        }
    }

    /**
     * 创建表信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $table_name string 操作表名
     * @param Closure $schema 处理方法
     * @param null $connection 指定数据库链接驱动
     * @return bool
     * @throws \Exception
     */
    public function createTable($table_name, \Closure $schema, $connection = null)
    {
        //判断是否存在该表
        if (!Schema::connection($connection)->hasTable($table_name)) {
            //执行运行
            $schema();
        }
        //返回成功
        return true;
    }

    /**
     * 添加/更新/删除表字段信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $table_name
     * @param $column_name string 操作字段名
     * @param Closure $schema
     * @param $mode string 操作方式 update：更新;insert：新增;remove：删除
     * @param null $connection 处理类型
     * @return bool
     * @throws \Exception
     */
    public function updateTableColumn($table_name, $column_name, \Closure $schema, $mode = 'insert', $connection = null)
    {
        //判断是否存在该表字段
        if (Schema::connection($connection)->hasTable($table_name)) {
            //根据类型判断
            switch ($mode) {
                case 'insert':
                    //判断字段是否存在
                    if (!Schema::connection($connection)->hasColumn($table_name, $column_name)) {
                        //执行运行
                        $schema();
                    }
                    break;
                default:
                    //判断字段是否存在
                    if (Schema::connection($connection)->hasColumn($table_name, $column_name)) {
                        //执行运行
                        $schema();
                    }
                    break;
            }
        }
        //返回成功
        return true;
    }

    /**
     * 删除表信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $table_name
     * @param $connection
     * @return bool
     * @throws \Exception
     */
    public function removeTable($table_name, $connection = null)
    {
        //删除表信息
        Schema::connection($connection)->dropIfExists($table_name);
        //返回成功
        return true;
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // do something...
    }
}
