@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Our Menu</h1>
            <p class="text-gray-600">Browse our delicious selection</p>
        </div>
        @auth
            @if(auth()->user()->isManager())
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Add Product
                </a>
            @endif
        @endauth
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <form method="GET" action="{{ route('products.index') }}" class="flex gap-4">
            <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Categories</option>
                @foreach(\App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                        <span class="text-lg font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->description, 80) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs {{ $product->is_available ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->is_available ? 'Available' : 'Out of Stock' }}
                        </span>
                        @if($product->inventory)
                            <span class="text-xs text-gray-500">Stock: {{ $product->inventory->quantity }}</span>
                        @endif
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('products.show', $product) }}" class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                            View
                        </a>
                        @if($product->is_available && $product->isInStock())
                            <button class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Add to Cart
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                No products found.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
