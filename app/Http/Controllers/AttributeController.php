<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();

        return AttributeResource::collection($attributes);
    }

    public function store(StoreAttributeRequest $request)
    {
        $validated = $request->validated();
        $attribute = Attribute::create($validated);
        return new AttributeResource($attribute);
    }

    public function show(Attribute $attribute)
    {
        return new AttributeResource($attribute);
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json(null, 204);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $validated = $request->validated();
        $attribute->update($validated);
        return new AttributeResource($attribute);
    }
}
