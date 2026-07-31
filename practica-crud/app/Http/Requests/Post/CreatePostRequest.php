<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
        'contenido' => 'required|string',
        'autor' => 'required|string',
        'imagen_noticia' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        'id_categoria' => 'required|exists:categorias,id_categoria',
        'status' => 'required|in:publicado,borrador',
        'es_principal' => 'nullable|boolean',
        ];
    }
}
