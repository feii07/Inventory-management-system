<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemDeleteRequest;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\Item;
use App\Models\ItemLog;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['creator', 'updater']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function store(ItemStoreRequest $request)
    {
        $item = Item::create(array_merge(
            $request->all(),
            ['created_by' => $request->user()->id]
        ));

        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'action' => 'create',
            'new_data' => $item->toArray(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $item->load(['creator', 'updater'])
        ], 201);
    }

    public function show(Request $request, Item $item)
    {
        return response()->json([
            'success' => true,
            'data' => $item->load(['creator', 'updater'])
        ]);
    }

    public function update(ItemUpdateRequest $request, Item $item)
    {
        
        $oldData = $item->toArray();

        $item->update(array_merge(
            $request->all(),
            ['updated_by' => $request->user()->id]
        ));

        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'action' => 'update',
            'old_data' => $oldData,
            'new_data' => $item->toArray(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $item->load(['creator', 'updater'])
        ]);
    }

    public function destroy(ItemDeleteRequest $request, Item $item)
    {

        $oldData = $item->toArray();

        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'action' => 'delete',
            'old_data' => $oldData,
        ]);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }
}
