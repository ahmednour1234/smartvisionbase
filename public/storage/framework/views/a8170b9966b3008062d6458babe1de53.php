


<?php $__env->startSection('title', __('Registrations.title')); ?>

<?php $__env->startPush('styles'); ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ثيم برتقالي بسيط */
        .btn { display:inline-flex; align-items:center; gap:.5rem; border-radius:.5rem; padding:.55rem .9rem; font-weight:600; }
        .btn-orange { background:#ea580c; color:#fff; }
        .btn-orange:hover { background:#c2410c; color:#fff; }
        .btn-orange-outline { border:1px solid #ea580c; color:#ea580c; background:#fff; }
        .btn-orange-outline:hover { background:#fff7ed; }
        .btn-muted { border:1px solid #e5e7eb; background:#fff; color:#111827; }
        .btn-muted:hover { background:#f9fafb; }

        /* أزرار أيقونة فقط */
        .icon-only { padding:.55rem; width:2.5rem; justify-content:center; }
        .chip-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        }
        .badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.75rem; border-radius:999px; padding:.25rem .55rem; }
        .badge-orange { background:#ffedd5; color:#9a3412; } /* نشط */
        .badge-gray { background:#f3f4f6; color:#4b5563; }   /* متوقف */

        .table thead th { font-size:.75rem; text-transform:uppercase; color:#6b7280; font-weight:700; }
        .table tbody td { font-size:.9rem; color:#111827; }
        .icon-left { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
        .input-icon { padding-left:2.25rem; }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto p-6 space-y-6">

    
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">الروابط التسويقية</h1>
            <p class="text-gray-600 text-sm">تتبّع مرجع التسجيل من حملات التسويق فقط (بدون مبيعات)</p>
        </div>
    </div>

    
    <div class="bg-white rounded-md p-4 border">
   

            
            <div class="grow"></div>

            <a href="<?php echo e(route('marketing-refs.create')); ?>" class="btn btn-orange" title="إضافة مرجع">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة مرجع</span>
                </a>
        
    </div>

    
    <div class="table-responsive">
      <table class="table text-center align-middle">
            <thead class="bg-gray-50">
                <tr class="text-right">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">الاسم</th>
                    <th class="px-4 py-3">الكود</th>
                    <th class="px-4 py-3">الحالة</th>
                    <th class="px-4 py-3">الاستخدامات</th>
                    <th class="px-4 py-3">ينتهي في</th>
                    <th class="px-4 py-3">أُنشئ</th>
                    <th class="px-4 py-3 text-left">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $refs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $share = url('/register').'?ref='.$ref->code; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($ref->id); ?></td>
                        <td class="px-4 py-3 text-sm font-medium"><?php echo e($ref->name); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 border rounded bg-gray-50 text-gray-800 chip-code"><?php echo e($ref->code); ?></span>

                                
                                <button type="button" data-copy="<?php echo e($share); ?>"
                                        class="btn btn-orange-outline icon-only copy-btn" title="نسخ الرابط">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                <a href="<?php echo e($share); ?>" target="_blank"
                                   class="btn btn-muted icon-only" title="معاينة">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($ref->active): ?>
                                <span class="badge badge-orange"><i class="fa-solid fa-circle-check"></i> نشط</span>
                            <?php else: ?>
                                <span class="badge badge-gray"><i class="fa-regular fa-circle-xmark"></i> متوقف</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if(!is_null($ref->max_uses)): ?>
                                <span class="font-semibold"><?php echo e($ref->uses_count); ?></span>
                                <span class="text-gray-400">/</span>
                                <span class="text-gray-600"><?php echo e($ref->max_uses); ?></span>
                            <?php else: ?>
                                <span class="font-semibold"><?php echo e($ref->uses_count); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm"><?php echo e($ref->expires_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3 text-sm"><?php echo e($ref->created_at?->format('Y-m-d')); ?></td>

                        
                        <td class="px-4 py-3 text-left">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route('marketing-refs.edit', $ref)); ?>"
                                   class="btn btn-orange icon-only" title="تعديل">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="<?php echo e(route('marketing-refs.destroy', $ref)); ?>" method="POST"
                                      onsubmit="return confirm('حذف المرجع؟ لا يمكن التراجع.');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-orange-outline icon-only" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-10">
                            <div class="text-center text-gray-500">
                                <i class="fa-regular fa-clipboard fa-2x mb-2"></i>
                                لا توجد بيانات حتى الآن. ابدأ بإضافة مرجع جديد.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
        <div class="px-4 py-4">
            <?php echo e($refs->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.copy-btn');
    if (btn) {
        const txt = btn.getAttribute('data-copy');
        navigator.clipboard.writeText(txt).then(() => {
            const prev = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i>';
            setTimeout(()=> btn.innerHTML = prev, 1200);
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/marketing_refs/index.blade.php ENDPATH**/ ?>