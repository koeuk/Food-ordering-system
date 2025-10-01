import { PropsWithChildren, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { User } from '@/types';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Menu, X, ShoppingCart, Package, BarChart3, ChefHat, Home } from 'lucide-react';

interface Props extends PropsWithChildren {
    user?: User;
}

export default function AppLayout({ children }: Props) {
    const { auth, flash } = usePage<any>().props;
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const user = auth?.user;

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Navigation */}
            <nav className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="flex-shrink-0 flex items-center">
                                <Link href="/dashboard" className="text-xl font-bold text-gray-900">
                                    Food Ordering System
                                </Link>
                            </div>

                            <div className="hidden sm:ml-8 sm:flex sm:space-x-8">
                                <Link
                                    href="/products"
                                    className="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                >
                                    <ShoppingCart className="w-4 h-4 mr-2" />
                                    Menu
                                </Link>

                                {user && (
                                    <>
                                        <Link
                                            href="/orders"
                                            className="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                        >
                                            My Orders
                                        </Link>

                                        {user.role === 'manager' && (
                                            <>
                                                <Link
                                                    href="/manager/inventory"
                                                    className="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                                >
                                                    <Package className="w-4 h-4 mr-2" />
                                                    Inventory
                                                </Link>
                                                <Link
                                                    href="/manager/reports/sales"
                                                    className="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                                >
                                                    <BarChart3 className="w-4 h-4 mr-2" />
                                                    Reports
                                                </Link>
                                            </>
                                        )}

                                        {user.role === 'kitchen' && (
                                            <Link
                                                href="/kitchen/orders"
                                                className="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                            >
                                                <ChefHat className="w-4 h-4 mr-2" />
                                                Kitchen Orders
                                            </Link>
                                        )}
                                    </>
                                )}
                            </div>
                        </div>

                        <div className="hidden sm:ml-6 sm:flex sm:items-center">
                            {user ? (
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" className="flex items-center">
                                            <span>{user.name}</span>
                                            <span className="ml-2 text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">
                                                {user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                            </span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>My Account</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem asChild>
                                            <Link href="/dashboard">
                                                <Home className="w-4 h-4 mr-2" />
                                                Dashboard
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem asChild>
                                            <Link href="/profile">Profile</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem asChild>
                                            <Link href="/logout" method="post" as="button" className="w-full">
                                                Logout
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            ) : (
                                <div className="flex items-center space-x-4">
                                    <Link href="/login">
                                        <Button variant="ghost">Login</Button>
                                    </Link>
                                    <Link href="/register">
                                        <Button>Register</Button>
                                    </Link>
                                </div>
                            )}
                        </div>

                        {/* Mobile menu button */}
                        <div className="flex items-center sm:hidden">
                            <Button
                                variant="ghost"
                                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            >
                                {mobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                            </Button>
                        </div>
                    </div>
                </div>

                {/* Mobile menu */}
                {mobileMenuOpen && (
                    <div className="sm:hidden">
                        <div className="pt-2 pb-3 space-y-1">
                            <Link
                                href="/products"
                                className="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300"
                            >
                                Menu
                            </Link>
                            {user && (
                                <Link
                                    href="/orders"
                                    className="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300"
                                >
                                    My Orders
                                </Link>
                            )}
                        </div>
                    </div>
                )}
            </nav>

            {/* Flash Messages */}
            {flash?.success && (
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <Alert className="bg-green-50 border-green-200">
                        <AlertDescription className="text-green-800">
                            {flash.success}
                        </AlertDescription>
                    </Alert>
                </div>
            )}

            {flash?.error && (
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <Alert variant="destructive">
                        <AlertDescription>{flash.error}</AlertDescription>
                    </Alert>
                </div>
            )}

            {/* Page Content */}
            <main className="py-6">
                {children}
            </main>

            {/* Footer */}
            <footer className="bg-white mt-12 py-6 border-t border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
                    <p>&copy; {new Date().getFullYear()} Food Ordering System. All rights reserved.</p>
                </div>
            </footer>
        </div>
    );
}
