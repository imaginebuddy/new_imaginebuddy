<!-- Bootstrap core CSS -->
<link href="{{ asset('public/css/core.min.css') }}?v={{$settings->version}}" rel="stylesheet">
<link href="{{ asset('public/css/bootstrap.min.css') }}?v={{$settings->version}}" rel="stylesheet">
<link href="{{ asset('public/css/bootstrap-icons.css') }}?v={{$settings->version}}" rel="stylesheet">
<link href="{{ asset('public/js/fleximages/jquery.flex-images.css') }}" rel="stylesheet">
<link href="{{ asset('public/css/styles.css') }}?v={{$settings->version}}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
<style type="text/css">
@if ($settings->custom_css)
  {!! $settings->custom_css !!}
@endif
.home-cover {
  background-image: url('{{ asset('public/img/' . $settings->image_header) }}') !important;
  background-size: cover !important;
  background-position: center center !important;
  background-repeat: no-repeat !important;
}
:root {
  --color-default: {{ $settings->color_default }} !important;
  --bg-auth: url('{{ url('public/img', $settings->image_header) }}');
}

.text-color-default,
.text-theme-default,
.text-mint {
  color: var(--color-default) !important;
}
.bg-color-default,
.bg-theme-default {
  background-color: var(--color-default) !important;
}
.border-color-default,
.border-theme-default {
  border-color: var(--color-default) !important;
}

/* --- Theme Dynamic Styles for Cards --- */
.item-category {
  background-color: var(--bs-card-bg, #ffffff) !important;
  border-color: var(--bs-border-color, #e9ecef) !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease, border-color 0.3s ease;
}

.item-category:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
}

/* Dark Mode Specific Overrides */
[data-bs-theme="dark"] .item-category {
  background-color: var(--bs-dark-bg-subtle, #1e2227) !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}
/* --- Custom Dynamic Theme Cards --- */
.bg-card-custom {
  background-color: #ffffff !important;
  border-color: #e9ecef !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease !important;
}

.bg-card-custom:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

.badge-custom {
  background-color: #f8f9fa !important;
  color: #6c757d !important;
  border-color: #e9ecef !important;
}

.title-custom {
  color: #1a1d20 !important;
}

/* --- Dark Mode Active State --- */
[data-bs-theme="dark"] .bg-card-custom {
  background-color: #1e2227 !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

[data-bs-theme="dark"] .bg-card-custom:hover {
  background-color: #242930 !important;
  border-color: rgba(0, 214, 144, 0.3) !important;
  box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.4) !important;
}

[data-bs-theme="dark"] .badge-custom {
  background-color: rgba(255, 255, 255, 0.06) !important;
  color: #a0aec0 !important;
  border-color: rgba(255, 255, 255, 0.12) !important;
}

[data-bs-theme="dark"] .title-custom {
  color: #ffffff !important;
}

[data-bs-theme="dark"] .tag-avatar-badge {
  background-color: rgba(0, 214, 144, 0.18) !important;
  color: #00d690 !important;
}

/* --- Dynamic Pill Tabs Styling --- */
.category-pills .nav-link {
  color: #6c757d;
  transition: all 0.2s ease;
}

.category-pills .nav-link.active {
  background-color: #00d690 !important;
  color: #ffffff !important;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 214, 144, 0.3) !important;
}

.bg-subtle-custom {
  background-color: #f8f9fa !important;
  border-color: #e9ecef !important;
}

[data-bs-theme="dark"] .bg-subtle-custom {
  background-color: #1e2227 !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

[data-bs-theme="dark"] .category-pills .nav-link {
  color: #a0aec0;
}

[data-bs-theme="dark"] .category-pills .nav-link.active {
  background-color: #00d690 !important;
  color: #ffffff !important;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 214, 144, 0.4) !important;
}

/* --- Search Input Styling --- */
.form-control.bg-card-custom:focus {
  border-color: #00d690 !important;
  box-shadow: 0 0 0 0.25rem rgba(0, 214, 144, 0.2) !important;
}

[data-bs-theme="dark"] .form-control.bg-card-custom {
  color: #ffffff !important;
}

[data-bs-theme="dark"] .form-control.bg-card-custom::placeholder {
  color: #a0aec0 !important;
}

/* --- Show Prompt Page Custom Styling --- */
.bg-custom-mint {
  background-color: #00d690 !important;
  color: #ffffff !important;
  font-weight: 600 !important;
}

.breadcrumb-pill-box {
  background-color: #ffffff !important;
  border: 1px solid #e9ecef !important;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important;
  max-width: 100% !important;
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
  scrollbar-width: none !important;
}

.breadcrumb-pill-box::-webkit-scrollbar {
  display: none !important;
}

.breadcrumb-pill-box .breadcrumb {
  flex-wrap: nowrap !important;
  white-space: nowrap !important;
}

.breadcrumb-pill-box .breadcrumb-item {
  display: inline-flex !important;
  align-items: center !important;
  white-space: nowrap !important;
}

[data-bs-theme="dark"] .breadcrumb-pill-box {
  background-color: #1e2227 !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

.breadcrumb-pill-box .breadcrumb-item + .breadcrumb-item::before {
  content: ">" !important;
  color: #a0aec0 !important;
  padding-left: 0.5rem !important;
  padding-right: 0.5rem !important;
}

.breadcrumb-pill-box .breadcrumb-item a {
  color: #718096 !important;
  font-weight: 500 !important;
  font-size: 0.86rem !important;
  transition: color 0.2s ease !important;
  white-space: nowrap !important;
}

.breadcrumb-pill-box .breadcrumb-item a:hover {
  color: #00d690 !important;
}

.breadcrumb-pill-box .breadcrumb-item.active {
  min-width: 0 !important;
}

.breadcrumb-pill-box .breadcrumb-item.active .badge {
  max-width: 155px;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  display: inline-block !important;
  vertical-align: middle !important;
}

@media (min-width: 576px) {
  .breadcrumb-pill-box .breadcrumb-item.active .badge {
    max-width: 260px;
  }
}

@media (min-width: 992px) {
  .breadcrumb-pill-box .breadcrumb-item.active .badge {
    max-width: 420px;
  }
}

@media (max-width: 575.98px) {
  .breadcrumb-pill-box {
    padding-left: 0.85rem !important;
    padding-right: 0.85rem !important;
    padding-top: 0.35rem !important;
    padding-bottom: 0.35rem !important;
  }
  .breadcrumb-pill-box .breadcrumb-item a {
    font-size: 0.8rem !important;
  }
  .breadcrumb-pill-box .breadcrumb-item + .breadcrumb-item::before {
    padding-left: 0.35rem !important;
    padding-right: 0.35rem !important;
    font-size: 0.75rem !important;
  }
  .breadcrumb-pill-box .breadcrumb-item.active .badge {
    font-size: 0.78rem !important;
    padding: 0.25rem 0.65rem !important;
  }
}

[data-bs-theme="dark"] .breadcrumb-pill-box .breadcrumb-item a {
  color: #a0aec0 !important;
}

[data-bs-theme="dark"] .breadcrumb-pill-box .breadcrumb-item a:hover {
  color: #00d690 !important;
}

/* --- Edit & Delete Pill Buttons --- */
.btn-outline-blue-pill {
  border: 1.5px solid #3b82f6 !important;
  color: #3b82f6 !important;
  background-color: #ffffff !important;
  transition: all 0.2s ease !important;
}

.btn-outline-blue-pill:hover {
  background-color: #3b82f6 !important;
  color: #ffffff !important;
}

.btn-outline-red-pill {
  border: 1.5px solid #ef4444 !important;
  color: #ef4444 !important;
  background-color: #ffffff !important;
  transition: all 0.2s ease !important;
}

.btn-outline-red-pill:hover {
  background-color: #ef4444 !important;
  color: #ffffff !important;
}

/* --- Action Pill Buttons with Icon Circle (Like & Collection) --- */
.btn-outline-pill-action {
  border: 1.5px solid #d1d5db !important;
  color: #1f2937 !important;
  background-color: #ffffff !important;
  transition: all 0.2s ease !important;
}

.btn-outline-pill-action:hover {
  border-color: #00d690 !important;
  color: #00d690 !important;
}

/* --- Liked Active State Styling --- */
.btn-outline-pill-action.active,
.btn-outline-pill-action.btn-liked {
  border-color: #ef4444 !important;
}

.btn-outline-pill-action.active .textLike,
.btn-outline-pill-action.btn-liked .textLike {
  color: #ef4444 !important;
}

.btn-outline-pill-action.active .action-icon-circle i,
.btn-outline-pill-action.btn-liked .action-icon-circle i {
  color: #ef4444 !important;
}

.btn-outline-pill-action .action-icon-circle {
  width: 28px;
  height: 28px;
  background-color: #f3f4f6;
  color: #374151;
  font-size: 13px;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.btn-outline-pill-action:hover .action-icon-circle {
  background-color: #00d690;
  color: #ffffff;
}

[data-bs-theme="dark"] .btn-outline-blue-pill {
  background-color: transparent !important;
}

[data-bs-theme="dark"] .btn-outline-red-pill {
  background-color: transparent !important;
}

[data-bs-theme="dark"] .btn-outline-pill-action {
  border-color: #374151 !important;
  color: #f3f4f6 !important;
  background-color: transparent !important;
}

[data-bs-theme="dark"] .btn-outline-pill-action .action-icon-circle {
  background-color: #374151;
  color: #f3f4f6;
}

/* --- Main Prompt Card Box & Action Buttons --- */
.prompt-card-box-custom {
  background-color: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 28px !important;
}

[data-bs-theme="dark"] .prompt-card-box-custom {
  background-color: #1a202c !important;
  border-color: #2d3748 !important;
}

.prompt-inner-content-box {
  background-color: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 20px !important;
}

[data-bs-theme="dark"] .prompt-inner-content-box {
  background-color: #2d3748 !important;
  border-color: #4a5568 !important;
}

/* --- Left Sticky Image Card Box --- */
[data-bs-theme="dark"] .left-image-card-box {
  background-color: #1a202c !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}

.btn-outline-custom {
  border-color: #e2e8f0;
  color: #4a5568;
  background-color: transparent;
  transition: all 0.2s ease;
}

.btn-outline-custom:hover {
  border-color: #00d690;
  color: #00d690;
  background-color: rgba(0, 214, 144, 0.05);
}

[data-bs-theme="dark"] .btn-outline-custom {
  border-color: #2d3748;
  color: #cbd5e0;
}

[data-bs-theme="dark"] .btn-outline-custom:hover {
  border-color: #00d690;
  color: #00d690;
  background-color: rgba(0, 214, 144, 0.1);
}

.sticky-preview-container {
  position: -webkit-sticky;
  position: sticky;
  top: 90px;
  z-index: 10;
}

/* --- Standardized Prompt Card Grid System Across All Pages: 3 Cards Per Row --- */
.flex-images {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 1.25rem !important;
  overflow: visible !important;
  height: auto !important;
  margin: 0 !important;
  padding: 0 !important;
  box-sizing: border-box !important;
}

.flex-images .item {
  width: calc((100% - 2.5rem) / 3) !important;
  max-width: calc((100% - 2.5rem) / 3) !important;
  min-width: 0 !important;
  flex: 0 0 calc((100% - 2.5rem) / 3) !important;
  margin: 0 !important;
  padding: 0 !important;
  float: none !important;
  box-sizing: border-box !important;
  height: auto !important;
}

@media (max-width: 991px) {
  .flex-images .item {
    width: calc((100% - 1.25rem) / 2) !important;
    max-width: calc((100% - 1.25rem) / 2) !important;
    min-width: 0 !important;
    flex: 0 0 calc((100% - 1.25rem) / 2) !important;
  }
}

@media (max-width: 575px) {
  .flex-images .item {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    flex: 0 0 100% !important;
  }
}

/* --- Navbar Homepage Scroll Search & Header Transition --- */
#header {
  transition: background-color 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease !important;
}

#header.header-home-transparent {
  background-color: transparent !important;
  background: transparent !important;
  box-shadow: none !important;
  border-bottom: none !important;
}

#header.header-home-transparent .link-dark,
#header.header-home-transparent .nav-link,
#header.header-home-transparent .toggle-menu {
  color: #ffffff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
}

/* Header logo display rules */
#header .logoMain,
#header .logoLight,
#header .logo {
  display: none !important;
}

@media (max-width: 991.98px) {
  #header .logo {
    display: block !important;
  }
}

@media (min-width: 992px) {
  /* Default Desktop: Light mode shows logoMain */
  #header .logoMain {
    display: block !important;
  }
  #header .logoLight {
    display: none !important;
  }

  /* Dark mode: show logoLight */
  [data-bs-theme="dark"] #header .logoMain {
    display: none !important;
  }
  [data-bs-theme="dark"] #header .logoLight {
    display: block !important;
  }

  /* Transparent header (homepage top over dark background): show logoLight */
  #header.header-home-transparent .logoMain {
    display: none !important;
  }
  #header.header-home-transparent .logoLight {
    display: block !important;
  }
}

.navbar-search-form {
  transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease !important;
}

.navbar-search-form.home-search-hidden {
  opacity: 0 !important;
  visibility: hidden !important;
  transform: translateY(-8px) !important;
  pointer-events: none !important;
}
</style>
@auth
@if ($settings->push_notification_status)
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
  const myDeviceKeysId = {!! json_encode(auth()->user()->oneSignalDevices->pluck('player_id')->all()) !!};

  var OneSignal = window.OneSignal || [];
    var initConfig = {
      appId: "{{ $settings->onesignal_appid }}",
      autoResubscribe: true,
      safari_web_id: "web.onesignal.auto.0c986762-0fae-40b1-a5f6-ee95f7275a97",
      notifyButton: {
        enable: false,
      },
      welcomeNotification: {
        message: "{{ __('misc.notifications_activated_successfully') }}"
      },
      persistNotification: true,

      promptOptions: {
      slidedown: {
        prompts: [
          {
            type: "push", // current types are "push" & "category"
            autoPrompt: true,
            text: {
              /* limited to 90 characters */
              actionMessage: "{{ __('misc.push_notification_title', ['app' => $settings->title]) }}",
              /* acceptButton limited to 15 characters */
              acceptButton: "{{ __('misc.activate') }}",
              /* cancelButton limited to 15 characters */
              cancelButton: "{{ __('misc.maybe_later') }}"
            },
            delay: {
              pageViews: 1,
              timeDelay: 20
            }
          }
        ]
      }
    }
    // END promptOptions,
    };
 

  OneSignal.push(function () {
        OneSignal.SERVICE_WORKER_PARAM = { scope: '/public/js/' };
        OneSignal.SERVICE_WORKER_PATH = 'public/js/OneSignalSDKWorker.js'
        OneSignal.SERVICE_WORKER_UPDATER_PATH = 'public/js/OneSignalSDKWorker.js'
        OneSignal.init(initConfig);

        OneSignal.showSlidedownPrompt();
    });

  OneSignal.push(function() {

    // Get User Id
    OneSignal.getUserId(function(userId) {
      pushUserId = userId;

      if (pushUserId !== null) {
        var isRegisterDevice = $.inArray(pushUserId, myDeviceKeysId);
        if (isRegisterDevice === -1) {
          $.post("{{ url('api/device/register') }}", {player_id: pushUserId, user_id: {{ auth()->id() }} });
        }
      }
    });

    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
    if (isEnabled)
      console.log("Push notifications are enabled!");
    else
      console.log("Push notifications are not enabled yet.");
  });
    
  // Subscription Change
	OneSignal.on("subscriptionChange", 
  function(isSubscribed) {

    OneSignal.push(function() {
        OneSignal.getUserId(function(userId) {
          pushUserId = userId;

        if (isSubscribed == false) {
        $.get("{{ url('api/device/delete') }}", {player_id: pushUserId});
      } else {
            $.post("{{ url('api/device/register') }}", {player_id: pushUserId, user_id: {{ auth()->id() }} });
          }});

        });
      });
});
</script>
@endif

@endauth

<script type="text/javascript">
var URL_BASE = "{{ url('/') }}";
var lang = '{{ session('locale') }}';
var _title = document.title ? document.title.replace(/^\(\d+\)\s*/, '') : '{{ e($settings->title) }}';
var session_status = "{{ auth()->check() ? 'on' : 'off' }}";
var colorStripe = '#000000';
var copiedSuccess = "{{ __('misc.copied_success') }}";
var error = "{{__('misc.error')}}";
var error_oops = "{{__('misc.error_oops')}}";
var resending_code = "{{__('misc.resending_code')}}";
var isProfile = {{ request()->route()->named('profile') ? 'true' : 'false' }};
var download = '{{__('misc.download')}}';
var downloading = '{{__('misc.downloading')}}';
var announcement_cookie = "{{$settings->announcement_cookie}}";
var ok = "{{__('misc.ok')}}";
var darkMode = "{{ __('misc.dark_mode') }}";
var lightMode = "{{ __('misc.light_mode') }}";

@auth
var stripeKey = "{{ PaymentGateways::where('id', 2)->where('enabled', '1')->first() ? env('STRIPE_KEY') : false }}";
var delete_confirm = "{{__('misc.delete_confirm')}}";
var confirm_delete = "{{ __('misc.yes') }}";
var cancel_confirm = "{{ __('misc.no') }}";
var your_subscribed = "{{__('misc.your_subscribed')}}";
var formats_available = "{{ __('misc.formats_available') }}";
var max_size_upload = "{{__('misc.max_size_upload').' '.Helper::formatBytes(1048576)}}";
var thanks = "{{ __('misc.thanks') }}";
@endauth
</script>

<style type="text/css">

@if ($settings->custom_css)
  {!! $settings->custom_css !!}
@endif

.home-cover {
  background-image: url('{{ asset('public/img/' . $settings->image_header) }}') !important;
  background-size: cover !important;
  background-position: center center !important;
  background-repeat: no-repeat !important;
}
:root {
  --color-default: {{ $settings->color_default }} !important;
  --bg-auth: url('{{ url('public/img', $settings->image_header) }}');
}

/* --- Modern AI Prompt Card Custom Styling --- */
.flex-images .item {
  margin: 14px 16px !important;
}

.prompt-card-container {
  background-color: #ffffff !important;
}

.prompt-card-bg {
  background-color: #ffffff !important;
}

.prompt-card-info-bg {
  background-color: #ffffff !important;
}

.prompt-badge-circle {
  background-color: #ffffff !important;
  color: #1e293b !important;
}

.prompt-badge-circle-text {
  color: #1e293b !important;
}

.prompt-card-title,
.photoshoot-card-title {
  white-space: normal !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
}

.prompt-title-link {
  color: #090d16 !important;
  display: inline-block !important;
  width: 100% !important;
}

.line-clamp-2 {
  display: -webkit-box !important;
  -webkit-line-clamp: 2 !important;
  -webkit-box-orient: vertical !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: normal !important;
}

.prompt-snippet-text {
  color: #94a3b8 !important;
  line-height: 1.4 !important;
}

.prompt-author-pill {
  background-color: #f1f5f9 !important;
  color: #1e293b !important;
  max-width: 185px !important;
}

.prompt-share-btn {
  background-color: #f1f5f9 !important;
  color: #334155 !important;
}

.prompt-copy-btn {
  background-color: #111827 !important;
  border-color: #111827 !important;
  color: #ffffff !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 0 4px 0 14px !important;
  height: 36px !important;
  border-radius: 50rem !important;
  gap: 8px !important;
  font-weight: 700 !important;
  font-size: 13px !important;
  line-height: 1 !important;
  border: 1px solid transparent !important;
}

.prompt-copy-btn .prompt-copy-text {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  margin: 0 !important;
  padding: 0 !important;
  font-weight: 700;
  font-size: 13px;
}

.prompt-copy-icon-bg {
  background-color: #ffffff !important;
  color: #111827 !important;
  width: 28px !important;
  height: 28px !important;
  min-width: 28px !important;
  border-radius: 50% !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 12px !important;
  flex-shrink: 0 !important;
  margin: 0 !important;
}

.prompt-copy-icon-bg i {
  line-height: 1 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .prompt-card-container,
[data-theme="dark"] .prompt-card-container,
body.dark-mode .prompt-card-container {
  background-color: #1e293b !important;
}

[data-bs-theme="dark"] .prompt-card-bg,
[data-theme="dark"] .prompt-card-bg,
body.dark-mode .prompt-card-bg {
  background-color: #1e293b !important;
}

[data-bs-theme="dark"] .prompt-card-info-bg,
[data-theme="dark"] .prompt-card-info-bg,
body.dark-mode .prompt-card-info-bg {
  background-color: #1e293b !important;
}

[data-bs-theme="dark"] .prompt-title-link,
[data-theme="dark"] .prompt-title-link,
body.dark-mode .prompt-title-link {
  color: #f8fafc !important;
}

[data-bs-theme="dark"] .prompt-snippet-text,
[data-theme="dark"] .prompt-snippet-text,
body.dark-mode .prompt-snippet-text {
  color: #94a3b8 !important;
}

[data-bs-theme="dark"] .prompt-author-pill,
[data-theme="dark"] .prompt-author-pill,
body.dark-mode .prompt-author-pill {
  background-color: #334155 !important;
  color: #e2e8f0 !important;
}

[data-bs-theme="dark"] .prompt-share-btn,
[data-theme="dark"] .prompt-share-btn,
body.dark-mode .prompt-share-btn {
  background-color: #334155 !important;
  color: #e2e8f0 !important;
}

[data-bs-theme="dark"] .prompt-copy-btn,
[data-theme="dark"] .prompt-copy-btn,
body.dark-mode .prompt-copy-btn {
  background-color: var(--color-default) !important;
  border-color: var(--color-default) !important;
  color: #ffffff !important;
}

[data-bs-theme="dark"] .prompt-copy-icon-bg,
[data-theme="dark"] .prompt-copy-icon-bg,
body.dark-mode .prompt-copy-icon-bg {
  background-color: #ffffff !important;
  color: var(--color-default) !important;
}

.prompt-card-item {
  border-radius: 20px !important;
  overflow: hidden !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease !important;
}

.prompt-card-item:hover {
  transform: translateY(-5px) !important;
}

.prompt-card-wrapper {
  border-radius: 20px !important;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.flex-images .item img.prompt-card-img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
  display: block !important;
}

.btn-copy-prompt-grid {
  transition: all 0.2s ease-in-out !important;
}

.btn-copy-prompt-grid:hover {
  transform: scale(1.04) !important;
}

.btn-share-prompt:hover {
  background-color: #e2e8f0 !important;
}

/* Guest copy protection for prompt detail text */
.prompt-guest-protected {
  -webkit-user-select: none !important;
  -moz-user-select: none !important;
  -ms-user-select: none !important;
  user-select: none !important;
  -webkit-touch-callout: none !important;
  cursor: default !important;
}

.prompt-guest-protected * {
  -webkit-user-select: none !important;
  -moz-user-select: none !important;
  -ms-user-select: none !important;
  user-select: none !important;
  -webkit-touch-callout: none !important;
}

.prompt-guest-protected::selection,
.prompt-guest-protected *::selection {
  background: transparent !important;
  color: inherit !important;
}

.prompt-guest-protected::-moz-selection,
.prompt-guest-protected *::-moz-selection {
  background: transparent !important;
  color: inherit !important;
}

@media print {
  .prompt-guest-protected {
    display: none !important;
  }
}

/* Force zero margin on flex-images items */
.flex-images .item {
  margin: 0px 0px !important;
}

/* Author Header & Follow Button Mobile Optimization */
.btn-author-follow {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  border-radius: 50rem !important;
  font-size: 14px !important;
  height: 40px !important;
  padding: 0 18px !important;
  line-height: 1 !important;
  gap: 6px !important;
}

.btn-author-follow i {
  font-size: 15px !important;
  line-height: 1 !important;
}

@media (max-width: 576px) {
  .btn-author-follow {
    font-size: 13px !important;
    height: 36px !important;
    padding: 0 14px !important;
    gap: 4px !important;
  }
  .author-header-username {
    font-size: 1.15rem !important;
  }
  .author-header-avatar {
    width: 44px !important;
    height: 44px !important;
  }
}

/* ==========================================================================
   Explore & Search Page Filter Dropdowns (Mobile & Desktop)
   ========================================================================== */
.explore-filters-wrap {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  width: 100%;
}

.explore-filters-wrap .form-select {
  width: auto;
  min-width: 140px;
  height: 42px;
  font-size: 14px;
  font-weight: 500;
  border-radius: 12px !important;
  border: 1.5px solid #e2e8f0 !important;
  background-color: #ffffff !important;
  color: #1e293b !important;
  padding: 0.4rem 2.2rem 0.4rem 1rem !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.explore-filters-wrap .form-select:focus {
  border-color: #0d6efd !important;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12) !important;
  outline: none;
}

@media (max-width: 767.98px) {
  .explore-filters-wrap {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 10px !important;
    width: 100% !important;
    margin-bottom: 1.5rem !important;
  }

  .explore-filters-wrap .filter-primary,
  .explore-filters-wrap .filter-timeframe {
    grid-column: span 2 !important;
  }

  .explore-filters-wrap .form-select {
    width: 100% !important;
    min-width: 0 !important;
    height: 44px !important;
    font-size: 14px !important;
    margin: 0 !important;
  }
}

@media (max-width: 350px) {
  .explore-filters-wrap {
    grid-template-columns: 1fr !important;
  }
  .explore-filters-wrap .filter-primary,
  .explore-filters-wrap .filter-timeframe {
    grid-column: span 1 !important;
  }
}

[data-bs-theme="dark"] .explore-filters-wrap .form-select,
[data-theme="dark"] .explore-filters-wrap .form-select,
body.dark-mode .explore-filters-wrap .form-select {
  background-color: #1e293b !important;
  border-color: #334155 !important;
  color: #f8fafc !important;
}

/* ==========================================================================
   Transformation Showcase & Before/After Slider Section
   ========================================================================== */
.font-serif-italic {
  font-family: 'Playfair Display', Georgia, 'Times New Roman', serif !important;
  font-style: italic !important;
  font-weight: 600 !important;
}

.transformation-section {
  position: relative;
  overflow: hidden;
}

.transformation-eyebrow {
  color: #00d690 !important;
  letter-spacing: 0.14em;
  font-size: 0.85rem;
}

.transformation-card {
  border-radius: 24px !important;
  background-color: #ffffff !important;
  border: 1px solid #e9ecef !important;
  box-shadow: 0 20px 45px -15px rgba(0, 0, 0, 0.07) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Before / After Slider Box */
.before-after-slider-container {
  aspect-ratio: 4 / 5;
  width: 100%;
  max-width: 540px;
  min-height: 540px;
  margin: 0 auto;
  border-radius: 20px !important;
  position: relative;
  overflow: hidden;
  user-select: none;
  -webkit-user-select: none;
  touch-action: none;
  cursor: ew-resize;
  background-color: #0d1117;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

@media (min-width: 992px) {
  .before-after-slider-container {
    min-height: 600px;
  }
}

@media (max-width: 576px) {
  .before-after-slider-container {
    min-height: 420px;
    aspect-ratio: 4 / 5;
  }
}

.before-after-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  pointer-events: none;
}

.before-img-clip {
  will-change: clip-path;
  clip-path: inset(0 50% 0 0);
  pointer-events: none;
}

/* Floating Badges */
.slider-badge {
  z-index: 12;
  pointer-events: none;
}

.slider-badge-before {
  top: 16px;
  left: 16px;
}

.slider-badge-after {
  top: 16px;
  right: 16px;
}

.badge-campaign-ready {
  background-color: #00d690 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  letter-spacing: 0.3px;
}

/* Divider & Draggable Handle */
.slider-divider {
  left: 50%;
  width: 2px;
  background: #ffffff;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.45);
  pointer-events: none;
  z-index: 10;
  will-change: left;
}

.slider-handle {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 44px;
  height: 44px;
  background: #ffffff;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
  pointer-events: auto;
  cursor: ew-resize;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  border: 2px solid #ffffff;
  outline: none;
}

.before-after-slider-container:hover .slider-handle,
.before-after-slider-container.is-dragging .slider-handle,
.slider-handle:focus {
  transform: translate(-50%, -50%) scale(1.12);
  box-shadow: 0 6px 22px rgba(0, 214, 144, 0.45);
}

/* Cost & Pricing CTA Elements */
.cost-traditional {
  color: #ef4444 !important;
}

.cost-ib-studio {
  color: #00d690 !important;
}

.badge-tier {
  background-color: rgba(0, 214, 144, 0.16) !important;
  color: #00d690 !important;
  border: 1px solid rgba(0, 214, 144, 0.32) !important;
  font-size: 0.72rem;
}

.pricing-cta-card {
  transition: all 0.25s ease;
}

/* --- Dark Theme Specific Optimizations --- */
[data-bs-theme="dark"] .transformation-card {
  background-color: #16191f !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 20px 45px -15px rgba(0, 214, 144, 0.06) !important;
}

[data-bs-theme="dark"] .pricing-cta-card {
  background-color: #1e2227 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .cost-traditional {
  color: #f87171 !important;
}

[data-bs-theme="dark"] .cost-ib-studio {
  color: #00d690 !important;
  text-shadow: 0 0 12px rgba(0, 214, 144, 0.35);
}

@media (min-width: 576px) {
  .border-end-sm {
    border-right: 1px solid var(--bs-border-color, #e9ecef) !important;
  }
}

[data-bs-theme="dark"] .border-end-sm {
  border-right-color: rgba(255, 255, 255, 0.08) !important;
}

.comparison-matrix-card {
  transition: all 0.25s ease;
}

[data-bs-theme="dark"] .comparison-matrix-card {
  background-color: #1a1e24 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

/* ==========================================================================
   Home FAQ Section
   ========================================================================== */
.faq-section {
  background-color: #faf8f5;
  transition: background-color 0.3s ease;
}

[data-bs-theme="dark"] .faq-section {
  background-color: #121519 !important;
}

@media (min-width: 992px) {
  .faq-sticky-header {
    position: sticky;
    top: 110px;
  }
}

.faq-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  border: 1px solid #e9ecef !important;
}

.faq-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
}

[data-bs-theme="dark"] .faq-card {
  background-color: #1a1e24 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .faq-card:hover {
  border-color: rgba(255, 255, 255, 0.16) !important;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2) !important;
}

.faq-toggle {
  cursor: pointer;
}

.faq-toggle:focus {
  outline: none;
}

.faq-toggle .faq-icon-indicator::before {
  content: '−';
  display: inline-block;
  font-weight: 300;
  font-size: 1.5rem;
  line-height: 1;
}

.faq-toggle.collapsed .faq-icon-indicator::before {
  content: '+';
  font-weight: 300;
  font-size: 1.5rem;
  line-height: 1;
}

/* 
==========================================================================
   Content Lists & Typography (ul / ol / li styling)
========================================================================== */
/* Ensure footer, navigation, breadcrumbs, and unstyled utility lists never display bullets or numbering */
footer li,
.py-footer-large li,
.list-unstyled,
.list-unstyled li,
.list-inline,
.list-inline li,
.breadcrumb,
.breadcrumb li,
.breadcrumb-item,
li.breadcrumb-item {
  list-style: none !important;
  list-style-type: none !important;
}
.breadcrumb-item::marker {
  content: "" !important;
}

ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu):not(.pagination):not(.breadcrumb) {
  list-style-type: disc !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 1rem;
}

ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu):not(.pagination):not(.breadcrumb) {
  list-style-type: decimal !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 1rem;
}

ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu):not(.pagination):not(.breadcrumb) > li {
  list-style-type: disc !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}

ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu):not(.pagination):not(.breadcrumb) > li {
  list-style-type: decimal !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}

/* Scoped Content Areas: FAQ & Pricing & Pages */
.faq-answer-content ul,
.faq-collapse ul,
.faq-section ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu),
.pricing-faq-body ul,
.accordion-body ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu),
dd ul {
  list-style-type: disc !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
}

.faq-answer-content ol,
.faq-collapse ol,
.faq-section ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu),
.pricing-faq-body ol,
.accordion-body ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu),
dd ol {
  list-style-type: decimal !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
}

.faq-answer-content ul > li,
.faq-collapse ul > li,
.faq-section ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu) > li,
.pricing-faq-body ul > li,
.accordion-body ul:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu) > li,
dd ul > li {
  list-style-type: disc !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}

.faq-answer-content ol > li,
.faq-collapse ol > li,
.faq-section ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu) > li,
.pricing-faq-body ol > li,
.accordion-body ol:not([class*="nav"]):not([class*="list-"]):not(.dropdown-menu) > li,
dd ol > li {
  list-style-type: decimal !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}

.faq-answer-content li::marker,
.pricing-faq-body li::marker {
  color: var(--color-default, #00d690);
}
</style>
