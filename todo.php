<?php

/**
 * ==========================
 * Project ToDo List (PHP/Laravel)
 * ==========================
 *
 * Organized into phases for better project planning.
 * Tasks are grouped by functional area.
 *
 * You can use this file as an internal project roadmap.
 */

// ==========================
// Phase 1 – Before GoLive
// ==========================

/**
 * --- General Issues ---
 */

// [x] Message Title: If replying to a message, the title should match the original, maybe with a prefix. Suggest a strategy and debug the current behavior.
// [x] Admin Panel: Previously, accepting a report deleted a post. This no longer happens because "reports" were extended to users and messages. Fix logic accordingly.
// [x] Ban Function: On main page, a modal opens for banning. In message-view it bans directly (permanently). Unify this to always show the modal.
// [x] User Search: When clicking "Follow", the spinner shows on all "Follow" buttons, not just the clicked one. Fix the frontend behavior.
// [x] Admin Dashboard Reports shown Post, but we have User and Message reports as well. Extend the admin dashboard to show all report types.
// [x] Admin Dashboard smaller cards: The cards on the admin dashboard are too large. Reduce their size for better visibility.

/**
 * --- Messages ---
 */

// [x] Delete and archive messages

/**
 * --- User Features ---
 */

// [x] Allow users to specify which languages they speak
// [x] Store the language of each post
// [x] Update user profile to include spoken languages
// [x] Update user and post seeder and factory to include spoken languages
// [ ] Add gender to User tabel, seeder, and factory
// [ ] Add gender to user profile
// [ ] Add user filter (age, nationality, spoken languages, etc.)
// [x] Add post filter (by post language and user language)

/**
 * --- Email & Verification ---
 */

// [ ] Implement email verification (must be done at the end of the project)

/**
 * --- Language & Translation ---
 */

// [ ] Translate more and more content

/**
 * --- Security ---
 */

// [ ] Perform a full pentest on the application

/**
 * --- Testing ---
 */

// [ ] Add general test coverage (backend + frontend)

/**
 * --- Frontend (not logged-in users) ---
 */

// [ ] Add guest user features (to be defined)


// ==================================================================================================================================


// ==========================
// Phase 2 – Bugfixing After GoLive
// ==========================

// [ ] Collect live-user feedback
// [ ] Patch newly reported bugs
// [ ] Monitor performance and log errors



// ==========================
// Phase 3 – Post GoLive Features
// ==========================

/**
 * --- Monetization ---
 */

// [ ] Integrate Google Ads or another ad service

/**
 * --- Further Bugfixing ---
 */

// [ ] Continue resolving issues based on user reports



// ==========================
// Phase 4 – Post GoLive Features
// ==========================

/**
 * --- Language & Translation ---
 */

// [ ] Add Spanish (ES), French (FR) and Italian (IT) to the language switcher

/**
 * --- Further Bugfixing ---
 */

// [ ] Continue resolving issues based on user reports


// ==========================
// Phase Unknown – Later / Optional Ideas
// ==========================

/**
 * --- Translation Enhancements ---
 */

// [ ] Use Laravel add-on for multilingual content (e.g. spatie/laravel-translatable)
// [ ] Install translation API (Google Translate, DeepL, or LibreTranslate) for dynamic translation support


// ==========================
// Tips / Notes
// ==========================

/**
 * - When adding language support, consider storing fallback versions for content.
 * - For filters, consider using Eloquent scopes for clean backend implementation.
 * - When dealing with modals (e.g. for banning), unify interaction patterns for better UX.
 * - For performance, consider lazy-loading translations or user filters.
 */

