<!-- resources/views/admin/add-category.blade.php -->
@extends('layouts.admin') <!-- Assuming you have a common layout -->

@section('content')
<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-4">Add New File Category</h1>

    <form action="{{ route('admin.file-category.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Category Name -->
        <div class="mb-4">
            <label for="name" class="block text-darkblue font-semibold">Category Name</label>
            <input type="text" name="name" id="name" class="w-full border-2 rounded-full p-3" value="{{ old('name') }}" required>
            @error('name') <div class="text-red-500">{{ $message }}</div> @enderror
        </div>

        <!-- Category Icon -->
        <div class="mb-4">
            <label for="image_url" class="block text-darkblue font-semibold">Category Icon</label>
            <input type="file" name="image_url" id="image_url" class="w-full border-2 rounded-full p-3">
            @error('image_url') <div class="text-red-500">{{ $message }}</div> @enderror
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-full">Add Category</button>
        </div>
    </form>

    <a href="{{ route('admin.file-categories.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Categories</a>
</div>
@endsection