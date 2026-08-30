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
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(promptText).then(function() {
            btn.removeClass('btn-primary').addClass('btn-success').html('<i class="bi bi-check-lg me-1"></i> Copied!');
            setTimeout(function() {
              btn.removeClass('btn-success').addClass('btn-primary').html(originalHtml);
            }, 3000);
          });
        } else {
          var textArea = document.createElement("textarea");
          textArea.value = promptText;
          document.body.appendChild(textArea);
          textArea.select();
          try {
            document.execCommand("copy");
          } catch(err) {}
          document.body.removeChild(textArea);
          btn.removeClass('btn-primary').addClass('btn-success').html('<i class="bi bi-check-lg me-1"></i> Copied!');
          setTimeout(function() {
            btn.removeClass('btn-success').addClass('btn-primary').html(originalHtml);
          }, 3000);
        }
      }
    },
    error: function(xhr) {
      btn.prop('disabled', false).html(originalHtml);
      var res = xhr.responseJSON;
      if (res && res.require_login) {
        window.location.href = URL_BASE + '/login';
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
