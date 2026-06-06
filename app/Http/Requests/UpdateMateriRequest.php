<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\MateriUpdateData;
use App\Enums\MateriType;
use App\Models\FileMateri;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;

final class UpdateMateriRequest extends FormRequest
{
    private const MAX_FILE_SIZE_KB = 10240;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                Rule::enum(MateriType::class),
            ],
            'file' => [
                Rule::when(
                    condition: $this->isYoutubeType(),
                    rules: ['prohibited'],
                    defaultRules: $this->fileRules(),
                ),
            ],
            'youtube_url' => [
                Rule::when(
                    condition: $this->isYoutubeType(),
                    rules: [
                        'required',
                        'url',
                        'max:255',
                    ],
                    defaultRules: [
                        'nullable',
                        'prohibited',
                    ],
                ),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->sometimes(
            attribute: 'file',
            rules: ['mimetypes:application/pdf'],
            callback: static fn (Fluent $input): bool => $input->get('type') === MateriType::Pdf->value,
        );

        $validator->sometimes(
            attribute: 'file',
            rules: ['mimetypes:image/jpeg,image/png'],
            callback: static fn (Fluent $input): bool => $input->get('type') === MateriType::Image->value,
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul soal wajib diisi.',
            'title.string' => 'Judul soal harus berupa teks.',
            'title.max' => 'Judul soal maksimal 255 karakter.',

            'description.required' => 'Deskripsi soal wajib diisi.',
            'description.string' => 'Deskripsi soal harus berupa teks.',

            'type.required' => 'Tipe soal wajib dipilih.',
            'type.Illuminate\Validation\Rules\Enum' => 'Tipe soal harus berupa pdf, image, atau youtube.',

            'file.required' => 'File wajib diunggah saat mengganti tipe materi ke PDF atau Image.',
            'file.file' => 'Upload harus berupa file yang valid.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'file.mimetypes' => 'File harus berupa PDF, JPG/JPEG, atau PNG sesuai tipe soal.',
            'file.prohibited' => 'File tidak boleh dikirim untuk tipe Youtube.',

            'youtube_url.required' => 'URL Youtube wajib diisi untuk tipe Youtube.',
            'youtube_url.url' => 'URL Youtube harus berupa URL yang valid.',
            'youtube_url.max' => 'URL Youtube maksimal 255 karakter.',
            'youtube_url.prohibited' => 'URL Youtube tidak boleh dikirim untuk tipe PDF atau Image.',
        ];
    }

    public function toUpdateData(): MateriUpdateData
    {
        return new MateriUpdateData(
            title: (string) $this->validated('title'),
            description: (string) $this->validated('description'),
            type: MateriType::from((string) $this->validated('type')),
            file: $this->uploadedMateriFile(),
            youtubeUrl: $this->validated('youtube_url'),
        );
    }

    /**
     * @return list<string>
     */
    private function fileRules(): array
    {
        return $this->mustUploadFile()
            ? ['required', 'file', 'max:' . self::MAX_FILE_SIZE_KB]
            : ['nullable', 'file', 'max:' . self::MAX_FILE_SIZE_KB];
    }

    private function mustUploadFile(): bool
    {
        $materi = $this->route('fileMateri');

        if (! $this->isFileBasedType()) {
            return false;
        }

        if (! $materi instanceof FileMateri) {
            return true;
        }

        return $materi->type->value !== $this->input('type') || $materi->file_path === null;
    }

    private function isFileBasedType(): bool
    {
        return in_array(
            needle: $this->input('type'),
            haystack: [
                MateriType::Pdf->value,
                MateriType::Image->value,
            ],
            strict: true,
        );
    }

    private function isYoutubeType(): bool
    {
        return $this->input('type') === MateriType::Youtube->value;
    }

    private function uploadedMateriFile(): ?UploadedFile
    {
        $file = $this->file('file');

        return $file instanceof UploadedFile ? $file : null;
    }
}
