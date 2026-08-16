<?php $__env->startSection('content'); ?>
   <style>
        .gallery-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .gallery-track {
            display: flex;
            width: max-content;
            animation: scroll-horizontal 60s linear infinite;
            gap: 24px;
            /* المسافة بين الصور */
            padding-block: 10px;
        }

        .thumb-wrapper {
            flex: 0 0 auto;
            width: 300px;
            /* العرض أكبر */
            height: 400px;
            /* الطول أطول */
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        @keyframes scroll-horizontal {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .sponsor-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .sponsor-track {
            display: flex;
            width: max-content;
            animation: sponsor-scroll 50s linear infinite;
            gap: 40px;
            /* المسافة بين الشعارات */
            align-items: center;
            justify-content: center;
        }

        .sponsor-item {
            flex: 0 0 auto;
            width: 160px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px;
        }

        .sponsor-item img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        @keyframes sponsor-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
     @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important; /* صورة أطول في الأجهزة الصغيرة */
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top:150px;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
    </style>
     <?php
        $locale = app()->getLocale();

    ?>
    <section class="breadcrumbs-custom bg-image context-dark"
        style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
        <div class="container">

            <h3 class="breadcrumbs-custom-title">event agenda</h3>
        </div>
    </section>

    <?php
        use Carbon\Carbon;

        $locale = app()->getLocale();

        $groupedSchedules = [];

        foreach ($schedules as $schedule) {
            $date = Carbon::parse($schedule->start_datetime)->format('Y-m-d');
            $groupedSchedules[$date][] = $schedule;
        }

        ksort($groupedSchedules);

        $navClasses = ['nav-link-secondary-darker', 'nav-link-purple-heart', 'nav-link-primary', 'nav-link-secodanry'];

        $dayNames = [__('First Day'), __('Second Day'), __('Third Day'), __('Fourth Day')];
    ?>

    <section class="section section-lg bg-default text-center">
        <div class="container">
            <h6 class="mt-1 sub-tit"><?php echo e($section->title[$locale] ?? ''); ?></h6>
            <h3 class="mt-3 gre-title"><?php echo e($section->description[$locale] ?? 'Event Agenda'); ?></h3>

            <div class="tabs-custom tabs-horizontal tabs-corporate" id="tabs-1">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
                    <?php $__currentLoopData = $groupedSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $daySchedules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $index = $loop->index;
                            $class = $navClasses[$index % count($navClasses)];
                            $dayLabel = $dayNames[$index] ?? __('Day') . ' ' . ($index + 1);
                            $tabId = 'tabs-1-' . $index;
                        ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo e($class); ?> <?php echo e($loop->first ? 'active' : ''); ?>"
                                href="#<?php echo e($tabId); ?>" data-toggle="tab" role="tab"
                                data-triangle=".nav-link-overlay">
                                <span class="nav-link-overlay"></span>
                                <span class="nav-link-cite"><?php echo e($dayLabel); ?></span>
                                <span class="nav-link-title">
                                    <?php echo e(Carbon::parse($date)->translatedFormat('j F Y')); ?>

                                </span>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content wow fadeIn">
                    <?php $__currentLoopData = $groupedSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $daySchedules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $tabId = 'tabs-1-' . $loop->index; ?>
                        <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>" id="<?php echo e($tabId); ?>">
                            <div class="card-group-custom card-group-corporate" role="tablist">
                                <?php $__currentLoopData = $daySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <article class="card card-custom card-corporate">
                                        <div class="card-header" role="tab">
                                            <div class="card-title">
                                                <a class="collapsed" data-toggle="collapse"
                                                    href="#collapse-<?php echo e($schedule->id); ?>" aria-expanded="false"
                                                    role="button">
                                                    <span class="schedule-classic">
                                                        <span
                                                            class="unit unit-spacing-md align-items-center d-block d-md-flex">
                                                            <span class="unit-left">
                                                                <span class="schedule-classic-img">
                                                                    <img src="<?php echo e(asset('public/'.$schedule->logo)); ?>"
                                                                        alt="" width="122" height="122" />
                                                                </span>
                                                            </span>
                                                            <span class="unit-body">
                                                                <span class="schedule-classic-content">
                                                                    <span class="schedule-classic-time">
                                                                        <?php echo e(Carbon::parse($schedule->start_datetime)->format('h:i A')); ?>

                                                                        to
                                                                        <?php echo e(Carbon::parse($schedule->end_datetime)->format('h:i A')); ?>

                                                                    </span>
                                                                    <span class="schedule-classic-title heading-4">
                                                                        <?php echo e($schedule->{'title_' . $locale}); ?>

                                                                    </span>
<?php if($schedule->speakers->count()): ?>
    <span class="schedule-classic-author" style="color:black;">
        by&nbsp;
        <?php $__currentLoopData = $schedule->speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $speaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span
                style="color: #cc252e; cursor: pointer;"
                onclick="window.location.href='<?php echo e(route('web.speaker')); ?>#speaker-<?php echo e($speaker->id); ?>'"
            >
                <?php echo e($speaker->name_en); ?>

            </span>
            <?php if(!$loop->last): ?>
                <span style="color: black;"> - </span>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </span>
<?php endif; ?>




                                                                </span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse-<?php echo e($schedule->id); ?>">
                                            <div class="card-body">
                                                <p><?php echo e($schedule->{'description_' . $locale}); ?></p>
                                                <div class="unit unit-spacing-xxs">
                                                    <div class="unit-left">
                                                        <svg class="svg-icon-sm svg-icon-primary" role="img">
                                                            <use
                                                                xlink:href="<?php echo e(asset('images/svg/sprite.svg#earth-globe')); ?>">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="unit-body">
                                                        <h5><?php echo e(__('Where')); ?></h5>
                                                        <p class="font-secondary"><?php echo e($schedule->{'location_' . $locale}); ?>

                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>

            </div>

        </div>
    </section>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/schdule.blade.php ENDPATH**/ ?>