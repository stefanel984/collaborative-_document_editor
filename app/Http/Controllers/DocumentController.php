<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
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
        $documents = Document::where('owner_id', $user->id)->get();

        return response()->json($documents);
    }


     /**
     * Get full content of a specific document
     */
    public function show($id)
    {
        $user = auth()->user();

        $document = Document::where('id', $id)
                            ->where('owner_id', $user->id)
                            ->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Assuming you want to return the document content + changes
        $documentChanges = $document->changes()->orderBy('version')->get();

        return response()->json([
            'document' => $document,
            'changes' => $documentChanges
        ]);
    }


    /**
     * Make a change
     */

     public function addChange(Request $request, $id)
    {
        $user = auth()->user();

        // Check if document exists and belongs to the user
        $document = Document::where('id', $id)
                            ->where('owner_id', $user->id)
                            ->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Validate the request
        $request->validate([
            'operation' => 'required|array', // e.g., { insert: "text", position: 5 }
            'version' => 'required|integer', // client version
        ]);

        // Optional: check version to handle simple OT/concurrency
        $lastVersion = $document->changes()->max('version') ?? 0;
        if ($request->version !== $lastVersion + 1) {
            return response()->json([
                'error' => 'Version mismatch',
                'server_version' => $lastVersion
            ], 409); // Conflict
        }

        // Save change
        $change = DocumentChange::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'operation' => $request->operation,
            'version' => $request->version,
        ]);

        broadcast(new DocumentChanged($document->id, $change))->toOthers();

        return response()->json([
            'message' => 'Change saved',
            'change' => $change
        ], 201);
    }
}