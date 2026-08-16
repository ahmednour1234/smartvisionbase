<style>
    a{
        text-decoration: none;
    }
 .card {
    /* ... باقي المتغيرات كما هي ... */
    border: none !important;
}

</style>

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
            <h3 class="mt-4 gre-title" style="font-size: 40px;font-weight: bolder;"><?php echo e($section->description[$locale] ?? 'Event Agenda'); ?></h3>

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
                                                <p class="collapsed" data-toggle="collapse"
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
                                                </p>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse-<?php echo e($schedule->id); ?>">
                                            <div class="card-body">
                                                <p><?php echo e($schedule->{'description_' . $locale}); ?></p>
                                                <div class="unit unit-spacing-xxs">
                                                    <div class="unit-left">
                                                        <svg class="svg-icon-sm svg-icon-primary" role="img">
                                                            <use
                                                                xlink:href="<?php echo e(asset('public/images/svg/sprite.svg#earth-globe')); ?>">
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
                 <div class="text-center">
            <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
               href="<?php echo e(route('web.schdule')); ?>" data-triangle=".button-overlay">
                <span>More Schdule</span>
                <span class="button-overlay"></span>
            </a>
        </div>
            </div>

        </div>
    </section>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/schedule.blade.php ENDPATH**/ ?>