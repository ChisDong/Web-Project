<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToCardRequest;
use App\Models\CustomerAddresses;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\ProductVariant;
use App\Http\Controllers\CustomerController;
class OrderController extends Controller
{
    public function addToCart(AddToCardRequest $request)
    {
        $data = $request->validated();

        $order = Order::where('user_id', $data['user_id'])
            ->where('status', 'pending')
            ->first();

        $address_id = CustomerAddresses::where('customer_id', $data['user_id'])->where('is_default', true)->value('address_id');
        if (!$order) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'total_price' => 0,
                'status' => 'pending',
                'address' => $address_id
            ]);
        }
        $productVariant = ProductVariant::where('product_id', $data['product_id'])
            ->where('color_id', $data['color_id'])
            ->where('size_id', $data['size_id'])
            ->first();

        // Ensure variant exists
        if (!$productVariant) {
            return response()->json(['message' => 'Product variant not found for the given product/color/size'], 404);
        }

        $items = OrderItems::where('order_id', $order->id)
            ->where('variant_id', $productVariant->id)
            ->first();

        $unitPrice = ProductVariant::where('id', $productVariant->id)->value('price');

        if ($items) {
            $items->quantity += $data['quantity'];
            $items->save();
        } else {
            OrderItems::create([
                'order_id' => $order->id,
                'variant_id' => $productVariant->id,
                'quantity' => $data['quantity'],
                'price' => $unitPrice,
            ]);
        }
        $order->total_price = OrderItems::where('order_id', $order->id)
            ->selectRaw('SUM(quantity * price) as total')
            ->value('total');
        $order->save();

        return response()->json(['message' => 'Order added to cart successfully'], 201);
    }

    public function recalcOrderTotal($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->total_price = OrderItems::where('order_id', $orderId)
            ->selectRaw('SUM(quantity * price) as total')
            ->value('total');
        Order::where('id', $orderId)->update(['total_price' => $order->total_price]);
    }
    public function handleEmptyCart($orderId)
    {
        $itemsCount = OrderItems::where('order_id', $orderId)->count();
        if ($itemsCount === 0) {
            Order::where('id', $orderId)->delete();
        }
    }

    public function updateCartItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $quantity = (int) $request->input('quantity', 1);
        $item = OrderItems::findOrFail($itemId);

        if($quantity <= 0){
            $orderId = $item->order_id;
            $item->delete();
            $this->recalcOrderTotal($orderId);
            $this->handleEmptyCart($orderId);
            return response()->json(['message' => 'Item removed from cart'], 200);
        }

        $variant = ProductVariant::findOrFail($item->variant_id);
        if($quantity > $variant->stock){
            return response()->json(['message' => 'Requested quantity exceeds available stock'], 400);
        }
        $item->quantity = $quantity;
        $item->save();

        $this->recalcOrderTotal($item->order_id);
        return response()->json(['message' => 'Cart item updated successfully'], 200);

    }

    public function removeCartItem($itemId)
    {
        $item = OrderItems::findOrFail($itemId);
        $orderId = $item->order_id;
        $item->delete();
        $this->recalcOrderTotal($orderId);
        $this->handleEmptyCart($orderId);
        return response()->json(['message' => 'Item removed from cart'], 200);
    }
    // phải thêm cả lấy hình ảnh và thông tin variant, frontend gọi tới các api get by id để lấy
    public function getCartItems($userId)
    {
        $order = Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'No active cart found',
                'data' => []
            ], 200);
        }

        $items = OrderItems::with([
            'productVariant.color',
            'productVariant.size',
        ])
        ->where('order_id', $order->id)
        ->get();

        $data = $items->map(function ($it) {
            $v = $it->productVariant;

            return [
                'id' => $it->id,
                'order_id' => $it->order_id,
                'variant_id' => $it->variant_id,
                'quantity' => $it->quantity,
                'price' => $it->price,

                'product_id' => $v?->product?->id,
                'name' => $v?->product?->name,

                'color_id' => $v?->color?->id,
                'color_name' => $v?->color?->color_name,
                'color_code' => $v?->color?->color_code,
                'main_image' => $v?->color?->main_image,

                'size_id' => $v?->size?->id,
                'size_name' => $v?->size?->size_name,
            ];
        });

        return response()->json([
            'message' => 'Cart items retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function applyAddress($orderId)
    {
        $order = Order::where('id', $orderId)->where('status', 'pending')->firstOrFail();
        $address = CustomerController::getDefaultAddress($order->user_id);
        $order->shipping_address_line = data_get($address, 'address_line', null);
        $order->shipping_ward = data_get($address, 'ward', null);
        $order->shipping_district = data_get($address, 'district', null);
        $order->shipping_city = data_get($address, 'city', null);
        $order->save();
        return response()->json(['message' => 'Address applied to order successfully'], 200);
    }

    public function setOrderPaymentMethod(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|string|max:100',
        ]);

        $order = Order::where('id', $orderId)->where('status', 'pending')->firstOrFail();
        $order->payment_method = $request->input('payment_method');
        $order->save();
        return response()->json(['message' => 'Payment method set for order successfully'], 200);
    }

    public function postAddressOrder(Request $request, $orderId)
    {
        $request->validate([
            'address_line' => 'required|string|max:255',
            'ward' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
        ]);

        $order = Order::where('id', $orderId)->where('status', 'pending')->firstOrFail();
        $order->shipping_address_line = $request->input('address_line') ? $request->input('address_line') : null;
        $order->shipping_ward = $request->input('ward') ? $request->input('ward') : null;
        $order->shipping_district = $request->input('district') ? $request->input('district') : null;
        $order->shipping_city = $request->input('city') ? $request->input('city') : null;
        $order->save();
        return response()->json(['message' => 'Address updated for order successfully'], 200);

    }
    public function checkoutOrder($orderId)
    {
        $order = Order::where('id', $orderId)->where('status', 'pending')->firstOrFail();
        $order->status = 'processing';
        $order->save();
        return response()->json(['message' => 'Order checked out successfully'], 200);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->input('status');
        $order->save();

        return response()->json(['message' => 'Order status updated successfully'], 200);
    }
}
