<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkTask;
use Illuminate\Http\Request;

class WorkTaskController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab') === 'done' ? 'done' : 'pending';
        $search = trim((string) $request->get('q', ''));

        $query = WorkTask::query();
        $tab === 'done' ? $query->done() : $query->pending();

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('note', 'LIKE', "%{$search}%");
            });
        }

        $tasks = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $pendingCount = WorkTask::pending()->count();
        $doneCount = WorkTask::done()->count();

        return view('admin.work-tasks.index', compact('tasks', 'tab', 'search', 'pendingCount', 'doneCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['note' => 'nullable|string|max:500']);

        $task = WorkTask::create([
            'code' => WorkTask::generateCode(),
            'note' => $data['note'] ?? null,
            'status' => WorkTask::STATUS_PENDING,
            'created_via' => 'web',
            'created_by' => (string) (auth()->id() ?? ''),
        ]);

        return back()->with('success', "Đã tạo mã công việc: {$task->code}");
    }

    public function toggle(WorkTask $workTask)
    {
        if ($workTask->isDone()) {
            $workTask->reopen();
            $msg = "Đã mở lại việc {$workTask->code}";
        } else {
            $workTask->markDone();
            $msg = "Đã đánh dấu hoàn thành {$workTask->code}";
        }

        return back()->with('success', $msg);
    }

    public function updateNote(Request $request, WorkTask $workTask)
    {
        $data = $request->validate(['note' => 'nullable|string|max:500']);
        $workTask->update(['note' => $data['note'] ?? null]);

        return back()->with('success', "Đã cập nhật ghi chú {$workTask->code}");
    }

    public function destroy(WorkTask $workTask)
    {
        $code = $workTask->code;
        $workTask->delete();

        return back()->with('success', "Đã xoá mã {$code}");
    }
}
