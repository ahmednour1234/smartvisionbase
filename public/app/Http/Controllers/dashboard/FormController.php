<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Repositories\FormRepository;
use Illuminate\Support\Facades\File;

class FormController extends Controller
{
    protected $repo;

    public function __construct(FormRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * عرض النموذج الحالي إن وجد.
     */
    public function index()
    {
        $form = $this->repo->get();

        return view('content.forms.index', compact('form'));
    }

    /**
     * حفظ أو تحديث بيانات النموذج.
     */
    public function store(StoreFormRequest $request)
    {
        $validated = $request->validated();

        // معالجة الصورة باستخدام FileHelper

        $this->repo->storeOrUpdate($request);

        return redirect()->back()->with('success', __('form_updated_successfully'));
    }
}
