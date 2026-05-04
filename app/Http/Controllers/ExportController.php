<?php

namespace App\Http\Controllers;

use App\Jobs\ExportContacts;
use App\Models\Export;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function contacts(Request $request): JsonResponse
    {
        $export = Export::create([
            'user_id' => $request->user()->id,
            'type'    => 'contacts',
            'status'  => 'pending',
        ]);

        ExportContacts::dispatch($export);

        return response()->json(['id' => $export->id]);
    }

    public function download(Request $request, Export $export): StreamedResponse|RedirectResponse
    {
        if ($export->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $export->isDone()) {
            return back()->with('error', 'Export is not ready yet.');
        }

        $path = $export->file_path;

        return response()->streamDownload(function () use ($path, $export) {
            echo Storage::get($path);
            $export->delete();
            Storage::delete($path);
        }, $export->file_name, ['Content-Type' => 'text/csv']);
    }

    public function status(Request $request, Export $export): \Illuminate\Http\JsonResponse
    {
        if ($export->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'status'       => $export->status,
            'download_url' => $export->isDone()
                ? route('exports.download', $export)
                : null,
        ]);
    }
}
