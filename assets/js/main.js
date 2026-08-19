// Animation for background Logic
document.addEventListener('DOMContentLoaded', function () {
  var canvas = document.getElementById('scanner-canvas');

  // If the canvas element exists, set up the animation logic.
  if (canvas) {
    var context = canvas.getContext('2d');
    var state = {
      width: 0,
      height: 0,
      time: 0,
      mouseX: 0.5,
      mouseY: 0.5,
      mouseActive: false
    };

    // Resize the canvas to match its parent element's size and adjust for device pixel ratio.
    function resizeCanvas() {
      var rect = canvas.parentElement.getBoundingClientRect();
      var dpr = window.devicePixelRatio || 1;
      state.width = rect.width;
      state.height = rect.height;
      canvas.width = Math.max(1, Math.round(rect.width * dpr));
      canvas.height = Math.max(1, Math.round(rect.height * dpr));
      context.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    // Draw random noise on the canvas to create a grainy effect.
    function drawNoise() {
      var grainCount = 1500;
      for (var i = 0; i < grainCount; i += 1) {
        var x = Math.random() * state.width;
        var y = Math.random() * state.height;
        var alpha = (Math.random() * 0.08) + 0.02;
        context.fillStyle = 'rgba(255,255,255,' + alpha + ')';
        context.fillRect(x, y, 1.3, 1.3);
      }
    }

    // Draw a glowing effect around the mouse cursor when it is active.
    function drawMouseGlow() {
      if (!state.mouseActive) {
        return;
      }

      var mx = state.mouseX * state.width;
      var my = state.mouseY * state.height;
      var glowRadius = Math.min(state.width, state.height) * 0.34;
      var glow = context.createRadialGradient(mx, my, 0, mx, my, glowRadius);

      glow.addColorStop(0, 'rgba(255,255,255,0.18)');
      glow.addColorStop(0.28, 'rgba(240,80,80,0.14)');
      glow.addColorStop(0.65, 'rgba(29,79,141,0.08)');
      glow.addColorStop(1, 'rgba(29,79,141,0)');

      context.fillStyle = glow;
      context.fillRect(0, 0, state.width, state.height);
    }

    // The main animation loop that draws the background, waves, sweeps, scan lines, mouse glow, noise, and vignette effect.
    function drawFrame() {
      var width = state.width;
      var height = state.height;
      var t = state.time;

      context.clearRect(0, 0, width, height);

      var baseGradient = context.createRadialGradient(
        width * 0.5,
        height * 0.4,
        0,
        width * 0.5,
        height * 0.4,
        Math.max(width, height) * 1.2
      );
      baseGradient.addColorStop(0, '#1c2d49');
      baseGradient.addColorStop(0.38, '#0b1730');
      baseGradient.addColorStop(0.72, '#070e1c');
      baseGradient.addColorStop(1, '#030810');
      context.fillStyle = baseGradient;
      context.fillRect(0, 0, width, height);

      for (var i = 0; i < 8; i += 1) {
        var y = height * (0.20 + i * 0.12) + Math.sin(t * 1.4 + i * 1.3) * 28;
        context.beginPath();
        context.moveTo(0, y);

        for (var x = 0; x <= width; x += 18) {
          var wave = Math.sin((x * 0.016) + (t * 2.2) + i) * (18 + i * 6);
          var redWave = Math.cos((x * 0.012) - (t * 2.1) + i) * (10 + i * 3);
          context.lineTo(x, y + wave + redWave * 0.4);
        }

        context.lineTo(width, height + 40);
        context.lineTo(0, height + 40);
        context.closePath();

        var alpha = 0.10 + i * 0.025;
        context.fillStyle = 'rgba(' + (i % 2 === 0 ? '29,79,141' : '240,80,80') + ',' + alpha + ')';
        context.fill();
      }

      var sweepCenter = width * (0.5 + Math.sin(t * 0.6) * 0.12);
      var sweepGradient = context.createLinearGradient(sweepCenter - 320, 0, sweepCenter + 320, 0);
      sweepGradient.addColorStop(0, 'rgba(5, 8, 17, 0)');
      sweepGradient.addColorStop(0.45, 'rgba(255,255,255,0.08)');
      sweepGradient.addColorStop(0.5, 'rgba(255,255,255,0.20)');
      sweepGradient.addColorStop(0.55, 'rgba(240,80,80,0.10)');
      sweepGradient.addColorStop(1, 'rgba(5, 8, 17, 0)');
      context.fillStyle = sweepGradient;
      context.fillRect(sweepCenter - 320, 0, 640, height);

      var scanY = ((t * 120) % (height + 220)) - 140;
      var scanBand = context.createLinearGradient(0, scanY, 0, scanY + 180);
      scanBand.addColorStop(0, 'rgba(5,8,17,0)');
      scanBand.addColorStop(0.45, 'rgba(255,255,255,0.12)');
      scanBand.addColorStop(0.50, 'rgba(240,80,80,0.22)');
      scanBand.addColorStop(0.55, 'rgba(255,255,255,0.12)');
      scanBand.addColorStop(1, 'rgba(5,8,17,0)');
      context.fillStyle = scanBand;
      context.fillRect(0, scanY, width, 180);

      drawMouseGlow();
      drawNoise();

      var vignette = context.createRadialGradient(
        width * 0.5,
        height * 0.5,
        Math.min(width, height) * 0.18,
        width * 0.5,
        height * 0.5,
        Math.max(width, height) * 0.9
      );
      vignette.addColorStop(0, 'rgba(3, 6, 12, 0)');
      vignette.addColorStop(0.72, 'rgba(3, 6, 12, 0.18)');
      vignette.addColorStop(1, 'rgba(3, 6, 12, 0.58)');
      context.fillStyle = vignette;
      context.fillRect(0, 0, width, height);

      state.time += 0.016;
      requestAnimationFrame(drawFrame);
    }

    window.addEventListener('resize', resizeCanvas);
    window.addEventListener('pointermove', function (event) {
      var rect = canvas.parentElement.getBoundingClientRect();
      state.mouseX = (event.clientX - rect.left) / rect.width;
      state.mouseY = (event.clientY - rect.top) / rect.height;
      state.mouseActive = true;
    });
    window.addEventListener('pointerleave', function () {
      state.mouseActive = false;
    });

    resizeCanvas();
    requestAnimationFrame(drawFrame);
  }

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