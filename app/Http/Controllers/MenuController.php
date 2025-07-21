<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\FixedMenu;

class MenuController extends Controller
{
    public function index()
    {
        $dayNames = ["Pühapäev", "Esmaspäev", "Teisipäev", "Kolmapäev", "Neljapäev", "Reede", "Lauapäev"];

        $targetDate = $this->getNextOrderableMenuDate();

        if (!$targetDate) {
            return view('menu.index', ['groupedMenu' => [], 'dayNames' => $dayNames]);
        }

        // Get all NÄDALATOIT menu items from target date forward
        $menu = Menu::where('KUUP', '>=', $targetDate)
            ->where('TYYP', 'NÄDALATOIT')
            ->orderBy('KUUP')
            ->get();

        // Collect all dates from the dynamic menu
        $menuDates = $menu->pluck('KUUP')->unique();

        // Get all fixed menu items
        $fixedMenu = FixedMenu::all();

        // Build grouped menu
        $groupedMenu = [];
        foreach ($menuDates as $date) {
            $dateKey = $date instanceof \Illuminate\Support\Carbon ? $date->format('Y-m-d') : $date;

            // Add NÄDALATOIT items for this date
            $groupedMenu[$dateKey]['NÄDALATOIT'] = $menu->where('KUUP', $date)->values();

            // Add fixed menu items by type for this date
            foreach ($fixedMenu as $fixed) {
                $type = $fixed->type;
                $groupedMenu[$dateKey]['MUU'][$type][] = $fixed;
            }
        }

        ksort($groupedMenu);

        return view('menu.menu', compact('groupedMenu', 'dayNames'));
    }

    public function redirectToLogin()
    {
        return redirect()->route('login');
    }

    private function getNextOrderableMenuDate()
    {
        $curDate = now()->format('Y-m-d');
        $currentHour = now()->hour;
        $currentMinute = now()->minute;

        $availableUntilHour = 12;
        $availableUntilMinute = 50;

        $isPastCutoff = ($currentHour > $availableUntilHour) ||
                        ($currentHour == $availableUntilHour && $currentMinute > $availableUntilMinute);

        // Get all future menu dates
        $menuDates = Menu::where('KUUP', '>', $curDate)
            ->distinct()
            ->orderBy('KUUP')
            ->pluck('KUUP')
            ->map(fn($date) => $date instanceof \Illuminate\Support\Carbon ? $date->format('Y-m-d') : $date)
            ->values()
            ->toArray();

        if (empty($menuDates)) {
            return null;
        }

        return $isPastCutoff ? ($menuDates[1] ?? null) : $menuDates[0];
    }
} 