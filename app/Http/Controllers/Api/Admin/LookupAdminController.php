<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\Package;
use App\Models\LostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class LookupAdminController extends Controller
{
    private function model(string $type)
    {
        return match($type) {
            'countries' => Country::class,
            'events' => Event::class,
            'packages' => Package::class,
            'lost-categories' => LostCategory::class,
            default => null,
        };
    }

    public function index(string $type)
    {
        $cls = $this->model($type);
        if (!$cls) return response()->json(['message'=>'Not found'], 404);
        $items = $cls::orderBy('sort_order')->orderBy('name')->get();
        return response()->json(['items'=>$items]);
    }

    public function store(Request $request, string $type)
    {
        $cls = $this->model($type);
        if (!$cls) return response()->json(['message'=>'Not found'], 404);

        $rules = match($type) {
            'countries' => ['iso2'=>'required|string|size:2|unique:countries,iso2', 'name'=>'required|string|max:150', 'is_active'=>'nullable|boolean','sort_order'=>'nullable|integer'],
            'events' => ['name'=>'required|string|max:180|unique:events,name', 'location'=>'nullable|string|max:180','event_date_from'=>'nullable|date','event_date_to'=>'nullable|date','is_active'=>'nullable|boolean','sort_order'=>'nullable|integer'],
            'packages' => ['name'=>'required|string|max:120|unique:packages,name','is_active'=>'nullable|boolean','sort_order'=>'nullable|integer'],
            'lost-categories' => ['name'=>'required|string|max:120|unique:lost_categories,name','is_active'=>'nullable|boolean','sort_order'=>'nullable|integer'],
        };

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return response()->json(['message'=>'Validation error','errors'=>$v->errors()], 422);

        $item = $cls::create($v->validated());
        Cache::forget((string) config('crm.lookups.cache_key'));
        return response()->json(['item'=>$item], 201);
    }

    public function update(Request $request, string $type, int $id)
    {
        $cls = $this->model($type);
        if (!$cls) return response()->json(['message'=>'Not found'], 404);

        $item = $cls::findOrFail($id);

        $rules = match($type) {
            'countries' => ['iso2'=>'sometimes|required|string|size:2|unique:countries,iso2,'.$id, 'name'=>'sometimes|required|string|max:150', 'is_active'=>'sometimes|boolean','sort_order'=>'sometimes|integer'],
            'events' => ['name'=>'sometimes|required|string|max:180|unique:events,name,'.$id, 'location'=>'sometimes|nullable|string|max:180','event_date_from'=>'sometimes|nullable|date','event_date_to'=>'sometimes|nullable|date','is_active'=>'sometimes|boolean','sort_order'=>'sometimes|integer'],
            'packages' => ['name'=>'sometimes|required|string|max:120|unique:packages,name,'.$id,'is_active'=>'sometimes|boolean','sort_order'=>'sometimes|integer'],
            'lost-categories' => ['name'=>'sometimes|required|string|max:120|unique:lost_categories,name,'.$id,'is_active'=>'sometimes|boolean','sort_order'=>'sometimes|integer'],
        };

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return response()->json(['message'=>'Validation error','errors'=>$v->errors()], 422);

        $item->fill($v->validated());
        $item->save();
        Cache::forget((string) config('crm.lookups.cache_key'));
        return response()->json(['item'=>$item]);
    }

    public function destroy(string $type, int $id)
    {
        $cls = $this->model($type);
        if (!$cls) return response()->json(['message'=>'Not found'], 404);
        $item = $cls::findOrFail($id);
        $item->delete();
        Cache::forget((string) config('crm.lookups.cache_key'));
        return response()->json(['message'=>'Deleted']);
    }
}
