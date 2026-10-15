<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class DashboardController extends ApiController
{
    /** @var list<string> */
    private const SALES_CHART_RANGES = ['hour', 'day', 'month', 'year'];

    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $today = now()->toDateString();
        $user = $this->user($request);
        $isOwner = $user->isSuperAdmin() || $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $business->id)->exists();

        $todaySales = $business->sales()->whereDate('transaction_date', $today)->where('status', 'completed');
        if (! $isOwner) {
            $todaySales->where('user_id', $user->id);
        }
        $totalSales = (float) $todaySales->sum('total');
        $transactions = $todaySales->count();
        $products = $business->products()->where('is_active', true)->count();
        $lowStock = Inventory::query()
            ->whereHas('product', fn ($query) => $query->where('business_id', $business->id)->where('is_active', true))
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->when(! $isOwner, function ($query) use ($business): void {
                $query->where('business_id', $business->id);
            })
            ->count();

        return $this->success([
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'kpi' => [
                'today_sales' => $totalSales,
                'today_transactions' => $transactions,
                'active_products' => $products,
                'low_stock_items' => $lowStock,
            ],
            'recent_sales' => Sale::query()
                ->where('business_id', $business->id)
                ->when(! $isOwner, fn ($query) => $query->where('user_id', $user->id))
                ->with(['customer', 'outlet'])
                ->latest('transaction_date')
                ->limit(10)
                ->get()
                ->map(fn (Sale $sale): array => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'total' => $sale->total,
                    'status' => $sale->status,
                    'transaction_date' => $sale->transaction_date,
                    'customer' => $sale->customer?->name,
                    'outlet' => $sale->business?->locationName(),
                ]),
        ], 'Dashboard retrieved successfully');
    }

    public function salesChart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'range' => ['required', Rule::in(self::SALES_CHART_RANGES)],
        ]);
        $business = $this->business($request);
        $user = $this->user($request);
        $isOwner = $user->isSuperAdmin() || $user->roles()
            ->where('roles.name', 'owner')
            ->wherePivot('business_id', $business->id)
            ->exists();

        abort_unless($isOwner, 403, 'Grafik omzet hanya tersedia untuk Owner.');

        return $this->success($this->buildSalesChart($business->id, $validated['range']), 'Sales chart retrieved successfully');
    }

    /** @return array{range: string, labels: list<string>, values: list<float>, total: float} */
    private function buildSalesChart(int $businessId, string $range): array
    {
        $now = now();
        $start = match ($range) {
            'hour' => $now->copy()->startOfHour()->subHours(23),
            'day' => $now->copy()->startOfDay()->subDays(6),
            'month' => $now->copy()->startOfMonth()->subMonths(11),
            'year' => $now->copy()->startOfYear()->subYears(4),
        };
        $bucketCount = match ($range) {
            'hour' => 24,
            'day' => 7,
            'month' => 12,
            'year' => 5,
        };

        $buckets = [];
        for ($index = 0; $index < $bucketCount; $index++) {
            $period = match ($range) {
                'hour' => $start->copy()->addHours($index),
                'day' => $start->copy()->addDays($index),
                'month' => $start->copy()->addMonths($index),
                'year' => $start->copy()->addYears($index),
            };
            $key = match ($range) {
                'hour' => $period->format('Y-m-d H'),
                'day' => $period->format('Y-m-d'),
                'month' => $period->format('Y-m'),
                'year' => $period->format('Y'),
            };
            $label = match ($range) {
                'hour' => $period->format('H:i'),
                'day' => $period->format('d M'),
                'month' => $period->format('M y'),
                'year' => $period->format('Y'),
            };
            $buckets[$key] = ['label' => $label, 'value' => 0.0];
        }

        $sales = Sale::query()
            ->where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$start, $now]);

        if ($range === 'hour') {
            foreach ($sales->get(['transaction_date', 'total']) as $sale) {
                $key = $sale->transaction_date->format('Y-m-d H');
                if (array_key_exists($key, $buckets)) {
                    $buckets[$key]['value'] += (float) $sale->total;
                }
            }
        } else {
            $dailySales = $sales
                ->selectRaw('DATE(transaction_date) as sales_day, SUM(total) as sales_total')
                ->groupBy('sales_day')
                ->orderBy('sales_day')
                ->get();

            foreach ($dailySales as $dailySale) {
                $date = Carbon::parse($dailySale->sales_day);
                $key = match ($range) {
                    'day' => $date->format('Y-m-d'),
                    'month' => $date->format('Y-m'),
                    'year' => $date->format('Y'),
                };
                if (array_key_exists($key, $buckets)) {
                    $buckets[$key]['value'] += (float) $dailySale->sales_total;
                }
            }
        }

        $values = array_values(array_map(static fn (array $bucket): float => $bucket['value'], $buckets));

        return [
            'range' => $range,
            'labels' => array_values(array_map(static fn (array $bucket): string => $bucket['label'], $buckets)),
            'values' => $values,
            'total' => array_sum($values),
        ];
    }
}
