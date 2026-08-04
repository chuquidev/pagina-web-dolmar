<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Http\Resources\StoreSettingResource;
use App\Models\StoreSetting;

class StoreSettingController extends Controller
{
    public function show()
    {
        return new StoreSettingResource(StoreSetting::current());
    }

    public function update(StoreSettingRequest $request)
    {
        $settings = StoreSetting::current();
        $settings->update($request->validated());

        if ($request->hasFile('logo')) {
            $settings->addMedia($request->file('logo'))->toMediaCollection('logo');
        }

        return new StoreSettingResource($settings->fresh());
    }
}
