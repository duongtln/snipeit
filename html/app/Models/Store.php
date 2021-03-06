<?php

namespace App\Models;

use App\Models\SnipeModel;
use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Auth;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;
use App\Models\Department;
use App\Models\Contract;

/**
 * Model for Components.
 *
 * @version  [v1.0]
 */
class Store extends SnipeModel
{
    protected $presenter = 'App\Presenters\StorePresenter';
    use Loggable, Presentable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'stores';

    /**
     * Set static properties to determine which checkout/checkin handlers we should use
     */
    public static $checkoutClass = null;
    public static $checkinClass = null;


    /**
     * Category validation rules
     */
    public $rules = array(
        'name'        => 'required|unsame_name:stores,company_id',
        'company_id'  => 'required|integer|nullable',
    );

    /**
     * Whether the model should inject it's identifier to the unique
     * validation rules before attempting validation. If this property
     * is not set in the model it will default to true.
     *
     * @var boolean
     */
    protected $injectUniqueIdentifier = true;
    use ValidatingTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'company_id',
        'name',
        'location_id',
        'image',
        'user_id'
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     * 
     * @var array
     */
    protected $searchableAttributes = ['name'];
    protected $searchableRelations = [
        'company'      => ['name'],
        'location'     => ['name'],
    ];

    public function location()
    {
        return $this->belongsTo('\App\Models\Location', 'location_id');
    }

    public function admin()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo('\App\Models\Company', 'company_id');
    }
    public function department()
    {
        return $this->belongsTo('\App\Models\Department', 'id', 'store_id');
    }
    public function contract()
    {
        $contract = $this->hasMany('\App\Models\Contract', 'object_id', 'id')
            ->where("contracts.object_type", "=", \DB::raw('"App\\\Models\\\Store"'));
        $department = Contract::Select("*")
            ->where("contracts.object_type", "=", \DB::raw('"App\\\Models\\\Department"'))
            ->whereIn(
                "contracts.object_id",
                Department::select('departments.id')
                    ->join('stores', 'stores.id', '=', 'departments.store_id')
                    ->where('stores.id', $this->id)
            )
            ->whereNull('contracts.deleted_at');
        return $contract->union($department);
    }
    /**
     * Get action logs for this consumable
     */
    public function assetlog()
    {
        return $this->hasMany('\App\Models\Actionlog', 'item_id')->where('item_type', Component::class)->orderBy('created_at', 'desc')->withTrashed();
    }

    /**
     * Query builder scope to order on company
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderLocation($query, $order)
    {
        return $query->leftJoin('locations', 'stores.location_id', '=', 'locations.id')->orderBy('locations.name', $order);
    }


    /**
     * Query builder scope to order on company
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderCompany($query, $order)
    {
        return $query->leftJoin('companies', 'stores.company_id', '=', 'companies.id')->orderBy('companies.name', $order);
    }

    /**
     * Query builder scope to order on company
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderStore($query, $order)
    {
        return $query->orderBy('stores.name', $order);
    }
    /**
     * Query builder scope to order on company
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderContract($query, $order)
    {
        return $query->select(
            DB::raw("
        stores.*,
        (       
            SELECT count(*)
            FROM contracts
            WHERE 
            case 
            when contracts.object_type = 'App\\\Models\\\Department'  
                then contracts.object_id IN
                                            (SELECT departments.id
                                                FROM departments
                                                where departments.store_id = stores.id and departments.deleted_at is null
                                            ) and contracts.deleted_at is null
            when contracts.object_type = 'App\\\Models\\\Store' 
                then contracts.object_id = stores.id and contracts.deleted_at is null
            end 
        ) as sum ")
        )
            ->withCount('department')
            ->orderBy('sum', $order);
    }
}