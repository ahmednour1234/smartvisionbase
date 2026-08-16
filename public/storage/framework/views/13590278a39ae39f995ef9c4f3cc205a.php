<?php $__env->startSection('title', 'User List - Pages'); ?>

<?php $__env->startSection('vendor-style'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>" />
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')); ?>" />
  <!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Toastr JS -->

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
  <script src="<?php echo e(asset('assets/js/app-user-list.js')); ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <!-- Add success and error notifications -->
  <?php if(session('toast_success')): ?>
  <script>
    toastr.success('<?php echo e(session('toast_success')); ?>');
  </script>
<?php endif; ?>

<?php if(session('toast_error')): ?>
  <script>
    toastr.error('<?php echo e(session('toast_error')); ?>');
  </script>
<?php endif; ?>

  <!-- User Management Page -->

<!-- Filter and Search Data Section -->
<div class="card mb-4">
  <h5 class="card-header"><?php echo app('translator')->get("Filter and Search Data"); ?></h5>
  <form class="card-body" action="<?php echo e(route('users.index')); ?>" method="GET">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="multicol-username"><?php echo app('translator')->get("Name"); ?></label>
        <input type="text" name="name" id="multicol-username" class="form-control" placeholder="<?php echo app('translator')->get('Enter name'); ?>" />
      </div>
      <div class="col-md-6">
        <label class="form-label" for="multicol-email"><?php echo app('translator')->get("Email"); ?></label>
        <div class="input-group input-group-merge">
          <input type="text" name="email" id="multicol-email" class="form-control" placeholder="<?php echo app('translator')->get('Enter email'); ?>" />
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="multicol-phone"><?php echo app('translator')->get("Phone"); ?></label>
        <div class="input-group input-group-merge">
          <input type="text" name="phone" id="multicol-phone" class="form-control" placeholder="<?php echo app('translator')->get('Enter phone'); ?>" />
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="role"><?php echo app('translator')->get("Role"); ?></label>
        <select id="role" name="role_id" class="select2 form-select" data-allow-clear="true">
          <option value=""><?php echo app('translator')->get("Select Role"); ?></option>
          <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <div class="pt-4 row g-3">
      <button type="submit" class="btn btn-primary me-sm-3 me-1"><?php echo app('translator')->get("Filter"); ?></button>
    </form>
    <form action="<?php echo e(route('users.export')); ?>" method="GET">
      <input type="hidden" name="name" value="<?php echo e(request('name')); ?>">
      <input type="hidden" name="phone" value="<?php echo e(request('phone')); ?>">
      <input type="hidden" name="role_id" value="<?php echo e(request('role_id')); ?>">
      <input type="hidden" name="email" value="<?php echo e(request('email')); ?>">

      <button type="submit" class="btn btn-label-secondary"><?php echo app('translator')->get("Export Excel"); ?></button>
    </form>
    </div>
</div>

<!-- Add New User Button -->
<button style="width: 100%; margin: 20px 0;" class="add-new btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
  <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block"><?php echo app('translator')->get("Add New User"); ?></span>
</button>

<!-- Users Table -->
<div class="card">
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th><?php echo app('translator')->get("Name"); ?></th>
          <th><?php echo app('translator')->get("Role"); ?></th>
          <th><?php echo app('translator')->get("Actions"); ?></th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><span class="fw-medium"><?php echo e($user->name); ?></span></td>
            <td><?php echo e($user->role->name); ?></td>
            <td>
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateUserModal<?php echo e($user->id); ?>"><i class="ti ti-pencil me-1"></i> <?php echo app('translator')->get("Edit"); ?></a>
                  <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($user->id); ?>"><i class="ti ti-trash me-1"></i> <?php echo app('translator')->get("Delete"); ?></a>
                </div>
              </div>
            </td>
          </tr>

          <!-- Update User Modal -->
          <div class="modal fade" id="updateUserModal<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="updateUserModalLabel"><?php echo app('translator')->get("Update User"); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo app('translator')->get('Close'); ?>"></button>
                </div>
                <div class="modal-body">
                  <form action="<?php echo e(route('users.update', $user->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                      <label class="form-label" for="update-user-fullname"><?php echo app('translator')->get("Full Name"); ?></label>
                      <input type="text" class="form-control" id="update-user-fullname" name="name" value="<?php echo e($user->name); ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="update-user-email"><?php echo app('translator')->get("Email"); ?></label>
                      <input type="email" class="form-control" id="update-user-email" name="email" value="<?php echo e($user->email); ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="update-user-password"><?php echo app('translator')->get("Password"); ?></label>
                      <input type="password" class="form-control" id="update-user-password" name="password" placeholder="<?php echo app('translator')->get('Enter new password'); ?>" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="update-user-contact"><?php echo app('translator')->get("Phone"); ?></label>
                      <input type="text" class="form-control" id="update-user-contact" name="phone" value="<?php echo e($user->phone); ?>" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="update-user-role"><?php echo app('translator')->get("Role"); ?></label>
                      <select class="form-select" id="update-user-role" name="role_id" required>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($role->id); ?>" <?php echo e($role->id == $user->role_id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("Update"); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get("Cancel"); ?></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Delete Confirmation Modal -->
          <div class="modal fade" id="deleteModal<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="deleteModalLabel"><?php echo app('translator')->get("Confirm Deletion"); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo app('translator')->get('Close'); ?>"></button>
                </div>
                <div class="modal-body">
                  <?php echo app('translator')->get("Are you sure you want to delete the user"); ?> <?php echo e($user->name); ?>?
                </div>
                <div class="modal-footer">
                  <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger"><?php echo app('translator')->get("Delete"); ?></button>
                  </form>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get("Cancel"); ?></button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel"><?php echo app('translator')->get("Add User"); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo app('translator')->get('Close'); ?>"></button>
      </div>
      <div class="modal-body">
        <form action="<?php echo e(route('users.store')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label" for="add-user-fullname"><?php echo app('translator')->get("Full Name"); ?></label>
            <input type="text" class="form-control" id="add-user-fullname" name="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="add-user-email"><?php echo app('translator')->get("Email"); ?></label>
            <input type="email" class="form-control" id="add-user-email" name="email" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="add-user-password"><?php echo app('translator')->get("Password"); ?></label>
            <input type="password" class="form-control" id="add-user-password" name="password" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="add-user-contact"><?php echo app('translator')->get("Phone"); ?></label>
            <input type="text" class="form-control" id="add-user-contact" name="phone" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="add-user-role"><?php echo app('translator')->get("Role"); ?></label>
            <select class="form-select" id="add-user-role" name="role_id" required>
              <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("Add"); ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get("Cancel"); ?></button>
        </form>
      </div>
    </div>
  </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/users/list.blade.php ENDPATH**/ ?>