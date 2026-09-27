<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\FarmhouseModel;
use Exception;

class FarmhousesController {

    private $farmhouseModel;
    private $db;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->farmhouseModel = new FarmhouseModel($this->db);
        } catch (Exception $e) {
            die("Application Error: " . $e->getMessage());
        }
    }

    public function farmhouses() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ── Read all filter params ──
        // Core filters (used in SQL)
        $filters = [
            'search'             => trim($_GET['search']   ?? ''),
            'location'           => trim($_GET['location'] ?? ''),
            'category'           => trim($_GET['category'] ?? ''),
            'min_price'          => (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (float)$_GET['min_price'] : null,
            'max_price'          => (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (float)$_GET['max_price'] : null,
            'bedrooms'           => (isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '') ? (int)$_GET['bedrooms'] : null,
            'is_negotiable'      => !empty($_GET['is_negotiable']),
            'allow_room_booking' => !empty($_GET['allow_room_booking']),
            'amenities'          => (isset($_GET['amenities']) && is_array($_GET['amenities'])) ? $_GET['amenities'] : [],
            'sort'               => trim($_GET['sort'] ?? ''),
        ];

        // Date & guest params (from home search bar — used for display only,
        // not in the farmhouse SQL since no availability table tracks exact
        // date availability per night yet; they pass through to the detail page)
        $checkin  = trim($_GET['checkin']  ?? '');
        $checkout = trim($_GET['checkout'] ?? '');
        $guests   = (int)($_GET['guests']  ?? 0);

        // Validate dates: if check-in/out were submitted, filter out
        // farmhouses whose blocked_dates or approved bookings fully overlap
        // with the requested range (only if both dates are valid)
        $occupiedFarmhouseIds = [];
        if ($checkin && $checkout && strtotime($checkin) && strtotime($checkout)
            && strtotime($checkout) > strtotime($checkin)) {
            $occupiedFarmhouseIds = $this->farmhouseModel->getUnavailableFarmhouseIds(
                $checkin, $checkout
            );
        }

        // ── Fetch data ──
        $farmhouses         = $this->farmhouseModel->getFilteredFarmhouses($filters);
        $availableLocations = $this->farmhouseModel->getDistinctLocations();
        $availableAmenities = $this->farmhouseModel->getAmenities();

        // ── Filter out unavailable farmhouses if date range given ──
        if (!empty($occupiedFarmhouseIds)) {
            $farmhouses = array_filter($farmhouses, function($fh) use ($occupiedFarmhouseIds) {
                return !in_array((int)$fh['id'], $occupiedFarmhouseIds);
            });
            $farmhouses = array_values($farmhouses);
        }

        // ── Apply guest capacity filter if guests > 0 ──
        if ($guests > 0) {
            $farmhouses = array_filter($farmhouses, function($fh) use ($guests) {
                // Use day_capacity as the primary capacity check;
                // fall back to night_capacity if day_capacity is null
                $cap = $fh['day_capacity'] ?? $fh['night_capacity'] ?? 0;
                return (int)$cap >= $guests;
            });
            $farmhouses = array_values($farmhouses);
        }

        // ── Apply sort (model returns newest-first by default) ──
        if ($filters['sort'] === 'low-high') {
            usort($farmhouses, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($filters['sort'] === 'high-low') {
            usort($farmhouses, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        // ── Wishlist ──
        $wishlistedIds = [];
        if (isset($_SESSION['user_id'])) {
            $wishlistedIds = $this->farmhouseModel->getUserWishlistIds((int)$_SESSION['user_id']);
        }

        // ── Site settings ──
        $siteSettings = [];
        $result = $this->db->query("SELECT * FROM site_settings LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $siteSettings = $result->fetch_assoc();
        }

                // Pass date/guest params to view so search form restores selected values
        $activeCheckin  = $checkin;
        $activeCheckout = $checkout;
        $activeGuests   = $guests ?: 2;

        include __DIR__ . '/../Views/farmhouses.php';
    }
}