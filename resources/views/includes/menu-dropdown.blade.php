@if (auth()->user()->role && ! request()->is('panel/admin') && ! request()->is('panel/admin/*'))
  <li><a class="dropdown-item" href="{{ url('panel/admin') }}"><i class="bi bi-speedometer2 me-2"></i> {{ __('admin.admin') }}</a></li>
  <li><hr class="dropdown-divider"></li>
@endif

{{-- HIDDEN_NAV: Balance and Add Funds (/user/dashboard/add/funds) --}}
@if(false)
@if ($settings->sell_option == 'on')
<li>
  <span class="dropdown-item disable-item">
    <i class="bi bi-cash-stack me-2"></i> {{ __('misc.balance') }}: {{ Helper::amountFormatDecimal(auth()->user()->balance) }}</span>
  </li>

<li>
<a class="dropdown-item" href="{{ url('user/dashboard/add/funds') }}">
  <i class="bi bi-wallet2 me-2"></i> {{ __('misc.wallet') }}: {{ Helper::amountFormatDecimal(auth()->user()->funds) }}
</a>
</li>
@endif
@endif

@if (auth()->user()->role != 'admin')
    <li>
        <span class="dropdown-item disable-item">
            <i class="bi bi-download me-2"></i> {{ __('misc.downloads') }}: {{ auth()->user()->dailyImageDownloadsCount() }}/{{ auth()->user()->totalDailyImageDownloadLimit() == 0 ? '∞' : auth()->user()->totalDailyImageDownloadLimit() }}
        </span>
    </li>
    <li>
        <span class="dropdown-item disable-item">
            <i class="bi bi-copy me-2"></i> Prompt copies: {{ auth()->user()->dailyPromptCopiesCount() }}/{{ auth()->user()->totalDailyPromptLimit() == 0 ? '∞' : auth()->user()->totalDailyPromptLimit() }}
        </span>
    </li>
@endif

{{-- HIDDEN_NAV: Dashboard (/user/dashboard) --}}
@if(false)
@if ($settings->sell_option == 'on')
  <li>
  <a class="dropdown-item" href="{{ url('user/dashboard') }}">
      <i class="bi bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
      </a>
  </li>
@endif
@endif

<li>
<a class="dropdown-item" href="{{ url(auth()->user()->username) }}">
    <i class="bi bi-person me-2"></i> {{ __('users.my_profile') }}
    </a>
</li>

@if ($settings->sell_option == 'on')
<li>
<a class="dropdown-item" href="{{ url('account/subscription') }}">
    <i class="bi-arrow-repeat me-2"></i> {{ __('misc.subscription') }}
    </a>
</li>

{{-- HIDDEN_NAV: Purchases (/user/dashboard/purchases) --}}
@if(false)
<li>
<a class="dropdown-item" href="{{ url('user/dashboard/purchases') }}">
    <i class="bi-bag-check me-2"></i> {{ __('misc.my_purchases') }}
    </a>
</li>
@endif
@endif

<li>
<a class="dropdown-item" href="{{ url(auth()->user()->username, 'collections') }}">
    <i class="bi bi-plus-square me-2"></i> {{ __('misc.collections') }}
    </a>
</li>

<li>
<a class="dropdown-item" href="{{ url('likes') }}">
    <i class="bi bi-heart me-2"></i> {{ __('users.likes') }}
    </a>
</li>

{{-- HIDDEN_NAV: Referrals (/my/referrals) --}}
@if(false)
@if ($settings->referral_system == 'on')
<li>
<a class="dropdown-item" href="{{ url('my/referrals') }}">
    <i class="bi-person-plus me-2"></i> {{ __('misc.referrals') }}
    </a>
</li>
@endif
@endif

<li>
<a class="dropdown-item" href="{{ url('account') }}">
    <i class="bi bi-gear me-2"></i> {{ __('users.account_settings') }}
    </a>
</li>

<li><hr class="dropdown-divider"></li>
<li>
  <a class="dropdown-item" href="javascript:void(0);" id="switchTheme">
    @if (is_null(request()->cookie('theme')))

        <i class="bi-{{ $settings->theme == 'light' ? 'moon-stars' : 'sun' }} me-2"></i> 
          {{ $settings->theme == 'light' ? __('misc.dark_mode') : __('misc.light_mode') }} 

      @elseif (request()->cookie('theme') == 'light')
      <i class="bi-moon-stars me-2"></i> {{ __('misc.dark_mode') }}
      @elseif (request()->cookie('theme') == 'dark')
      <i class="bi-sun me-2"></i> {{ __('misc.light_mode') }}
      @endif
  </a>
  </li>

<li><hr class="dropdown-divider"></li>
<li>
  <a class="dropdown-item" href="{{ url('logout') }}">
    <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('users.logout') }}</a>
  </li>
