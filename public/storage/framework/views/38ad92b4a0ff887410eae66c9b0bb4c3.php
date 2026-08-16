

<?php $__env->startSection('content'); ?>
<?php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    // safe asset: يضمن إضافة public/ لو المسار محلي ومش سابقًا تبدأ بـ public/ أو storage/ أو لينك كامل
    $assetPublic = function (?string $path) {
        if (!$path) return null;
        if (Str::startsWith($path, ['http://','https://','storage/','public/'])) {
            return asset($path);
        }
        return asset('public/' . ltrim($path, '/'));
    };

    // استخراج ID اليوتيوب من عدة صيغ، أو قبول الـID مباشرة
    $youtubeEmbed = function (?string $url) {
        if (!$url) return null;
        $u = trim($url);

        // ID مباشر (11 حرف/رقم/شرطة سفلية)
        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $u)) {
            $id = $u;
            return "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1&playsinline=1";
        }

        // حاول تفكيك الرابط
        $parts = @parse_url($u) ?: [];
        $host  = $parts['host']  ?? '';
        $path  = $parts['path']  ?? '';
        $query = $parts['query'] ?? '';

        $id = null;

        // youtu.be/<id>
        if (Str::contains($host, 'youtu.be')) {
            $id = ltrim($path, '/');
        }

        // youtube.com/* أنماط متعددة
        if (!$id && Str::contains($host, 'youtube.com')) {
            // /watch?v=<id>
            if (Str::startsWith($path, '/watch') && $query) {
                parse_str($query, $q);
                $id = $q['v'] ?? null;
            }
            // /shorts/<id>
            if (!$id && Str::startsWith($path, '/shorts/')) {
                $chunks = explode('/', trim($path, '/')); // ['shorts', '<id>']
                $id = $chunks[1] ?? null;
            }
            // /embed/<id>
            if (!$id && Str::startsWith($path, '/embed/')) {
                $chunks = explode('/', trim($path, '/')); // ['embed', '<id>']
                $id = $chunks[1] ?? null;
            }
        }

        // تأكد من صحة الـID
        if ($id && preg_match('~^[A-Za-z0-9_-]{11}$~', $id)) {
            return "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1&playsinline=1";
        }

        return null;
    };
?>

<style>
  @media (max-width: 991.98px) {
    .breadcrumbs-custom { height: 350px !important; background-size: cover; background-position: center; }
    .breadcrumbs-custom-title { font-size: 28px; padding-top:150px; }
  }
  .breadcrumbs-custom { background-size: cover; background-position: center; }
</style>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>); background-size: cover;">
  <div class="container text-start">
    <h3 class="breadcrumbs-custom-title text-white fw-bold">
      <?php echo e($multi_media_category->{'name_' . $locale} ?? ''); ?>

    </h3>
  </div>
</section>

<!-- Multimedia Section -->
<section class="section section-lg bg-light">
  <div class="container">

    <!-- Videos -->
    <div class="mb-5">
      <h3 class="text-primary text-center mb-4"><?php echo e(__('Videos')); ?></h3>
      <div class="row justify-content-center g-4">
        <?php $__currentLoopData = $multimedias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            // دعم: مصفوفة / JSON / نص فيه روابط متعددة مفصولة بمسافات أو فواصل
            $raw = $media->links ?? [];
            $videos = is_array($raw) ? $raw : (is_string($raw) ? preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) : []);
          ?>
          <?php $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $embedUrl = $youtubeEmbed($link); ?>
            <?php if($embedUrl): ?>
              <div class="col-md-8 col-lg-6">
                <iframe
                  src="<?php echo e($embedUrl); ?>"
                  class="w-100 rounded shadow-sm"
                  style="aspect-ratio: 16 / 9;"
                  loading="lazy"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen>
                </iframe>
              </div>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    <!-- Images -->
    <div>
      <h3 class="text-primary text-center mb-4"><?php echo e(__('Images')); ?></h3>
      <div class="row justify-content-center g-4">
        <div class="row g-4">
          <?php $__currentLoopData = $multimedias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $rawImgs = $media->images ?? [];
              $images  = is_array($rawImgs) ? $rawImgs : (is_string($rawImgs) ? json_decode($rawImgs, true) : []);
              $images  = is_array($images) ? $images : [];
            ?>
            <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $imgSrc = $assetPublic($image);
              ?>
              <div class="col-md-4 col-lg-3 pt-4">
                <a href="#" class="popup-trigger d-block" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="<?php echo e($imgSrc); ?>">
                  <div class="image-wrapper rounded overflow-hidden">
                    <img src="<?php echo e($imgSrc); ?>" class="img-fluid gallery-thumb" alt="image">
                  </div>
                </a>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-white border-0">
      <div class="modal-body text-center p-0">
        <img src="" class="img-fluid rounded w-100" id="popupImage" alt="popup image">
      </div>
    </div>
  </div>
</div>

<!-- Styles -->
<style>
  .gallery-thumb { height: 250px; object-fit: cover; width: 100%; transition: transform 0.3s ease; }
  .gallery-thumb:hover { transform: scale(1.03); }
  .image-wrapper { height: 250px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; }
  @media (max-width: 768px) { .gallery-thumb, .image-wrapper { height: 180px; } }
</style>

<!-- Script -->
<script>
  const imageModal = document.getElementById('imageModal');
  imageModal.addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;
    const imageUrl = trigger.getAttribute('data-image');
    const modalImage = imageModal.querySelector('#popupImage');
    modalImage.src = imageUrl;
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/showmultimedia.blade.php ENDPATH**/ ?>