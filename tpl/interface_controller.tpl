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

    /**
     * {__DATA_NAME__}页面
     * @Author {__AUTHOR__} <{__AUTHOR_CONTACT_EMAIL}>
     * @Originate in {__ORIGINATE__}
     * @Time {__DATE__} {__TIME__}
     * @param Request $request
     * @param {__LOWER_CASE_NAME__}InterfaceService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
    */
    public function index(Request $request, {__LOWER_CASE_NAME__}InterfaceService $service)
    {

        // TODO : 逻辑操作

        //响应接口
        return responseService($service);
    }

}
