<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $forums = Forum::active()->ordered()->withCount(['topics'])->get();
        
        // Get recent activity across all forums
        $recentTopics = ForumTopic::with(['forum', 'user', 'latestReply.user'])
            ->orderBy('last_activity_at', 'desc')
            ->limit(10)
            ->get();

        return view('community.forums.index', compact('forums', 'recentTopics'));
    }

    public function show(Forum $forum)
    {
        $topics = $forum->topics()
            ->with(['user', 'latestReply.user'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        return view('community.forums.show', compact('forum', 'topics'));
    }

    public function createTopic(Forum $forum)
    {
        return view('community.forums.create-topic', compact('forum'));
    }

    public function storeTopic(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $topic = $forum->topics()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Award points for creating a topic
        UserPoint::awardPoints(
            Auth::id(),
            'forum_topic',
            $topic,
            'Started forum topic: ' . $topic->title
        );

        return redirect()->route('forums.topics.show', $topic)
            ->with('success', 'Topic created successfully!');
    }

    public function showTopic(ForumTopic $topic)
    {
        // Increment views
        $topic->incrementViews();

        $replies = $topic->replies()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        return view('community.forums.topic', compact('topic', 'replies'));
    }

    public function replyToTopic(Request $request, ForumTopic $topic)
    {
        if ($topic->is_locked) {
            return back()->with('error', 'This topic is locked and cannot receive new replies.');
        }

        $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $reply = $topic->replies()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('forums.topics.show', $topic)
            ->with('success', 'Reply posted successfully!');
    }

    public function editTopic(ForumTopic $topic)
    {
        if ($topic->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own topics.');
        }

        return view('community.forums.edit-topic', compact('topic'));
    }

    public function updateTopic(Request $request, ForumTopic $topic)
    {
        if ($topic->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own topics.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('forums.topics.show', $topic)
            ->with('success', 'Topic updated successfully!');
    }

    public function deleteTopic(ForumTopic $topic)
    {
        if ($topic->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only delete your own topics.');
        }

        $forum = $topic->forum;
        $topic->delete();

        return redirect()->route('forums.show', $forum)
            ->with('success', 'Topic deleted successfully!');
    }

    public function editReply(ForumReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own replies.');
        }

        return view('community.forums.edit-reply', compact('reply'));
    }

    public function updateReply(Request $request, ForumReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own replies.');
        }

        $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $reply->update([
            'content' => $request->content,
        ]);

        return redirect()->route('forums.topics.show', $reply->topic)
            ->with('success', 'Reply updated successfully!');
    }

    public function deleteReply(ForumReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only delete your own replies.');
        }

        $topic = $reply->topic;
        $reply->delete();

        return redirect()->route('forums.topics.show', $topic)
            ->with('success', 'Reply deleted successfully!');
    }

    // Admin functions
    public function pinTopic(ForumTopic $topic)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $topic->update(['is_pinned' => !$topic->is_pinned]);

        $status = $topic->is_pinned ? 'pinned' : 'unpinned';
        return back()->with('success', "Topic {$status} successfully!");
    }

    public function lockTopic(ForumTopic $topic)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $topic->update(['is_locked' => !$topic->is_locked]);

        $status = $topic->is_locked ? 'locked' : 'unlocked';
        return back()->with('success', "Topic {$status} successfully!");
    }
}
