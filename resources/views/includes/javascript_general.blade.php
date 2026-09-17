<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{ asset('public/js/core.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/bootstrap.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/fleximages/jquery.flex-images.min.js') }}"></script>
<script src="{{ asset('public/js/timeago/jqueryTimeago_'.Lang::locale().'.js') }}"></script>
<script src="{{ asset('public/js/functions.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/install-app.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/switch-theme.js') }}?v={{$settings->version}}"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script type="text/javascript">

// Initialize GLightbox Modal Popup
$(document).ready(function() {
  if (typeof GLightbox === 'function') {
    const lightbox = GLightbox({
      selector: '.glightbox',
      touchNavigation: true,
      loop: false,
      zoomable: true
    });
  }
});

// Delete Confirmation Handler
$(document).on('click', '#deletePhoto, .actionDelete', function(e) {
  e.preventDefault();
  var element = $(this);
  var form = element.closest('form');
  var url = element.attr('data-url') || element.attr('href');
  var confirmTitle = element.attr('data-confirm-title') || (typeof delete_confirm !== 'undefined' ? delete_confirm : "Are you sure you want to delete this?");

  function performDelete() {
    if (url && url !== 'javascript:void(0);' && url !== '#') {
      window.location.href = url;
    } else if (form && form.length) {
      form.submit();
    }
  }

  if (typeof swal === 'function') {
    swal({
      title: confirmTitle,
      type: "warning",
      showLoaderOnConfirm: true,
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: typeof yes_confirm !== 'undefined' ? yes_confirm : "Yes, delete it!",
      cancelButtonText: typeof cancel_confirm !== 'undefined' ? cancel_confirm : "Cancel",
      closeOnConfirm: false,
    }, function (isConfirm) {
      if (isConfirm) {
        performDelete();
      }
    });
  } else if (confirm(confirmTitle)) {
    performDelete();
  }
});

@if ($settings->custom_js)
  {!! $settings->custom_js !!}
@endif

@if (session('required_2fa'))
var myModal = new bootstrap.Modal(document.getElementById('modal2fa'), {
  backdrop: 'static',
  keyboard: false
});
myModal.show();
@endif

// Grid Card & Show Page Copy & Share Handlers
$(document).on('click', '.btn-copy-prompt-grid, .btn-copy-prompt', function(e) {
  e.preventDefault();
  e.stopPropagation();
  var btn = $(this);
  var id = btn.data('id');
  var originalHtml = btn.html();

  @guest
    if ($('#authCopyModal').length > 0) {
      var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
      authModal.show();
      return false;
    } else {
      var currentTarget = window.location.href;
      window.location.href = URL_BASE + '/login?return=' + encodeURIComponent(currentTarget);
      return false;
    }
  @endguest

  btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Copying...');

  $.ajax({
    url: URL_BASE + '/prompt/copy/' + id,
    type: 'POST',
    data: { _token: '{{ csrf_token() }}' },
    dataType: 'json',
    success: function(response) {
      btn.prop('disabled', false).html(originalHtml);
      if (response.success) {
        var promptText = response.prompt;
        var remainingMsg = (response.remaining_copies !== undefined) ? ' (' + response.remaining_copies + ' left)' : '';
        if (response.remaining_copies !== undefined) {
          $('.remaining-copies-count').text(response.remaining_copies);
        }

        var applyCopiedState = function() {
          btn.removeClass('btn-primary btn-dark').addClass('btn-success').html('<i class="bi bi-check-lg me-1"></i> Copied!' + remainingMsg);
          setTimeout(function() {
            btn.removeClass('btn-success').html(originalHtml);
          }, 3000);
        };

        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(promptText).then(applyCopiedState);
        } else {
          var textArea = document.createElement("textarea");
          textArea.value = promptText;
          document.body.appendChild(textArea);
          textArea.select();
          try {
            document.execCommand("copy");
          } catch(err) {}
          document.body.removeChild(textArea);
          applyCopiedState();
        }
      }
    },
    error: function(xhr) {
      btn.prop('disabled', false).html(originalHtml);
      var res = xhr.responseJSON;
      if (res && res.require_login) {
        if ($('#authCopyModal').length > 0) {
          var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
          authModal.show();
        } else {
          var currentTarget = window.location.href;
          window.location.href = URL_BASE + '/login?return=' + encodeURIComponent(currentTarget);
        }
      } else if (res && res.require_subscription) {
        window.location.href = URL_BASE + '/pricing';
      } else if (res && res.limit_reached) {
        alert(res.message || 'Daily prompt copy limit reached.');
      } else {
        alert(res && res.message ? res.message : 'An error occurred while copying the prompt.');
      }
    }
  });
});

@guest
// Guest Copy Protection: Scoped event listeners on .prompt-guest-protected
$(document).on('selectstart', '.prompt-guest-protected', function(e) {
  e.preventDefault();
  return false;
});

$(document).on('copy cut', '.prompt-guest-protected', function(e) {
  e.preventDefault();
  if (e.originalEvent && e.originalEvent.clipboardData) {
    e.originalEvent.clipboardData.clearData();
  }
  if ($('#authCopyModal').length > 0) {
    var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
    authModal.show();
  }
  return false;
});

document.addEventListener('copy', function(e) {
  var protectedEl = document.querySelector('.prompt-guest-protected');
  if (protectedEl) {
    var sel = window.getSelection();
    if (sel && sel.rangeCount > 0) {
      var range = sel.getRangeAt(0);
      if (protectedEl.contains(range.commonAncestorContainer) || protectedEl.contains(document.activeElement)) {
        e.preventDefault();
        if (e.clipboardData) {
          e.clipboardData.clearData();
        }
        if ($('#authCopyModal').length > 0) {
          var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
          authModal.show();
        }
      }
    }
  }
});

$(document).on('contextmenu', '.prompt-guest-protected', function(e) {
  e.preventDefault();
  if ($('#authCopyModal').length > 0) {
    var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
    authModal.show();
  }
  return false;
});

$(document).on('keydown', function(e) {
  var isCtrlOrCmd = e.ctrlKey || e.metaKey;
  if (isCtrlOrCmd && (e.key === 'c' || e.key === 'C' || e.key === 'x' || e.key === 'X')) {
    var protectedEl = document.querySelector('.prompt-guest-protected');
    if (protectedEl) {
      var sel = window.getSelection();
      var isWithin = false;
      if (sel && sel.rangeCount > 0) {
        var container = sel.getRangeAt(0).commonAncestorContainer;
        if (protectedEl.contains(container)) {
          isWithin = true;
        }
      }
      if (protectedEl.contains(document.activeElement) || isWithin) {
        e.preventDefault();
        if ($('#authCopyModal').length > 0) {
          var authModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authCopyModal'));
          authModal.show();
        }
        return false;
      }
    }
  }
});

$(document).on('dragstart', '.prompt-guest-protected', function(e) {
  e.preventDefault();
  return false;
});
@else
$(document).ready(function() {
  var urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('autocopy') === '1') {
    urlParams.delete('autocopy');
    var newSearch = urlParams.toString();
    var newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '') + window.location.hash;
    window.history.replaceState({}, document.title, newUrl);

    var copyBtn = $('.btn-copy-prompt');
    if (copyBtn.length > 0) {
      setTimeout(function() {
        copyBtn.trigger('click');
      }, 350);
    }
  }
});
@endguest

$(document).on('click', '.btn-share-prompt', function(e) {
  e.preventDefault();
  e.stopPropagation();
  var url = $(this).data('url');
  var title = $(this).data('title');

  if (navigator.share) {
    navigator.share({ title: title, url: url }).catch(function(){});
  } else {
    navigator.clipboard.writeText(url);
    alert('Prompt link copied to clipboard!');
  }
});

@if (request()->is('/') || request()->is('home'))
$(window).on('scroll resize', function() {
  if ($(window).scrollTop() > 150) {
    $('#header').removeClass('header-home-transparent').addClass('shadow-sm bg-white');
    $('.navbar-search-form').removeClass('home-search-hidden');
  } else {
    $('#header').addClass('header-home-transparent').removeClass('shadow-sm bg-white');
    $('.navbar-search-form').addClass('home-search-hidden');
  }
});
@endif
</script>
