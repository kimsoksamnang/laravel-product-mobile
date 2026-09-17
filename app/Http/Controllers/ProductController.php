<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products with mobile-first filters.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');
        $selectedStatus = $request->query('stock_status', 'all');
        $search = $request->query('search');
        $sortBy = $request->query('sort', 'newest');

        $query = Product::with('category');

        // Filter by category
        if ($selectedCategory && $selectedCategory !== 'all') {
            if (is_numeric($selectedCategory)) {
                $query->where('category_id', $selectedCategory);
            } else {
                $query->whereHas('category', function ($q) use ($selectedCategory) {
                    $q->where('slug', $selectedCategory);
                });
            }
        }

        // Filter by stock status
        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->stockStatus($selectedStatus);
        }

        // Search
        if (!empty($search)) {
            $query->search($search);
        }

        // Sorting
        match ($sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock_asc' => $query->orderBy('stock_quantity', 'asc'),
            'stock_desc' => $query->orderBy('stock_quantity', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        // Quick statistics for header badges
        $totalProductsCount = Product::count();
        $lowStockCount = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();

        return view('products.index', compact(
            'products',
            'categories',
            'selectedCategory',
            'selectedStatus',
            'search',
            'sortBy',
            'totalProductsCount',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'status' => 'required|in:active,draft,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        if (empty($validated['sku'])) {
            $validated['sku'] = 'SKU-' . strtoupper(Str::random(6));
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $product = Product::create($validated);

        // Record initial stock entry
        if ($product->stock_quantity > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $product->stock_quantity,
                'balance_after' => $product->stock_quantity,
                'reason' => 'Initial Stock on Creation',
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product "' . $product->name . '" created successfully!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'stockMovements' => function ($q) {
            $q->latest()->take(20);
        }]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'status' => 'required|in:active,draft,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // Delete old file if locally stored
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        $oldStock = $product->stock_quantity;
        $newStock = (int)$validated['stock_quantity'];

        $product->update($validated);

        // Record stock movement if quantity changed
        if ($oldStock !== $newStock) {
            $diff = $newStock - $oldStock;
            StockMovement::create([
                'product_id' => $product->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'balance_after' => $newStock,
                'reason' => 'Manual Stock Update via Edit Form',
            ]);
        }

        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product "' . $name . '" deleted successfully.');
    }

    /**
     * AJAX/Quick Stock Adjustment endpoint for touch/mobile drawers.
     */
    public function adjustStock(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'mode' => 'required|in:increment,decrement,set',
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $mode = $request->input('mode');
        $amount = (int)$request->input('amount');
        $reason = $request->input('reason') ?: 'Quick Stock Adjustment';

        $oldStock = $product->stock_quantity;
        $newStock = $oldStock;
        $type = 'adjustment';

        if ($mode === 'increment') {
            $newStock = $oldStock + $amount;
            $type = 'in';
        } elseif ($mode === 'decrement') {
            $newStock = max(0, $oldStock - $amount);
            $type = 'out';
        } elseif ($mode === 'set') {
            $newStock = $amount;
            $type = $newStock >= $oldStock ? 'in' : 'out';
        }

        $product->stock_quantity = $newStock;
        $product->save();

        StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => abs($newStock - $oldStock),
            'balance_after' => $newStock,
            'reason' => $reason,
        ]);

        return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'new_stock' => $product->stock_quantity,
            'stock_status' => $product->stock_status,
            'stock_badge_label' => $product->stock_badge_label,
            'message' => "Stock updated to {$product->stock_quantity} units",
        ]);
    }

    /**
     * Export products to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-export-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'SKU', 'Name', 'Category', 'Price ($)', 'Cost ($)', 'Stock', 'Status', 'Total Value ($)']);

            Product::with('category')->chunk(100, function ($products) use ($handle) {
                foreach ($products as $p) {
                    $totalVal = round($p->stock_quantity * $p->price, 2);
                    fputcsv($handle, [
                        $p->id,
                        $p->sku,
                        $p->name,
                        $p->category?->name ?? 'Uncategorized',
                        $p->price,
                        $p->cost_price ?? 0,
                        $p->stock_quantity,
                        $p->stock_badge_label,
                        $totalVal,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
