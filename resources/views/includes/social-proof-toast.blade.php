@php
    $socialProofData = \App\Helper::socialProofPayload();
@endphp

@if (!empty($socialProofData['enabled']))
<!-- Social Proof Notification Toast Component -->
<div id="social-proof-toast-wrapper" class="sp-toast-wrapper" aria-live="polite" aria-atomic="true">
    <div id="social-proof-toast" class="sp-toast" role="status">
        <div class="sp-toast-body">
            <!-- Left Avatar / Badge Area -->
            <div class="sp-avatar-col">
                <div class="sp-avatar-box" id="sp-avatar-box">
                    <span class="sp-avatar-text" id="sp-avatar-text">RM</span>
                    <i class="sp-avatar-icon bi-lightning-charge-fill display-none" id="sp-avatar-icon"></i>
                </div>
                <span class="sp-status-indicator" id="sp-status-indicator" title="Verified Activity"></span>
            </div>

            <!-- Content Area -->
            <div class="sp-content-col">
                <div class="sp-header-row">
                    <span class="sp-headline" id="sp-headline">Rahul M. from Mumbai</span>
                    <div class="sp-header-actions">
                        <span class="sp-category-badge" id="sp-category-badge">Pro Plan</span>
                        <button type="button" class="sp-close-btn" id="sp-close-btn" aria-label="Dismiss notification" title="Dismiss">
                            <i class="bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="sp-message" id="sp-message">Just upgraded to Pro Photography Plan</div>
                <div class="sp-meta-row">
                    <span class="sp-pulse-dot" aria-hidden="true"></span>
                    <span class="sp-time" id="sp-time">2 minutes ago</span>
                    <span class="sp-tag display-none" id="sp-tag">Verified</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   Social Proof Premium Toast Styles (Glassmorphism & Responsive)
   ========================================================================== */
.sp-toast-wrapper {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 1040;
    pointer-events: none;
    font-family: inherit;
    transition: bottom 0.3s ease;
}

.sp-toast {
    position: relative;
    width: 360px;
    max-width: calc(100vw - 32px);
    background: rgba(255, 255, 255, 0.92);
    -webkit-backdrop-filter: blur(14px) saturate(180%);
    backdrop-filter: blur(14px) saturate(180%);
    border: 1px solid rgba(226, 232, 240, 0.85);
    border-radius: 14px;
    box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.05);
    padding: 12px 14px;
    pointer-events: none;
    opacity: 0;
    transform: translateX(-120%);
    transition: transform 320ms cubic-bezier(0.16, 1, 0.3, 1), opacity 300ms ease, box-shadow 0.2s ease;
    user-select: none;
}

/* Entry state */
.sp-toast.sp-show {
    opacity: 1;
    transform: translateX(0);
    pointer-events: auto;
}

/* Exit state */
.sp-toast.sp-hiding {
    opacity: 0;
    transform: translateX(-24px);
    pointer-events: none;
}

/* Subtle elevation on hover */
.sp-toast.sp-show:hover {
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.16), 0 6px 16px -2px rgba(15, 23, 42, 0.08);
}

.sp-toast-body {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Avatar / Icon Col */
.sp-avatar-col {
    position: relative;
    flex-shrink: 0;
}

.sp-avatar-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #059669;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.5px;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.3), 0 4px 10px rgba(0, 0, 0, 0.08);
    transition: background 0.3s ease;
}

.sp-avatar-icon {
    font-size: 18px;
    line-height: 1;
}

.sp-status-indicator {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background-color: #10b981;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}

/* Content Col */
.sp-content-col {
    flex: 1 1 auto;
    min-width: 0;
}

.sp-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 3px;
}

.sp-headline {
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
    flex: 1 1 auto;
    min-width: 0;
}

.sp-header-actions {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.sp-category-badge {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7.5px;
    border-radius: 20px;
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    white-space: nowrap;
    letter-spacing: 0.2px;
    flex-shrink: 0;
    line-height: 1.25;
}

.sp-message {
    font-size: 12px;
    color: #334155;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sp-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
    font-size: 11px;
    color: #64748b;
}

.sp-pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    animation: sp-pulse 2s infinite ease-in-out;
}

@keyframes sp-pulse {
    0%, 100% {
        transform: scale(0.9);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.3);
        opacity: 1;
    }
}

.sp-time {
    font-size: 11px;
    color: #64748b;
}

/* Close Button (Cleanly integrated in header actions) */
.sp-close-btn {
    width: 20px;
    height: 20px;
    border: none;
    background: rgba(148, 163, 184, 0.14);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    font-size: 14px;
    line-height: 1;
    padding: 0;
    margin: 0;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.sp-close-btn i {
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    font-size: 14px;
}

.sp-close-btn:hover {
    background: rgba(148, 163, 184, 0.28);
    color: #0f172a;
    transform: scale(1.08);
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .sp-toast,
body.dark-mode .sp-toast {
    background: rgba(22, 27, 34, 0.92);
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 0 14px 32px -4px rgba(0, 0, 0, 0.5), 0 4px 12px -2px rgba(0, 0, 0, 0.3);
}

[data-bs-theme="dark"] .sp-headline,
body.dark-mode .sp-headline {
    color: #f1f5f9;
}

[data-bs-theme="dark"] .sp-message,
body.dark-mode .sp-message {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .sp-meta-row,
[data-bs-theme="dark"] .sp-time,
body.dark-mode .sp-meta-row,
body.dark-mode .sp-time {
    color: #94a3b8;
}

[data-bs-theme="dark"] .sp-status-indicator,
body.dark-mode .sp-status-indicator {
    border-color: #161b22;
}

[data-bs-theme="dark"] .sp-close-btn,
body.dark-mode .sp-close-btn {
    background: rgba(255, 255, 255, 0.08);
    color: #94a3b8;
}

[data-bs-theme="dark"] .sp-close-btn:hover,
body.dark-mode .sp-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* Accessibility: Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .sp-toast {
        transform: none !important;
        transition: opacity 200ms linear !important;
    }
    .sp-toast.sp-show {
        transform: none !important;
    }
    .sp-toast.sp-hiding {
        transform: none !important;
    }
    .sp-pulse-dot {
        animation: none !important;
    }
}

/* Mobile Responsiveness */
@media (max-width: 576px) {
    .sp-toast-wrapper {
        left: 12px;
        right: 12px;
        bottom: 16px;
    }
    .sp-toast {
        width: 100%;
        max-width: 100%;
        padding: 10px 12px;
        border-radius: 12px;
    }
    .sp-avatar-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        font-size: 13px;
    }
    .sp-headline {
        font-size: 12.5px;
    }
    .sp-message {
        font-size: 11.5px;
    }
}
</style>

<script>
(function() {
    'use strict';

    var payload = {!! json_encode($socialProofData) !!};
    if (!payload || !payload.enabled || !payload.categories) {
        return;
    }

    var config = payload.config || {
        displayDuration: 8000,
        minInterval: 12000,
        maxInterval: 20000,
        initialDelay: 6000,
        dismissCooldownMinutes: 15
    };

    var categories = payload.categories;
    var wrapperEl = document.getElementById('social-proof-toast-wrapper');
    var toastEl = document.getElementById('social-proof-toast');
    var closeBtn = document.getElementById('sp-close-btn');
    var headlineEl = document.getElementById('sp-headline');
    var messageEl = document.getElementById('sp-message');
    var timeEl = document.getElementById('sp-time');
    var badgeEl = document.getElementById('sp-category-badge');
    var avatarBoxEl = document.getElementById('sp-avatar-box');
    var avatarTextEl = document.getElementById('sp-avatar-text');
    var avatarIconEl = document.getElementById('sp-avatar-icon');
    var statusIndicator = document.getElementById('sp-status-indicator');

    if (!toastEl || !wrapperEl) return;

    // Check dismissal cooldown in sessionStorage
    var COOLDOWN_KEY = 'sp_toast_dismissed_until';
    function isDismissed() {
        try {
            var dismissedUntil = sessionStorage.getItem(COOLDOWN_KEY);
            if (dismissedUntil && parseInt(dismissedUntil, 10) > Date.now()) {
                return true;
            }
        } catch (e) {}
        return false;
    }

    function recordDismissal() {
        try {
            var expires = Date.now() + (config.dismissCooldownMinutes * 60 * 1000);
            sessionStorage.setItem(COOLDOWN_KEY, expires.toString());
        } catch (e) {}
    }

    // Dynamic collision offset with cookie banner
    function adjustOffsetForCookieBanner() {
        try {
            var cookieBanner = document.querySelector('.showBanner:not(.display-none)');
            if (cookieBanner && cookieBanner.offsetParent !== null) {
                var rect = cookieBanner.getBoundingClientRect();
                var bannerHeight = rect.height || 70;
                wrapperEl.style.bottom = (bannerHeight + 24) + 'px';
            } else {
                wrapperEl.style.bottom = '';
            }
        } catch (e) {}
    }

    // State Tracking
    var timerId = null;
    var displayTimerId = null;
    var isVisible = false;
    var isHovered = false;
    var lastCategoryId = null;
    var lastItemId = null;

    // Category Distribution Weights: Purchase (50%), Signup (25%), Trending (15%), Milestone (10%)
    var categoryKeys = ['purchase', 'signup', 'trending', 'milestone'];
    var weights = {
        purchase: 50,
        signup: 25,
        trending: 15,
        milestone: 10
    };

    /**
     * Weighted Category Selection with Anti-Immediate-Repetition
     */
    function pickNextCategory() {
        var available = categoryKeys.filter(function(k) {
            return categories[k] && categories[k].items && categories[k].items.length > 0;
        });

        if (available.length === 0) return null;
        if (available.length === 1) return available[0];

        // Filter out immediate predecessor if more than one category is available
        var candidates = available;
        if (lastCategoryId && candidates.length > 1) {
            candidates = candidates.filter(function(k) { return k !== lastCategoryId; });
        }

        // Calculate total weight of valid candidates
        var totalWeight = 0;
        candidates.forEach(function(k) {
            totalWeight += (weights[k] || 10);
        });

        var rand = Math.random() * totalWeight;
        var cumulative = 0;
        for (var i = 0; i < candidates.length; i++) {
            cumulative += (weights[candidates[i]] || 10);
            if (rand <= cumulative) {
                return candidates[i];
            }
        }

        return candidates[candidates.length - 1];
    }

    /**
     * Pick Item from Category avoiding immediate duplicate
     */
    function pickItemFromCategory(catId) {
        var cat = categories[catId];
        if (!cat || !cat.items || cat.items.length === 0) return null;

        var items = cat.items;
        if (items.length === 1) return items[0];

        var candidates = items;
        if (lastItemId && candidates.length > 1) {
            candidates = items.filter(function(item) { return item.id !== lastItemId; });
        }

        var idx = Math.floor(Math.random() * candidates.length);
        return candidates[idx];
    }

    /**
     * Populate and Render Toast DOM
     */
    function renderToast(catId, item) {
        var cat = categories[catId];

        headlineEl.textContent = item.headline || 'Community Member';
        messageEl.textContent = item.message || '';
        timeEl.textContent = item.time || 'Recently';

        // Badge styling
        badgeEl.textContent = cat.badge || 'Activity';
        badgeEl.style.backgroundColor = (cat.accent || '#10b981') + '1a';
        badgeEl.style.color = cat.accent || '#10b981';

        // Avatar or Icon presentation
        if (item.avatar_icon) {
            avatarTextEl.classList.add('display-none');
            avatarIconEl.className = 'sp-avatar-icon ' + item.avatar_icon;
            avatarIconEl.classList.remove('display-none');
            avatarBoxEl.style.backgroundColor = item.avatar_color || cat.accent || '#10b981';
        } else {
            avatarIconEl.classList.add('display-none');
            avatarTextEl.textContent = item.avatar || 'CR';
            avatarTextEl.classList.remove('display-none');
            avatarBoxEl.style.backgroundColor = item.avatar_color || '#059669';
        }

        // Verified indicator dot
        if (item.verified) {
            statusIndicator.style.display = 'block';
            statusIndicator.style.backgroundColor = '#10b981';
        } else {
            statusIndicator.style.display = 'block';
            statusIndicator.style.backgroundColor = cat.accent || '#f59e0b';
        }

        adjustOffsetForCookieBanner();

        // Show animation
        toastEl.classList.remove('sp-hiding');
        toastEl.classList.add('sp-show');
        isVisible = true;

        lastCategoryId = catId;
        lastItemId = item.id;

        // Schedule automatic hide after displayDuration
        clearTimeout(displayTimerId);
        displayTimerId = setTimeout(function() {
            if (!isHovered) {
                hideToast();
            }
        }, config.displayDuration);
    }

    /**
     * Hide Toast with Exit Animation
     */
    function hideToast(immediate) {
        if (!isVisible) return;

        clearTimeout(displayTimerId);
        isVisible = false;

        if (immediate) {
            toastEl.classList.remove('sp-show');
            toastEl.classList.add('sp-hiding');
            scheduleNext();
            return;
        }

        toastEl.classList.add('sp-hiding');
        setTimeout(function() {
            toastEl.classList.remove('sp-show');
            scheduleNext();
        }, 320);
    }

    /**
     * Show Next Notification
     */
    function triggerNextNotification() {
        if (document.hidden || isDismissed()) {
            scheduleNext();
            return;
        }

        var catId = pickNextCategory();
        if (!catId) return;

        var item = pickItemFromCategory(catId);
        if (!item) return;

        renderToast(catId, item);
    }

    /**
     * Schedule the Next Notification after a Randomized Interval (12-20s)
     */
    function scheduleNext(delayMs) {
        clearTimeout(timerId);

        if (isDismissed()) return;

        var interval = typeof delayMs === 'number' 
            ? delayMs 
            : config.minInterval + Math.floor(Math.random() * (config.maxInterval - config.minInterval));

        timerId = setTimeout(function() {
            triggerNextNotification();
        }, interval);
    }

    // Event Listeners
    closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        recordDismissal();
        hideToast(true);
    });

    // Hover to Pause display timer for optimal UX
    toastEl.addEventListener('mouseenter', function() {
        isHovered = true;
        clearTimeout(displayTimerId);
    });

    toastEl.addEventListener('mouseleave', function() {
        isHovered = false;
        if (isVisible) {
            clearTimeout(displayTimerId);
            displayTimerId = setTimeout(function() {
                hideToast();
            }, 2500); // 2.5s grace period on unhover
        }
    });

    // Pause timers when tab is hidden, resume when tab is active
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearTimeout(timerId);
            clearTimeout(displayTimerId);
        } else {
            if (!isVisible && !isDismissed()) {
                scheduleNext(4000); // Resume shortly after refocusing tab
            }
        }
    });

    // Start lifecycle if not dismissed
    if (!isDismissed()) {
        window.addEventListener('DOMContentLoaded', function() {
            adjustOffsetForCookieBanner();
            scheduleNext(config.initialDelay);
        });
        // In case DOM is already loaded
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            adjustOffsetForCookieBanner();
            scheduleNext(config.initialDelay);
        }
    }
})();
</script>
@endif
