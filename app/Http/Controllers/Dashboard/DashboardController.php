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

            // monthly counts for graphing
            $testMonthly = TestRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $contactMonthly = Contact::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            // ensure every month key exists
            for ($m = 1; $m <= 12; $m++) {
                if (!isset($testMonthly[$m])) {
                    $testMonthly[$m] = 0;
                }
                if (!isset($contactMonthly[$m])) {
                    $contactMonthly[$m] = 0;
                }
            }
            ksort($testMonthly);
            ksort($contactMonthly);

            // replace numeric keys with month names for frontend
            $namedTestMonthly = [];
            foreach ($testMonthly as $num => $count) {
                $name = Carbon::create()->month($num)->format('F');
                $namedTestMonthly[$name] = $count;
            }
            $namedContactMonthly = [];
            foreach ($contactMonthly as $num => $count) {
                $name = Carbon::create()->month($num)->format('F');
                $namedContactMonthly[$name] = $count;
            }
            // use named arrays below

            $pendingOrders = Order::where('order_status', 'pending')->count();

            $data = [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'pending_orders' => $pendingOrders,
                'test_requests' => [
                    'count' => $testRequestsCount,
                    'items' => $testRequests,
                    'monthly_counts' => $namedTestMonthly,
                ],
                'contact_requests' => [
                    'count' => $contactRequestsCount,
                    'items' => $contactRequests,
                    'monthly_counts' => $namedContactMonthly,
                ],
            ];

            return $this->sendResponse($data, 'Dashboard data retrived successfully');
        } catch (\Throwable $e) {
            return $this->sendError('Unable to fetch dashboard data: ' . $e->getMessage());
        }
    }
}
