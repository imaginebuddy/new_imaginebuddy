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
.home-cover { background-image: url('{{ url('public/img', $settings->image_header) }}') }
:root {
  --color-default: {{ $settings->color_default }} !important;
  --bg-auth: url('{{ url('public/img', $settings->image_header) }}');
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
}

[data-bs-theme="dark"] .breadcrumb-pill-box {
  background-color: #1e2227 !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

.breadcrumb-pill-box .breadcrumb-item + .breadcrumb-item::before {
  content: ">" !important;
  color: #a0aec0 !important;
  padding-left: 0.6rem !important;
  padding-right: 0.6rem !important;
}

.breadcrumb-pill-box .breadcrumb-item a {
  color: #718096 !important;
  font-weight: 500 !important;
  font-size: 0.88rem !important;
  transition: color 0.2s ease !important;
}

.breadcrumb-pill-box .breadcrumb-item a:hover {
  color: #00d690 !important;
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

#header.header-home-transparent .logoMain {
  display: none !important;
}

#header.header-home-transparent .logoLight {
  display: block !important;
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
var _title = '@section("title")@show {{e($settings->title.' - '.__('seo.welcome_subtitle'))}}';
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

.home-cover { background-image: url('{{ url('public/img', $settings->image_header) }}') }
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

.prompt-title-link {
  color: #090d16 !important;
}

.prompt-snippet-text {
  color: #94a3b8 !important;
}

.prompt-author-pill {
  background-color: #f1f5f9 !important;
  color: #1e293b !important;
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
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  height: 36px !important;
}

.prompt-copy-icon-bg {
  background-color: #ffffff !important;
  color: #111827 !important;
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
  background-color: #2563eb !important;
  border-color: #2563eb !important;
  color: #ffffff !important;
}

[data-bs-theme="dark"] .prompt-copy-icon-bg,
[data-theme="dark"] .prompt-copy-icon-bg,
body.dark-mode .prompt-copy-icon-bg {
  background-color: #ffffff !important;
  color: #2563eb !important;
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

/* Force zero margin on flex-images items */
.flex-images .item {
  margin: 0px 0px !important;
}
</style>
