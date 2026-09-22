<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UrlRedirect;
use App\Models\AdminSettings;

class RedirectsController extends Controller
{
    protected $settings;

    public function __construct(AdminSettings $settings)
    {
        $this->settings = $settings::first();
    }

    /**
     * List redirects with filtering and statistics.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        $query = trim($request->get('q', ''));
        $type = $request->get('type', '');
        $status = $request->get('status', '');

        $dataQuery = UrlRedirect::query()
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($sub) use ($query) {
                    $sub->where('source_url', 'LIKE', "%{$query}%")
                        ->orWhere('destination_url', 'LIKE', "%{$query}%")
                        ->orWhere('notes', 'LIKE', "%{$query}%");
                });
            })
            ->when($type !== '' && in_array($type, ['301', '302']), function ($q) use ($type) {
                return $q->where('redirect_type', (int) $type);
            })
            ->when($status !== '' && in_array($status, ['0', '1']), function ($q) use ($status) {
                return $q->where('status', (int) $status);
            })
            ->orderBy('id', 'desc');

        $data = $dataQuery->paginate(20);

        $stats = [
            'total' => UrlRedirect::count(),
            'active' => UrlRedirect::where('status', 1)->count(),
            'inactive' => UrlRedirect::where('status', 0)->count(),
            'permanent' => UrlRedirect::where('redirect_type', 301)->count(),
            'temporary' => UrlRedirect::where('redirect_type', 302)->count(),
            'total_hits' => UrlRedirect::sum('hits_count'),
        ];

        return view('admin.redirects', compact('data', 'stats', 'query', 'type', 'status'));
    }

    /**
     * Show redirect creation form.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        return view('admin.add-redirect');
    }

    /**
     * Store newly created redirect.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'source_url' => 'required|string|max:500',
            'destination_url' => 'required|string|max:1000',
            'redirect_type' => 'required|in:301,302',
            'notes' => 'nullable|string|max:255',
        ]);

        $source = UrlRedirect::normalizeSourceUrl($request->source_url);
        $destination = UrlRedirect::normalizeDestinationUrl($request->destination_url);

        // Disallow redirecting the homepage or reserved routes
        $reservedError = $this->validateReservedRoutes($source);
        if ($reservedError) {
            return back()->withInput()->withErrors(['source_url' => $reservedError]);
        }

        // Check uniqueness on normalized source URL
        if (UrlRedirect::where('source_url', $source)->exists()) {
            return back()->withInput()->withErrors([
                'source_url' => "A redirect for source URL '{$source}' already exists."
            ]);
        }

        // Check for self-redirects and cyclic loops
        $loopError = UrlRedirect::detectLoop($source, $destination);
        if ($loopError) {
            return back()->withInput()->withErrors(['destination_url' => $loopError]);
        }

        UrlRedirect::create([
            'source_url' => $source,
            'destination_url' => $destination,
            'redirect_type' => (int) $request->redirect_type,
            'status' => $request->has('status') ? (bool) $request->status : true,
            'preserve_query' => $request->has('preserve_query') ? (bool) $request->preserve_query : true,
            'notes' => $request->notes ? trim($request->notes) : null,
        ]);

        return redirect('panel/admin/redirects')
            ->withSuccessMessage('URL redirect created successfully!');
    }

    /**
     * Show redirect edit form.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        $data = UrlRedirect::findOrFail($id);

        return view('admin.edit-redirect', compact('data'));
    }

    /**
     * Update existing redirect.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        $redirect = UrlRedirect::findOrFail($id);

        $request->validate([
            'source_url' => 'required|string|max:500',
            'destination_url' => 'required|string|max:1000',
            'redirect_type' => 'required|in:301,302',
            'notes' => 'nullable|string|max:255',
        ]);

        $source = UrlRedirect::normalizeSourceUrl($request->source_url);
        $destination = UrlRedirect::normalizeDestinationUrl($request->destination_url);

        // Disallow reserved routes
        $reservedError = $this->validateReservedRoutes($source);
        if ($reservedError) {
            return back()->withInput()->withErrors(['source_url' => $reservedError]);
        }

        // Check uniqueness excluding current record
        if (UrlRedirect::where('source_url', $source)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors([
                'source_url' => "A redirect for source URL '{$source}' already exists."
            ]);
        }

        // Check for loops
        $loopError = UrlRedirect::detectLoop($source, $destination, $id);
        if ($loopError) {
            return back()->withInput()->withErrors(['destination_url' => $loopError]);
        }

        $redirect->source_url = $source;
        $redirect->destination_url = $destination;
        $redirect->redirect_type = (int) $request->redirect_type;
        $redirect->status = $request->has('status') ? (bool) $request->status : true;
        $redirect->preserve_query = $request->has('preserve_query') ? (bool) $request->preserve_query : false;
        $redirect->notes = $request->notes ? trim($request->notes) : null;
        $redirect->save();

        return redirect('panel/admin/redirects')
            ->withSuccessMessage('URL redirect updated successfully!');
    }

    /**
     * Delete redirect.
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            return view('admin.unauthorized');
        }

        $redirect = UrlRedirect::findOrFail($id);
        $redirect->delete();

        return redirect('panel/admin/redirects')
            ->withSuccessMessage('URL redirect removed successfully.');
    }

    /**
     * Toggle redirect active/inactive status.
     */
    public function toggleStatus(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('redirects')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return view('admin.unauthorized');
        }

        $redirect = UrlRedirect::findOrFail($id);
        $redirect->status = !$redirect->status;
        $redirect->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $redirect->status,
                'message' => $redirect->status ? 'Redirect enabled.' : 'Redirect disabled.'
            ]);
        }

        return back()->withSuccessMessage(
            $redirect->status ? 'Redirect enabled successfully.' : 'Redirect disabled successfully.'
        );
    }

    /**
     * Validate against reserved system paths.
     */
    protected function validateReservedRoutes(string $source): ?string
    {
        $cleanPath = trim(explode('?', $source)[0], '/');

        if ($cleanPath === '') {
            return 'Cannot redirect the homepage root (/).';
        }

        $blockedPrefixes = [
            'panel',
            'api',
            'oauth',
            'webhook',
            'login',
            'logout',
            'register',
            'password',
            'installer',
        ];

        foreach ($blockedPrefixes as $prefix) {
            if ($cleanPath === $prefix || str_starts_with($cleanPath, $prefix . '/')) {
                return "Cannot create a redirect for reserved system route '{$source}'.";
            }
        }

        return null;
    }
}
