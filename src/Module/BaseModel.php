<?php

namespace Abnermouke\EasyBuilder\Module;

use Illuminate\Database\Eloquent\Model;

/**
 * Easy Builder Basic Model Module Power By Abnermouke
 * Class BaseModel
 * @package Abnermouke\EasyBuilder\Module
 */
class BaseModel extends Model
{
    // Laravel builder basic model ignore fields
    protected $guarded = [];

    // Laravel builder basic model automatic update timestamp
    public $timestamps = false;

    // Laravel builder basic model order sort [desc]
    public const LATEST_ORDER_BY = 'latest()';
    // Laravel builder basic model order sort [asc]
    public const OLDEST_ORDER_BY = 'oldest()';
    // Laravel builder basic model order sort [random]
    public const RANDOM_ORDER_BY = 'random_order()';
    // Laravel builder basic model order sort [custom raw]
    public const RAW_ORDER_BY = 'raw()';

    // Laravel builder basic model status [enabled]
    public const STATUS_ENABLED = 1;
    // Laravel builder basic model status [disabled]
    public const STATUS_DISABLED = 2;
    // Laravel builder basic model status [verify]
    public const STATUS_VERIFYING = 3;
    // Laravel builder basic model status [verify failed]
    public const STATUS_VERIFY_FAILED = 4;
    // Laravel builder basic model status [deleted]
    public const STATUS_DELETED = 5;

    // Laravel builder basic model switch [on]
    public const SWITCH_ON = 1;
    // Laravel builder basic model switch [off]
    public const SWITCH_OFF = 2;

}
