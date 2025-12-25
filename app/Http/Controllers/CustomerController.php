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
use App\Models\Notifications;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
class CustomerController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    //update profile
    public function getAllCustomer()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function getUserInfo($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $user_id)
    {
        /** @var User $user */
        // ensure we have a concrete User model instance (not a nullable Authenticatable)
        $user = User::findOrFail($user_id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // assign attributes explicitly
        $user->name = $data['name'] ?? null ? $data['name'] : $user->name;
        $user->phone = $data['phone'] ?? null ? $data['phone'] : $user->phone;
        $user->address = $data['address'] ?? null ? $data['address'] : $user->address;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return response()->json(['message' => 'User information updated successfully']);
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
        $items = OrderItems::with('productVariant')->where('order_id', $order_id)->get();
        $data = $items->map(function ($it) {
            $p = $it->productVariant;
            $c = $p?->color;
            return [
                'id' => $it->order_id,
                'variant_id' => $it->variant_id,
                'quantity' => $it->quantity,
                'price' => $it->price,
                'sku' => $p?->sku,
                'main_image' => $c?->main_image,
            ];
        });

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($data);
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
    // Phần này nhớ cập nhật frontend nhớ cập nhật header 'Content-Type: application/json' khi test API
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
    // Vấn đề phát sinh khi đơn hàng còn tham chiếu đến địa chỉ này thì không cho xoá
    public function deleteAddress($address_id)
    {
        $address = CustomerAddresses::find($address_id);
        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
    // 18. POST /api/addresses/{id}/set-default
    public function setDefaultAddress($user_id, $address_id)
    {
        DB::transaction(function () use ($user_id, $address_id) {

            CustomerAddresses::where('customer_id', $user_id)
            ->where('is_default', 1)
            ->update(['is_default' => 0]);

        // 2) Set default mới
        CustomerAddresses::where('address_id', $address_id)
            ->where('customer_id', $user_id)
            ->update(['is_default' => 1]);
        });
        return response()->json(['message' => 'Default address set successfully'], 200);
    }

    public static function getDefaultAddress($user_id)
    {
        $address = CustomerAddresses::where('customer_id', $user_id)
            ->where('is_default', 1)
            ->first();

        if (!$address) {
            return response()->json(['error' => 'No default address found'], 404);
        }

        return $address;
    }

    //NHÓM 4 — WISHLIST (YÊU THÍCH)
    // 19. GET /api/wishlist
    // 20. POST /api/wishlist/{product_id}
    // 21. DELETE /api/wishlist/{product_id}
    //NHÓM 5 — REVIEWS (ĐÁNH GIÁ)
    // 21. GET /api/reviews/mine
    public function getMyReviews($user_id){
        $reviews = ProductReview::with('products')->where('user_id', $user_id)->get();
        if ($reviews->isEmpty()) {
            return response()->json(['error' => 'No reviews found'], 404);
        }
        $data = $reviews->map(function ($rev) {
            $p = $rev->products;
            return [
                'id' => $rev->id,
                'product_id' => $rev->product_id,
                'rating' => $rev->rating,
                'comment' => $rev->comment,
                'product_name' => $p?->name,
            ];
        });
        return response()->json($data);
    }
    // 22. POST /api/reviews/{product_id} -- 12 đã làm ở trên trong phần review order items
    // 23. PUT /api/reviews/{review_id}
    public function updateReview(ReviewRequest $request, $review_id)
    {
        $review = ProductReview::find($review_id);
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }
        $data = $request->validated();
        $review->rating = $data['rating'] ?? $review->rating;
        $review->comment = $data['comment'] ?? $review->comment;
        $review->save();
        return response()->json(['message' => 'Review updated successfully'], 200);
    }
    // 24. DELETE /api/reviews/{review_id}
    public function deleteReview($review_id)
    {
        $review = ProductReview::find($review_id);
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
    //Soft delete (khuyên dùng cho sản phẩm đã từng xuất hiện trong đơn hàng)
    //NHÓM 6 — NOTIFICATIONS -- lưu ý phần này khi cập nhật đơn hàng đồ phải tạo ra notification tương ứng để user nhận được
    //làm thế nào để người dùng nhận được notification ngay khi có điều cần thông báo giả xử như có khuyến mãi hot
    // 25. GET /api/notifications
    public function getNotifications($user_id)
    {
        $notifications = Notifications::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['error' => 'No notifications found'], 404);
        }
        return response()->json($notifications);
    }
    // 26. POST /api/notifications/read-all
    public function markAllNotificationsAsRead($user_id)
    {
        $notifications = Notifications::where('user_id', $user_id)
            ->where('is_read', 0)
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'All notifications are already read'], 200);
        }

        foreach ($notifications as $notification) {
            $notification->is_read = 1;
            $notification->read_at = now();
            $notification->save();
        }

        return response()->json(['message' => 'All notifications marked as read'], 200);
    }
    // 27. POST /api/notifications/{id}/read
    public function markNotificationAsRead($notification_id)
    {
        $notification = Notifications::find($notification_id);

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        if ($notification->is_read) {
            return response()->json(['message' => 'Notification is already marked as read'], 200);
        }

        $notification->is_read = 1;
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read'], 200);
    }
    // 28. DELETE /api/notifications/clear-read
    public function clearReadNotifications($notification_id)
    {
        $readNotifications = Notifications::find($notification_id);

        if (!$readNotifications) {
            return response()->json(['message' => 'No read notifications to clear'], 200);
        }
        $readNotifications->delete();

        return response()->json(['message' => 'Notification cleared'], 200);
    }
    // 29. DELETE /api/notifications/clear-all
    public function clearAllNotifications($user_id)
    {
        $notifications = Notifications::where('user_id', $user_id)->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications to clear'], 200);
        }

        foreach ($notifications as $notification) {
            $notification->delete();
        }

        return response()->json(['message' => 'All notifications cleared'], 200);
    }

    public function countAccessTokens($user_id){
        $user = PersonalAccessToken::where('tokenable_id', $user_id)->get();
        if($user->isEmpty()){
            return response()->json(['error' => 'User not found'], 404);
        }
        $count = $user->count();
        return response()->json(['access_token_count' => $count], 200);
    }

    public function getAllAccessTokens(){
        $personalAccessTokens = PersonalAccessToken::all();

        return response()->json([
            'status' => 'success',
            'data' => $personalAccessTokens
        ], 200);
    }

    public function countNumberofAccessTokens(){
      $personalAccessTokens = PersonalAccessToken::all();
      $count = $personalAccessTokens->count();
      return response()->json(['access_token_count' => $count], 200);
    }
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
