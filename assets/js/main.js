// Lost and Found - Main JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // ===== PASSWORD TOGGLE =====
  const togglePasswordBtns = document.querySelectorAll(".toggle-password");
  togglePasswordBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const input = this.closest(".input-group").querySelector("input");
      const icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    });
  });

  // ===== AUTO-DISMISS ALERTS =====
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
  alerts.forEach((alert) => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });

  // ===== FORM VALIDATION =====
  const forms = document.querySelectorAll(".needs-validation");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add("was-validated");
    });
  });

  // ===== IMAGE PREVIEW FOR MULTIPLE FILES =====
  const imageInputs = document.querySelectorAll(
    'input[type="file"][accept*="image"]'
  );
  imageInputs.forEach((input) => {
    input.addEventListener("change", function (e) {
      const preview = document.getElementById("imagePreview");
      if (!preview) return;

      preview.innerHTML = "";
      const files = Array.from(this.files).slice(0, 5); // Max 5 images

      files.forEach((file, index) => {
        if (file.size > 5 * 1024 * 1024) {
          showToast("File too large. Maximum size is 5MB.", "danger");
          return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
          const col = document.createElement("div");
          col.className = "col-4 col-md-2";
          col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image" data-index="${index}">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
          preview.appendChild(col);
        };
        reader.readAsDataURL(file);
      });
    });
  });

  // ===== CONFIRM DELETE =====
  const deleteButtons = document.querySelectorAll("[data-confirm]");
  deleteButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      const message = this.dataset.confirm || "Are you sure?";
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });

  // ===== SEARCH FORM ENHANCEMENTS =====
  const searchInput = document.querySelector('input[name="q"]');
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout);
      // Live search hint
      const hint = document.getElementById("searchHint");
      if (hint) {
        hint.textContent =
          this.value.length >= 2 ? "Press Enter to search" : "";
      }
    });
  }

  // ===== SMOOTH SCROLL =====
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      // Skip if href is just "#" or empty
      if (!href || href === "#" || href.length < 2) return;

      try {
        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      } catch (err) {
        // Invalid selector, ignore
      }
    });
  });

  // ===== NAVBAR ACTIVE STATE =====
  const currentPath = window.location.pathname;
  document.querySelectorAll(".nav-link").forEach((link) => {
    const href = link.getAttribute("href");
    if (href && currentPath === href) {
      link.classList.add("active");
    }
  });

  // ===== LOADING STATE FOR FORMS =====
  const submitForms = document.querySelectorAll("form:not([data-no-loading])");
  submitForms.forEach((form) => {
    form.addEventListener("submit", function () {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.disabled) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Please wait...
                `;
        submitBtn.disabled = true;

        // Re-enable after timeout (in case of error)
        setTimeout(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }, 15000);
      }
    });
  });

  // ===== TOOLTIPS =====
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // ===== POPOVERS =====
  const popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // ===== COPY TO CLIPBOARD =====
  const copyButtons = document.querySelectorAll("[data-copy]");
  copyButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      const text = this.dataset.copy;
      navigator.clipboard.writeText(text).then(() => {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        setTimeout(() => {
          this.innerHTML = originalText;
        }, 2000);
      });
    });
  });

  // ===== ANIMATE ON SCROLL =====
  const observerOptions = {
    root: null,
    rootMargin: "0px",
    threshold: 0.1,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll(".item-card, .stat-card").forEach((el) => {
    observer.observe(el);
  });

  // ===== NOTIFICATION BADGE UPDATE =====
  updateNotificationBadge();
  setInterval(updateNotificationBadge, 60000); // Update every minute

  // ===== NOTIFICATION DROPDOWN =====
  const notificationDropdown = document.getElementById("notificationDropdown");
  if (notificationDropdown) {
    notificationDropdown.addEventListener(
      "show.bs.dropdown",
      loadNotifications
    );
  }

  // ===== SCHOOL ID FORMATTING =====
  const schoolIdInput = document.getElementById("school_id");
  if (schoolIdInput) {
    schoolIdInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/[^a-zA-Z0-9]/g, "").toUpperCase();
      if (value.length > 2) {
        value = value.slice(0, 2) + "-" + value.slice(2, 7);
      }
      e.target.value = value;
    });
  }

  // ===== PHONE NUMBER FORMATTING =====
  const phoneInputs = document.querySelectorAll('input[type="tel"]');
  phoneInputs.forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value.replace(/[^0-9+]/g, "");
      if (value.startsWith("+63")) {
        value = "0" + value.slice(3);
      }
      e.target.value = value.slice(0, 11);
    });
  });
});

// ===== UTILITY FUNCTIONS =====

// Format date
function formatDate(dateString, format = "full") {
  const date = new Date(dateString);
  if (format === "full") {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return date.toLocaleDateString("en-US", options);
  } else if (format === "short") {
    const options = { month: "short", day: "numeric" };
    return date.toLocaleDateString("en-US", options);
  }
  return date.toLocaleDateString();
}

// Truncate text
function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.substr(0, maxLength) + "...";
}

// Show toast notification
function showToast(message, type = "info") {
  const toastContainer =
    document.getElementById("toastContainer") || createToastContainer();

  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${type} border-0`;
  toast.setAttribute("role", "alert");
  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  toastContainer.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });
}

function createToastContainer() {
  const container = document.createElement("div");
  container.id = "toastContainer";
  container.className = "toast-container position-fixed bottom-0 end-0 p-3";
  container.style.zIndex = "9999";
  document.body.appendChild(container);
  return container;
}

// Update notification badge
async function updateNotificationBadge() {
  const badge = document.getElementById("notificationBadge");
  if (!badge) return;

  try {
    const response = await fetch("/lostandfound/notifications/unread-count");
    const data = await response.json();

    if (data.count > 0) {
      badge.textContent = data.count > 99 ? "99+" : data.count;
      badge.style.display = "inline-block";
    } else {
      badge.style.display = "none";
    }
  } catch (error) {
    console.log("Could not update notification badge");
  }
}

// Load notifications for dropdown
async function loadNotifications() {
  const notificationList = document.getElementById("notificationList");
  if (!notificationList) return;

  try {
    const response = await fetch("/lostandfound/api/notifications?limit=5");
    const result = await response.json();

    const notifications = result.data?.data || result.data || [];

    if (notifications.length === 0) {
      notificationList.innerHTML = `
        <div class="text-center py-4 text-muted">
          <i class="bi bi-bell-slash fs-1 mb-2 d-block"></i>
          <small>No notifications</small>
        </div>
      `;
      return;
    }

    notificationList.innerHTML = notifications
      .map((notif) => {
        const isRead = notif.is_read || notif.read || false;
        const iconClass = getNotificationIcon(notif.type);
        const timeAgo = formatTimeAgo(notif.created_at);

        return `
        <a href="${notif.action_url || "/lostandfound/notifications"}" 
           class="dropdown-item d-flex align-items-start py-2 px-3 ${
             !isRead ? "bg-light" : ""
           }" 
           style="white-space: normal;">
          <div class="me-2">
            <i class="bi ${iconClass.icon} ${iconClass.color}"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong class="small ${!isRead ? "fw-bold" : ""}">${escapeHtml(
          notif.title || ""
        )}</strong>
              ${
                !isRead
                  ? '<span class="badge bg-primary" style="font-size: 0.6rem;">New</span>'
                  : ""
              }
            </div>
            <p class="mb-0 small text-muted" style="line-height: 1.3;">${escapeHtml(
              truncateText(notif.message || "", 60)
            )}</p>
            <small class="text-muted">${timeAgo}</small>
          </div>
        </a>
      `;
      })
      .join("");
  } catch (error) {
    console.error("Could not load notifications:", error);
    notificationList.innerHTML = `
      <div class="text-center py-4 text-muted">
        <i class="bi bi-exclamation-circle fs-1 mb-2 d-block"></i>
        <small>Could not load notifications</small>
      </div>
    `;
  }
}

// Get notification icon based on type
function getNotificationIcon(type) {
  const icons = {
    match: { icon: "bi-link-45deg", color: "text-info" },
    claim: { icon: "bi-hand-index", color: "text-warning" },
    claim_approved: { icon: "bi-check-circle", color: "text-success" },
    claim_rejected: { icon: "bi-x-circle", color: "text-danger" },
    item_found: { icon: "bi-box-seam", color: "text-success" },
    item_approved: { icon: "bi-check-circle", color: "text-success" },
    item_rejected: { icon: "bi-x-circle", color: "text-danger" },
  };
  return icons[type] || { icon: "bi-bell", color: "text-primary" };
}

// Format time ago
function formatTimeAgo(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);

  if (seconds < 60) return "Just now";
  if (seconds < 3600) return Math.floor(seconds / 60) + "m ago";
  if (seconds < 86400) return Math.floor(seconds / 3600) + "h ago";
  if (seconds < 604800) return Math.floor(seconds / 86400) + "d ago";
  return date.toLocaleDateString();
}

// Escape HTML
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// API Request Helper
async function apiRequest(endpoint, method = "GET", data = null) {
  const options = {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
  };

  if (data && ["POST", "PUT", "PATCH"].includes(method)) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(endpoint, options);
    const result = await response.json();
    return { status: response.status, data: result };
  } catch (error) {
    console.error("API Error:", error);
    return { status: 500, data: { message: "An error occurred" } };
  }
}

// Confirm action
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// Debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
