<div class="row g-4">
  
  <?php if(empty($homeSection->id) || $homeSection->id != 2): ?>
    <div class="col-md-6">
      <label class="form-label"><?php echo e(__('home_sections.title_ar')); ?></label>
      <input type="text" name="title[ar]" class="form-control"
             value="<?php echo e(old('title.ar', $homeSection->title['ar'] ?? '')); ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo e(__('home_sections.title_en')); ?></label>
      <input type="text" name="title[en]" class="form-control"
             value="<?php echo e(old('title.en', $homeSection->title['en'] ?? '')); ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label"><?php echo e(__('home_sections.description_ar')); ?></label>
      <textarea name="description[ar]" class="form-control" rows="3"><?php echo e(old('description.ar', $homeSection->description['ar'] ?? '')); ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo e(__('home_sections.description_en')); ?></label>
      <textarea name="description[en]" class="form-control" rows="3"><?php echo e(old('description.en', $homeSection->description['en'] ?? '')); ?></textarea>
    </div>
  <?php endif; ?>

  
  <?php if(empty($homeSection->id) || !in_array($homeSection->id, [5, 8])): ?>
    <div class="col-md-4">
      <label class="form-label"><?php echo e(__('home_sections.media_type')); ?></label>
      <select name="media_type" class="form-select" id="media_type_select" required>
        <option value="image" <?php echo e(old('media_type', $homeSection->media_type ?? '') == 'image' ? 'selected' : ''); ?>>
          <?php echo e(__('home_sections.image')); ?>

        </option>
        <option value="video" <?php echo e(old('media_type', $homeSection->media_type ?? '') == 'video' ? 'selected' : ''); ?>>
          <?php echo e(__('home_sections.video')); ?>

        </option>
        <option value="link" <?php echo e(old('media_type', $homeSection->media_type ?? '') == 'link' ? 'selected' : ''); ?>>
          <?php echo e(__('home_sections.link')); ?>

        </option>
      </select>
    </div>

    
    <div class="col-md-8 media-upload-section">
      <label class="form-label"><?php echo e(__('home_sections.media_path')); ?></label>
      <input type="file" name="media" class="form-control" <?php echo e(isset($homeSection) ? '' : 'required'); ?>>
    </div>

    
    <div class="col-md-8 media-link-section d-none">
      <label class="form-label"><?php echo e(__('home_sections.media_link')); ?></label>
      <input type="text" name="media_path" class="form-control"
             value="<?php echo e(old('media_path', $homeSection->media_path ?? '')); ?>" placeholder="https://example.com/video.mp4">
    </div>

    
    <div class="col-md-8 thumbnail-upload-section d-none">
      <label class="form-label"><?php echo e(__('home_sections.thumbnail')); ?></label>
      <input type="file" name="thumbnail" class="form-control" accept="image/*">
    </div>

    
    <?php if(isset($homeSection) && $homeSection->media_path): ?>
      <div class="col-md-12">
        <div class="mt-3">
          <?php echo e(__('home_sections.current_file')); ?>:
          <?php if($homeSection->media_type === 'image'): ?>
            <img src="<?php echo e(asset($homeSection->media_path)); ?>" class="img-fluid rounded mt-2" style="max-height: 200px;" alt="Image">
          <?php elseif($homeSection->media_type === 'video'): ?>
            <div class="mt-3">
              <label class="form-label d-block"><?php echo e(__('home_sections.preview')); ?></label>
              <div class="rounded overflow-hidden shadow-sm" style="max-height: 250px;">
                <video autoplay muted loop playsinline style="width: 100%; height: auto; object-fit: cover; display: block;">
                  <source src="<?php echo e(asset($homeSection->media_path)); ?>" type="video/mp4">
                  <?php echo e(__('home_sections.your_browser_does_not_support_video')); ?>

                </video>
              </div>
            </div>
          <?php elseif($homeSection->media_type === 'link'): ?>
            <div class="mt-2">
              <a href="<?php echo e($homeSection->media_path); ?>" target="_blank"><?php echo e($homeSection->media_path); ?></a>
              <?php if($homeSection->thumbnail ?? false): ?>
                <div class="mt-2">
                  <label><?php echo e(__('home_sections.thumbnail')); ?></label><br>
                  <img src="<?php echo e(asset($homeSection->thumbnail)); ?>" class="img-fluid rounded" style="max-height: 150px;">
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  
  <?php if(empty($homeSection->id) || $homeSection->id != 2): ?>
    <div class="col-md-4">
      <label class="form-label"><?php echo e(__('home_sections.section_order')); ?></label>
      <input type="number" name="section_order" class="form-control"
             value="<?php echo e(old('section_order', $homeSection->section_order ?? 1)); ?>" required>
    </div>
  <?php endif; ?>

  
  <div class="col-md-4">
    <label class="form-label d-block"><?php echo e(__('home_sections.is_active')); ?></label>
    <input type="hidden" name="is_active" value="0">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="is_active" value="1"
             <?php echo e(old('is_active', $homeSection->is_active ?? true) ? 'checked' : ''); ?>>
      <label class="form-check-label"><?php echo e(__('home_sections.active')); ?></label>
    </div>
  </div>
</div>


<script>
  function toggleMediaFields() {
    const type = document.getElementById('media_type_select').value;
    const uploadSection = document.querySelector('.media-upload-section');
    const linkSection = document.querySelector('.media-link-section');
    const thumbSection = document.querySelector('.thumbnail-upload-section');

    if (type === 'link') {
      uploadSection.classList.add('d-none');
      linkSection.classList.remove('d-none');
      thumbSection.classList.remove('d-none');
    } else {
      uploadSection.classList.remove('d-none');
      linkSection.classList.add('d-none');
      thumbSection.classList.add('d-none');
    }
  }

  document.getElementById('media_type_select').addEventListener('change', toggleMediaFields);

  // تنفيذ عند التحميل لتفعيل الحالة الصحيحة
  document.addEventListener('DOMContentLoaded', toggleMediaFields);
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/home_sections/_form.blade.php ENDPATH**/ ?>