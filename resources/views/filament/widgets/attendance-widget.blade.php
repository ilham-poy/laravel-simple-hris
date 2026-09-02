<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
            <div>
                <h2 class="text-lg font-bold">Presensi Kerja Harian</h2>
                <p class="text-sm text-gray-500">
                    Hari ini: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if ($this->hasAttendedToday())
                <x-filament::badge color="success" icon="heroicon-o-check-badge" size="lg" class="py-2 px-4">
                    Anda Sudah Absen Hari Ini
                </x-filament::badge>
                @else
                {{ $this->hadirAction }}
                @endif

                {{ $this->kendalaAction }}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>