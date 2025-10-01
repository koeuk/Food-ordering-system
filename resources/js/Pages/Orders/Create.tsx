import { FormEventHandler, useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Product, Category } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Plus, Minus, Trash2, ShoppingCart } from 'lucide-react';
import { formatPrice } from '@/lib/utils';

interface Props {
    products: Product[];
    categories: Category[];
}

interface CartItem {
    product: Product;
    quantity: number;
    special_instructions?: string;
}

export default function Create({ products, categories }: Props) {
    const [cart, setCart] = useState<CartItem[]>([]);
    const [selectedCategory, setSelectedCategory] = useState<string>('');
    const [deliveryAddress, setDeliveryAddress] = useState('');
    const [notes, setNotes] = useState('');

    const { post, processing } = useForm();

    const addToCart = (product: Product) => {
        const existingItem = cart.find(item => item.product.id === product.id);
        if (existingItem) {
            updateQuantity(product.id, existingItem.quantity + 1);
        } else {
            setCart([...cart, { product, quantity: 1 }]);
        }
    };

    const updateQuantity = (productId: number, quantity: number) => {
        if (quantity <= 0) {
            removeFromCart(productId);
            return;
        }
        setCart(cart.map(item =>
            item.product.id === productId ? { ...item, quantity } : item
        ));
    };

    const updateInstructions = (productId: number, instructions: string) => {
        setCart(cart.map(item =>
            item.product.id === productId ? { ...item, special_instructions: instructions } : item
        ));
    };

    const removeFromCart = (productId: number) => {
        setCart(cart.filter(item => item.product.id !== productId));
    };

    const calculateSubtotal = () => {
        return cart.reduce((sum, item) => {
            const price = typeof item.product.price === 'string'
                ? parseFloat(item.product.price)
                : item.product.price;
            return sum + (price * item.quantity);
        }, 0);
    };

    const calculateTax = () => {
        return calculateSubtotal() * 0.1; // 10% tax
    };

    const calculateTotal = () => {
        return calculateSubtotal() + calculateTax();
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        const orderData = {
            items: cart.map(item => ({
                product_id: item.product.id,
                quantity: item.quantity,
                special_instructions: item.special_instructions,
            })),
            delivery_address: deliveryAddress,
            notes: notes,
        };

        post('/orders', {
            data: orderData,
        });
    };

    const filteredProducts = selectedCategory
        ? products.filter(p => p.category_id.toString() === selectedCategory)
        : products;

    return (
        <AppLayout>
            <Head title="Create Order" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/orders">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Orders
                    </Button>
                </Link>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Products Selection */}
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Select Products</CardTitle>
                                <CardDescription>Choose items for your order</CardDescription>
                            </CardHeader>
                            <CardContent>
                                {/* Category Filter */}
                                <div className="mb-4">
                                    <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="All Categories" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">All Categories</SelectItem>
                                            {categories.map((category) => (
                                                <SelectItem key={category.id} value={category.id.toString()}>
                                                    {category.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Products Grid */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto">
                                    {filteredProducts.map((product) => {
                                        const isAvailable = product.is_available && product.inventory && product.inventory.quantity > 0;
                                        return (
                                            <Card key={product.id} className={!isAvailable ? 'opacity-50' : ''}>
                                                <CardHeader className="p-4">
                                                    <div className="flex justify-between items-start">
                                                        <div className="flex-1">
                                                            <CardTitle className="text-sm">{product.name}</CardTitle>
                                                            <p className="text-xs text-gray-500 mt-1">
                                                                {product.category?.name}
                                                            </p>
                                                        </div>
                                                        <Badge variant={isAvailable ? "default" : "destructive"} className="text-xs">
                                                            {isAvailable ? 'Available' : 'Out of Stock'}
                                                        </Badge>
                                                    </div>
                                                    <p className="text-sm font-bold text-indigo-600 mt-2">
                                                        {formatPrice(product.price)}
                                                    </p>
                                                </CardHeader>
                                                <CardContent className="p-4 pt-0">
                                                    <Button
                                                        size="sm"
                                                        className="w-full"
                                                        onClick={() => addToCart(product)}
                                                        disabled={!isAvailable}
                                                    >
                                                        <Plus className="w-3 h-3 mr-1" />
                                                        Add to Cart
                                                    </Button>
                                                </CardContent>
                                            </Card>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Cart & Checkout */}
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Your Cart</CardTitle>
                                <CardDescription>{cart.length} items</CardDescription>
                            </CardHeader>
                            <CardContent>
                                {cart.length === 0 ? (
                                    <div className="text-center py-8 text-gray-500">
                                        <ShoppingCart className="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                        <p>Your cart is empty</p>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {cart.map((item) => (
                                            <div key={item.product.id} className="border-b pb-4">
                                                <div className="flex justify-between items-start mb-2">
                                                    <div className="flex-1">
                                                        <p className="font-medium text-sm">{item.product.name}</p>
                                                        <p className="text-xs text-gray-500">
                                                            {formatPrice(item.product.price)} each
                                                        </p>
                                                    </div>
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        onClick={() => removeFromCart(item.product.id)}
                                                    >
                                                        <Trash2 className="w-4 h-4 text-red-500" />
                                                    </Button>
                                                </div>
                                                <div className="flex items-center gap-2 mb-2">
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        onClick={() => updateQuantity(item.product.id, item.quantity - 1)}
                                                    >
                                                        <Minus className="w-3 h-3" />
                                                    </Button>
                                                    <span className="w-8 text-center">{item.quantity}</span>
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        onClick={() => updateQuantity(item.product.id, item.quantity + 1)}
                                                    >
                                                        <Plus className="w-3 h-3" />
                                                    </Button>
                                                </div>
                                                <Input
                                                    placeholder="Special instructions..."
                                                    value={item.special_instructions || ''}
                                                    onChange={(e) => updateInstructions(item.product.id, e.target.value)}
                                                    className="text-sm"
                                                />
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {cart.length > 0 && (
                            <form onSubmit={submit}>
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Order Details</CardTitle>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div>
                                            <label className="text-sm font-medium mb-2 block">
                                                Delivery Address
                                            </label>
                                            <textarea
                                                value={deliveryAddress}
                                                onChange={(e) => setDeliveryAddress(e.target.value)}
                                                rows={2}
                                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                                placeholder="Enter delivery address..."
                                            />
                                        </div>
                                        <div>
                                            <label className="text-sm font-medium mb-2 block">
                                                Order Notes
                                            </label>
                                            <textarea
                                                value={notes}
                                                onChange={(e) => setNotes(e.target.value)}
                                                rows={2}
                                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                                placeholder="Any special requests..."
                                            />
                                        </div>

                                        <div className="border-t pt-4 space-y-2">
                                            <div className="flex justify-between text-sm">
                                                <span>Subtotal:</span>
                                                <span>{formatPrice(calculateSubtotal())}</span>
                                            </div>
                                            <div className="flex justify-between text-sm">
                                                <span>Tax (10%):</span>
                                                <span>{formatPrice(calculateTax())}</span>
                                            </div>
                                            <div className="flex justify-between text-lg font-bold">
                                                <span>Total:</span>
                                                <span className="text-indigo-600">
                                                    {formatPrice(calculateTotal())}
                                                </span>
                                            </div>
                                        </div>

                                        <Button type="submit" className="w-full" disabled={processing}>
                                            <ShoppingCart className="w-4 h-4 mr-2" />
                                            Place Order
                                        </Button>
                                    </CardContent>
                                </Card>
                            </form>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
