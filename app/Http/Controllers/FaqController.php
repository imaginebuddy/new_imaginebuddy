<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display public FAQ page at /frequently-asked-questions.
     */
    public function index()
    {
        seo()->setPage('faq');

        $faqs = Faq::active()->ordered()->get();
        $categories = $faqs->pluck('category')->filter()->unique()->values();

        return view('default.faq', compact('faqs', 'categories'));
    }

    /**
     * Redirect legacy /faq alias to /frequently-asked-questions (301 Permanent Redirect).
     */
    public function faqRedirect()
    {
        return redirect('frequently-asked-questions', 301);
    }
}
