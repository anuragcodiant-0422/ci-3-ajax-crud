<div class="container">
  <div class="row">
    <div class="col-8">
      <h2>User Accounts</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-user-modal">Add User</button>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6 form-group">
      <label class="sr-only" for="user-search">Search users</label>
      <input class="form-control" type="search" id="user-search" placeholder="Search name, email, or phone">
    </div>
    <div class="col-md-3 form-group">
      <label class="sr-only" for="gender-filter">Filter by gender</label>
      <select class="form-control" id="gender-filter">
        <option value="">All genders</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <div class="col-md-3 form-group">
      <button type="button" class="btn btn-secondary" id="clear-user-filters">Clear filters</button>
    </div>
  </div>
  <p id="users-result-count" class="text-muted" aria-live="polite"></p>

  <div id="users-feedback" role="status" aria-live="polite"></div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th>ID</th>
          <th>First name</th>
          <th>Last name</th>
          <th>Email</th>
          <th>Password</th>
          <th>Gender</th>
          <th>Phone</th>
          <th>Created</th>
          <th>Modified</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="users-list">
        <tr><td colspan="11" class="text-center">Loading users...</td></tr>
      </tbody>
    </table>
  </div>

  <ul id="pagination" class="pagination pagination-sm"></ul>
</div>

<div class="modal fade" id="create-user-modal" tabindex="-1" role="dialog" aria-labelledby="create-user-title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="create-user-form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
          <h4 class="modal-title" id="create-user-title">Add user</h4>
        </div>
        <div class="modal-body">
          <div id="create-user-error" class="text-danger" role="alert" aria-live="polite" hidden></div>
          <div class="form-group">
            <label for="create-first-name">First name</label>
            <input class="form-control" type="text" name="first_name" id="create-first-name" aria-describedby="create-first-name-error" required>
            <small class="field-error text-danger" data-error-for="first_name" id="create-first-name-error" hidden></small>
          </div>
          <div class="form-group">
            <label for="create-last-name">Last name</label>
            <input class="form-control" type="text" name="last_name" id="create-last-name" aria-describedby="create-last-name-error" required>
            <small class="field-error text-danger" data-error-for="last_name" id="create-last-name-error" hidden></small>
          </div>
          <div class="form-group">
            <label for="create-email">Email</label>
            <input class="form-control" type="email" name="email" id="create-email" aria-describedby="create-email-error" required>
            <small class="field-error text-danger" data-error-for="email" id="create-email-error" hidden></small>
          </div>
          <div class="form-group">
            <label for="create-password">Password</label>
            <input class="form-control" type="password" name="password" id="create-password" minlength="6" autocomplete="new-password" aria-describedby="create-password-error" required>
            <small class="field-error text-danger" data-error-for="password" id="create-password-error" hidden></small>
          </div>
          <div class="form-group">
            <label for="create-gender">Gender</label>
            <select class="form-control" name="gender" id="create-gender" aria-describedby="create-gender-error" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <small class="field-error text-danger" data-error-for="gender" id="create-gender-error" hidden></small>
          </div>
          <div class="form-group">
            <label for="create-phone">Phone</label>
            <input class="form-control" type="text" name="phone" id="create-phone">
          </div>
          <div class="form-group">
            <label for="create-status">Status</label>
            <select class="form-control" name="status" id="create-status" aria-describedby="create-status-error" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
            <small class="field-error text-danger" data-error-for="status" id="create-status-error" hidden></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add user</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="edit-user-title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="edit-user-form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
          <h4 class="modal-title" id="edit-user-title">Edit user</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-user-id">
          <div class="form-group">
            <label for="edit-first-name">First name</label>
            <input class="form-control" type="text" name="first_name" id="edit-first-name" required>
          </div>
          <div class="form-group">
            <label for="edit-last-name">Last name</label>
            <input class="form-control" type="text" name="last_name" id="edit-last-name" required>
          </div>
          <div class="form-group">
            <label for="edit-email">Email</label>
            <input class="form-control" type="email" name="email" id="edit-email" required>
          </div>
          <div class="form-group">
            <label for="edit-password">New password</label>
            <input class="form-control" type="password" name="password" id="edit-password" autocomplete="new-password">
            <small class="form-text text-muted">Leave blank to keep the current password.</small>
          </div>
          <div class="form-group">
            <label for="edit-gender">Gender</label>
            <select class="form-control" name="gender" id="edit-gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="form-group">
            <label for="edit-phone">Phone</label>
            <input class="form-control" type="text" name="phone" id="edit-phone">
          </div>
          <div class="form-group">
            <label for="edit-status">Status</label>
            <select class="form-control" name="status" id="edit-status" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
<script>
  var url = "<?php echo site_url('users'); ?>";
  var createUrl = "<?php echo site_url('users/create'); ?>";
  var updateUrl = "<?php echo site_url('users/update'); ?>";
  var deleteUrl = "<?php echo site_url('users/delete'); ?>";
</script>
<script src="<?php echo base_url('assets/js/users-js.js'); ?>"></script>

