<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class TestimonialsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        $data = Testimonial::orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.testimonials', compact('data'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        return view('admin.add-testimonial');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $path = config('path.testimonials');
            $fullDir = public_path($path);
            if (!File::exists($fullDir)) {
                File::makeDirectory($fullDir, 0755, true, true);
            }

            $photo = $request->file('image');
            $extension = $photo->getClientOriginalExtension();
            $imageName = 'testimonial-' . Str::random(16) . '-' . time() . '.' . $extension;

            $img = Image::make($photo)->orientate()->fit(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($extension);

            Storage::put($path . $imageName, $img, 'public');
        }

        Testimonial::create([
            'name' => trim($request->name),
            'designation' => trim($request->designation ?: ''),
            'company' => trim($request->company ?: ''),
            'rating' => $request->rating ?: null,
            'content' => trim($request->content),
            'sort_order' => $request->sort_order !== null ? (int)$request->sort_order : 0,
            'status' => $request->status,
            'image' => $imageName,
        ]);

        return redirect('panel/admin/testimonials')->withSuccessMessage(trans('admin.success_add'));
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        $testimonial = Testimonial::findOrFail($id);

        return view('admin.edit-testimonial', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $path = config('path.testimonials');

        if ($request->hasFile('image')) {
            if ($testimonial->image && Storage::exists($path . $testimonial->image)) {
                Storage::delete($path . $testimonial->image);
            }

            $fullDir = public_path($path);
            if (!File::exists($fullDir)) {
                File::makeDirectory($fullDir, 0755, true, true);
            }

            $photo = $request->file('image');
            $extension = $photo->getClientOriginalExtension();
            $imageName = 'testimonial-' . Str::random(16) . '-' . time() . '.' . $extension;

            $img = Image::make($photo)->orientate()->fit(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($extension);

            Storage::put($path . $imageName, $img, 'public');
            $testimonial->image = $imageName;
        } elseif ($request->boolean('remove_image')) {
            if ($testimonial->image && Storage::exists($path . $testimonial->image)) {
                Storage::delete($path . $testimonial->image);
            }
            $testimonial->image = null;
        }

        $testimonial->name = trim($request->name);
        $testimonial->designation = trim($request->designation ?: '');
        $testimonial->company = trim($request->company ?: '');
        $testimonial->rating = $request->rating ?: null;
        $testimonial->content = trim($request->content);
        $testimonial->sort_order = $request->sort_order !== null ? (int)$request->sort_order : 0;
        $testimonial->status = $request->status;
        $testimonial->save();

        return redirect('panel/admin/testimonials')->withSuccessMessage(trans('admin.success_update'));
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            return view('admin.unauthorized');
        }

        $testimonial = Testimonial::findOrFail($id);
        $path = config('path.testimonials');

        if ($testimonial->image && Storage::exists($path . $testimonial->image)) {
            Storage::delete($path . $testimonial->image);
        }

        $testimonial->delete();

        return redirect('panel/admin/testimonials')->withSuccessMessage(trans('admin.success_delete'));
    }

    public function toggleStatus($id)
    {
        if (!auth()->user()->hasPermission('testimonials')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return view('admin.unauthorized');
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->status = $testimonial->status === 'active' ? 'inactive' : 'active';
        $testimonial->save();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'status' => $testimonial->status]);
        }

        return back()->withSuccessMessage(trans('admin.success_update'));
    }
}
