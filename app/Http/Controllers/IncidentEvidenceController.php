<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentEvidenceController extends Controller
{
    public function store(Request $request, Incident $incident)
    {
        $request->validate([
            'type' => 'required|in:comment,photo',
            'content' => 'required_if:type,comment|string|max:1000',
            'photo' => 'required_if:type,photo|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'photo_description' => 'nullable|string|max:255',
        ]);

        $evidenceData = [
            'incident_id' => $incident->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
        ];

        if ($request->type === 'comment') {
            $evidenceData['content'] = $request->content;
        } else {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('evidence-photos', $filename, 'public');

                $evidenceData['file_path'] = $path;
                $evidenceData['file_name'] = $file->getClientOriginalName();
                $evidenceData['file_size'] = $file->getSize();
                $evidenceData['mime_type'] = $file->getMimeType();
                $evidenceData['content'] = $request->photo_description;
            }
        }

        $evidence = IncidentEvidence::create($evidenceData);

        return response()->json([
            'success' => true,
            'message' => $request->type === 'comment' ? 'Comment added successfully!' : 'Photo evidence added successfully!',
            'evidence' => [
                'id' => $evidence->id,
                'type' => $evidence->type,
                'content' => $evidence->content,
                'user_name' => $evidence->user->name,
                'created_at' => $evidence->created_at->format('M j, Y \a\t g:i A'),
                'file_url' => $evidence->file_url,
                'formatted_file_size' => $evidence->formatted_file_size,
            ]
        ]);
    }

    public function index(Incident $incident)
    {
        $evidence = $incident->evidence()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'content' => $item->content,
                    'user_name' => $item->user->name,
                    'created_at' => $item->created_at->format('M j, Y \a\t g:i A'),
                    'file_url' => $item->file_url,
                    'file_name' => $item->file_name,
                    'formatted_file_size' => $item->formatted_file_size,
                    'is_verified' => $item->is_verified,
                ];
            });

        return response()->json([
            'success' => true,
            'evidence' => $evidence
        ]);
    }

    public function destroy(IncidentEvidence $evidence)
    {
        // Only allow the user who submitted the evidence or admins to delete
        if ($evidence->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own evidence.'
            ], 403);
        }

        // Delete file if it exists
        if ($evidence->file_path) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evidence deleted successfully!'
        ]);
    }
}
