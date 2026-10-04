var page = 1;
var pageSize = 5;

function showFeedback(message, type) {
   $('#users-feedback')
      .removeClass()
      .addClass('alert alert-' + type)
      .text(message);
}

function clearCreateErrors() {
   $('#create-user-error').text('').prop('hidden', true);
   $('#create-user-form .field-error').text('').prop('hidden', true);
   $('#create-user-form [aria-invalid="true"]')
      .removeAttr('aria-invalid')
      .removeClass('is-invalid');
}

function showCreateError(xhr) {
   var response = xhr.responseJSON || {};
   var message = response.message || 'Unable to add the user.';
   if (response.field) {
      var input = $('#create-user-form [name="' + response.field + '"]');
      var fieldError = $('#create-user-form [data-error-for="' + response.field + '"]');
      if (input.length && fieldError.length) {
         input.addClass('is-invalid').attr('aria-invalid', 'true').trigger('focus');
         fieldError.text(message).prop('hidden', false);
         return;
      }
   }

   $('#create-user-error').text(message).prop('hidden', false);
}

function renderPagination(totalPages, activePage) {
   var pagination = $('#pagination').empty();
   if (totalPages === 0) {
      return;
   }

   var firstPage = Math.max(1, activePage - 2);
   var lastPage = Math.min(totalPages, firstPage + 4);
   firstPage = Math.max(1, lastPage - 4);

   function addPage(label, targetPage, active, disabled) {
      var item = $('<li>').addClass('page-item');
      var link = $('<a>').addClass('page-link').attr('href', '#').text(label);
      if (active) {
         item.addClass('active');
      }
      if (disabled) {
         item.addClass('disabled');
      } else {
         link.attr('data-page', targetPage);
      }
      pagination.append(item.append(link));
   }

   addPage('Previous', activePage - 1, false, activePage === 1);
   for (var pageNumber = firstPage; pageNumber <= lastPage; pageNumber++) {
      addPage(pageNumber, pageNumber, pageNumber === activePage, false);
   }
   addPage('Next', activePage + 1, false, activePage === totalPages);
}

function loadUsers(requestedPage) {
   page = requestedPage;
   $.ajax({
      dataType: 'json',
      url: url,
      data: {
         page: requestedPage,
         search: $('#user-search').val().trim(),
         gender: $('#gender-filter').val()
      }
   }).done(function (response) {
      var users = Array.isArray(response.data) ? response.data : [];
      var total = Number(response.total || 0);
      var totalPages = Math.ceil(total / pageSize);

      if (totalPages > 0 && requestedPage > totalPages) {
         loadUsers(totalPages);
         return;
      }

      var list = $('#users-list').empty();
      $('#users-result-count').text('Showing ' + users.length + ' of ' + total + ' users');
      if (users.length === 0) {
         list.append($('<tr>').append(
            $('<td>', {colspan: 11, class: 'text-center'}).text('No users found.')
         ));
         renderPagination(totalPages, requestedPage);
         return;
      }

      users.forEach(function (user) {
         var actions = $('<td>');
         var editButton = $('<button>', {
            type: 'button',
            class: 'btn btn-primary btn-sm edit-user',
            text: 'Edit'
         }).data('user', user);
         var deleteButton = $('<button>', {
            type: 'button',
            class: 'btn btn-danger btn-sm delete-user',
            text: 'Delete'
         }).data('id', user.id);

         actions.append(editButton).append(' ').append(deleteButton);
         list.append($('<tr>').append(
            $('<td>').text(user.id || '-'),
            $('<td>').text(user.first_name || '-'),
            $('<td>').text(user.last_name || '-'),
            $('<td>').text(user.email || '-'),
            $('<td>').text(user.password || '********'),
            $('<td>').text(user.gender || '-'),
            $('<td>').text(user.phone || '-'),
            $('<td>').text(user.created || '-'),
            $('<td>').text(user.modified || '-'),
            $('<td>').text(user.status === '1' || user.status === 1 ? 'Active' : 'Inactive'),
            actions
         ));
      });

      renderPagination(totalPages, requestedPage);
      $('#users-feedback').removeClass().empty();
   }).fail(function (xhr) {
      var message = xhr.responseJSON && xhr.responseJSON.message
         ? xhr.responseJSON.message
         : 'Unable to load users.';
      $('#users-list').html(
         $('<tr>').append($('<td>', {colspan: 11, class: 'text-danger text-center'}).text(message))
      );
   });
}

$(document).on('click', '#pagination a[data-page]', function (event) {
   event.preventDefault();
   loadUsers(Number($(this).attr('data-page')));
});

$(document).on('click', '.edit-user', function () {
   var user = $(this).data('user');
   $('#edit-user-id').val(user.id);
   $('#edit-first-name').val(user.first_name);
   $('#edit-last-name').val(user.last_name);
   $('#edit-email').val(user.email);
   $('#edit-password').val('');
   $('#edit-gender').val(user.gender);
   $('#edit-phone').val(user.phone);
   $('#edit-status').val(String(user.status));
   $('#edit-user-modal').modal('show');
});

$('#create-user-form').on('submit', function (event) {
   event.preventDefault();
   clearCreateErrors();
   $.ajax({
      url: createUrl,
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json'
   }).done(function (response) {
      $('#create-user-modal').modal('hide');
      $('#create-user-form')[0].reset();
      clearCreateErrors();
      showFeedback(response.message || 'User added.', 'success');
      loadUsers(1);
   }).fail(function (xhr) {
      showCreateError(xhr);
   });
});

$('#create-user-form').on('input change', 'input, select', function () {
   $(this).removeClass('is-invalid').removeAttr('aria-invalid');
   $('#create-user-form [data-error-for="' + this.name + '"]').text('').prop('hidden', true);
   $('#create-user-error').text('').prop('hidden', true);
});

$('#create-user-modal').on('show.bs.modal', clearCreateErrors);

$('#edit-user-form').on('submit', function (event) {
   event.preventDefault();
   var id = $('#edit-user-id').val();
   $.ajax({
      url: updateUrl + '/' + encodeURIComponent(id),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json'
   }).done(function (response) {
      $('#edit-user-modal').modal('hide');
      showFeedback(response.message || 'User updated.', 'success');
      loadUsers(page);
   }).fail(function (xhr) {
      var message = xhr.responseJSON && xhr.responseJSON.message
         ? xhr.responseJSON.message
         : 'Unable to update the user.';
      showFeedback(message, 'danger');
   });
});

$(document).on('click', '.delete-user', function () {
   var id = $(this).data('id');
   if (!window.confirm('Delete this user account? This cannot be undone.')) {
      return;
   }

   $.ajax({
      url: deleteUrl + '/' + encodeURIComponent(id),
      type: 'POST',
      dataType: 'json'
   }).done(function (response) {
      showFeedback(response.message || 'User deleted.', 'success');
      loadUsers(page);
   }).fail(function (xhr) {
      var message = xhr.responseJSON && xhr.responseJSON.message
         ? xhr.responseJSON.message
         : 'Unable to delete the user.';
      showFeedback(message, 'danger');
   });
});

$(function () {
   loadUsers(page);
});

var searchTimer;
$('#user-search').on('input', function () {
   clearTimeout(searchTimer);
   searchTimer = setTimeout(function () {
      loadUsers(1);
   }, 250);
});

$('#gender-filter').on('change', function () {
   loadUsers(1);
});

$('#clear-user-filters').on('click', function () {
   $('#user-search').val('');
   $('#gender-filter').val('');
   loadUsers(1);
});