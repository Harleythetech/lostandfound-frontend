/* Shared image preview + remove + undo utility
   Usage: initImagePreview(inputId, previewId, uploadZoneId, options)
   options: { maxFiles: number, toastTimeoutMs: number }
*/
(function () {
  function createToast(message, undoHandler, timeout = 8000) {
    const containerId = "imagePreviewToastContainer";
    let container = document.getElementById(containerId);
    if (!container) {
      container = document.createElement("div");
      container.id = containerId;
      container.style.position = "fixed";
      container.style.top = "12px";
      container.style.right = "12px";
      container.style.zIndex = 12000;
      document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = "card shadow-sm p-2 mb-2 image-preview-toast";
    toast.style.minWidth = "220px";
    toast.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div style="flex:1">${message}</div>
                <div class="ms-2">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-undo">Undo</button>
                </div>
            </div>
        `;
    container.appendChild(toast);

    let removed = false;
    const btn = toast.querySelector(".btn-undo");
    btn.addEventListener("click", function () {
      if (removed) return;
      removed = true;
      undoHandler();
      container.removeChild(toast);
    });

    const t = setTimeout(() => {
      if (!removed && toast.parentNode) container.removeChild(toast);
    }, timeout);

    return {
      remove() {
        removed = true;
        clearTimeout(t);
        if (toast.parentNode) container.removeChild(toast);
      },
    };
  }

  function initImagePreview(inputId, previewId, uploadZoneId, options = {}) {
    const maxFiles = options.maxFiles || 5;
    const toastTimeout = options.toastTimeoutMs || 8000;

    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const uploadZone = document.getElementById(uploadZoneId);
    if (!input || !preview || !uploadZone) return;

    function render() {
      preview.innerHTML = "";
      const files = Array.from(input.files).slice(0, maxFiles);
      if (files.length > 0) uploadZone.style.display = "none";
      else uploadZone.style.display = "";

      files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const col = document.createElement("div");
          col.className = "col-6";
          const fileKey = `${file.name}|${file.size}`;
          col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                            <button type="button" class="image-remove-btn" data-key="${fileKey}" title="Remove image" role="button" aria-label="Remove image" tabindex="0">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
          preview.appendChild(col);
        };
        reader.readAsDataURL(file);
      });
    }

    function removeByKey(keyToRemove) {
      const dt = new DataTransfer();
      const files = Array.from(input.files);
      let removedFile = null;
      files.forEach((f) => {
        const k = `${f.name}|${f.size}`;
        if (k === keyToRemove && removedFile === null) {
          removedFile = f;
        } else {
          dt.items.add(f);
        }
      });
      input.files = dt.files;
      render();

      if (removedFile) {
        // show toast with undo
        const toast = createToast(
          "Image removed",
          function undo() {
            const dt2 = new DataTransfer();
            Array.from(input.files).forEach((f) => dt2.items.add(f));
            dt2.items.add(removedFile);
            input.files = dt2.files;
            render();
          },
          toastTimeout
        );
        return toast;
      }
      return null;
    }

    // Initial render
    render();

    input.addEventListener("change", function () {
      render();
    });

    // delegated click and keyboard handling
    preview.addEventListener("click", function (e) {
      const btn = e.target.closest(".image-remove-btn");
      if (!btn) return;
      const key = btn.dataset.key;
      if (key) removeByKey(key);
    });

    preview.addEventListener("keydown", function (e) {
      const btn = e.target.closest(".image-remove-btn");
      if (!btn) return;
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        const key = btn.dataset.key;
        if (key) removeByKey(key);
      }
    });
  }

  window.initImagePreview = initImagePreview;
})();
