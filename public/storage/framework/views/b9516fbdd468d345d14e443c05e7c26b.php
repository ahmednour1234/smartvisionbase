<?php $__env->startSection('content'); ?>
<?php $locale = app()->getLocale(); ?>
<style>
    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important;
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top: 150px;
            text-align: center;
        }

        .post-single-img img {
            width: 100%;
            height: auto;
        }

        .post-single-meta {
            flex-direction: column !important;
            gap: 6px !important;
        }

        .post-aside-title {
            margin-top: 50px;
        }

        .unit {
            flex-direction: row !important;
        }

        .unit-left img {
            width: 70px !important;
            height: 70px !important;
            object-fit: cover;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
    <div class="container">
        <h3 class="breadcrumbs-custom-title"><?php echo e($blog->{'name_' . $locale} ?? ''); ?></h3>
    </div>
</section>

<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50">
            <!-- Blog Content -->
            <div class="col-12 col-lg-7 col-xl-8">
                <article class="post-single">
                    <div class="post-single-img mb-4">
                        <img src="<?php echo e(asset('public/'.$blog->image)); ?>" alt="Blog image"
                             class="img-fluid rounded">
                    </div>

                    <ul class="post-single-meta list-unstyled d-flex flex-wrap gap-3 mb-3">
                        <li class="post-single-meta-author text-muted">
                            <i class="fa fa-user me-1"></i>
                            <?php echo e($blog->{'name_' . $locale} ?? ''); ?>

                        </li>
                        
                        <li class="post-single-meta-date text-muted">
                            <i class="fa fa-globe me-1"></i>
                            <a href="<?php echo e($blog->link ?? '#'); ?>">
                                <?php echo e($blog->{'name_' . $locale} ?? ''); ?>

                            </a>
                        </li>
                    </ul>

                    <h3 class="post-single-title text-primary mb-3">
                        <?php echo e($blog->{'title_' . $locale} ?? ''); ?>

                    </h3>

                    <div class="post-single-text text-start">
                        <?php echo $blog->{'description_' . $locale} ?? ''; ?>

                    </div>

                    <?php if($blog->link): ?>
                        <div class="text-center mt-5">
                            <a href="<?php echo e($blog->link); ?>" target="_blank" class="btn btn-lg btn-primary px-5 shadow">
                                <?php echo e(__('Become This Event Sponsor')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-12 col-lg-5 col-xl-4">
                <div class="post-aside">
                    <h5 class="post-aside-title mb-3"><?php echo e(__('Latest Posts')); ?></h5>
                    <div class="post-aside-content">
                        <?php $__currentLoopData = $latestblogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $latest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="post-mini mb-3">
                                <div class="unit d-flex align-items-center">
                                    <div class="unit-left me-3">
                                        <a href="<?php echo e(route('web.blog.show', $latest->id)); ?>">
                                            <img src="<?php echo e(asset('public/'.$latest->image)); ?>" alt="thumb"
                                                 width="59" height="59" class="rounded">
                                        </a>
                                    </div>
                                    <div class="unit-body">
                                        <p class="post-mini-author small mb-1 text-muted">
                                            <?php echo e($latest->{'name_' . $locale} ?? ''); ?>

                                        </p>
                                        <p class="post-mini-title mb-0">
                                            <a href="<?php echo e(route('web.blog.show', $latest->id)); ?>">
                                                <?php echo e(Str::limit($latest->{'title_' . $locale} ?? '', 50)); ?>

                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/singleblog.blade.php ENDPATH**/ ?>