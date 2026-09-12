<?php

namespace App\Http\Controllers;

use App\Models\ClientLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class ClientLogosController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        $data = ClientLogo::orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.client-logos', compact('data'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        return view('admin.add-client-logo');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $path = config('path.client_logos', 'uploads/logos/');
            $fullDir = public_path($path);
            if (!File::exists($fullDir)) {
                File::makeDirectory($fullDir, 0755, true, true);
            }

            $photo = $request->file('image');
            $extension = strtolower($photo->getClientOriginalExtension());
            $imageName = 'logo-' . Str::random(16) . '-' . time() . '.' . $extension;

            if ($extension === 'svg') {
                $content = file_get_contents($photo->getRealPath());
                // Basic security sanitize: ensure valid SVG and disallow script tags
                if (stripos($content, '<svg') === false || stripos($content, '<script') !== false) {
                    return back()->withInput()->withErrors(['image' => 'The uploaded SVG file is invalid or contains forbidden content.']);
                }
                Storage::put($path . $imageName, $content, 'public');
            } else {
                $img = Image::make($photo)->orientate()->resize(350, 120, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($extension);

                Storage::put($path . $imageName, $img, 'public');
            }
        }

        ClientLogo::create([
            'name' => trim($request->name),
            'website_url' => trim($request->website_url ?: ''),
            'sort_order' => $request->sort_order !== null ? (int)$request->sort_order : 0,
            'status' => $request->status,
            'image' => $imageName,
        ]);

        return redirect('panel/admin/client-logos')->withSuccessMessage(trans('admin.success_add'));
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        $logo = ClientLogo::findOrFail($id);

        return view('admin.edit-client-logo', compact('logo'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $logo = ClientLogo::findOrFail($id);
        $path = config('path.client_logos', 'uploads/logos/');

        if ($request->hasFile('image')) {
            // Delete previous image if exists
            if ($logo->image && Storage::exists($path . $logo->image)) {
                Storage::delete($path . $logo->image);
            }

            $fullDir = public_path($path);
            if (!File::exists($fullDir)) {
                File::makeDirectory($fullDir, 0755, true, true);
            }

            $photo = $request->file('image');
            $extension = strtolower($photo->getClientOriginalExtension());
            $imageName = 'logo-' . Str::random(16) . '-' . time() . '.' . $extension;

            if ($extension === 'svg') {
                $content = file_get_contents($photo->getRealPath());
                if (stripos($content, '<svg') === false || stripos($content, '<script') !== false) {
                    return back()->withInput()->withErrors(['image' => 'The uploaded SVG file is invalid or contains forbidden content.']);
                }
                Storage::put($path . $imageName, $content, 'public');
            } else {
                $img = Image::make($photo)->orientate()->resize(350, 120, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($extension);

                Storage::put($path . $imageName, $img, 'public');
            }

            $logo->image = $imageName;
        }

        $logo->name = trim($request->name);
        $logo->website_url = trim($request->website_url ?: '');
        $logo->sort_order = $request->sort_order !== null ? (int)$request->sort_order : 0;
        $logo->status = $request->status;
        $logo->save();

        return redirect('panel/admin/client-logos')->withSuccessMessage(trans('admin.success_update'));
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            return view('admin.unauthorized');
        }

        $logo = ClientLogo::findOrFail($id);
        $path = config('path.client_logos', 'uploads/logos/');

        if ($logo->image && Storage::exists($path . $logo->image)) {
            Storage::delete($path . $logo->image);
        }

        $logo->delete();

        return redirect('panel/admin/client-logos')->withSuccessMessage(trans('admin.success_delete'));
    }

    public function toggleStatus($id)
    {
        if (!auth()->user()->hasPermission('client_logos')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return view('admin.unauthorized');
        }

        $logo = ClientLogo::findOrFail($id);
        $logo->status = $logo->status === 'active' ? 'inactive' : 'active';
        $logo->save();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'status' => $logo->status]);
        }

        return back()->withSuccessMessage(trans('admin.success_update'));
    }
}
