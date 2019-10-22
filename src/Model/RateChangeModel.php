<?php

namespace Ryu\Seat\Tax\Models;

use Illuminate\Database\Eloquent\Model;

class RateChangeModel extends Model
{

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='seat_tax_rate_change';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 主键
     *
     * @var string
     */
    protected $primaryKey='id';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable=['corporation_id','notification_id','pve_taxrate_old','pve_taxrate_new','begin_time','end_time'];



}
