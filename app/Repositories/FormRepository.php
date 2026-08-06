<?php

namespace App\Repositories;

use App\Models\Form;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
class FormRepository
{
    public function get()
    {
        // جلب آخر نموذج مضاف حسب رقم التعريف التلقائي (id)
        return Form::latest('id')->first();
    }
// ❗ إذا اخترت أن تستقبل Request
public function storeOrUpdate(Request $request)
{
    $data = $request->only(['number', 'description','name','active','img']);

    if ($request->hasFile('img')) {
        $data['img'] = FileHelper::uploadImage($request->file('img'), 'uploads/packages');
    }

    $latestForm = Form::latest('id')->first();


    if ($latestForm->number != ($data['number'] ?? null)) {
        return Form::create($data);
    }

    $latestForm->update($data);
    return $latestForm;
}

}
