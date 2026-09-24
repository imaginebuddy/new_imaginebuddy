<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SeoMetadata;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    /**
     * Display a listing of FAQs in the Admin panel.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $query = Faq::query();

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('question', 'LIKE', '%' . $search . '%')
                  ->orWhere('answer', 'LIKE', '%' . $search . '%')
                  ->orWhere('category', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $faqSeo = SeoMetadata::where('page_key', 'faq')->first();

        return view('admin.faqs', compact('data', 'faqSeo'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $existingCategories = Faq::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        return view('admin.add-faq', compact('existingCategories'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $request->merge([
            'status' => ($request->has('status') && $request->status === 'active') ? 'active' : 'inactive',
        ]);

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Faq::create([
            'question' => trim(strip_tags($request->question)),
            'answer' => trim($request->answer),
            'category' => $request->category ? trim(strip_tags($request->category)) : null,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'status' => $request->status,
        ]);

        return redirect('panel/admin/faqs')->withSuccessMessage(trans('admin.success_add'));
    }

    /**
     * Show the form for editing an FAQ.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $faq = Faq::findOrFail($id);

        $existingCategories = Faq::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        return view('admin.edit-faq', compact('faq', 'existingCategories'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $request->merge([
            'status' => ($request->has('status') && $request->status === 'active') ? 'active' : 'inactive',
        ]);

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $faq = Faq::findOrFail($id);

        $faq->question = trim(strip_tags($request->question));
        $faq->answer = trim($request->answer);
        $faq->category = $request->category ? trim(strip_tags($request->category)) : null;
        $faq->sort_order = $request->filled('sort_order') ? (int) $request->sort_order : 0;
        $faq->status = $request->status;
        $faq->save();

        return redirect('panel/admin/faqs')->withSuccessMessage(trans('admin.success_update'));
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            return view('admin.unauthorized');
        }

        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect('panel/admin/faqs')->withSuccessMessage(trans('admin.success_delete'));
    }

    /**
     * Toggle the status of an FAQ (active/inactive).
     */
    public function toggleStatus($id)
    {
        if (!auth()->user()->hasPermission('faqs')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return view('admin.unauthorized');
        }

        $faq = Faq::findOrFail($id);
        $faq->status = $faq->status === 'active' ? 'inactive' : 'active';
        $faq->save();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'status' => $faq->status]);
        }

        return back()->withSuccessMessage(trans('admin.success_update'));
    }
}
