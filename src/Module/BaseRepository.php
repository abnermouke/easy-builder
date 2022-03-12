<?php

namespace Abnermouke\EasyBuilder\Module;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Easy Builder Basic Repository Module Power By Abnermouke
 * Class BaseRepository
 * @package Abnermouke\EasyBuilder\Module
 */
class BaseRepository
{

    // default model object
    protected $default_model;

    // current model object
    protected $model;

    // current clean model object
    protected $clean_model;

    // current model connection
    protected $connection;

    // current model table name
    protected $table_name;

    // debug current model sql query
    protected $debug_sql = false;

    /**
     * Easy Builder Basic Repository Construct
     * BaseRepository constructor.
     * @param $model mixed current model object
     * @param null $connection current model connection
     * @throws \Exception
     */
    public function __construct($model, $connection = null)
    {
        // set current model and default model object
        $this->default_model = $model;
        // set current model table name
        $this->table_name = $this->default_model::TABLE_NAME;
        // set current model connection
        $this->setConnection($connection);
    }

    /**
     * set current model connection
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:22:59
     * @param null $connection current model connection
     * @return $this
     * @throws \Exception
     */
    public function setConnection($connection = null)
    {
        // set current model connection
        $this->connection = $connection;
        // set current model connection to current model object
        $this->model = $this->clean_model = $this->default_model::on($connection);
        // return this
        return $this;
    }

    /**
     * set current model sql query debug
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:23:50
     * @param bool $debug current model sql query debug
     * @return $this
     * @throws \Exception
     */
    public function setDebug($debug = true)
    {
        // debug current model sql query
        $this->debug_sql = $debug;
        // return this
        return $this;
    }

    /**
     * set current model object and table name
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:24:41
     * @param $table_name string current model table
     * @return $this
     * @throws \Exception
     */
    public function setTableName($table_name)
    {
        // check table name
        if ($table_name && !empty($table_name)) {
            // set current table name
            $this->table_name = $table_name;
            // set current model object and table name
            $this->model = $this->clean_model = $this->default_model->setTable($table_name);
        }
        // return this
        return $this;
    }

    /**
     * run a custom sql
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:25:52
     * @param $sql string custom sql
     * @param null $connection current model connection
     * @return bool
     * @throws \Exception
     */
    public function sqlStatement($sql, $connection = null)
    {
        // run a custom sql and return result
        return DB::connection($connection)->statement($sql);
    }

    /**
     * 转义判断值，防止SQL注入
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:30:41
     * @param $value string 储存内容
     * @return string
     * @throws \Exception
     */
    private function addslashesValue($value)
    {
        //判断类型
        return $value && !empty($value) && is_string($value) ? addslashes($value) : $value;
    }

    /**
     * 反转义系统值，避免错误
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-31 14:37:31
     * @param $value
     * @return string
     * @throws \Exception
     */
    private function stripslashesValue($value)
    {
        //判断类型
        return $value && !empty($value) && is_string($value) ? stripslashes($value) : $value;
    }

    /**
     * 反转移系统实例返回对象
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-17 00:21:43
     * @param $values
     * @return array|mixed
     * @throws \Exception
     */
    private function stripslashesResult($values)
    {
        //判断数据
        if ($values && !empty($values) && is_array($values)) {
            //循环数据
            foreach ($values as $field => $value) {
                //初始化数据
                $values[$field] = is_array($value) ? $this->stripslashesResult($value) : $this->stripslashesValue((is_null($value) ? '' : $this->checkJsonValue($value, true)));
            }
        } elseif (is_string($values)) {
            //设置反转义
            $values = $this->stripslashesValue($values);
        }
        //返回数据
        return $values;
    }

    /**
     * 检测是否为json字符串系统值
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-17 00:22:49
     * @param string $value
     * @param false $assoc
     * @return array|mixed|string
     * @throws \Exception
     */
    private function checkJsonValue($value = '', $assoc = false)
    {
        //反json数据
        $data = json_decode($value, $assoc);
        //判断是否为json字符串
        if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
            //返回反json数据
            return $data;
        }
        //返回愿数据
        return $value;
    }

    /**
     * 设置处理数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:31:53
     * @param $data array 处理数据
     * @return mixed
     * @throws \Exception
     */
    private function setData($data)
    {
        //循环数据
        foreach ($data as $field => $value)
        {
            //初始化数据信息
            $data[$field] = !is_array($value) ? $this->addslashesValue((is_null($value) ? '' : $value)) : json_encode($value);
        }
        //返回处理数据
        return $data;
    }

    /**
     * 设置查询字段
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:33:49
     * @param array $fields
     * @return array|false|string[]
     * @throws \Exception
     */
    private function setFields($fields = [])
    {
        //整理字段信息
        return $fields && !empty($fields) ? (is_string($fields) ? explode(',', $fields) : $fields) : ['*'];
    }

    /**
     * 设置查询条件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:03:56
     * @param $query mixed 请求实例
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    private function setConditions($query, $conditions = [])
    {
        //判断条件
        if (!$conditions || empty($conditions)) {
            //返回请求实例
            return $query;
        }
        //循环信息
        foreach ($conditions as $field => $condition) {
            //判断条件类型
            if (is_array($condition)) {
                //整理规则信息
                $rule = Arr::first($condition);
                //整理数据信息
                $value = Arr::last($condition);
            } else {
                //整理规则信息
                $rule = '=';
                //设置处理数据
                $value = $this->addslashesValue($condition);
            }
            //根据处理规则处理
            switch (strtolower($rule)) {
                case 'or':
                    //初始化或者条件
                    $query = $query->orWhere($field, $value);
                    break;
                case 'or-not-between':
                    //初始化orWhereNotBetween查询条件
                    $query = $query->orWhereNotBetween($field, $value);
                    break;
                case 'or-between':
                    //初始化orWhereBetween查询条件
                    $query = $query->orWhereBetween($field, $value);
                    break;
                case 'or-in':
                    //初始化orWhereIn查询条件
                    $query = $query->orWhereIn($field, $value);
                    break;
                case 'or-not-in':
                    //初始化orWhereNotIn查询条件
                    $query = $query->orWhereNotIn($field, $value);
                    break;
                case 'or-null':
                    //初始化orWhereNull查询条件
                    $query = $query->orWhereNull($field);
                    break;
                case 'or-not-null':
                    //初始化orWhereNotNull查询条件
                    $query = $query->orWhereNotNull($field);
                    break;
                case 'column':
                    //判断类型
                    if (is_string($value)) {
                        //初始化whereColumn比较两个字段的值是否相等
                        $query = $query->whereColumn($field, $value);
                    } else {
                        //初始化whereColumn比较两个字段的值
                        $query = $query->whereColumn($field, Arr::first($value), Arr::last($value));
                    }
                    break;
                case 'or-column':
                    if (is_string($value)) {
                        //初始化whereColumn比较两个字段的值是否相等
                        $query = $query->orWhereColumn($field, $value);
                    } else {
                        //初始化whereColumn比较两个字段的值
                        $query = $query->orWhereColumn($field, Arr::first($value), Arr::last($value));
                    }
                    break;
                case 'between':
                    //初始化whereBetween查询条件
                    $query = $query->whereBetween($field, $value);
                    break;
                case 'not-between':
                    //初始化whereNotBetween查询条件
                    $query = $query->whereNotBetween($field, $value);
                    break;
                case 'in':
                    //初始化whereIn查询条件
                    $query = $query->whereIn($field, $value);
                    break;
                case 'not-in':
                    //初始化whereNotIn查询条件
                    $query = $query->whereNotIn($field, $value);
                    break;
                case 'null':
                    //初始化whereNull查询条件
                    $query = $query->whereNull($field);
                    break;
                case 'not-null':
                    //初始化whereNotNull查询条件
                    $query = $query->whereNotNull($field);
                    break;
                case 'select-raw':
                    //初始化selectRaw查询条件
                    $query = $query->selectRaw($value);
                    break;
                case 'date':
                    //初始化whereDate查询条件
                    $query = $query->whereDate($field, $value);
                    break;
                case 'month':
                    //初始化whereMonth查询条件
                    $query = $query->whereMonth($field, $value);
                    break;
                case 'day':
                    //初始化whereDay查询条件
                    $query = $query->whereDay($field, $value);
                    break;
                case 'year':
                    //初始化whereYear查询条件
                    $query = $query->whereYear($field, $value);
                    break;
                case 'time':
                    //初始化whereTime查询条件
                    $query = $query->whereTime($field, '=', $value);
                    break;
                case 'columns':
                    //初始化whereColumn查询条件
                    $query = $query->whereColumn($value);
                    break;
                case 'raw':
                    //初始化whereRaw查询条件
                    $query = $query->whereRaw($value);
                    break;
                case 'like':
                    //初始化查询字段信息
                    $fields = explode('|', $field);
                    //整理字段信息
                    foreach ($fields as $k => $field) {
                        //判断是否存在表名
                        $field = strstr($field, '.') ? ("`".Arr::first(explode('.', $field))."`.`".Arr::last(explode('.', $field))."`") : "`".$field."`";
                        //设置字段信息
                        $fields[$k] = "trim(replace(".$field.", ' ', ''))";
                    }
                    //初始化whereRaw查询条件
                    $query = $query->whereRaw("concat(".implode(',', $fields).") like trim(replace('".$value."', ' ', ''))");
                    break;
                case 'not-like':
                    //初始化查询字段信息
                    $fields = explode('|', $field);
                    //整理字段信息
                    foreach ($fields as $k => $field) {
                        //判断是否存在表名
                        $field = strstr($field, '.') ? ("`".Arr::first(explode('.', $field))."`.`".Arr::last(explode('.', $field))."`") : "`".$field."`";
                        //设置字段信息
                        $fields[$k] = "trim(replace(".$field.", ' ', ''))";
                    }
                    //初始化whereRaw查询条件
                    $query = $query->whereRaw("concat(".implode(',', $fields).") not like trim(replace('".$value."', ' ', ''))");
                    break;
                case 'json':
                    //初始化where(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->where($field, $value);
                    break;
                case 'json-contains':
                    //初始化whereJsonContains(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonContains($field, $value);
                    break;
                case 'json-length':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, (int)($value));
                    break;
                case 'json-length-gt':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, '>', (int)($value));
                    break;
                case 'json-length-egt':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, '>=', (int)($value));
                    break;
                case 'json-length-lt':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, '<', (int)($value));
                    break;
                case 'json-length-elt':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, '<=', (int)($value));
                    break;
                case 'json-length-eq':
                    //初始化whereJsonLength(JSON 本特性仅支持 MySQL 5.7、PostgreSQL、SQL Server 2016 以及 SQLite 3.9.0)查询条件
                    $query = $query->whereJsonLength($field, '=', (int)($value));
                    break;
                default:
                    //初始化where查询条件
                    $query = $query->where($field, $rule, $value);
                    break;
            }
        }
        //返回实例对象
        return $query;
    }

    /**
     * 重置模型
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-29 19:22:39
     * @return mixed
     * @throws \Exception
     */
    private function model()
    {
        //重新设置模型
        $this->setConnection($this->connection);
        //返回模型对象
        return $this->model;
    }

    /**
     * 设置group规则
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:05:50
     * @param $query
     * @param string $group
     * @return mixed
     * @throws \Exception
     */
    private function setGroup($query, $group = '')
    {
        //判断group规则
        if (!$group && !empty($group)) {
            //设置group
            $query = $query->groupByRaw($group);
        }
        //返回实例对象
        return $query;
    }

    /**
     * 设置链接规则
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:51:01
     * @param $query mixed 请求实例
     * @param array $joins 链接方式
     * @return mixed
     * @throws \Exception
     */
    private function setJoins($query, $joins = [])
    {
        // 判断表链接相关信息
        if (!$joins || empty($joins)) {
            //返回请求实例
            return $query;
        }
        //循环表链接信息
        foreach ($joins as $join) {
            //获取链接规则：left right inner
            $join_rule = $join[0];
            //获取左表表名
            $join_left_table = $join[1];
            //获取左表关联字段
            $join_left_field = $join[2];
            //获取表链接条件
            $join_condition = $join[3];
            //获取右表表名与关联字段
            $join_right_field = $join[4];
            //判断处理方式
            switch (strtolower($join_rule)) {
                //执行左链接
                case 'left':
                    $query = $query->leftJoin($join_left_table, $join_left_field, $join_condition, $join_right_field);
                    break;
                //执行右链接
                case 'right':
                    $query = $query->rightJoin($join_left_table, $join_left_field, $join_condition, $join_right_field);
                    break;
                //执行inner链接
                default:
                    $query = $query->join($join_left_table, $join_left_field, $join_condition, $join_right_field);
                    break;
            }
        }
        //返回请求实例
        return $query;
    }

    /**
     * 设置排序信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:44:27
     * @param $query mixed 请求实例
     * @param array $orders 排序规则
     * @return mixed
     * @throws \Exception
     */
    private function setOrders($query, $orders = [])
    {
        //判断排序信息
        if (!$orders || empty($orders)) {
            //返回请求实例
            return $query;
        }
        //循环排序方式
        foreach ($orders as $field => $order) {
            //根据类型处理
            switch ($order) {
                case BaseModel::LATEST_ORDER_BY:
                    //执行倒序排序
                    $query = $query->latest();
                    break;
                case BaseModel::OLDEST_ORDER_BY:
                    //执行正序排序
                    $query = $query->oldest();
                    break;
                case BaseModel::RANDOM_ORDER_BY:
                    //执行随机排序
                    $query = $query->inRandomOrder();
                    break;
                case BaseModel::RAW_ORDER_BY:
                    //执行自定义排序
                    $query = $query->orderByRaw($field);
                    break;
                default:
                    //执行指定排序
                    $query = $query->orderBy($field, strtolower($order));
                    break;
            }
        }
        //返回请求实例
        return $query;
    }

    /**
     * 初始化结果集
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 16:42:41
     * @param $query_result mixed 结果集
     * @return mixed
     * @throws \Exception
     */
    private function setResult($query_result)
    {
        //初始化返回数据
        $query_result = $query_result && !empty($query_result) && is_object($query_result) ? json_decode($query_result, true) : $query_result;
        //初始化返回数据
        return $this->stripslashesResult($query_result);
    }

    /**
     * 生成唯一md5编码
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:33:05
     * @param string $secret 加密串
     * @param string $field 对比字段
     * @return string
     * @throws \Exception
     */
    public function uniqueMd5($secret = '', $field = 'code')
    {
        //生成唯一编码信息
        $code = md5($secret.Uuid::uuid4()->toString().$secret.Str::random());
        //查询信息是否存在
        if ($this->find([$field => $code], $field)) {
            //继续生成
            return $this->uniqueMd5($secret, $field);
        }
        //返回唯一md5编码
        return $code;
    }

    /**
     * 获取列表（详细列表数据）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:34:08
     * @param $conditions array 筛选条件
     * @param array $fields 查询字段
     * @param array $joins 表链接信息
     * @param array $orders 排序规则
     * @param string $group group规则
     * @param int $page 页码
     * @param int $page_size 每页条数
     * @return array
     * @throws \Exception
     */
    public function lists($conditions, $fields = [], $joins = [], $orders = [], $group = '', $page = 1, $page_size = 20)
    {
        //查询总数量
        $total_count = $this->count();
        //查询匹配总数量
        $matched_count = $this->count($conditions, $joins);
        //数据列表
        $lists = $this->limit($conditions, $fields, $joins, $orders && !empty($orders) ? $orders : ['id' => 'desc'], $group, (int)$page, (int)$page_size);
        //生成总页码
        $total_pages = (int)ceil($matched_count/$page_size);
        //返回数据
        return compact('total_count', 'matched_count', 'lists', 'total_pages', 'page', 'page_size');
    }

    /**
     * 获取一条信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:21:48
     * @param array $conditions 查询条件
     * @param string[] $fields 查询字段
     * @param string[] $orders 排序条件
     * @return mixed
     * @throws \Exception
     */
    public function row($conditions = [], $fields = ['*'], $orders = ['created_at' => 'desc'])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //设置排序惠泽
        $query = $this->setOrders($query, $orders);
        //调试sql
        $this->debug_sql && $query->dd();
        //返回第一条相关信息
        return $this->setResult($query->first($this->setFields($fields)));
    }

    /**
     * 获取某个字段信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:23:08
     * @param $conditions array 筛选条件
     * @param $field string 获取字段
     * @return mixed
     * @throws \Exception
     */
    public function find($conditions, $field)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //获取信息
        $data = $query->first($this->setFields([$field]));
        //返回查询数据
        return $this->setResult(data_get($data, $field, false));
    }

    /**
     * 返回单独某字段的值集合
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:24:16
     * @param $field string 获取字段
     * @param array $conditions 筛选条件
     * @return mixed
     * @throws \Exception
     */
    public function pluck($field, $conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //调试SQL
        $this->debug_sql && $query->dd();
        //继续查询
        return $this->setResult($query->pluck($field));
    }

    /**
     * 获取全部数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:26:20
     * @param array $conditions 筛选条件
     * @param array $fields 查询字段
     * @param array $joins 表链接信息
     * @param array $orders 排序规则
     * @param string $group group规则
     * @return mixed
     * @throws \Exception
     */
    public function get($conditions = [], $fields = [], $joins = [], $orders = [], $group = '')
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //初始化表链接信息
        $query = $this->setJoins($query, $joins);
        //初始化排序信息
        $query = $this->setOrders($query, $orders);
        //初始化group
        $query = $this->setGroup($query, $group);
        //调试SQL
        $this->debug_sql && $query->dd();
        //获取全部信息
        return $this->setResult($query->get($this->setFields($fields)));
    }

    /**
     * 获取限制条数数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:27:35
     * @param array $conditions 筛选条件
     * @param array $fields 查询字段
     * @param array $joins 表链接信息
     * @param array $orders 排序规则
     * @param string $group group规则
     * @param int $page 页码
     * @param int $page_size 每页条数
     * @return mixed
     * @throws \Exception
     */
    public function limit($conditions, $fields = [], $joins = [], $orders = [], $group = '', $page = 1, $page_size = 20)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //初始化表链接信息
        $query = $this->setJoins($query, $joins);
        //初始化排序信息
        $query = $this->setOrders($query, $orders);
        //初始化group
        $query = $this->setGroup($query, $group);
        //调试SQL
        $this->debug_sql && $query->dd();
        //获取全部信息
        return $this->setResult($query->offset(($page - 1) * $page_size)->limit((int)($page_size))->get($this->setFields($fields)));
    }

    /**
     * 创建数据并返回自增ID
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:28:14
     * @param $data array 处理数据
     * @return mixed
     * @throws \Exception
     */
    public function insertGetId($data)
    {
        //整理数据
        $data = $this->setData($data);
        //新增信息
        return $this->setResult($this->model->insertGetId($data));
    }

    /**
     * 批量导入数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:28:53
     * @param $data array 处理数据
     * @param int $chunk 分割数据条数
     * @param bool $autoIgnore 自动忽略错误
     * @return mixed
     * @throws \Exception
     */
    public function insertAll($data, $chunk = 50, $autoIgnore = false)
    {
        //分割数据组合
        $groupData = array_chunk($data, (int)($chunk), true);
        //开始事务处理
        DB::beginTransaction();
        //尝试开始操作
        try {
            //循环数据组
            foreach ($groupData as $num => $group) {
                //判断是否忽略重复插入记录到数据库的错误
                if ($autoIgnore) {
                    //返回插入结果
                    $this->model->insertOrIgnore($group);
                } else {
                    //正常插入数据
                    $this->model->insert($group);
                }
            }
            //提交事务
            DB::commit();
        } catch (\Exception $exception) {
            //回滚事物
            DB::rollBack();
            //返回失败
            return $this->setResult(false);
        }
        //返回提交的数据
        return $this->setResult($data);
    }

    /**
     * 更新数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:29:44
     * @param $conditions array 筛选条件
     * @param $data array 更新数据集
     * @return mixed
     * @throws \Exception
     */
    public function update($conditions, $data)
    {
        //整理更新数据
        $data = $this->setData($data);
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->update($data));
    }

    /**
     * 删除数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:30:06
     * @param $conditions array 筛选条件
     * @return mixed
     * @throws \Exception
     */
    public function delete($conditions)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->delete());
    }

    /**
     * 清空表数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:30:27
     * @return mixed
     * @throws \Exception
     */
    public function truncate()
    {
        // 清空数据
        return $this->setResult($this->model->truncate());
    }

    /**
     * 更新或新增数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:30:52
     * @param $condition array 判断数据是否存在的条件
     * @param $data array 需要新增/更新的数据
     * @return mixed
     * @throws \Exception
     */
    public function updateOrInsert($condition, $data)
    {
        //整理条件
        $condition = $this->setData($condition);
        //整理更新数据
        $data = $this->setData($data);
        //更新或新增数据
        return $this->model->updateOrInsert($condition, $data);
    }

    /**
     * 自增字段数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-15 17:31:19
     * @param $field string 自增数据名
     * @param array $conditions 查询条件
     * @param int|float $inc 自增值
     * @return mixed
     * @throws \Exception
     */
    public function increment($field, $conditions = [], $inc = 1)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //自增数据
        return $this->setResult($query->increment($field, $inc));
    }

    /**
     * 自减字段数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 16:32:16
     * @param $field string 自增数据名
     * @param array $conditions 查询条件
     * @param int|float $dec 自减值
     * @return mixed
     * @throws \Exception
     */
    public function decrement($field, $conditions = [], $dec = 1)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //自增数据
        return $this->setResult($query->decrement($field, $dec));
    }

    /**
     * 聚合查询总数量
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:00:52
     * @param array $conditions 查询条件
     * @param mixed $joins join查询条件
     * @return mixed
     * @throws \Exception
     */
    public function count($conditions = [], $joins = false)
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //设置join条件
        $query = $this->setJoins($query, $joins);
        //继续查询
        return $this->setResult($query->count());
    }

    /**
     * 获取某字段最大值
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:02:05
     * @param $field string 获取指定字段名称
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function max($field, $conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->max($field));
    }

    /**
     * 获取某字段最小值
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:02:05
     * @param $field string 获取指定字段名称
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function min($field, $conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->min($field));
    }

    /**
     * 获取某字段平均值
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:02:05
     * @param $field string 获取指定字段名称
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function avg($field, $conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->avg($field));
    }

    /**
     * 获取某字段和
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:02:05
     * @param $field string 获取指定字段名称
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function sum($field, $conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->sum($field));
    }

    /**
     * 判断查询条件对应的数据是否存在
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:04:31
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function exists($conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->exists());
    }

    /**
     * 判断查询条件对应的数据是否不存在
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-29 15:04:31
     * @param array $conditions 查询条件
     * @return mixed
     * @throws \Exception
     */
    public function doesntExists($conditions = [])
    {
        //初始化请求
        $query = $this->setConditions($this->model(), $conditions);
        //继续查询
        return $this->setResult($query->doesntExist());
    }

    /**
     * 设置自增ID
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2019-12-09 15:01:09
     * @param int $auto_increment 自增ID值
     * @param null $connection current model connection
     * @return bool
     * @throws \Exception
     */
    public function setIncrementId($auto_increment = 1, $connection = null)
    {
        //整理设置自增ID语句
        $sql = "ALTER TABLE `$this->table_name` auto_increment = $auto_increment";
        //开始执行
        return $this->sqlStatement($sql, $connection);
    }

    /**
     * 设置表注释
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-17 00:30:37
     * @param string $comment 注释内容
     * @param null $connection current model connection
     * @return bool
     * @throws \Exception
     */
    public function setTableComment($comment = '', $connection = null)
    {
        //整理设置表注释
        $sql = "ALTER TABLE `$this->table_name` comment '$comment'";
        //开始执行
        return $this->sqlStatement($sql, $connection);
    }

    /**
     * 生成唯一编码
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 15:35:27
     * @param $field string 字段
     * @param string $type 生成类型（md5、su <string upper>、sl <string lower>、number）
     * @param int $length 生成长度 （md5固定为32位，其他有效）
     * @return false|string
     * @throws \Exception
     */
    public function uniqueCode($field, $type = 'string', $length = 8)
    {
        //根据类型生成对应编码
        switch ($type) {
            case 'md5':
                //生成加密字符串
                $code = md5(Uuid::uuid4()->toString().Str::random($length));
                break;
            case 'su':      //string upper
                //生成加密字符串（大写）
                $code = strtoupper(Str::random($length));
                break;
            case 'sl':      //string lower
                //生成加密字符串（小写）
                $code = strtoupper(Str::random($length));
                break;
            case 'number':
                //生成加密数字
                $code = getRandChar($length, true);
                break;
            default:
                //生成加密字符串
                $code = Str::random($length);
                break;
        }
        //判断数据是否存在
        if ($this->exists([$field => $code])) {
            //重新生成
            return $this->uniqueCode($field, $type, $length);
        }
        //返回编码
        return $code;
    }
}
