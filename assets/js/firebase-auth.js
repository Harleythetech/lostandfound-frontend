// Firebase Authentication Handler
// Firebase config is injected from PHP via FIREBASE_CONFIG global variable
// This is set in the login.php and register.php views

let auth = null;
let googleProvider = null;

if (typeof FIREBASE_CONFIG === "undefined" || !FIREBASE_CONFIG.apiKey) {
  console.warn(
    "Firebase config not provided. Firebase authentication will not work."
  );
} else {
  // Initialize Firebase only if config is available
  firebase.initializeApp(FIREBASE_CONFIG);
  auth = firebase.auth();
  googleProvider = new firebase.auth.GoogleAuthProvider();
  googleProvider.addScope("email");
  googleProvider.addScope("profile");
}

// Google Sign In
async function signInWithGoogle() {
  try {
    showLoading();
    const result = await auth.signInWithPopup(googleProvider);
    const idToken = await result.user.getIdToken();

    // Check if this is login or register page
    const isRegisterPage = window.location.pathname.includes("/register");

    if (isRegisterPage) {
      // Store token for registration form
      sessionStorage.setItem("firebase_token", idToken);
      sessionStorage.setItem("firebase_email", result.user.email);
      sessionStorage.setItem("firebase_name", result.user.displayName || "");

      // Pre-fill form fields
      prefillFormFromFirebase(result.user);
      showFirebaseLinked(result.user.email);
      hideLoading();
    } else {
      // Try to login with Firebase
      await firebaseLogin(idToken);
    }
  } catch (error) {
    console.error("Google Sign In Error:", error);
    showAuthError(getFirebaseErrorMessage(error));
    hideLoading();
  }
}

// Email/Password Sign Up (Firebase)
async function signUpWithEmail(email, password) {
  try {
    showLoading();
    const result = await auth.createUserWithEmailAndPassword(email, password);
    const idToken = await result.user.getIdToken();

    // Store token for registration
    sessionStorage.setItem("firebase_token", idToken);
    sessionStorage.setItem("firebase_email", result.user.email);

    showFirebaseLinked(result.user.email);
    hideLoading();
    return idToken;
  } catch (error) {
    console.error("Email Sign Up Error:", error);
    showAuthError(getFirebaseErrorMessage(error));
    hideLoading();
    throw error;
  }
}

// Email/Password Sign In (Firebase)
async function signInWithEmail(email, password) {
  try {
    showLoading();
    const result = await auth.signInWithEmailAndPassword(email, password);
    const idToken = await result.user.getIdToken();

    // Try to login with Firebase
    await firebaseLogin(idToken);
  } catch (error) {
    console.error("Email Sign In Error:", error);
    showAuthError(getFirebaseErrorMessage(error));
    hideLoading();
  }
}

// Login with Firebase token via PHP proxy (avoids CORS)
async function firebaseLogin(idToken) {
  try {
    const response = await fetch(APP_URL + "/auth/firebase/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ firebase_token: idToken }),
    });

    const data = await response.json();

    if (data.success) {
      // Create PHP session
      await createPhpSession(data.data);
    } else {
      hideLoading();
      if (data.message && data.message.includes("not linked")) {
        // Account not linked, show link option
        showLinkAccountModal(idToken);
      } else {
        showAuthError(data.message || "Login failed. Please try again.");
      }
    }
  } catch (error) {
    console.error("Firebase Login Error:", error);
    hideLoading();
    showAuthError("Connection error. Please try again.");
  }
}

// Create PHP session from API response
async function createPhpSession(apiData) {
  try {
    const response = await fetch(APP_URL + "/auth/firebase/callback", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        accessToken: apiData.accessToken,
        refreshToken: apiData.refreshToken || null,
        user: apiData.user,
      }),
    });

    const result = await response.json();
    if (result.success && result.redirect) {
      window.location.href = result.redirect;
    } else {
      throw new Error(result.message || "Failed to create session");
    }
  } catch (error) {
    console.error("Session creation error:", error);
    showAuthError("Failed to complete login. Please try again.");
  }
}

// Register with Firebase token via PHP proxy (avoids CORS)
async function firebaseRegister(formData, firebaseToken) {
  try {
    const response = await fetch(APP_URL + "/auth/firebase/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        firebase_token: firebaseToken,
        ...formData,
      }),
    });

    const data = await response.json();

    if (data.success) {
      // Clear stored Firebase data
      sessionStorage.removeItem("firebase_token");
      sessionStorage.removeItem("firebase_email");
      sessionStorage.removeItem("firebase_name");

      // Sign out from Firebase (don't keep them logged in)
      if (auth) {
        await auth.signOut();
      }

      // Redirect to login with success message (account is pending approval)
      window.location.href = APP_URL + "/login?registered=1";
    } else {
      showAuthError(data.message || "Registration failed");
      return false;
    }
    return true;
  } catch (error) {
    console.error("Firebase Register Error:", error);
    showAuthError("Connection error. Please try again.");
    return false;
  }
}

// Link Firebase to existing account via PHP proxy (avoids CORS)
async function linkFirebaseAccount(idToken, schoolId, password) {
  try {
    showLoading();
    const response = await fetch(APP_URL + "/auth/firebase/link", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        firebase_token: idToken,
        school_id: schoolId,
        password: password,
      }),
    });

    const data = await response.json();

    if (data.success) {
      // Close modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("linkAccountModal")
      );
      if (modal) modal.hide();

      // Now try to login
      await firebaseLogin(idToken);
    } else {
      hideLoading();
      const linkError = document.getElementById("linkError");
      if (linkError) {
        linkError.textContent = data.message || "Failed to link account";
        linkError.classList.remove("d-none");
      }
    }
  } catch (error) {
    console.error("Link Account Error:", error);
    hideLoading();
    showAuthError("Connection error. Please try again.");
  }
}

// Pre-fill form fields from Firebase user
function prefillFormFromFirebase(user) {
  const emailField = document.getElementById("email");
  if (emailField && user.email) {
    emailField.value = user.email;
    emailField.readOnly = true;
  }

  if (user.displayName) {
    const names = user.displayName.split(" ");
    const firstNameField = document.getElementById("first_name");
    const lastNameField = document.getElementById("last_name");

    if (firstNameField && names[0]) {
      firstNameField.value = names[0];
    }
    if (lastNameField && names.length > 1) {
      lastNameField.value = names.slice(1).join(" ");
    }
  }
}

// Show Firebase linked status
function showFirebaseLinked(email) {
  const container = document.getElementById("firebaseStatus");
  if (container) {
    container.innerHTML = `
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>
                    <strong>Connected:</strong> ${email}
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="disconnectFirebase()">
                        Disconnect
                    </button>
                </div>
            </div>
        `;
  }

  // Hide Firebase sign-in buttons
  const firebaseButtons = document.getElementById("firebaseButtons");
  if (firebaseButtons) {
    firebaseButtons.classList.add("d-none");
  }

  // Disable password fields for Firebase registration
  const passwordField = document.getElementById("password");
  const confirmField = document.getElementById("confirm_password");
  if (passwordField) {
    passwordField.removeAttribute("required");
    passwordField.closest(".col-md-6")?.classList.add("d-none");
  }
  if (confirmField) {
    confirmField.removeAttribute("required");
    confirmField.closest(".col-md-6")?.classList.add("d-none");
  }
}

// Disconnect Firebase (for registration)
function disconnectFirebase() {
  sessionStorage.removeItem("firebase_token");
  sessionStorage.removeItem("firebase_email");
  sessionStorage.removeItem("firebase_name");

  auth.signOut();

  // Reload page to reset form
  window.location.reload();
}

// Show link account modal
function showLinkAccountModal(idToken) {
  const modalHtml = `
        <div class="modal fade" id="linkAccountModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Link Your Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Your Google account is not linked to any School ID. Please enter your existing credentials to link them.</p>
                        <div class="mb-3">
                            <label class="form-label">School ID</label>
                            <input type="text" class="form-control" id="linkSchoolId" placeholder="XX-XXXXX" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="linkPassword" required>
                        </div>
                        <div id="linkError" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitLinkAccount('${idToken}')">
                            <i class="bi bi-link-45deg me-2"></i>Link Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

  // Remove existing modal if any
  const existingModal = document.getElementById("linkAccountModal");
  if (existingModal) {
    existingModal.remove();
  }

  document.body.insertAdjacentHTML("beforeend", modalHtml);
  const modal = new bootstrap.Modal(
    document.getElementById("linkAccountModal")
  );
  modal.show();
}

// Submit link account
function submitLinkAccount(idToken) {
  const schoolId = document.getElementById("linkSchoolId").value;
  const password = document.getElementById("linkPassword").value;

  if (!schoolId || !password) {
    document.getElementById("linkError").textContent =
      "Please fill in all fields";
    document.getElementById("linkError").classList.remove("d-none");
    return;
  }

  linkFirebaseAccount(idToken, schoolId, password);
}

// UI Helpers
function showLoading() {
  const overlay = document.getElementById("authLoadingOverlay");
  if (overlay) {
    overlay.classList.remove("d-none");
  }

  // Disable buttons
  document
    .querySelectorAll('.firebase-btn, .btn-submit, button[type="submit"]')
    .forEach((btn) => {
      btn.disabled = true;
    });
}

function hideLoading() {
  const overlay = document.getElementById("authLoadingOverlay");
  if (overlay) {
    overlay.classList.add("d-none");
  }

  // Enable buttons
  document
    .querySelectorAll('.firebase-btn, .btn-submit, button[type="submit"]')
    .forEach((btn) => {
      btn.disabled = false;
    });
}

function showAuthError(message) {
  const container = document.getElementById("authError");
  if (container) {
    container.textContent = message;
    container.classList.remove("d-none");
    setTimeout(() => {
      container.classList.add("d-none");
    }, 5000);
  } else {
    alert(message);
  }
}

function getFirebaseErrorMessage(error) {
  const errorMessages = {
    "auth/email-already-in-use":
      "This email is already registered. Try signing in instead.",
    "auth/invalid-email": "Please enter a valid email address.",
    "auth/operation-not-allowed": "This sign-in method is not enabled.",
    "auth/weak-password": "Password should be at least 6 characters.",
    "auth/user-disabled": "This account has been disabled.",
    "auth/user-not-found": "No account found with this email.",
    "auth/wrong-password": "Incorrect password.",
    "auth/popup-closed-by-user": "Sign-in was cancelled.",
    "auth/network-request-failed":
      "Network error. Please check your connection.",
    "auth/too-many-requests": "Too many attempts. Please try again later.",
    "auth/popup-blocked":
      "Pop-up was blocked. Please allow pop-ups for this site.",
  };

  return (
    errorMessages[error.code] ||
    error.message ||
    "An error occurred. Please try again."
  );
}

// Check for stored Firebase token on page load (for registration)
document.addEventListener("DOMContentLoaded", function () {
  const storedToken = sessionStorage.getItem("firebase_token");
  const storedEmail = sessionStorage.getItem("firebase_email");

  if (
    storedToken &&
    storedEmail &&
    window.location.pathname.includes("/register")
  ) {
    showFirebaseLinked(storedEmail);

    // Pre-fill email
    const emailField = document.getElementById("email");
    if (emailField) {
      emailField.value = storedEmail;
      emailField.readOnly = true;
    }

    // Pre-fill name if available
    const storedName = sessionStorage.getItem("firebase_name");
    if (storedName) {
      const names = storedName.split(" ");
      const firstNameField = document.getElementById("first_name");
      const lastNameField = document.getElementById("last_name");

      if (firstNameField && names[0]) {
        firstNameField.value = names[0];
      }
      if (lastNameField && names.length > 1) {
        lastNameField.value = names.slice(1).join(" ");
      }
    }
  }
});
