Change Notes — Recent Frontend Fixes
Date: 2025-12-09

Summary:
- Fix: Decode and escape item/claim descriptions to display apostrophes and HTML entities correctly (`sanitizeForDisplay`).
- Fix: Remove temporary debug output and stray console/server debug lines across views and JS.
- UI: Notifications link/behavior centralized (`notificationUrl`) and notifications page now uses role-appropriate sidebar.
- Fix: Hardened "Potential Matches" display to skip invalid entries and show a friendly "No potential matches" message.
- Fix: Admin edit flows reverted — admins can no longer edit items via `/admin/*/edit`; edit remains available only to item owners via public edit routes.
- Change: Admins may delete items from the admin UI (still POST to the existing public delete endpoints).
- Enhancement: Admin item detail pages now show "Contact Owner" and will pull contact from the item first, then fall back to the finder/user profile when the finder report lacks contact.
- Fix: Addressed undefined variable warnings in admin found-item view by ensuring contact vars are defined.
- Improvement: Views updated with robust fallbacks for multiple API payload shapes (nested `user` object vs flattened fields).
- Pending: Dashboard "Matches" stat synchronization (make count use same filtering logic as `my-matches`) — planned but not yet applied.

Files (not exhaustive):
- app/config/config.php (helpers: `sanitizeForDisplay`, `notificationUrl`)
- app/controllers/AdminController.php (removed admin edit routes; admin show/lists adjusted)
- index.php (routing adjustments)
- views/admin/items/show-found.php (contact-from-profile fallback, contact modal, delete kept)
- views/admin/items/show-lost.php (delete + contact behavior)
- views/admin/items/show.php (dropdown actions adjusted for owner vs admin)
- views/items/* (sanitization, potential matches, contact modals)

Notes:
- Deletion still uses public endpoints (`/lost-items/{id}/delete`, `/found-items/{id}/delete`).
- QA recommended: test admin found/lost item pages while logged in as admin and as the item owner; verify contact modal, edit availability, and delete flow.

If you want this added to a formal changelog or commit message, I can prepare a compact commit-ready note next.