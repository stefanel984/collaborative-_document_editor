<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Models\DocumentChange;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Events\DocumentChanged;

class DocumentController extends Controller
{
    public function __construct()
    {
        // Protect all methods with JWT middleware
        $this->middleware('auth:api');
    }

    /**
     * Create a new document
     */
    // Create a new document
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $document = Document::create([
            'title' => $request->title,
            'owner_id' => auth()->id(),
        ]);

        return response()->json($document, 201);
    }

    /**
     * Get all documents for authenticated user
     */
     public function index(){
        $user = auth()->user();
       $documents = Document::orderBy('created_at', 'desc')->get();

        return response()->json($documents);
    }


     /**
     * Get full content of a specific document
     */
    public function show($id)
    {
        $user = auth()->user();

        $document = Document::with('owner')
                            ->where('id', $id)
                            ->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Assuming you want to return the document content + changes
        $documentChanges = $document->changes()->orderBy('version')->get();

        return response()->json([
            'document' => $document,
            'changes' => $documentChanges,
            'owner' => [
                'id' => $document->owner->id ?? null,
                'name' => $document->owner->name ?? 'Unknown',
                'email' => $document->owner->email ?? 'Unknown',
            ],
        ]);
    }


    /**
     * Make a change
     */

public function addChange(Request $request, $id)
{
    $user = auth()->user();

    $document = Document::where('id', $id)
                        ->first();

    if (!$document) {
        return response()->json(['error' => 'Document not found'], 404);
    }

    $request->validate([
        'operation' => 'required|array', // array of changes
        'version' => 'required|integer',
    ]);

    $lastVersion = $document->changes()->max('version') ?? 0;
    if ($request->version !== $lastVersion + 1) {
        return response()->json([
            'error' => 'Version mismatch',
            'server_version' => $lastVersion
        ], 409);
    }

    // Save change
    $change = DocumentChange::create([
        'document_id' => $document->id,
        'user_id' => $user->id,
        'operation' => $request->operation,
        'version' => $request->version,
    ]);

    // Update document content based on the operation
    // Here we assume frontend sends operation like [{ insert: "text", position: X }]
    $fullContent = $document->content ?? '';
    foreach ($request->operation as $op) {
        if (isset($op['insert'])) {
            $position = $op['position'] ?? strlen($fullContent);
            $fullContent = substr($fullContent, 0, $position) . $op['insert'] . substr($fullContent, $position);
        }
    }

    // Update document (this automatically updates updated_at)
    $document->update(['content' => $fullContent]);

    // Broadcast to other users
    broadcast(new DocumentChanged($document->id, $change))->toOthers();

    return response()->json([
        'message' => 'Change saved',
        'change' => $change,
    ], 201);
}

public function getChanges($id)
{
    $document = Document::findOrFail($id);
    $changes = $document->changes()->orderBy('version', 'asc')->get();
    return response()->json($changes);
}

}