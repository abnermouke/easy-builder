<?php
/**
 * Power by abnermouke/easy-builder.
 * User: {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
 * Originate in {__ORIGINATE__}
 * Date: {__DATE__}
 * Time: {__TIME__}
*/

namespace App\Interfaces{__DICTIONARY__}\Controllers;

use App\Interfaces{__DICTIONARY__}\Services\{__LOWER_CASE_NAME__}InterfaceService;
use Illuminate\Http\Request;
use Abnermouke\EasyBuilder\Module\BaseController;

/**
 * {__DATA_NAME__}基础控制器
 * Class {__LOWER_CASE_NAME__}Controller
 * @package App\Interfaces{__DICTIONARY__}\Controllers
 */
class {__LOWER_CASE_NAME__}Controller extends BaseController
{

    //TODO : 复制下方路由至指定路由节点下

    //{__DATA_NAME__}相关路由
//    Route::group(['as' => '{__CONSOLE_ROUTE_NODES__}.', 'prefix' => '{__CONSOLE_PATH__}'], function () {
//        //{__DATA_NAME__}列表
//        Route::get('', '{__LOWER_CASE_NAME__}Controller@index')->name('index');
//        //获取{__DATA_NAME__}列表
//        Route::post('lists', '{__LOWER_CASE_NAME__}Controller@lists')->name('lists');
//        //{__DATA_NAME__}详情
//        Route::post('{id}', '{__LOWER_CASE_NAME__}Controller@detail')->name('detail');
//        //保存{__DATA_NAME__}信息
//        Route::post('{id}/store', '{__LOWER_CASE_NAME__}Controller@store')->name('store');
//        //删除{__DATA_NAME__}信息
//        Route::post('{id}/delete', '{__LOWER_CASE_NAME__}Controller@delete')->name('delete');
//        //快速更改{__DATA_NAME__}状态
//        Route::post('{id}/enable', '{__LOWER_CASE_NAME__}Controller@enable')->name('enable');
//    });


    /**
     * {__DATA_NAME__}页面
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //渲染页面
        return view('pros.console.{__CONSOLE_ROUTE_NODES__}.index');
    }

    /**
     * 获取{__DATA_NAME__}列表
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function lists(Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //获取列表
        $service->lists($request);
        //响应接口
        return responseService($service);
    }

    /**
     * 获取{__DATA_NAME__}详情
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function detail($id, Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //获取详情
        $service->detail($id, $request);
        //响应接口
        return responseService($service);
    }

    /**
     * 保存{__DATA_NAME__}信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store($id, Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //保存信息
        $service->store($id, $request);
        //响应接口
        return responseService($service);
    }

    /**
     * 删除{__DATA_NAME__}信息
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete($id, Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //删除信息
        $service->delete($id, $request);
        //响应接口
        return responseService($service);
    }

    /**
     * 快速更改{__DATA_NAME__}状态
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param $id
     * @param Request $request
     * @param  {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function enable($id, Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {
        //更新状态
        $service->enable($id, $request);
        //响应接口
        return responseService($service);
    }

}
