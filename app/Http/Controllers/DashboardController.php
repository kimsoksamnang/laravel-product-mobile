<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalStockUnits = Product::sum('stock_quantity');

        // Total Inventory Retail Valuation & Cost Valuation
        $allProducts = Product::select('price', 'cost_price', 'stock_quantity')->get();
        $totalValuation = $allProducts->sum(fn ($p) => $p->price * $p->stock_quantity);
        $totalCostValuation = $allProducts->sum(fn ($p) => ($p->cost_price ?? 0) * $p->stock_quantity);
        $potentialProfit = max(0, $totalValuation - $totalCostValuation);

        // Low stock and out of stock
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        $lowStockProducts = Product::with('category')
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();
        $lowStockCount = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        // Category breakdown
        $categoriesStats = Category::withCount('products')
            ->with(['products' => function ($q) {
                $q->select('id', 'category_id', 'stock_quantity', 'price');
            }])
            ->get()
            ->map(function ($cat) {
                $catValuation = $cat->products->sum(fn ($p) => $p->price * $p->stock_quantity);
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon' => $cat->icon,
                    'color_code' => $cat->color_code,
                    'products_count' => $cat->products_count,
                    'valuation' => $catValuation,
                ];
            });

        // Recent stock logs
        $recentMovements = StockMovement::with('product')->latest()->take(6)->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStockUnits',
            'totalValuation',
            'potentialProfit',
            'outOfStockCount',
            'lowStockCount',
            'lowStockProducts',
            'categoriesStats',
            'recentMovements'
        ));
    }
}
