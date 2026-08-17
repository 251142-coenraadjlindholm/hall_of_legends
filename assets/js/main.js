document.addEventListener('DOMContentLoaded', function () {
  var dropzone = document.getElementById('dropzone');
  var fileInput = document.getElementById('file-input');
  var label = document.getElementById('dropzone-label');

  // If both the dropzone and file input elements exist, set up event listeners for drag-and-drop and file selection.
  if (dropzone && fileInput) {
    dropzone.addEventListener('click', function () { fileInput.click(); });

    // Update the label text when a file is selected.
    fileInput.addEventListener('change', function () {
      if (fileInput.files.length > 0) {
        label.textContent = fileInput.files[0].name;
      }
    });

    // Add and remove the 'is-dragging' class on drag events to provide visual feedback.
    ['dragover', 'dragenter'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        dropzone.classList.add('is-dragging');
      });
    });

    // Remove the 'is-dragging' class when the drag leaves or a file is dropped.
    ['dragleave', 'drop'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        dropzone.classList.remove('is-dragging');
      });
    });

    // Update the label text when a file is dropped.
    dropzone.addEventListener('drop', function (e) {
      if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        label.textContent = e.dataTransfer.files[0].name;
      }
    });
  }

  // Add a confirmation dialog for forms with the 'data-confirm' attribute to prevent accidental submissions.
  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });
});