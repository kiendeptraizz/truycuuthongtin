<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentSchedulerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContentPost::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $contentPosts = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        return view('admin.content-scheduler.index', compact('contentPosts'));
    }

    /**
     * Get calendar events for FullCalendar
     */
    public function calendar(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $posts = ContentPost::whereBetween('scheduled_at', [$start, $end])->get();

        $events = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'start' => $post->scheduled_at->toISOString(),
                'backgroundColor' => $this->getStatusColor($post->status),
                'borderColor' => $this->getStatusColor($post->status),
                'extendedProps' => [
                    'content' => $post->content,
                    'target_groups' => $post->target_groups_string,
                    'status' => $post->status,
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        return view('admin.content-scheduler.create', compact('selectedDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'target_groups' => 'required|array|min:1',
            'target_groups.*' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'content', 'target_groups', 'scheduled_at', 'notes']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('content-images', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        ContentPost::create($data);

        return redirect()->route('admin.content-scheduler.index')
            ->with('success', 'Bài đăng đã được lên lịch thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContentPost $content_scheduler)
    {
        return view('admin.content-scheduler.show', ['contentPost' => $content_scheduler]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentPost $content_scheduler)
    {
        return view('admin.content-scheduler.edit', ['contentPost' => $content_scheduler]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentPost $content_scheduler)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'target_groups' => 'required|array|min:1',
            'target_groups.*' => 'required|string',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,posted,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'content', 'target_groups', 'scheduled_at', 'status', 'notes']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($content_scheduler->image_path) {
                Storage::disk('public')->delete($content_scheduler->image_path);
            }
            $data['image_path'] = $request->file('image')->store('content-images', 'public');
            $data['image_url'] = null; // Clear URL if uploading new file
        } elseif ($request->filled('image_url')) {
            // Delete old image if switching to URL
            if ($content_scheduler->image_path) {
                Storage::disk('public')->delete($content_scheduler->image_path);
                $data['image_path'] = null;
            }
            $data['image_url'] = $request->image_url;
        }

        $content_scheduler->update($data);

        return redirect()->route('admin.content-scheduler.index')
            ->with('success', 'Bài đăng đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentPost $content_scheduler)
    {
        // Delete associated image
        if ($content_scheduler->image_path) {
            Storage::disk('public')->delete($content_scheduler->image_path);
        }

        $content_scheduler->delete();

        return redirect()->route('admin.content-scheduler.index')
            ->with('success', 'Bài đăng đã được xóa thành công!');
    }

    /**
     * Mark post as posted
     */
    public function markAsPosted(ContentPost $content_scheduler)
    {
        $content_scheduler->update(['status' => 'posted']);

        return redirect()->back()
            ->with('success', 'Bài đăng đã được đánh dấu là đã đăng!');
    }

    /**
     * Get status color for calendar
     */
    private function getStatusColor($status)
    {
        return match ($status) {
            'scheduled' => '#007bff',
            'posted' => '#28a745',
            'cancelled' => '#dc3545',
            default => '#6c757d',
        };
    }
}
