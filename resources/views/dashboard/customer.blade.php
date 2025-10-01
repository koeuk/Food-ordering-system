@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600">Here's an overview of your orders</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-2">Total Orders</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-2">Pending</div>
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-2">Completed</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['completed_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-2">Total Spent</div>
            <div class="text-3xl font-bold text-indigo-600">${{ number_format($stats['total_spent'], 2) }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <a href="{{ route('orders.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Place New Order
        </a>
        <a href="{{ route('products.index') }}" class="ml-4 inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
            Browse Menu
        </a>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentOrders as $order)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-gray-900">Order #{{ $order->order_number }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900">${{ number_format($order->total, 2) }}</div>
                            <div class="mt-2 space-x-2">
                                <a href="{{ route('orders.show', $order) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View</a>
                                @if($order->bill && !$order->bill->isPaid())
                                    <a href="{{ route('bills.show', $order->bill) }}" class="text-sm text-green-600 hover:text-green-900">Pay</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    No orders yet. <a href="{{ route('orders.create') }}" class="text-indigo-600 hover:text-indigo-900">Place your first order!</a>
                </div>
            @endforelse
        </div>
        @if($recentOrders->count() > 0)
            <div class="px-6 py-4 border-t border-gray-200 text-center">
                <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-900">View All Orders →</a>
            </div>
        @endif
    </div>
</div>
@endsection
