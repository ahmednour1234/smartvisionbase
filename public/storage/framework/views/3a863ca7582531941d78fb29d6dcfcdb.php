<?php $__env->startSection('title', __('qr_code_list')); ?>

<?php $__env->startSection('content'); ?>
<style>
    .custom-pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin-top: 20px;
    flex-wrap: wrap;
}

.custom-pagination li {
    margin: 0 5px;
}

.custom-pagination li a {
    display: block;
    padding: 8px 14px;
    text-decoration: none;
    background-color: #f0f0f0;
    color: #333;
    border-radius: 6px;
    transition: all 0.3s ease;
    border: 1px solid #ccc;
}

.custom-pagination li a:hover {
    background-color: #2c4470;
    color: #fff;
}

.custom-pagination li.active a {
    background-color: #95bb48;
    color: #fff;
    font-weight: bold;
    border-color: #95bb48;
}

.custom-pagination li.disabled a {
    color: #aaa;
    pointer-events: none;
    background-color: #e0e0e0;
}

</style>
<div class="container">
    <h3><?php echo e(__('qr_code_list')); ?></h3>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('dashboard.qrcodes.index')); ?>" class="row g-3 mb-4 align-items-end">
        <div class="col-md-2">
            <input type="number" name="register_id" class="form-control" placeholder="<?php echo e(__('register_id')); ?>"
                   value="<?php echo e(request('register_id')); ?>">
        </div>

    

        <div class="col-md-1">
            <input type="number" name="scan" class="form-control" placeholder="<?php echo e(__('scan_count')); ?>"
                   value="<?php echo e(request('scan')); ?>">
        </div>

        <div class="col-md-2">
            <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
        </div>

        <div class="col-md-2">
            <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
        </div>

        <div class="col-md-2">
            <input type="text" name="short_code" id="short_code_input" class="form-control" placeholder="<?php echo e(__('short_code')); ?>"
                   value="<?php echo e(request('short_code')); ?>">
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-secondary w-100" onclick="startQrScanner()">📷</button>
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100"><?php echo e(__('filter')); ?></button>
        </div>
    </form>

    <div id="qr-reader" style="width:300px; display:none; margin-bottom: 20px;"></div>

    
<div class="table-responsive">
    <table class="table table-hover align-middle text-center border rounded shadow-sm">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?php echo e(__('email')); ?></th>
                <th scope="col"><?php echo e(__('register_id')); ?></th>
                <th scope="col"><?php echo e(__('scan_count')); ?></th>
                <th scope="col"><?php echo e(__('status')); ?></th>
                <th scope="col"><?php echo e(__('created_at')); ?></th>
                <th scope="col"><?php echo e(__('actions')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $qrcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td>
                        <img src="<?php echo e(asset('qrcodes/' . $qr->qrcode)); ?>" width="60" class="rounded border" alt="QR">
                    </td>

                    
                    <td>
                        <span class="text-primary fw-semibold"><?php echo e($qr->email); ?></span>
                    </td>

                    
                    <td>
                        <span class="badge bg-secondary"><?php echo e($qr->register_id); ?></span>
                    </td>

                    
                    <td>
                        <span class="fw-bold text-dark"><?php echo e($qr->scan); ?></span>
                    </td>

                    
                    <td>
                        <?php if($qr->active): ?>
                            <span class="badge bg-success"><?php echo e(__('active')); ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?php echo e(__('inactive')); ?></span>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <span class="text-muted"><?php echo e($qr->created_at->format('Y-m-d')); ?></span>
                    </td>

                    
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo e(route('dashboard.qrcodes.toggle', $qr->id)); ?>"
                               class="btn btn-sm btn-outline-warning"
                               title="<?php echo e(__('toggle')); ?>">
                                <i class="fas fa-exchange-alt"></i>
                            </a>
                            <a href="<?php echo e(route('dashboard.qrcodes.regenerate', $qr->id)); ?>"
                               class="btn btn-sm btn-outline-primary"
                               title="<?php echo e(__('regenerate_and_resend')); ?>">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-info my-2">
                            <?php echo e(__('no_results')); ?>

                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<?php if($qrcodes->hasPages()): ?>
    <ul class="custom-pagination">
        <li class="<?php echo e($qrcodes->onFirstPage() ? 'disabled' : ''); ?>">
            <a href="<?php echo e($qrcodes->previousPageUrl() ?? '#'); ?>">&laquo; <?php echo e(__('Previous')); ?></a>
        </li>

        <?php for($i = 1; $i <= $qrcodes->lastPage(); $i++): ?>
            <li class="<?php echo e($qrcodes->currentPage() == $i ? 'active' : ''); ?>">
                <a href="<?php echo e($qrcodes->url($i)); ?>"><?php echo e($i); ?></a>
            </li>
        <?php endfor; ?>

        <li class="<?php echo e(!$qrcodes->hasMorePages() ? 'disabled' : ''); ?>">
            <a href="<?php echo e($qrcodes->nextPageUrl() ?? '#'); ?>"><?php echo e(__('Next')); ?> &raquo;</a>
        </li>
    </ul>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function startQrScanner() {
        const qrReader = new Html5Qrcode("qr-reader");
        document.getElementById("qr-reader").style.display = "block";

        qrReader.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                const shortCode = decodedText.split('/qr/')[1] || decodedText;
                document.getElementById("short_code_input").value = shortCode;
                qrReader.stop();
                document.getElementById("qr-reader").style.display = "none";
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        ).catch(err => console.error(err));
    }
</script>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/qrcodes/index.blade.php ENDPATH**/ ?>