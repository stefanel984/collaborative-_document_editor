<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentChange;
use Tymon\JWTAuth\Facades\JWTAuth;

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
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $document = Document::create([
            'title' => $request->title,
            'owner_id' => $user->id,
        ]);

        return response()->json($document, 201);
    }

    /**
     * Get all documents for authenticated user
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $documents = Document::where('owner_id', $user->id)->get();

        return response()->json($documents);
    }

    /**
     * Get a specific document with its changes
     */
    public function show($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $document = Document::with('changes')->findOrFail($id);

        // Check ownership
        if ($document->owner_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($document);
    }

    /**
     * Log a new document change (from WebSocket or API)
     */
    public function addChange(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $document = Document::findOrFail($id);

        if ($document->owner_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'operation' => 'required|array',
            'version' => 'required|integer',
        ]);

        $change = DocumentChange::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'operation' => $request->operation,
            'version' => $request->version,
        ]);

        // TODO: Broadcast via WebSocket here
        // broadcast(new DocumentUpdated($document, $change));

        return response()->json($change, 201);
    }
}