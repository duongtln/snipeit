<?php

namespace App\Http\Controllers;

use App\Models\InventoryResult;
use App\Helpers\Helper;
use App\Models\Inventory;
use App\Models\Setting;
use App\Models\Contract;

/**
 * This class controls all actions related to inventory for
 * the Snipe-IT Asset Management application.
 * @author [Dang.HT]
 * @version    v1.0
 */
class InventoryResultsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the inventory listing, which is generated in getDatatable.
     * @see inventoryController::getDatatable() method that generates the JSON response
     * @return \Illuminate\InventoryResult\View\View
     */
    public function index()
    {
        $this->authorize('view', InventoryResult::class);
        return view('inventories/result', ['item' => new InventoryResult]);
    }

    /**
     * Scan Results Online
     * @author [Thong.LT]
     * @version v1.0 - 2019/07/30
     * @return \Illuminate\InventoryResult\View\View
     */
    public function scanOnline()
    {
        $setting = Setting::first();
        return view('inventories/scan-online')
            ->with('item', new InventoryResult)
            ->with('statuslabel_list', Helper::statusLabelList())
            ->with('asset_tag', $setting->auto_increment_prefix);
    }

    /**
     * Scan Results Offline
     * @author [Thong.LT]
     * @version v1.0 - 2019/07/30
     * @return \Illuminate\InventoryResult\View\View
     */
    public function scanOffline()
    {
        $setting = Setting::first();
        return view('inventories/scan-offline')
            ->with('item', new InventoryResult)
            ->with('statuslabel_list', Helper::statusLabelList())
            ->with('asset_tag', $setting->auto_increment_prefix);
    }

    /**
     * show a inventory.
     *
     * @param int $inventoryId
     * @return \Illuminate\InventoryResult\View\View
     */
    public function show($inventoryId = null)
    {
        if (is_null($inventory = Inventory::find($inventoryId))) {
            return redirect()->route('inventories.index')->with('error', trans('admin/inventories/message.does_not_exist'));
        }
        return view('inventories/result')
            ->with('item', $inventory);
    }
    /**
     * Delete a inventory.
     *
     * @param int $inventoryresultId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($inventoryresultId)
    {
        if (is_null($inventoryresult = InventoryResult::find($inventoryresultId))) {
            return redirect()->back()->with('error', trans('admin/inventories/message.result.does_not_exist'));
        }
        $this->authorize('delete', $inventoryresult);
        $inventoryresult->delete();
        return redirect()->back()->with('success', trans('admin/inventories/message.result.delete'));
    }
}