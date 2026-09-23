<?php

namespace App\Http\Controllers;

use DB;
use Lang;
use Mail;
use App\Helper;
use App\Models\User;
use App\Models\Plans;
use App\Models\Query;
use App\Models\Images;
use App\Models\Categories;
use App\Models\Collections;
use App\Models\Photoshoot;
use App\Models\Testimonial;
use App\Models\ClientLogo;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;


class HomeController extends Controller
{
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      // Check Datebase access
      AdminSettings::select('id')->first();
    } catch (\Exception $e) {
      // Redirect to Installer
      return redirect('installer/script');
    }

    Helper::seo()->setPage('home');

    $categories = Categories::where('mode', 'on')
      ->withCount(['images' => function ($q) {
        $q->where('status', 'active');
      }])
      ->orderBy('name')
      ->take(4)
      ->get();
    $images     = Query::latestImagesHome();
    $featured   = in_array(config('settings.show_images_index'), ['featured', 'both']) ? Query::featuredImages() : null;
    $popularCategories = Categories::withCount('images')
      ->latest('images_count')
      ->has('images')->take(5)
      ->get();

    if ($popularCategories->count() != 0) {
      foreach ($popularCategories as $popularCategorie) {
        $categoryName = Lang::has('categories.' . $popularCategorie->slug) ? __('categories.' . $popularCategorie->slug) : $popularCategorie->name;

        $popularCategorieArray[]  = '<a style="color:#FFF;" href="' . url('category', $popularCategorie->slug) . '">' . $categoryName . '</a>';
      }
      $categoryPopular = implode(', ', $popularCategorieArray);
    } else {
      $categoryPopular = false;
    }

    $testimonials = Testimonial::active()->ordered()->get();
    $clientLogos  = ClientLogo::active()->ordered()->get();

    return view(
      'index.home',
      [
        'categories' => $categories,
        'images' => $images,
        'featured' => $featured,
        'categoryPopular' => $categoryPopular,
        'testimonials' => $testimonials,
        'clientLogos' => $clientLogos
      ]
    );
  }

  public function getVerifyAccount($confirmation_code)
  {
    if (
      Auth::guest()
      || Auth::check()
      && Auth::user()->activation_code == $confirmation_code
      && Auth::user()->status == 'pending'
    ) {
      $user = User::where('activation_code', $confirmation_code)->where('status', 'pending')->first();

      if ($user) {

        $update = User::where('activation_code', $confirmation_code)
          ->where('status', 'pending')
          ->update(array('status' => 'active', 'activation_code' => ''));


        Auth::loginUsingId($user->id);

        return redirect('/')
          ->with([
            'success_verify' => true,
          ]);
      } else {
        return redirect('/')
          ->with([
            'error_verify' => true,
          ]);
      }
    } else {
      return redirect('/');
    }
  }

  public function getSearch()
  {
    $q = trim(request()->get('q', ''));

    //<--- * If $q is empty or is less than 3 characters * ---->
    if ($q == '' || mb_strlen($q) <= 2) {
      return redirect('/latest');
    }

    $images = Query::searchImages();

    if (request()->ajax()) {
      return view('includes.images')->with($images)->render() . view('includes.pagination-links')->with($images)->render();
    }

    return view('default.search')->with($images);
  }

  public function members()
  {
    abort(404);

    $users = Query::users();

    if (request()->ajax()) {
      return view('includes.users')->withUsers($users)->render();
    }

    return view('default.members')->withUsers($users);
  }

  public function free()
  {
    Helper::seo()->setPage('prompts_free');
    $images = Query::freeImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.free') . ' Prompts',
      'description' => __('misc.free_desc'),
    ]);
  }

  public function premium()
  {
    if (config('settings.sell_option') == 'off') {
      abort(404);
    }

    Helper::seo()->setPage('prompts_premium');
    $images = Query::premiumImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.premium'),
      'description' => __('misc.premium_desc'),
    ]);
  }

  public function latest()
  {
    Helper::seo()->setPage('explore_latest');
    $images = Query::latestImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.latest'),
      'description' => __('misc.latest_desc'),
    ]);
  }

  public function featured()
  {
    Helper::seo()->setPage('explore_featured');
    $images = Query::featuredImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.featured'),
      'description' => __('misc.featured_desc'),
    ]);
  }


  public function popular()
  {
    Helper::seo()->setPage('explore_popular');
    $images = Query::popularImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.popular'),
      'description' => __('misc.popular_desc'),
    ]);
  }

  public function commented()
  {
    $images = Query::commentedImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.most_commented'),
      'description' => __('misc.most_commented_desc'),
    ]);
  }

  public function viewed()
  {
    Helper::seo()->setPage('explore_viewed');
    $images = Query::viewedImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.most_viewed'),
      'description' => __('misc.most_viewed_desc'),
    ]);
  }

  public function downloads()
  {
    Helper::seo()->setPage('explore_downloads');
    $images = Query::downloadsImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.most_downloads'),
      'description' => __('misc.most_downloads_desc'),
    ]);
  }

  public function copied()
  {
    Helper::seo()->setPage('explore_copied');
    $images = Query::copiedImages();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.most_copied_prompts'),
      'description' => __('misc.most_copied_prompts_desc'),
    ]);
  }

  public function categories()
  {
    $tab = request()->get('tab', 'categories');
    $page = (int) request()->get('page', 1);
    $q = trim(request()->get('q', ''));
    $initialLimit = 24;
    $perPage = 16;

    if (request()->ajax()) {
      if ($tab === 'tags') {
        $tags = $this->getPaginatedTags($page, $initialLimit, $perPage, $q, $hasMore, $nextPage);
        return response()->json([
          'html' => view('includes.tags-listing', ['tags' => $tags])->render(),
          'hasMore' => $hasMore,
          'nextPage' => $nextPage
        ]);
      } else {
        $categories = $this->getPaginatedCategories($page, $initialLimit, $perPage, $q, $hasMore, $nextPage);
        return response()->json([
          'html' => view('includes.categories-listing', ['categories' => $categories])->render(),
          'hasMore' => $hasMore,
          'nextPage' => $nextPage
        ]);
      }
    }

    $initialCategories = $this->getPaginatedCategories(1, $initialLimit, $perPage, $q, $categoriesHasMore, $categoriesNextPage);
    $initialTags = $this->getPaginatedTags(1, $initialLimit, $perPage, $q, $tagsHasMore, $tagsNextPage);

    return view('default.categories', [
      'categories' => $initialCategories,
      'categoriesHasMore' => $categoriesHasMore,
      'categoriesNextPage' => $categoriesNextPage,
      'tags' => $initialTags,
      'tagsHasMore' => $tagsHasMore,
      'tagsNextPage' => $tagsNextPage,
      'activeTab' => $tab
    ]);
  }

  private function getPaginatedCategories($page, $initialLimit, $perPage, $queryStr = '', &$hasMore = false, &$nextPage = null)
  {
    $query = Categories::whereMode('on')->withCount(['images' => function ($q) {
      $q->where('status', 'active');
    }]);

    if ($queryStr !== '') {
      $query->where('name', 'LIKE', '%' . $queryStr . '%');
    }

    $query->orderBy('name');

    $totalCategories = (clone $query)->count();

    if ($page <= 1) {
      $offset = 0;
      $limit = $initialLimit;
    } else {
      $offset = $initialLimit + ($page - 2) * $perPage;
      $limit = $perPage;
    }

    $categories = $query->skip($offset)->take($limit)->get();
    $hasMore = ($offset + $limit) < $totalCategories;
    $nextPage = $hasMore ? ($page + 1) : null;

    return $categories;
  }

  private function getPaginatedTags($page, $initialLimit, $perPage, $queryStr = '', &$hasMore = false, &$nextPage = null)
  {
    $images = Images::select('tags')->where('status', 'active')->whereNotNull('tags')->where('tags', '!=', '')->get();
    $tagsCount = [];

    foreach ($images as $img) {
      $tagsArray = explode(',', $img->tags);
      foreach ($tagsArray as $tag) {
        $tag = trim($tag);
        if ($tag !== '') {
          if ($queryStr !== '' && stripos($tag, $queryStr) === false) {
            continue;
          }

          $key = strtolower($tag);
          if (!isset($tagsCount[$key])) {
            $tagsCount[$key] = [
              'name' => ucwords($tag),
              'slug' => trim(str_replace(' ', '_', $tag)),
              'count' => 0
            ];
          }
          $tagsCount[$key]['count']++;
        }
      }
    }

    usort($tagsCount, function ($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });

    $totalTags = count($tagsCount);

    if ($page <= 1) {
      $offset = 0;
      $limit = $initialLimit;
    } else {
      $offset = $initialLimit + ($page - 2) * $perPage;
      $limit = $perPage;
    }

    $pagedTags = array_slice($tagsCount, $offset, $limit);
    $hasMore = ($offset + $limit) < $totalTags;
    $nextPage = $hasMore ? ($page + 1) : null;

    return $pagedTags;
  }

  public function aiModels()
  {
    Helper::seo()->setPage('ai_models');

    $page = (int) request()->get('page', 1);
    $q = trim(request()->get('q', ''));
    $initialLimit = 24;
    $perPage = 16;

    if (request()->ajax() || request()->wantsJson()) {
      $models = $this->getPaginatedAiModels($page, $initialLimit, $perPage, $q, $hasMore, $nextPage);
      return response()->json([
        'html' => view('includes.ai-models-listing', ['models' => $models])->render(),
        'hasMore' => $hasMore,
        'nextPage' => $nextPage
      ]);
    }

    $initialModels = $this->getPaginatedAiModels(1, $initialLimit, $perPage, $q, $modelsHasMore, $modelsNextPage);

    return view('default.ai-models', [
      'models' => $initialModels,
      'modelsHasMore' => $modelsHasMore,
      'modelsNextPage' => $modelsNextPage,
    ]);
  }

  private function getPaginatedAiModels($page, $initialLimit, $perPage, $queryStr = '', &$hasMore = false, &$nextPage = null)
  {
    $configuredModels = Images::getAiModels();

    $dbCounts = Images::where('status', 'active')
      ->whereNotNull('ai_model')
      ->where('ai_model', '!=', '')
      ->select('ai_model', \DB::raw('count(*) as total'))
      ->groupBy('ai_model')
      ->pluck('total', 'ai_model')
      ->toArray();

    $countsMap = [];
    foreach ($dbCounts as $mName => $c) {
      $countsMap[strtolower(trim($mName))] = $c;
    }

    $allModelsList = [];
    $seenSlugs = [];

    foreach ($configuredModels as $modelName) {
      $slug = \Illuminate\Support\Str::slug($modelName);
      if (isset($seenSlugs[$slug])) continue;
      $seenSlugs[$slug] = true;

      $count = $countsMap[strtolower(trim($modelName))] ?? 0;

      $allModelsList[] = [
        'name' => $modelName,
        'slug' => $slug,
        'count' => $count,
      ];
    }

    foreach ($dbCounts as $mName => $count) {
      $slug = \Illuminate\Support\Str::slug($mName);
      if (isset($seenSlugs[$slug]) || empty($slug)) continue;
      $seenSlugs[$slug] = true;

      $allModelsList[] = [
        'name' => $mName,
        'slug' => $slug,
        'count' => $count,
      ];
    }

    if ($queryStr !== '') {
      $allModelsList = array_values(array_filter($allModelsList, function ($m) use ($queryStr) {
        return stripos($m['name'], $queryStr) !== false;
      }));
    }

    usort($allModelsList, function ($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });

    $totalModels = count($allModelsList);

    if ($page <= 1) {
      $offset = 0;
      $limit = $initialLimit;
    } else {
      $offset = $initialLimit + ($page - 2) * $perPage;
      $limit = $perPage;
    }

    $pagedModels = array_slice($allModelsList, $offset, $limit);
    $hasMore = ($offset + $limit) < $totalModels;
    $nextPage = $hasMore ? ($page + 1) : null;

    return $pagedModels;
  }

  public function aiModelDetail($slug)
  {
    $configuredModels = Images::getAiModels();
    $distinctModels = Images::whereNotNull('ai_model')
      ->where('ai_model', '!=', '')
      ->distinct()
      ->pluck('ai_model')
      ->toArray();

    $combined = array_unique(array_merge($configuredModels, $distinctModels));
    $matchedModel = null;

    foreach ($combined as $model) {
      if (\Illuminate\Support\Str::slug($model) === $slug) {
        $matchedModel = $model;
        break;
      }
    }

    if (!$matchedModel) {
      abort(404);
    }

    $data = Query::aiModelImages($matchedModel);

    $otherModels = [];
    foreach ($configuredModels as $m) {
      $otherModels[] = [
        'name' => $m,
        'slug' => \Illuminate\Support\Str::slug($m),
        'active' => (\Illuminate\Support\Str::slug($m) === $slug),
      ];
    }
    $data['otherModels'] = $otherModels;

    $aiModelEntity = new \App\Models\AiModel([
      'name' => $matchedModel,
      'slug' => $slug,
      'total_prompts' => $data['images']->total(),
      'is_ai_model' => true,
    ]);
    Helper::seo()->setEntity($aiModelEntity);

    if (request()->ajax()) {
      return view('includes.images')->with($data)->render();
    }

    return view('default.ai-model')->with($data);
  }

  public function category($slug)
  {
    $images = Query::categoryImages($slug);

    if (isset($images['category'])) {
      Helper::seo()->setEntity($images['category']);
    }

    if (request()->ajax()) {
      return view('includes.images')->with($images)->render();
    }

    return view('default.category')->with($images);
  }

  public function subcategory($slug, $subcategory)
  {
    $images = Query::subCategoryImages($slug, $subcategory);

    if (isset($images['subcategory'])) {
      Helper::seo()->setEntity($images['subcategory']);
    }

    if (request()->ajax()) {
      return view('includes.images')->with($images)->render();
    }

    return view('default.subcategory')->with($images);
  }

  public function cameras($slug)
  {
    if (strlen($slug) > 3) {
      $images = Query::camerasImages($slug);

      if (request()->ajax()) {
        return view('includes.images')->with($images)->render();
      }

      return view('default.cameras')->with($images);
    } else {
      abort('404');
    }
  }

  public function colors($slug)
  {
    abort(404);
    
    if (strlen($slug) == 6) {
      $images = Query::colorsImages($slug);

      if (request()->ajax()) {
        return view('includes.images')->with($images)->render();
      }

      return view('default.colors')->with($images);
    } else {
      abort('404');
    }
  }

  public function collections(Request $request)
  {
    $title = __('misc.collections') . ' - ';

    $data = Collections::has('collectionImages')
      ->where('type', 'public')
      ->orderBy('id', 'desc')
      ->with(['collectionImages' => function($q) {
        $q->with(['stockCollection', 'images']);
      } , 'creator'])
      ->paginate(config('settings.result_request'));

    if ($request->input('page') > $data->lastPage()) {
      abort('404');
    }

    if (request()->ajax()) {
      return view('includes.collections-grid', ['data' => $data])->render();
    }

    return view('default.collections', ['title' => $title, 'data' => $data]);
  } //<--- End Method

  public function contact()
  {
    Helper::seo()->setPage('contact');
    return view('default.contact');
  }

  public function contactStore(Request $request)
  {
    $input = $request->all();

    $errorMessages = [
      'g-recaptcha-response.required' => 'reCAPTCHA Error',
      'g-recaptcha-response.captcha' => 'reCAPTCHA Error',
    ];

    $validator = Validator::make($input, [
      'full_name' => 'min:3|max:25',
      'email'     => 'required|email',
      'subject'     => 'required',
      'message' => 'min:10|required',
      'g-recaptcha-response' => 'required|captcha'
    ], $errorMessages);

    if ($validator->fails()) {
      return redirect('contact')
        ->withInput()->withErrors($validator);
    }

    // SEND EMAIL TO SUPPORT
    $fullname    = $input['full_name'];
    $email_user  = $input['email'];
    $title_site  = config('settings.title');
    $subject     = $input['subject'];
    $email_reply = config('settings.email_admin');

    Mail::send(
      'emails.contact-email',
      array(
        'full_name' => $input['full_name'],
        'email' => $input['email'],
        'subject' => $input['subject'],
        '_message' => $input['message'],
        'ip' => request()->ip(),
      ),
      function ($message) use (
        $fullname,
        $email_user,
        $title_site,
        $email_reply,
        $subject
      ) {
        $message->from($email_reply, $fullname);
        $message->subject(__('misc.message') . ' - ' . $subject . ' - ' . $email_user);
        $message->to($email_reply, $title_site);
        $message->replyTo($email_user);
      }
    );

    return redirect('contact')->with(['notification' => __('misc.send_contact_success')]);
  }

  public function pricing()
  {
    $plans = Plans::whereStatus('1');

    if ($plans->count() == 0 || config('settings.sell_option') == 'off') {
      abort(404);
    }

    Helper::seo()->setPage('pricing');

    \App\Services\AnalyticsService::logEvent('pricing_view', request()->fullUrl(), null, [
      'currency' => Helper::currentCurrency()['code'] ?? 'USD',
      'plans_count' => $plans->count(),
    ]);

    return view('default.pricing')->with([
      'plans' => $plans,
      'getSubscription' => auth()->check() ? auth()->user()->getSubscription() : null
    ]);
  }

  public function tags()
  {
    return redirect('categories?tab=tags');
  }

  public function tagsShow($slug)
  {
    $slug = str_replace('_', ' ', $slug);

    if (strlen($slug) > 1) {
      $images = Query::tagsImages($slug);

      if (request()->ajax()) {
        return view('includes.images')->with($images)->render();
      }

      return view('default.tags-show')->with($images);
    } else {
      abort('404');
    }
  }

  public function vectors()
  {
    $images = Query::vectors();

    if (request()->ajax()) {
      return view('includes.images', ['images' => $images])->render();
    }

    return view('index.explore', [
      'images' => $images,
      'title' => __('misc.vectors'),
      'description' => __('misc.vectors_desc'),
    ]);
  }

  public function photoshoots(Request $request)
  {
    Helper::seo()->setPage('photoshoots_index');
    $categorySlug = trim($request->get('category', ''));

    $categories = Categories::where('mode', 'on')
      ->orderBy('name')
      ->get();

    $query = Photoshoot::with([
        'category',
        'user',
        'images' => function ($q) {
          $q->where('status', 'active')->orderBy('id', 'asc');
        }
      ])
      ->whereHas('images', function ($q) {
        $q->where('status', 'active');
      });

    if (!empty($categorySlug)) {
      $query->where(function ($q) use ($categorySlug) {
        $q->whereHas('category', function ($c) use ($categorySlug) {
          $c->where('slug', $categorySlug);
        })->orWhereHas('images', function ($img) use ($categorySlug) {
          $img->whereHas('category', function ($c) use ($categorySlug) {
            $c->where('slug', $categorySlug);
          });
        });
      });
    }

    $photoshoots = $query->orderBy('id', 'desc')->paginate(12);

    if ($request->ajax()) {
      $html = '';
      foreach ($photoshoots as $photoshoot) {
        $html .= view('includes.photoshoot-card', compact('photoshoot'))->render();
      }
      return response()->json([
        'html' => $html,
        'hasMore' => $photoshoots->hasMorePages(),
        'nextPage' => $photoshoots->hasMorePages() ? ($photoshoots->currentPage() + 1) : null
      ]);
    }

    $currentCategory = !empty($categorySlug) ? Categories::where('slug', $categorySlug)->first() : null;

    return view('photoshoots.index', compact('photoshoots', 'categories', 'categorySlug', 'currentCategory'));
  }

  public function photoshootDetail($slug)
  {
    $photoshoot = Photoshoot::with(['category', 'user'])->where('slug', $slug)->firstOrFail();

    $images = Images::selectFieldsRelation()
      ->where('photoshoot_id', $photoshoot->id)
      ->whereStatus('active')
      ->orderBy('id', 'desc')
      ->paginate(12);

    $photoshoot->setRelation('images', $images->getCollection());
    Helper::seo()->setEntity($photoshoot);

    if (request()->ajax()) {
      if (request()->wantsJson()) {
        return response()->json([
          'html' => view('includes.images', ['images' => $images])->render(),
          'hasMore' => $images->hasMorePages(),
          'nextPage' => $images->hasMorePages() ? ($images->currentPage() + 1) : null,
          'total' => $images->total(),
          'count' => $images->count(),
        ]);
      }

      return view('includes.images', ['images' => $images])->render() . view('includes.pagination-links', ['images' => $images])->render();
    }

    return view('photoshoots.show', compact('photoshoot', 'images'));
  }
}
