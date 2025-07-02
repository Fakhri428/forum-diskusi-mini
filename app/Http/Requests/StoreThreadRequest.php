<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agree_terms' => 'accepted'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul thread harus diisi.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'body.required' => 'Isi thread harus diisi.',
            'body.min' => 'Isi thread minimal 10 karakter.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'tags.max' => 'Tag tidak boleh lebih dari 500 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, JPEG, atau GIF.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'agree_terms.accepted' => 'Anda harus menyetujui aturan komunitas.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up tags
        if ($this->tags) {
            $tags = array_map('trim', explode(',', $this->tags));
            $tags = array_filter($tags); // Remove empty tags
            $tags = array_unique($tags); // Remove duplicates
            $tags = array_slice($tags, 0, 5); // Limit to 5 tags
            $this->merge([
                'tags' => implode(',', $tags)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation for tags count
            if ($this->tags) {
                $tags = array_filter(array_map('trim', explode(',', $this->tags)));
                if (count($tags) > 5) {
                    $validator->errors()->add('tags', 'Maksimal 5 tag diperbolehkan.');
                }
            }
        });
    }
}
