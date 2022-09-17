<?php
/**
 * Power by abnermouke/easy-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

namespace App\Interfaces{__DICTIONARY__}\Services;

use Abnermouke\EasyBuilder\Library\CodeLibrary;
use Abnermouke\EasyBuilder\Library\Cryptography\AesLibrary;
use Abnermouke\EasyBuilder\Module\BaseService;
use Abnermouke\Pros\Builders\Form\FormBuilder;
use Abnermouke\Pros\Builders\Form\Tools\FormItemBuilder;
use Abnermouke\Pros\Builders\Table\TableBuilder;
use App\Services\Pros\Console\AdminLogService;
use App\Repository{__RESOURCE_DICTIONARY__}\{__LOWER_CASE_NAME__}Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * {__DATA_NAME__}接口逻辑服务容器
 * Class {__LOWER_CASE_NAME__}Service
 * @package App\Interfaces{__DICTIONARY__}\Services
*/
class {__LOWER_CASE_NAME__}InterfaceService extends BaseService
{

    /**
    * 引入父级构造
    * {__LOWER_CASE_NAME__}InterfaceService constructor.
    * @param bool $pass 是否直接获取结果
    */
    public function __construct($pass = false) { parent::__construct($pass); }

    /**
     * 获取列表
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $request Request
     * @return array|bool
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        //获取加密信息
        if (!$data = AesLibrary::decryptFormData($request->all())) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_MISSING, '非法参数');
        }
        //整理查询条件
        $conditions = [];
        //判断筛选条件
        if ($filters = data_get($data, 'filters', [])) {
            //循环筛选条件
            foreach ($filters as $filter => $value) {
                //根据筛选项设置条件
                switch ($filter) {
                    case 'keyword':
                        $value && $conditions[implode('|', ['id'])] = ['like', '%'.$value.'%'];
                        break;
                    case 'created_at':
                        $value && $conditions['created_at'] = ['date', $value];
                        break;
                    case 'updated_at':
                        $value && $conditions['updated_at'] = ['date', $value];
                        break;
                }
            }
        }
        //查询列表
        $lists = (new {__LOWER_CASE_NAME__}Repository())->lists($conditions, [], [], data_get($data, 'sorts', ['id' => 'desc']), '', (int)data_get($data, 'page', config('pros.table.default_page')), (int)data_get($data, 'page_size', config('pros.table.default_page_size')));
        //渲染表格内容
        $render = TableBuilder::CONTENT()->signature($data['signature'])->setLists($lists)->render();
        //返回成功
        return $this->success($render);
    }

    /**
     * 获取详情
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param $request Request
     * @return array|bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function detail($id, Request $request)
    {
        //渲染表单内容
        $render = FormBuilder::make()
            ->setSubmit(route('pros.console.{__CONSOLE_ROUTE_NODES__}.store', ['id' => (int)$id]))
            ->setItems(function (FormItemBuilder $builder) use ($id) {

                //TODO : 配置表单内容

            })
            ->setData((int)$id > 0 ? (new {__LOWER_CASE_NAME__}Repository())->row(['id' => (int)$id]) : [])
            ->render();
        //返回成功
        return $this->success(['html' => $render]);
    }

    /**
     * 保存信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param $request Request
     * @return array|bool
     * @throws \Exception
     */
    public function store($id, Request $request)
    {
        //获取加密信息
        if (!$data = AesLibrary::decryptFormData($request->all())) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_MISSING, '非法参数');
        }
        //判断更改项
        if (!($edited = $data['__edited__'])) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_MISSING, '信息无更新');
        }
        //获取更改项
        $info = Arr::only($data['__data__'], $data['__edited__']);
        //添加修改时间
        $info['updated_at'] = auto_datetime();
        //判断是否为新增
        if ((int)$id <= 0) {
            //添加信息
            $info['created_at'] = auto_datetime();
            //添加信息
            if (!$id = (new {__LOWER_CASE_NAME__}Repository())->insertGetId($info)) {
                //返回失败
                return $this->fail(CodeLibrary::DATA_UPDATE_FAIL, '{__DATA_NAME__}创建失败');
            }
            //记录日志
            (new AdminLogService())->record( '新增{__DATA_NAME__}信息', compact('id'));
        } else {
            //修改信息
            if (!(new {__LOWER_CASE_NAME__}Repository())->update(['id' => (int)$id], $info)) {
                //返回失败
                return $this->fail(CodeLibrary::DATA_UPDATE_FAIL, '修改失败');
            }
            //记录日志
            (new AdminLogService())->record( '更新{__DATA_NAME__}信息', compact('id'));
        }
        //返回成功
        return $this->success(compact('id'));
    }

    /**
     * 删除信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param $request Request
     * @return array|bool
     * @throws \Exception
     */
    public function delete($id, Request $request)
    {
        //删除{__DATA_NAME__}信息
        if (!(new {__LOWER_CASE_NAME__}Repository())->delete(['id' => (int)$id])) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_DELETE_FAIL, '删除失败');
        }
        //记录日志
        (new AdminLogService())->record('删除{__DATA_NAME__}', compact('id'));
        //返回成功
        return $this->success(compact('id'));
    }

    /**
     * 更改状态
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param $request Request
     * @return array|bool
     * @throws \Exception
     */
    public function enable($id, Request $request)
    {
        //获取加密信息
        if (!$data = AesLibrary::decryptFormData($request->all())) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_MISSING, '非法参数');
        }
        //更改状态
        if (!(new {__LOWER_CASE_NAME__}Repository())->update(['id' => (int)$id], ['status' => (int)$data['value'], 'updated_at' => auto_datetime()])) {
            //返回失败
            return $this->fail(CodeLibrary::DATA_UPDATE_FAIL, '更改失败');
        }
        //记录日志
        (new AdminLogService())->record('更改{__DATA_NAME__}为：'.$data['text'], $data);
        //返回成功
        return $this->success(compact('id'));
    }

}
