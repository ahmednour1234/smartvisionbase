<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="<?php echo app('translator')->get('Close'); ?>"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="role-title mb-2"><?php echo app('translator')->get("Add New Role"); ?></h3>
          <p class="text-muted"><?php echo app('translator')->get("Set role permissions"); ?></p>
        </div>
        <!-- Add role form -->
        <form id="addRoleForm" class="row g-3" action="<?php echo e(route('roles.store')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <div class="col-12 mb-4">
            <label class="form-label" for="roleName"><?php echo app('translator')->get("Role Name"); ?></label>
            <input type="text" id="roleName" name="name" class="form-control" placeholder="<?php echo app('translator')->get('Enter a role name'); ?>" required />
          </div>
          <div class="col-12">
            <h5><?php echo app('translator')->get("Role Permissions"); ?></h5>
            <!-- Permission table -->
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium"><?php echo app('translator')->get("Administrator Access"); ?></td>
                    <td>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllPermissions" onclick="toggleAllPermissions()" />
                        <label class="form-check-label" for="selectAllPermissions">
                          <?php echo app('translator')->get("Select All"); ?>
                        </label>
                      </div>
                    </td>
                  </tr>
                  <?php $__currentLoopData = ['User Management']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td class="text-nowrap fw-medium"><?php echo app('translator')->get($permission); ?></td>
                    <td>
                      <div class="d-flex">
                        <?php $__currentLoopData = ['read', 'write', 'create']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check me-3">
                          <input class="form-check-input" type="checkbox" name="permissions[<?php echo e($permission); ?>][actions][]" value="<?php echo e($action); ?>" id="<?php echo e($permission . $action); ?>" />
                          <label class="form-check-label" for="<?php echo e($permission . $action); ?>"><?php echo app('translator')->get(ucfirst($action)); ?></label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1"><?php echo app('translator')->get("Submit"); ?></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="<?php echo app('translator')->get('Close'); ?>"><?php echo app('translator')->get("Cancel"); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script defer>
  // Function to toggle all permission checkboxes
  function toggleAllPermissions() {
    const checkboxes = document.querySelectorAll('#addRoleForm input[type="checkbox"]:not(#selectAllPermissions)');
    const selectAll = document.getElementById('selectAllPermissions');

    // Update checkboxes state based on "select all" checkbox
    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAll.checked;
    });
  }

  // Ensure the toggleAllPermissions runs when the page loads
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize the toggle permissions function
    toggleAllPermissions();
  });
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/_partials/_modals/modal-add-role.blade.php ENDPATH**/ ?>