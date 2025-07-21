<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\FixedMenu;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuService
{
    protected $availableUntilHour = 12;
    protected $availableUntilMinute = 50;

    /**
     * Get the next orderable menu date
     */
    public function getNextOrderableMenuDate(?string $testDate = null, ?string $testTime = null): ?string
    {
        $curDate = $testDate ?? now()->format('Y-m-d');
        $currentTime = $testTime ? Carbon::parse($testTime) : now();
        
        // Determine if cutoff time has passed
        $isPastCutoff = $currentTime->hour > $this->availableUntilHour || 
                       ($currentTime->hour == $this->availableUntilHour && $currentTime->minute > $this->availableUntilMinute);

        // Get distinct future dates from menu
        $menuDates = Menu::where('KUUP', '>', $curDate)
                        ->distinct()
                        ->orderBy('KUUP')
                        ->pluck('KUUP')
                        ->toArray();

        if (empty($menuDates)) {
            return null;
        }

        return $isPastCutoff ? ($menuDates[1] ?? null) : $menuDates[0];
    }

    /**
     * Get menu items including fixed menu items
     */
    public function getMenu(?string $testDate = null, ?string $testTime = null): array
    {
        $targetDate = $this->getNextOrderableMenuDate($testDate, $testTime);

        if (is_null($targetDate)) {
            return [];
        }

        // Get all menu items from target date (not just NÄDALATOIT)
        $menu = Menu::where('KUUP', '>=', $targetDate)
                    ->orderBy('KUUP')
                    ->get()
                    ->toArray();

        // Collect all dates from the dynamic menu
        $menuDates = collect($menu)->pluck('KUUP')->unique()->toArray();

        // Get fixed menu items
        $fixedMenu = FixedMenu::all();

        // Append fixed items to each date
        foreach ($menuDates as $date) {
            foreach ($fixedMenu as $fixedItem) {
                $menu[] = [
                    'M' => $fixedItem->id,
                    'ID' => $fixedItem->id,
                    'ROANIMI' => $fixedItem->name,
                    'ROAHIND' => $fixedItem->price,
                    'TYYP' => $fixedItem->type,
                    'KUUP' => $date,
                ];
            }
        }

        return $menu;
    }

    /**
     * Get user orders for the next day
     */
    public function getUserOrdersForNextDay(string $tabne): array
    {
        $targetDate = $this->getNextOrderableMenuDate();

        if (!$targetDate) {
            return [];
        }

        return Order::where('TABN', $tabne)
                   ->where('KUUP', $targetDate)
                   ->get()
                   ->toArray();
    }

    /**
     * Debug helper function
     */
    public function dd($data): void
    {
        dd($data); // Laravel's built-in dd() function
    }
} 