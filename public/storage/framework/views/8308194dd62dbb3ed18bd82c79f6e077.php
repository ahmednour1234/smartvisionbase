<?php
  $configData = Helper::appClasses();
?>



<?php $__env->startSection('title', 'Roles - Apps'); ?>

<?php $__env->startSection('vendor-style'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')); ?>" />
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <h4 class="mb-4"><?php echo app('translator')->get("Roles List"); ?></h4>
<!-- Role cards -->
<div class="row g-4">
  <div class="col-xl-12 col-lg-12 col-md-12">
    <div class="card h-100">
      <div class="row h-100">
        <div class="col-sm-5">
          <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
            <img src="<?php echo e(asset('assets/img/illustrations/add-new-roles.png')); ?>" class="img-fluid mt-sm-4 mt-md-0" alt="<?php echo app('translator')->get('Add New Role Image'); ?>" width="83">
          </div>
        </div>
        <div class="col-sm-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-primary mb-2 text-nowrap add-new-role"><?php echo app('translator')->get("Add New Role"); ?></button>
            <p class="mb-0 mt-1"><?php echo app('translator')->get("Add role, if it does not exist"); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <!-- Role Table -->
    <div class="card">
      <h5 class="card-header"><?php echo app('translator')->get("Roles Table"); ?></h5>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
          <tr>
            <th><?php echo app('translator')->get("Role Name"); ?></th>
            <th><?php echo app('translator')->get("Actions"); ?></th>
          </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <span class="fw-medium"><?php echo e($role->name); ?></span>
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu">
                    <!-- Edit Role Action -->
                    <a class="dropdown-item" data-bs-target="#editRoleModal" data-bs-toggle="modal" href="javascript:void(0);"
                       onclick="openEditRoleModal(<?php echo e(json_encode($role)); ?>)">
                      <i class="ti ti-pencil me-1"></i> <?php echo app('translator')->get("Edit"); ?>
                    </a>

                    <!-- Delete Role Action -->
                    <form action="<?php echo e(route('roles.destroy', $role->id)); ?>" method="POST" id="deleteRoleForm<?php echo e($role->id); ?>" style="display: none;">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                    </form>
                    <a class="dropdown-item" href="javascript:void(0);"
                       onclick="confirmDelete(<?php echo e($role->id); ?>)">
                      <i class="ti ti-trash me-1"></i> <?php echo app('translator')->get("Delete"); ?>
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Role Table -->
  </div>
</div>
<!--/ Role cards -->


  <!-- Add Role Modal -->
  <?php echo $__env->make('_partials/_modals/modal-add-role', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('_partials/_modals/modal-edit-role', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


  <!-- / Add Role Modal -->
<?php $__env->stopSection(); ?>
<script>
  function openEditRoleModal(role) {
  const form = document.getElementById('editRoleForm');
  form.action = `/roles/${role.id}`; // Update form action with role ID
  document.getElementById('editRoleName').value = role.name;

  // Clear existing permissions
  const checkboxes = document.querySelectorAll('#editRoleForm .edit-permission-checkbox');
  checkboxes.forEach(checkbox => checkbox.checked = false);

  // Populate permissions
  if (role.data) {
    const permissions = JSON.parse(role.data);
    for (const [permission, actions] of Object.entries(permissions)) {
      actions.forEach(action => {
        const checkbox = document.getElementById(`edit${permission}${action}`);
        if (checkbox) {
          checkbox.checked = true;
        }
      });
    }
  }

  // Show the modal
  new bootstrap.Modal(document.getElementById('editRoleModal')).show();
}
function confirmDelete(roleId) {
  if (confirm('Are you sure you want to delete this role?')) {
    document.getElementById(`deleteRoleForm${roleId}`).submit();
  }
}

  </script>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/users/roles.blade.php ENDPATH**/ ?>