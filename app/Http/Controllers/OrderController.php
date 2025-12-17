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

        $address_id = CustomerAddresses::where('user_id', $data['user_id'])->where('is_default', true)->value('address_id');
        if (!$order) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'total_price' => 0,
                'status' => 'pending',
                'address' => $address_id
            ]);
        }

        $items = OrderItems::where('order_id', $order->id)
            ->where('variant_id', $data['variant_id'])
            ->first();

        $unitPrice = ProductVariant::where('id', $data['variant_id'])->value('price');

        if ($items) {
            $items->quantity += $data['quantity'];
            $items->total_price = $items->quantity * $unitPrice;
            $items->save();
        } else {
            OrderItems::create([
                'order_id' => $order->id,
                'variant_id' => $data['variant_id'],
                'quantity' => $data['quantity'],
                'price' => $unitPrice,
            ]);
            $totalPrice = $data['quantity'] * $unitPrice;
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
    // phải thêm cả lấy hình ảnh và thông tin variant
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

        $items = OrderItems::where('order_id', $order->id)->get();

        return response()->json([
            'message' => 'Cart items retrieved successfully',
            'data' => $items
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
}
