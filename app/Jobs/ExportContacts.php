<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Export;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExportContacts implements ShouldQueue
{
    use Queueable;

    public function __construct(public Export $export) {}

    public function handle(): void
    {
        $this->export->update(['status' => 'processing']);

        $fileName = "contacts-{$this->export->id}-" . now()->format('Y-m-d-His') . '.csv';
        $filePath = "exports/{$fileName}";

        $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Job Title', 'Company', 'Owner', 'Created At'];

        $csv = implode(',', $headers) . "\n";

        Contact::with(['company', 'owner'])
            ->where('owner_id', $this->export->user_id)
            ->orderBy('first_name')
            ->chunk(500, function ($contacts) use (&$csv) {
                foreach ($contacts as $contact) {
                    $csv .= implode(',', [
                        $contact->id,
                        $this->escape($contact->first_name),
                        $this->escape($contact->last_name),
                        $this->escape($contact->email ?? ''),
                        $this->escape($contact->phone ?? ''),
                        $this->escape($contact->job_title ?? ''),
                        $this->escape($contact->company?->name ?? ''),
                        $this->escape($contact->owner?->name ?? ''),
                        $contact->created_at->toDateTimeString(),
                    ]) . "\n";
                }
            });

        Storage::put($filePath, $csv);

        $this->export->update([
            'status'    => 'done',
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

    }

    public function failed(Throwable $e): void
    {
        $this->export->update([
            'status' => 'failed',
            'error'  => $e->getMessage(),
        ]);
    }

    private function escape(string $value): string
    {
        $value = str_replace('"', '""', $value);

        return str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")
            ? '"' . $value . '"'
            : $value;
    }
}
