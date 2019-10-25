<?php

namespace Ryu\Seat\Tax\Models;

use Illuminate\Database\Eloquent\Model;

use Seat\Eveapi\Models\Corporation\CorporationInfo;

class CorpBillModel extends Model
{

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='seat_tax_corp_bill';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = true;

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
    protected $fillable = [
        'id', 'corporation_id', 'month', 'year', 'pve_bill', 'mining_bill',
        'pve_taxrate', 'mining_taxrate', 'mining_modifier'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id')
            ->withDefault(function () {
                return new CorporationInfo([
                    'name' => 'Unknown',
                ]);
            });
    }

}
