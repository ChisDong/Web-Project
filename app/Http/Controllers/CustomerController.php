<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAddressRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use App\Models\CustomerAddresses;

class CustomerController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    //update profile
    public function update(Request $request)
    {
        /** @var User $user */
        // ensure we have a concrete User model instance (not a nullable Authenticatable)
        $user = User::findOrFail(Auth::id());

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // assign attributes explicitly
        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;
        $user->save();

        return redirect()->route('profile.edit')->with('message', 'Cập nhật thông tin thành công.');
    }
    //8. GET /api/orders
    public function getOrder($user_id)
    {
       // Lấy tất cả order của user này
        $orders = Order::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['error' => 'No orders found'], 404);
        }

        return response()->json($orders);
    }
    //9. GET /api/orders/{order_id} -- mới lấy chi tiết hoá đơn gồm các thông tin thông thường thôi, chưa lấy các thông tin liên kết
    public function getOrderById($order_id)
    {
        $items = OrderItems::where('order_id', $order_id)->get();

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($items);
    }
    //10.POST /api/orders/cancel/{order_id}
    public function cancelOrder($order_id){
        $order = Order::find($order_id);
        if(!$order){
            return response()->json(['error' => 'Order not found'], 404);
        }
        else{
            $order->status = 'cancelled';
            $order->save();
            return response()->json(['message' => 'Order canceled successfully'], 200);
        }
    }
    //11. GET /api/orders/status/{status}
    public function getOrdersByStatus($user_id, $status)
    {
        $orders = Order::where('user_id', $user_id)->where('status', $status)->get();

        if ($orders->isEmpty()) {
            return response()->json(['error' => 'No orders found with the specified status'], 404);
        }

        return response()->json($orders);
    }
    //12. POST /api/orders/{order_id}/review-reminder
    // return list các order items chưa được review
    public function sendReviewReminder($user_id)
    {
        $orders = Order::where('user_id', $user_id)->get();
        $itemsNeedingReview = [];

        foreach ($orders as $order) {
            $orderItems = OrderItems::where('order_id', $order->id)
                ->where('review', '0')
                ->get();

            foreach ($orderItems as $item) {
                $itemsNeedingReview[] = $item;
            }
        }

        if (empty($itemsNeedingReview)) {
            return response()->json(['message' => 'All items in your orders have already been reviewed'], 200);
        }

        return response()->json($itemsNeedingReview);
    }

    // 13. POST /api/orders/review/{variant_id}
    public function reviewOrderItem(ReviewRequest $request, $variant_id)
    {
        $product = ProductVariant::find($variant_id);
        if (!$product) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }
        $data = $request->validated();
        $productReview = new ProductReview();
        $productReview->product_id = $product->product_id;
        $productReview->user_id = $data['user_id'];
        $productReview->rating = $data['rating'] ?? null;
        $productReview->comment = $data['comment'] ?? null;
        $productReview->save();
        return response()->json(['message' => 'Review submitted successfully'], 200);
    }
    //NHÓM 3 — ADDRESS BOOK (ĐỊA CHỈ GIAO HÀNG)
    // 14. GET /api/addresses
    public function getAddresses($user_id)
    {
        $addresses = CustomerAddresses::where('customer_id', $user_id)->get();
        if ($addresses->isEmpty()) {
            return response()->json(['error' => 'No addresses found'], 404);
        }
        return response()->json($addresses);
    }
    // 15. POST /api/addresses
    public function postAddress(CustomerAddressRequest $request)
    {
        $data = $request->validated();
        $address = new CustomerAddresses();
        $address->customer_id = $data['customer_id'];
        $address->address_line = $data['address_line'] ?? null;
        $address->ward = $data['ward'];
        $address->district = $data['district'];
        $address->city = $data['city'];
        $address->country = $data['country'];
        $address->save();
        return response()->json(['message' => 'Address added successfully'], 200);
    }
    // 16. PUT /api/addresses/{id}
    public function updateAddress(CustomerAddressRequest $request, $address_id)
    {
        $address = CustomerAddresses::find($address_id);
        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }
        $data = $request->validated();
        $address->address_line = $data['address_line'] ?? $address->address_line;
        $address->ward = $data['ward'] ?? $address->ward;
        $address->district = $data['district'] ?? $address->district;
        $address->city = $data['city'] ?? $address->city;
        $address->country = $data['country'] ?? $address->country;
        $address->save();
        return response()->json(['message' => 'Address updated successfully'], 200);
    }

    // 17. DELETE /api/addresses/{id}
    // 18. POST /api/addresses/{id}/set-default
    //NHÓM 4 — WISHLIST (YÊU THÍCH)
    // 19. GET /api/wishlist
    // 20. POST /api/wishlist/{product_id}
    // 21. DELETE /api/wishlist/{product_id}
    //NHÓM 5 — REVIEWS (ĐÁNH GIÁ)
    // 21. GET /api/reviews/mine
    // 22. POST /api/reviews/{product_id}
    // 23. PUT /api/reviews/{review_id}
    // 24. DELETE /api/reviews/{review_id}
    //NHÓM 6 — NOTIFICATIONS
    // 25. GET /api/notifications
    // 26. POST /api/notifications/read-all
    // 27. POST /api/notifications/{id}/read
    //NHÓM 7 — PAYMENT METHODS
    // 28. GET /api/vouchers
    // 29. GET /api/vouchers/available
    // 30. GET /api/vouchers/expired
    // 31. POST /api/vouchers/apply
    //NHÓM 8 — CART (TÙY CHỌN, NẾU ĐỂ TRONG DASHBOARD)
    // 32. GET /api/cart
    // 33. POST /api/cart/add
    // 34. PUT /api/cart/update
    // 35. DELETE /api/cart/remove/{item_id}
    //NHÓM 9 — THỐNG KÊ USER DASHBOARD
    //36. GET /api/user/stats
}