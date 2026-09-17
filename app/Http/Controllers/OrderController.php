<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMessage;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display Open Orders (Pending, Confirmed, Processing, Shipped).
     */
    public function open(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        $activePage = $this->getActiveFacebookPage();
        $isAllPages = session('active_facebook_page_id') === 'all';

        $selectedPage = $request->query('facebook_page_id', session('active_facebook_page_id'));
        $shouldFilterPage = $selectedPage && $selectedPage !== 'all';
        if (!$selectedPage && !app()->runningUnitTests() && $activePage) {
            $selectedPage = $activePage->id;
            $shouldFilterPage = true;
        }

        $query = Order::open()
            ->with(['facebookPage', 'items', 'latestMessage'])
            ->when($shouldFilterPage, fn($q) => $q->where('facebook_page_id', $selectedPage));

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Status counts for tab pills
        $baseQuery = Order::open()->when($shouldFilterPage, fn($q) => $q->where('facebook_page_id', $selectedPage));
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'processing' => (clone $baseQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $baseQuery)->where('status', 'shipped')->count(),
        ];

        return view('orders.open', compact('orders', 'counts', 'status', 'search', 'activePage', 'isAllPages'));
    }

    /**
     * Display Closed Orders (Success, Fail).
     */
    public function closed(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        $activePage = $this->getActiveFacebookPage();
        $isAllPages = session('active_facebook_page_id') === 'all';

        $selectedPage = $request->query('facebook_page_id', session('active_facebook_page_id'));
        $shouldFilterPage = $selectedPage && $selectedPage !== 'all';
        if (!$selectedPage && !app()->runningUnitTests() && $activePage) {
            $selectedPage = $activePage->id;
            $shouldFilterPage = true;
        }

        $query = Order::closed()
            ->with(['facebookPage', 'items', 'latestMessage'])
            ->when($shouldFilterPage, fn($q) => $q->where('facebook_page_id', $selectedPage));

        if ($status === 'success') {
            $query->where('status', 'success');
        } elseif ($status === 'fail') {
            $query->where('status', 'fail');
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $orders = $query->latest('closed_at')->paginate(15)->withQueryString();

        // Closed analytics metrics
        $baseQuery = Order::closed()->when($shouldFilterPage, fn($q) => $q->where('facebook_page_id', $selectedPage));
        $totalClosed = (clone $baseQuery)->count();
        $successCount = (clone $baseQuery)->where('status', 'success')->count();
        $failCount = (clone $baseQuery)->where('status', 'fail')->count();
        $totalRevenue = (clone $baseQuery)->where('status', 'success')->sum('total_amount');
        $successRate = $totalClosed > 0 ? round(($successCount / $totalClosed) * 100, 1) : 0;

        $counts = [
            'all' => $totalClosed,
            'success' => $successCount,
            'fail' => $failCount,
        ];

        return view('orders.closed', compact(
            'orders',
            'counts',
            'status',
            'search',
            'activePage',
            'isAllPages',
            'totalRevenue',
            'successRate',
            'totalClosed',
            'failCount'
        ));
    }

    /**
     * Display a specific order with its Facebook chat/comment history.
     */
    public function show(Order $order): View
    {
        $order->load(['facebookPage', 'items.product', 'messages']);

        return view('orders.show', compact('order'));
    }

    /**
     * Form to record a new order manually from a Facebook chat/comment.
     */
    public function create(): View
    {
        $activePage = $this->getActiveFacebookPage();
        $products = Product::where('status', 'active')
            ->when($activePage, fn($q) => $q->forFacebookPage($activePage->id))
            ->orderBy('name')
            ->get();

        $pages = FacebookPage::all();

        return view('orders.create', compact('products', 'pages', 'activePage'));
    }

    /**
     * Store newly created order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:1000',
            'source' => 'required|in:messenger,comment,livestream,manual',
            'payment_status' => 'required|in:cod,paid,unpaid',
            'shipping_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'initial_message' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $qty = (int) $item['quantity'];
            $lineSubtotal = $product->price * $qty;
            $subtotal += $lineSubtotal;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => $product->price,
                'quantity' => $qty,
                'subtotal' => $lineSubtotal,
            ];
        }

        $shippingFee = (float) ($validated['shipping_fee'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);
        $total = max(0, ($subtotal + $shippingFee) - $discount);

        $order = Order::create([
            'facebook_page_id' => $validated['facebook_page_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'source' => $validated['source'],
            'status' => 'pending',
            'payment_status' => $validated['payment_status'],
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        // Add initial conversation message
        if (!empty($validated['initial_message'])) {
            OrderMessage::create([
                'order_id' => $order->id,
                'sender_type' => 'customer',
                'sender_name' => $order->customer_name,
                'type' => $order->source === 'comment' ? 'comment' : 'messenger',
                'message' => $validated['initial_message'],
            ]);
        }

        // Add system message
        OrderMessage::create([
            'order_id' => $order->id,
            'sender_type' => 'system',
            'sender_name' => 'System',
            'type' => 'system_event',
            'message' => "Order #{$order->order_number} registered from {$order->source}.",
        ]);

        return redirect()->route('orders.show', $order)->with('success', "Order #{$order->order_number} created successfully!");
    }

    /**
     * Update order status with transition workflow.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,success,fail',
            'fail_reason' => 'nullable|string|max:500',
            'status_note' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];

        if (in_array($newStatus, ['success', 'fail'])) {
            $updateData['closed_at'] = Carbon::now();
        } else {
            $updateData['closed_at'] = null;
        }

        if ($newStatus === 'fail') {
            $updateData['fail_reason'] = $validated['fail_reason'] ?? 'Cancelled by customer or unavailable.';
        } elseif ($newStatus === 'success') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        // Record system log message in history
        $note = $validated['status_note'] ?? null;
        $msgText = "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus);
        if ($newStatus === 'fail' && !empty($order->fail_reason)) {
            $msgText .= " (Reason: {$order->fail_reason})";
        }
        if ($note) {
            $msgText .= " - Note: {$note}";
        }

        OrderMessage::create([
            'order_id' => $order->id,
            'sender_type' => 'system',
            'sender_name' => 'System',
            'type' => 'system_event',
            'message' => $msgText,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'status_label' => $order->status_label,
                'badge_classes' => $order->status_badge_classes,
                'is_open' => $order->isOpen(),
                'message' => "Order #{$order->order_number} status updated to " . ucfirst($newStatus),
            ]);
        }

        return back()->with('success', "Order #{$order->order_number} status updated to " . ucfirst($newStatus));
    }

    /**
     * Add a chat message or comment to the order history.
     */
    public function addMessage(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'sender_type' => 'nullable|in:customer,page_agent,system',
            'type' => 'nullable|in:messenger,comment,system_event',
        ]);

        $senderType = $validated['sender_type'] ?? 'page_agent';
        $senderName = match ($senderType) {
            'page_agent' => $order->facebookPage?->name ?? 'Page Admin',
            'system' => 'Internal Note',
            default => $order->customer_name,
        };

        $msg = OrderMessage::create([
            'order_id' => $order->id,
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'type' => $validated['type'] ?? ($order->source === 'comment' ? 'comment' : 'messenger'),
            'message' => $validated['message'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message posted successfully',
                'data' => $msg,
            ]);
        }

        return back()->with('success', 'Message posted to order conversation.');
    }
}
