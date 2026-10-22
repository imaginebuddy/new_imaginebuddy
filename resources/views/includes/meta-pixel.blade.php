@if (!empty($settings->meta_pixel_status))
  @if (!empty($settings->meta_pixel))
{!! $settings->meta_pixel !!}
  @elseif (!empty($settings->meta_pixel_id))
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ e($settings->meta_pixel_id) }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ e($settings->meta_pixel_id) }}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
  @endif
@endif
