<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Store;
use App\Http\Transformers\DepartmentsTransformer;
use App\Helpers\Helper;
use Auth;
use App\Http\Transformers\SelectlistTransformer;
use DB;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Department::class);


        $departments = Department::select([
            'departments.id',
            'departments.name',
            'departments.location_id',
            'departments.store_id',
            'departments.manager_id',
            'departments.created_at',
            'departments.updated_at',
            'departments.image'
        ])->with('users')->with('location')->with('manager')->with('store')->withCount('users')->withCount('contract');

        if ($request->has('store_id')) {
            $departments = $departments->where('store_id', $request->input('store_id'));
        }
        if ($request->has('search')) {
            $departments = self::search($request->input('search'), $departments);
        }
        $allowed_columns = ['id', 'name', 'image', 'users_count', 'contract_count', 'store', 'company'];
        $offset = (($departments) && (request('offset') > $departments->count())) ? 0 : request('offset', 0);
        $limit = $request->input('limit', 50);
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';

        switch ($request->input('sort')) {
            case 'location':
                $departments->OrderLocation($order);
                break;
            case 'manager':
                $departments->OrderManager($order);
                break;
            case 'store':
                $departments->OrderStore($order);
                break;
            case 'company':
                $departments->OrderCompany($order);
                break;
            default:
                $departments->orderBy($sort, $order);
                break;
        }

        $total = $departments->count();
        $departments = $departments->skip($offset)->take($limit)->get();
        return (new DepartmentsTransformer)->transformDepartments($departments, $total);
    }

    /**
     * Search Department
     * @param search
     * @return Department
     */
    public function search($search = "", $query)
    {
        $query = $query->select(
            'departments.id',
            'departments.name',
            'departments.location_id',
            'departments.store_id',
            'departments.manager_id',
            'departments.created_at',
            'departments.updated_at',
            'departments.image',
            'stores.name as store_name',
            'locations.name as location_name',
            'companies.name as companies_name'
        )
            ->leftjoin('stores', 'stores.id', '=', 'departments.store_id')
            ->leftjoin('companies', 'companies.id', '=', 'stores.company_id')
            ->leftjoin('locations', 'locations.id', '=', 'departments.location_id')
            ->leftjoin('users', 'users.id', '=', 'departments.manager_id')
            ->Where(function ($query) use ($search) {
                $query = $query
                    ->Where('departments.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('stores.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('companies.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('locations.name', 'LIKE', '%' . $search . '%')
                    ->orWhereRaw('CONCAT(' . DB::getTablePrefix() . 'users.first_name," ",' . DB::getTablePrefix() . 'users.last_name) LIKE ?', ["%$search%", "%$search%"]);
            })
            ->with('users')->with('location')->with('manager')->with('store')->withCount('users')->withCount('contract');
        return $query;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Department::class);
        $department = new Department;
        $department->fill($request->all());
        $department->user_id = Auth::user()->id;
        $department->manager_id = ($request->has('manager_id') ? $request->input('manager_id') : null);

        if ($department->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $department, trans('admin/departments/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $department->getErrors()));
    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', Department::class);
        $department = Department::findOrFail($id);

        return (new DepartmentsTransformer)->transformDepartment($department);
    }



    /**
     * Validates and deletes selected location.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $locationId
     * @since [v1.0]
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        $this->authorize('delete', $department);

        if ($department->users->count() > 0) {
            return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/departments/message.assoc_users')));
        }

        $department->delete();
        return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/departments/message.delete.success')));
    }

    /**
     * Gets a paginated collection for the select2 menus
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0.16]
     * @see \App\Http\Transformers\SelectlistTransformer
     *
     */
    public function selectlist(Request $request)
    {

        $departments = Department::select([
            'id',
            'name',
            'image',
        ]);

        if ($request->has('search')) {
            $departments = $departments->where('name', 'LIKE', '%' . $request->get('search') . '%');
        }

        if ($request->get('store_id')) {
            $departments = $departments->where('store_id', '=', $request->get('store_id'));
        } else {
            if ($request->get('company_id')) {
                $departments = $departments
                    ->whereIn(
                        'departments.store_id',
                        Store::select('stores.id')
                            ->where('stores.company_id', '=', $request->get('company_id'))
                    );
            }
        }

        $departments = $departments->orderBy('name', 'ASC')->paginate(50);

        // Loop through and set some custom properties for the transformer to use.
        // This lets us have more flexibility in special cases like assets, where
        // they may not have a ->name value but we want to display something anyway
        foreach ($departments as $department) {
            $department->use_image = ($department->image) ? url('/') . '/uploads/departments/' . $department->image : null;
        }

        return (new SelectlistTransformer)->transformSelectlist($departments);
    }
}