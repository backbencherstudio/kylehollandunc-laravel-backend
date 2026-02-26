<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contact\Contact;
use App\Models\Order\Order;
use App\Models\Request\Request as TestRequest;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $totalUsers = User::count();

            $activeUsers = User::where('updated_at', '>=', Carbon::now()->subDays(30))->count();

            $testRequests = TestRequest::all();
            $testRequestsCount = $testRequests->count();

            $contactRequests = Contact::all();
            $contactRequestsCount = $contactRequests->count();

            $pendingOrders = Order::where('order_status', 'pending')->count();

            $data = [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'pending_orders' => $pendingOrders,
                'test_requests' => [
                    'count' => $testRequestsCount,
                    'items' => $testRequests,
                ],
                'contact_requests' => [
                    'count' => $contactRequestsCount,
                    'items' => $contactRequests,
                ],
            ];

            return $this->sendResponse($data);
        } catch (\Throwable $e) {
            return $this->sendError('Unable to fetch dashboard data: ' . $e->getMessage());
        }
    }
}
