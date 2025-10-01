import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Product, Category, PaginatedData } from '@/types';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { ShoppingCart, Search, Plus } from 'lucide-react';

interface Props {
    products: PaginatedData<Product>;
    categories: Category[];
    filters: {
        search?: string;
        category_id?: string;
        available?: string;
    };
    auth?: {
        user?: any;
    };
}

export default function Index({ products, categories, filters, auth }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');

    const handleFilter = () => {
        router.get('/products', {
            search: search || undefined,
            category_id: categoryId || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AppLayout>
            <Head title="Menu" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="mb-6 flex justify-between items-center">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Our Menu</h1>
                        <p className="text-gray-600">Browse our delicious selection</p>
                    </div>
                    {auth?.user?.role === 'manager' && (
                        <Link href="/manager/products/create">
                            <Button>
                                <Plus className="w-4 h-4 mr-2" />
                                Add Product
                            </Button>
                        </Link>
                    )}
                </div>

                {/* Search and Filters */}
                <Card className="mb-6">
                    <CardContent className="pt-6">
                        <div className="flex flex-col sm:flex-row gap-4">
                            <div className="flex-1 relative">
                                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                                <Input
                                    type="text"
                                    placeholder="Search products..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                                    className="pl-10"
                                />
                            </div>
                            <Select value={categoryId} onValueChange={setCategoryId}>
                                <SelectTrigger className="w-full sm:w-[200px]">
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
                            <Button onClick={handleFilter}>
                                Filter
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Products Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    {products.data.map((product) => (
                        <Card key={product.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                            <div className="aspect-video bg-gray-200 relative">
                                {product.image ? (
                                    <img
                                        src={`/storage/${product.image}`}
                                        alt={product.name}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center">
                                        <ShoppingCart className="w-16 h-16 text-gray-400" />
                                    </div>
                                )}
                            </div>

                            <CardHeader>
                                <div className="flex justify-between items-start">
                                    <CardTitle className="text-lg">{product.name}</CardTitle>
                                    <span className="text-lg font-bold text-indigo-600">
                                        ${typeof product.price === 'number' ? product.price.toFixed(2) : product.price}
                                    </span>
                                </div>
                                <CardDescription className="line-clamp-2">
                                    {product.description}
                                </CardDescription>
                            </CardHeader>

                            <CardContent>
                                <div className="flex justify-between items-center">
                                    <Badge variant={product.is_available ? "default" : "destructive"}>
                                        {product.is_available ? 'Available' : 'Out of Stock'}
                                    </Badge>
                                    {product.inventory && (
                                        <span className="text-xs text-gray-500">
                                            Stock: {product.inventory.quantity}
                                        </span>
                                    )}
                                </div>
                            </CardContent>

                            <CardFooter className="gap-2">
                                <Link href={`/products/${product.id}`} className="flex-1">
                                    <Button variant="outline" className="w-full">
                                        View
                                    </Button>
                                </Link>
                                {product.is_available && product.inventory && product.inventory.quantity > 0 && (
                                    <Button className="flex-1">
                                        <ShoppingCart className="w-4 h-4 mr-2" />
                                        Add
                                    </Button>
                                )}
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {/* Pagination */}
                {products.data.length === 0 && (
                    <div className="text-center py-12 text-gray-500">
                        No products found.
                    </div>
                )}

                {products.meta && products.meta.last_page > 1 && (
                    <div className="mt-8 flex justify-center gap-2">
                        {products.links?.prev && (
                            <Link href={products.links.prev}>
                                <Button variant="outline">Previous</Button>
                            </Link>
                        )}
                        <span className="flex items-center px-4 text-sm text-gray-700">
                            Page {products.meta.current_page} of {products.meta.last_page}
                        </span>
                        {products.links?.next && (
                            <Link href={products.links.next}>
                                <Button variant="outline">Next</Button>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
