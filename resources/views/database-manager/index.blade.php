<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Database Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Database Management Tools</h3>
                        <p class="text-gray-600">Manage quote-related database tables with inline editing capabilities.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Core Quote Tables -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-blue-800 mb-3">Core Tables</h4>
                            <div class="space-y-2">
                                <a href="{{ route('database-manager.customers') }}" 
                                   class="block px-3 py-2 bg-white border border-blue-300 rounded text-blue-700 hover:bg-blue-100 transition">
                                    Customers
                                </a>
                            </div>
                        </div>

                        <!-- Product Tables -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-green-800 mb-3">Product Tables</h4>
                            <div class="space-y-2">
                                <a href="{{ route('database-manager.product-classes') }}" 
                                   class="block px-3 py-2 bg-white border border-green-300 rounded text-green-700 hover:bg-green-100 transition">
                                    Product Classes
                                </a>
                                <a href="{{ route('database-manager.products') }}" 
                                   class="block px-3 py-2 bg-white border border-green-300 rounded text-green-700 hover:bg-green-100 transition">
                                    Products
                                </a>
                            </div>
                        </div>


                        <!-- Pricing Tables -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-purple-800 mb-3">Special Pricing</h4>
                            <div class="space-y-2">
                                <a href="{{ route('database-manager.contract-prices') }}" 
                                   class="block px-3 py-2 bg-white border border-purple-300 rounded text-purple-700 hover:bg-purple-100 transition">
                                    Contract Prices
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">⚠️ Access Control</h4>
                        <p class="text-sm text-yellow-700">
                            These database management tools are restricted to users with database manager privileges. 
                            All changes are logged and can affect quote functionality.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>