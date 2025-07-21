<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Menüü</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/4ca46a4711.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#E0D8DE] text-black mb-16 overflow-x-hidden">

<div class="relative my-8 w-full">
    <h1 class="text-4xl font-bold text-center">MENÜÜ</h1>
    @if(!empty($groupedMenu))
        <form action="{{ route('menu.redirect-login') }}" method="post">
            @csrf
            <button class="absolute top-0 right-8 px-4 h-12 rounded-md border border-black bg-gray-400 text-xl" type="submit">Tellima</button>
        </form>
    @endif
</div>

<main class="flex flex-col items-center">
    @if(!empty($groupedMenu))
        @php $dateIndex = 0; @endphp
        @foreach($groupedMenu as $kuup => $types)
            @php
                $weekDay = $dayNames[date('w', strtotime($kuup))];
                $dateLabel = date("d.m", strtotime($kuup));
            @endphp

            <!-- Menu Row -->
            <div class="w-5/6 flex mb-6 shadow-lg rounded overflow-hidden bg-[#E0D8DE]">
                <!-- Left column: Date -->
                <div class="w-1/6 bg-[#E0D8DE] p-4 flex flex-col justify-start text-left">
                    <div class="text-2xl font-bold mb-2">{{ $weekDay }}</div>
                    <div class="text-xl">{{ $dateLabel }}</div>
                </div>

                <!-- Right column: Menu -->
                <div class="w-4/5 p-4">
                    <!-- Supp / Praad -->
                    @if(isset($types['NÄDALATOIT']))
                        <div class="mb-4 shadow-sm rounded bg-[#E0D8DE]">
                            <div class="p-2 font-bold flex justify-between items-center">
                                <span>Supp / Praad</span>
                                <span class="font-bold text-md italic">Hind</span>
                            </div>
                            <div class="p-2">
                                <table class="w-full text-left">
                                    <tbody>
                                        @foreach($types['NÄDALATOIT'] as $food)
                                            <tr class="border-b border-gray-400">
                                                <td class="py-2 pr-4">{{ htmlspecialchars($food['ROANIMI'] ?? $food->ROANIMI) }}</td>
                                                <td class="py-2 text-right">{{ number_format($food['ROAHIND'] ?? $food->ROAHIND, 2, ',', ' ') }} €</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Muu -->
                    @if(isset($types['MUU']))
                        <div class="mb-4 shadow-md rounded bg-[#E0D8DE]">
                            <div class="p-2 font-bold cursor-pointer flex justify-between items-center shadow-lg rounded" onclick="toggleCollapse('muu{{ $dateIndex }}')">
                                <span>Muu</span>
                                <i class="fa fa-chevron-down" id="icon_muu{{ $dateIndex }}"></i>
                            </div>
                            <div id="muu{{ $dateIndex }}" class="p-2 hidden">
                                @php $typeIndex = 0; @endphp
                                @foreach ($types['MUU'] as $type => $items)
                                    <div class="mb-2 shadow-md rounded bg-[#E0D8DE]">
                                        <div class="p-2 font-semibold cursor-pointer flex justify-between items-center" onclick="toggleCollapse('type{{ $dateIndex }}_{{ $typeIndex }}')">
                                            <span>{{ $type }}</span>
                                            <i class="fa fa-chevron-down ml-2" id="icon_type{{ $dateIndex }}_{{ $typeIndex }}"></i>
                                        </div>
                                        <div id="type{{ $dateIndex }}_{{ $typeIndex }}" class="p-2 hidden">
                                            <table class="w-full text-left mb-4">
                                                <tbody>
                                                    @foreach ($items as $food)
                                                        <tr class="border-b border-gray-400">
                                                            <td class="py-2 pr-4">{{ $food->name ?? $food->ROANIMI }}</td>
                                                            <td class="py-2 text-right">{{ number_format($food->price ?? $food->ROAHIND, 2, ',', ' ') }} €</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @php $typeIndex++; @endphp
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @php $dateIndex++; @endphp
        @endforeach
    @else
        <p class="text-xl">Menüü pole saadaval!</p>
    @endif
</main>

<script>
function toggleCollapse(elementId) {
    const element = document.getElementById(elementId);
    const icon = document.getElementById('icon_' + elementId);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        element.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>

</body>
</html> 