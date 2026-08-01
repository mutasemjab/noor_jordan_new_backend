<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to the Google Directions API to order a bus trip's stops for the
 * fastest route (native waypoint optimization — solves the per-trip TSP for
 * us, up to 25 waypoints, which comfortably covers typical bus capacities).
 */
class GoogleMapsService
{
    /**
     * @param  array{lat: float, lng: float}  $origin
     * @param  array{lat: float, lng: float}  $destination
     * @param  array<int, array{lat: float, lng: float}>  $waypoints  in original (unordered) order
     * @return array{
     *     waypoint_order: int[],
     *     total_distance_km: float,
     *     total_duration_minutes: int,
     *     etas_seconds: int[],
     *     google_maps_url: string
     * }
     *
     * @throws \RuntimeException if the API key is missing or Google returns a non-OK status
     */
    public static function optimizeRoute(array $origin, array $destination, array $waypoints): array
    {
        $key = env('GOOGLE_MAPS_API_KEY');

        if (! $key) {
            throw new \RuntimeException('لم يتم إعداد مفتاح Google Maps API (GOOGLE_MAPS_API_KEY).');
        }

        if (empty($waypoints)) {
            throw new \RuntimeException('لا يمكن تحسين مسار بدون طلاب.');
        }

        $waypointsParam = 'optimize:true|' . collect($waypoints)
            ->map(fn (array $w) => "{$w['lat']},{$w['lng']}")
            ->implode('|');

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin'      => "{$origin['lat']},{$origin['lng']}",
            'destination' => "{$destination['lat']},{$destination['lng']}",
            'waypoints'   => $waypointsParam,
            'mode'        => 'driving',
            'key'         => $key,
        ]);

        $data = $response->json();

        if (($data['status'] ?? null) !== 'OK') {
            Log::error('Google Directions API error', ['status' => $data['status'] ?? null, 'body' => $data]);
            throw new \RuntimeException('تعذر الحصول على مسار من خرائط جوجل (' . ($data['status'] ?? 'unknown') . ').');
        }

        $route = $data['routes'][0];
        $order = $route['waypoint_order'];
        $legs  = $route['legs'];

        $totalDistanceMeters  = collect($legs)->sum('distance.value');
        $totalDurationSeconds = collect($legs)->sum('duration.value');

        $cumulative = 0;
        $etas       = [];
        foreach ($legs as $leg) {
            $cumulative += $leg['duration']['value'];
            $etas[]      = $cumulative;
        }
        // Last leg is the final destination, not a student stop — drop it.
        array_pop($etas);

        return [
            'waypoint_order'          => $order,
            'total_distance_km'       => round($totalDistanceMeters / 1000, 2),
            'total_duration_minutes'  => (int) ceil($totalDurationSeconds / 60),
            'etas_seconds'            => $etas,
            'google_maps_url'         => self::buildMapsUrl($origin, $destination, $waypoints, $order),
        ];
    }

    /**
     * A shareable "Get Directions" link with stops already in optimized order.
     */
    private static function buildMapsUrl(array $origin, array $destination, array $waypoints, array $order): string
    {
        $orderedWaypoints = collect($order)
            ->map(fn (int $i) => "{$waypoints[$i]['lat']},{$waypoints[$i]['lng']}")
            ->implode('|');

        $params = [
            'api'         => 1,
            'origin'      => "{$origin['lat']},{$origin['lng']}",
            'destination' => "{$destination['lat']},{$destination['lng']}",
            'waypoints'   => $orderedWaypoints,
            'travelmode'  => 'driving',
        ];

        return 'https://www.google.com/maps/dir/?' . http_build_query($params);
    }
}
