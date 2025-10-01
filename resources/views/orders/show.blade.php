@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                    @if($order->status === 'delivered') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-2">CUSTOMER INFORMATION</h3>
            <p class="text-gray-900">{{ $order->customer->name }}</p>
            <p class="text-gray-600">{{ $order->customer->email }}</p>
            <p class="text-gray-600">{{ $order->customer->phone }}</p>
        </div>

        <!-- Delivery Address -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-2">DELIVERY ADDRESS</h3>
            <p class="text-gray-900">{{ $order->delivery_address }}</p>
            @if($order->notes)
                <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Notes:</span> {{ $order->notes }}</p>
            @endif
        </div>

        <!-- Order Items -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-4">ORDER ITEMS</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                            <div class="text-sm text-gray-600">Quantity: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</div>
                            @if($item->special_instructions)
                                <div class="text-sm text-gray-500 italic mt-1">{{ $item->special_instructions }}</div>
                            @endif
                        </div>
                        <div class="font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax (10%)</span>
                    <span>${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t border-gray-300">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        @if($order->bill)
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">PAYMENT STATUS</h3>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->bill->isPaid() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->bill->payment_status) }}
                            </span>
                            @if($order->bill->payment_method)
                                <span class="ml-2 text-gray-600">via {{ ucfirst($order->bill->payment_method) }}</span>
                            @endif
                        </p>
                    </div>
                    @if(!$order->bill->isPaid())
                        <a href="{{ route('bills.show', $order->bill) }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Pay Now
                        </a>
                    @else
                        <a href="{{ route('bills.download', $order->bill) }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Download Receipt
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100">
                    Back to Orders
                </a>
                @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
