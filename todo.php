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

// [ ] Fix user filter (spoken languages, hobby, travelStyle). This 3 first selection does not work properly.
// [ ] Find and delete "No new notifications." in notification card

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

