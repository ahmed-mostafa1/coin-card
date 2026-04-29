<?php

namespace App\Http\Controllers;

use App\Models\VerificationField;
use App\Models\VerificationFile;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountVerificationController extends Controller
{
    public function show(): View
    {
        $fields = VerificationField::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $verificationRequest = auth()->user()->verificationRequests()
            ->with('files.field')
            ->latest()
            ->first();

        return view('account.verification.show', compact('fields', 'verificationRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $fields = VerificationField::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();

        $rules = [];
        foreach ($fields as $field) {
            $key = 'fields.'.$field->name_key;
            $presence = $field->is_required ? 'required' : 'nullable';

            if ($field->type === VerificationField::TYPE_NUMBER) {
                $rules[$key] = [$presence, 'numeric'];
            } elseif ($field->type === VerificationField::TYPE_DATE) {
                $rules[$key] = [$presence, 'date'];
            } elseif ($field->type === VerificationField::TYPE_SELECT) {
                $rules[$key] = [$presence, 'string', 'max:255'];
            } elseif ($field->type === VerificationField::TYPE_FILE) {
                $rules[$key] = [$presence, 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'];
            } elseif ($field->type === VerificationField::TYPE_IMAGE) {
                $rules[$key] = [$presence, 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            } elseif ($field->type === VerificationField::TYPE_CAMERA) {
                $rules[$key] = [$presence, 'string'];
            } else {
                $rules[$key] = [$presence, 'string', 'max:2000'];
            }
        }

        $validated = $request->validate($rules);
        $payload = [];

        DB::transaction(function () use ($request, $fields, $validated, &$payload): void {
            $verificationRequest = VerificationRequest::create([
                'user_id' => $request->user()->id,
                'status' => VerificationRequest::STATUS_PENDING,
                'payload' => [],
            ]);

            foreach ($fields as $field) {
                $value = data_get($validated, 'fields.'.$field->name_key);

                if ($field->isFileType()) {
                    if ($field->type === VerificationField::TYPE_CAMERA && filled($value)) {
                        $this->storeCameraImage($verificationRequest, $field, (string) $value);
                        $payload[$field->name_key] = '[camera_image]';
                    } elseif ($request->hasFile('fields.'.$field->name_key)) {
                        $this->storeUploadedFile($verificationRequest, $field, $request->file('fields.'.$field->name_key));
                        $payload[$field->name_key] = '[file]';
                    }
                    continue;
                }

                $payload[$field->name_key] = $value;
            }

            $verificationRequest->update(['payload' => $payload]);
        });

        return redirect()->route('account.verification.show')->with('status', 'تم إرسال طلب التوثيق بنجاح.');
    }

    private function storeUploadedFile(VerificationRequest $request, VerificationField $field, \Illuminate\Http\UploadedFile $file): void
    {
        $path = $file->store('verification/'.$request->user_id, 'local');
        VerificationFile::create([
            'verification_request_id' => $request->id,
            'verification_field_id' => $field->id,
            'field_key' => $field->name_key,
            'file_path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'file_hash' => hash_file('sha256', $file->getRealPath()),
        ]);
    }

    private function storeCameraImage(VerificationRequest $request, VerificationField $field, string $dataUrl): void
    {
        abort_unless(str_starts_with($dataUrl, 'data:image/'), 422);
        [$meta, $encoded] = explode(',', $dataUrl, 2);
        $binary = base64_decode($encoded, true);
        abort_if($binary === false || strlen($binary) > 5 * 1024 * 1024, 422);

        $extension = str_contains($meta, 'png') ? 'png' : 'jpg';
        $path = 'verification/'.$request->user_id.'/camera-'.Str::uuid().'.'.$extension;
        Storage::disk('local')->put($path, $binary);

        VerificationFile::create([
            'verification_request_id' => $request->id,
            'verification_field_id' => $field->id,
            'field_key' => $field->name_key,
            'file_path' => $path,
            'mime' => $extension === 'png' ? 'image/png' : 'image/jpeg',
            'size' => strlen($binary),
            'file_hash' => hash('sha256', $binary),
        ]);
    }
}
