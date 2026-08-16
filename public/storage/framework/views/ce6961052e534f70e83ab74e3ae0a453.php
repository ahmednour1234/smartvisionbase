<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-role">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="role-title mb-2">Edit Role</h3>
          <p class="text-muted">Update role permissions</p>
        </div>
        <!-- Edit role form -->
        <form id="editRoleForm" class="row g-3" action="" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <div class="col-12 mb-4">
            <label class="form-label" for="editRoleName">Role Name</label>
            <input type="text" id="editRoleName" name="name" class="form-control"
                   placeholder="Enter role name" value="<?php echo e(old('name', isset($role) ? $role->name : '')); ?>" required />
          </div>
          <div class="col-12">
            <h5>Role Permissions</h5>
            <!-- Permission table -->
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium">Administrator Access</td>
                    <td>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="editSelectAllPermissions" onclick="toggleEditAllPermissions()" />
                        <label class="form-check-label" for="editSelectAllPermissions">
                          Select All
                        </label>
                      </div>
                    </td>
                  </tr>
                  <?php $__currentLoopData = ['User Management']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td class="text-nowrap fw-medium"><?php echo e($permission); ?></td>
                    <td>
                      <div class="d-flex">
                        <?php $__currentLoopData = ['read', 'write', 'create']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check me-3">
                          <input class="form-check-input edit-permission-checkbox" type="checkbox"
                                 name="permissions[<?php echo e($permission); ?>][actions][]"
                                 value="<?php echo e($action); ?>"
                                 id="edit<?php echo e($permission . $action); ?>"
                                 <?php if(isset($role->permissions[$permission]) && in_array($action, $role->permissions[$permission]['actions'])): ?> checked <?php endif; ?> />
                          <label class="form-check-label" for="edit<?php echo e($permission . $action); ?>"><?php echo e(ucfirst($action)); ?></label>
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
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script defer>
  // Function to toggle all permission checkboxes for the edit modal
  function toggleEditAllPermissions() {
    const checkboxes = document.querySelectorAll('#editRoleForm .edit-permission-checkbox');
    const selectAll = document.getElementById('editSelectAllPermissions');

    // Update checkboxes state based on "select all" checkbox
    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAll.checked;
    });
  }

  // Populate the edit modal with role data
  function showEditModal(role) {
    const form = document.getElementById('editRoleForm');
    form.action = `/roles/${role.id}`; // Update form action with role ID
    document.getElementById('editRoleName').value = role.name;

    // Clear existing permissions
    const checkboxes = document.querySelectorAll('#editRoleForm .edit-permission-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);

    // Parse permissions from the role and populate checkboxes
    if (role.permissions) {
      const permissions = JSON.parse(role.permissions); // Assuming role.permissions is a JSON string, parse it
      for (const [permission, actions] of Object.entries(permissions)) {
        actions.forEach(action => {
          const checkbox = document.getElementById(`edit${permission}${action}`);
          if (checkbox) {
            checkbox.checked = true;  // Check the relevant checkbox based on actions
          }
        });
      }
    }

    // Show modal
    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
  }
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/_partials/_modals/modal-edit-role.blade.php ENDPATH**/ ?>