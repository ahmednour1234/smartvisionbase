<?php
    $isEdit = isset($company);
    $currentRating = old('stars', $company->stars ?? 0);
?>

<form action="<?php echo e($isEdit ? route('dashboard.companies.update', $company->id) : route('dashboard.companies.store')); ?>"
      method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    
    <div class="mb-3">
        <label class="form-label">Name (AR)</label>
        <input type="text" name="name_ar" class="form-control" value="<?php echo e(old('name_ar', $company->name_ar ?? '')); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Name (EN)</label>
        <input type="text" name="name_en" class="form-control" value="<?php echo e(old('name_en', $company->name_en ?? '')); ?>" required>
    </div>

    
    <div class="mb-3">
        <label class="form-label">Title (AR)</label>
        <input type="text" name="title_ar" class="form-control" value="<?php echo e(old('title_ar', $company->title_ar ?? '')); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Title (EN)</label>
        <input type="text" name="title_en" class="form-control" value="<?php echo e(old('title_en', $company->title_en ?? '')); ?>">
    </div>
    

    <!---->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Country</label>-->
    <!--    <input type="text" name="country" class="form-control" value="<?php echo e(old('country', $company->country ?? '')); ?>">-->
    <!--</div>-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Link</label>-->
    <!--    <input type="url" name="link" class="form-control" value="<?php echo e(old('link', $company->link ?? '')); ?>">-->
    <!--</div>-->

    
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Description (AR)</label>-->
    <!--    <textarea name="description_ar" class="form-control"><?php echo e(old('description_ar', $company->description_ar ?? '')); ?></textarea>-->
    <!--</div>-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Description (EN)</label>-->
    <!--    <textarea name="description_en" class="form-control"><?php echo e(old('description_en', $company->description_en ?? '')); ?></textarea>-->
    <!--</div>-->

    <!---->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Regulation</label>-->
    <!--    <textarea name="regulation" class="form-control"><?php echo e(old('regulation', $company->regulation ?? '')); ?></textarea>-->
    <!--</div>-->

    

    
    <div class="mb-3">
                <label class="form-label fw-bold"><?php echo app('translator')->get('influncer.category'); ?></label>
        <input type="text" name="category" class="form-control" placeholder="<?php echo app('translator')->get('influncer.followers'); ?>" min="0"
               value="<?php echo e(old('category', $company->category	 ?? 0)); ?>">
    
    </div>
     <div class="col-md-6 mb-3">
        <label class="form-label fw-bold"><?php echo app('translator')->get('influncer.followers'); ?></label>
        <input type="text" name="number_of_followers" class="form-control" placeholder="<?php echo app('translator')->get('influncer.followers'); ?>" min="0"
               value="<?php echo e(old('number_of_followers', $company->number_of_followers	 ?? 0)); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold"><?php echo app('translator')->get('influncer.voting'); ?></label>
        <input type="number" name="count_vote" class="form-control" placeholder="<?php echo app('translator')->get('influncer.voting'); ?>" min="0"
               value="<?php echo e(old('count_vote', $company->count_vote	 ?? 0)); ?>">
    </div>
    
        <div class="col-md-6 mb-3">
        <label class="form-label fw-bold"><?php echo app('translator')->get('influncer.orders'); ?></label>
        <input type="number" name="orders" class="form-control" placeholder="<?php echo app('translator')->get('influncer.orders'); ?>" min="0"
               value="<?php echo e(old('orders', $company->orders	 ?? 0)); ?>">
    </div>
        <div class="col-md-6">
          <label class="form-label"><?php echo e(__('speaker.followers_ticktock')); ?></label>
          <input type="text" name="followers_ticktock" class="form-control" value="<?php echo e(old('followers_ticktock', $isEdit ? $company->followers_ticktock : '')); ?>">
        </div>
    
    <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
        <?php if($isEdit && $company->image): ?>
            <img src="<?php echo e(asset('public/'.$company->image)); ?>" width="80" class="mt-2">
        <?php endif; ?>
    </div>

    
    <button type="submit" class="btn btn-primary">
        <?php echo e($isEdit ? 'Update' : 'Save'); ?>

    </button>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#star-rating .star');
        const hiddenInput = document.getElementById('stars');
        const ratingText = document.getElementById('rating-value');

        stars.forEach((star, index) => {
            star.addEventListener('mousemove', function (e) {
                const isHalf = e.offsetX < star.offsetWidth / 2;
                highlightStars(index, isHalf ? 0.5 : 1);
            });

            star.addEventListener('click', function (e) {
                const isHalf = e.offsetX < star.offsetWidth / 2;
                const rating = index + (isHalf ? 0.5 : 1);
                hiddenInput.value = rating;
                ratingText.textContent = rating;
            });
        });

        function highlightStars(index, partial) {
            stars.forEach((star, i) => {
                const icon = star.querySelector('i');
                if (i < index) {
                    icon.className = 'fas fa-star';
                } else if (i === index) {
                    icon.className = partial === 0.5 ? 'fas fa-star-half-alt' : 'fas fa-star';
                } else {
                    icon.className = 'far fa-star';
                }
            });
        }
    });
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/companies/_form.blade.php ENDPATH**/ ?>