import { FormEventHandler, useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Category } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ArrowLeft, Save, Upload } from 'lucide-react';

interface Props {
    categories: Category[];
}

export default function Create({ categories }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        category_id: '',
        name: '',
        description: '',
        price: '',
        image: null as File | null,
        is_available: true,
        initial_stock: '0',
        minimum_stock: '5',
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('image', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/manager/products', {
            forceFormData: true,
        });
    };

    return (
        <AppLayout>
            <Head title="Create Product" />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/products">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Products
                    </Button>
                </Link>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-2xl">Create New Product</CardTitle>
                        <CardDescription>Add a new item to your menu</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            {/* Category */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Category *</label>
                                <Select
                                    value={data.category_id}
                                    onValueChange={(value) => setData('category_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {categories.map((category) => (
                                            <SelectItem key={category.id} value={category.id.toString()}>
                                                {category.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.category_id && (
                                    <p className="text-sm text-red-600">{errors.category_id}</p>
                                )}
                            </div>

                            {/* Name */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Product Name *</label>
                                <Input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="e.g., Margherita Pizza"
                                />
                                {errors.name && (
                                    <p className="text-sm text-red-600">{errors.name}</p>
                                )}
                            </div>

                            {/* Description */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Description</label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="Describe your product..."
                                    rows={4}
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                />
                                {errors.description && (
                                    <p className="text-sm text-red-600">{errors.description}</p>
                                )}
                            </div>

                            {/* Price */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Price ($) *</label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={data.price}
                                    onChange={(e) => setData('price', e.target.value)}
                                    placeholder="0.00"
                                />
                                {errors.price && (
                                    <p className="text-sm text-red-600">{errors.price}</p>
                                )}
                            </div>

                            {/* Image */}
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Product Image</label>
                                <div className="space-y-4">
                                    {imagePreview && (
                                        <div className="w-full h-48 rounded-lg overflow-hidden bg-gray-100">
                                            <img
                                                src={imagePreview}
                                                alt="Preview"
                                                className="w-full h-full object-cover"
                                            />
                                        </div>
                                    )}
                                    <div className="flex items-center gap-2">
                                        <Input
                                            type="file"
                                            accept="image/*"
                                            onChange={handleImageChange}
                                            className="flex-1"
                                        />
                                        <Upload className="w-5 h-5 text-gray-400" />
                                    </div>
                                </div>
                                {errors.image && (
                                    <p className="text-sm text-red-600">{errors.image}</p>
                                )}
                            </div>

                            {/* Stock */}
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Initial Stock *</label>
                                    <Input
                                        type="number"
                                        min="0"
                                        value={data.initial_stock}
                                        onChange={(e) => setData('initial_stock', e.target.value)}
                                    />
                                    {errors.initial_stock && (
                                        <p className="text-sm text-red-600">{errors.initial_stock}</p>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Minimum Stock *</label>
                                    <Input
                                        type="number"
                                        min="0"
                                        value={data.minimum_stock}
                                        onChange={(e) => setData('minimum_stock', e.target.value)}
                                    />
                                    {errors.minimum_stock && (
                                        <p className="text-sm text-red-600">{errors.minimum_stock}</p>
                                    )}
                                </div>
                            </div>

                            {/* Available */}
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="is_available"
                                    checked={data.is_available}
                                    onCheckedChange={(checked) => setData('is_available', checked as boolean)}
                                />
                                <label htmlFor="is_available" className="text-sm font-medium cursor-pointer">
                                    Available for purchase
                                </label>
                            </div>

                            {/* Submit */}
                            <div className="flex gap-2 pt-4">
                                <Button type="submit" disabled={processing} className="flex-1">
                                    <Save className="w-4 h-4 mr-2" />
                                    Create Product
                                </Button>
                                <Link href="/products">
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
