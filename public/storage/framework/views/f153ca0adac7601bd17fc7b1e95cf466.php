  <?php if($section && $section->media_type === 'image'): ?>
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h6 class="text-secondary sub-tit"><?php echo e($section->title[$locale] ?? ''); ?></h6>
        <h3 class="mb-5 gre-title" style="font-size: 40px;font-weight: bolder;"><?php echo e($section->description[$locale] ?? ''); ?></h3>

        <div class="row row-30">
            <?php $locale = app()->getLocale(); ?>

            <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="<?php echo e(route('web.blog.show', [$blog->id])); ?>">
                            <img src="<?php echo e(asset('public/'.$blog->image)); ?>" class="card-img-top rounded-top" alt="Blog Image" style="height: 230px; object-fit: cover;"    loading="lazy" decoding="async" fetchpriority="low">
                        </a>
                        <div class="card-body text-start">
                    
                            <h5 class="card-title"  style="color:black;">
                                <a href="<?php echo e(route('web.blog.show', [$blog->id])); ?>" class="text-dark text-decoration-none">
                                    <?php echo e($blog->{'title_' . $locale} ?? ''); ?>

                                </a>
                            </h5>

                            <p class="card-text"  style="color:black;">
                                <?php echo e(Str::limit(strip_tags($blog->{'description_' . $locale} ?? ''), 150)); ?>

                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 text-start">
                            <a href="<?php echo e(route('web.blog.show', [$blog->id])); ?>" style="color: #cc252e !important; border-color: #cc252e;" class="btn btn-sm btn-outline-primary">
                                <?php echo e(__('Read More')); ?>

                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/blog.blade.php ENDPATH**/ ?>