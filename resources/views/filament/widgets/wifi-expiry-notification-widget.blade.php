<x-filament-widgets::widget>
    <x-filament::section class="wifi-expiry-widget">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center pulse-animation">
                    <span class="text-blue-600 dark:text-blue-400 text-lg">📶</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">WiFi Network Expiry Alerts</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor jaringan WiFi yang akan expired</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-4">
            <!-- Expired Networks -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-red-200 dark:border-red-700 rounded-xl p-5 transition-all duration-200 hover:border-red-400 dark:hover:border-red-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-red-600 dark:text-red-400 text-sm">❌</span>
                        </div>
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Sudah Expired</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800/50 dark:text-red-100 border border-red-200 dark:border-red-700">
                        {{ count($expired) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expired) > 0)
                        <div
                            class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 border border-red-100 dark:border-red-800/50">
                            <div class="space-y-2">
                                @foreach ($expired->take(3) as $wifi)
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-red-700 dark:text-red-200 truncate">
                                                {{ $wifi->name }}
                                            </p>
                                            <p class="text-xs text-red-600 dark:text-red-300 truncate">
                                                📍 {{ $wifi->location }}
                                            </p>
                                            <p class="text-xs text-red-600 dark:text-red-300">
                                                🏢 {{ $wifi->provider?->name ?? 'No Provider' }}
                                            </p>
                                            <p class="text-xs text-red-600 dark:text-red-300">
                                                📅 {{ $wifi->service_expiry_date?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                @if (count($expired) > 3)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 bg-red-300 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-red-600 dark:text-red-300 italic">... dan
                                            {{ count($expired) - 3 }} lainnya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada WiFi yang expired</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring Today -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-orange-200 dark:border-orange-700 rounded-xl p-5 transition-all duration-200 hover:border-orange-400 dark:hover:border-orange-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-orange-600 dark:text-orange-400 text-sm">⚠️</span>
                        </div>
                        <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200">Expired Hari Ini</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-800/50 dark:text-orange-100 border border-orange-200 dark:border-orange-700">
                        {{ count($expiringToday) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringToday) > 0)
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border border-orange-100 dark:border-orange-800/50">
                            <div class="space-y-2">
                                @foreach ($expiringToday as $wifi)
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 bg-orange-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-orange-700 dark:text-orange-200 truncate">
                                                {{ $wifi->name }}
                                            </p>
                                            <p class="text-xs text-orange-600 dark:text-orange-300 truncate">
                                                📍 {{ $wifi->location }}
                                            </p>
                                            <p class="text-xs text-orange-600 dark:text-orange-300">
                                                🏢 {{ $wifi->provider?->name ?? 'No Provider' }}
                                            </p>
                                            <p class="text-xs text-orange-600 dark:text-orange-300">
                                                💰 Rp {{ number_format($wifi->monthly_cost ?? 0, 0, ',', '.') }}/bulan
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada WiFi expired hari ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring in 7 Days -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-yellow-200 dark:border-yellow-700 rounded-xl p-5 transition-all duration-200 hover:border-yellow-400 dark:hover:border-yellow-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-yellow-600 dark:text-yellow-400 text-sm">⏰</span>
                        </div>
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">7 Hari Lagi</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-800/50 dark:text-yellow-100 border border-yellow-200 dark:border-yellow-700">
                        {{ count($expiringIn7Days) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringIn7Days) > 0)
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-100 dark:border-yellow-800/50">
                            <div class="space-y-2">
                                @foreach ($expiringIn7Days->take(3) as $wifi)
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-yellow-700 dark:text-yellow-200 truncate">
                                                {{ $wifi->name }}
                                            </p>
                                            <p class="text-xs text-yellow-600 dark:text-yellow-300 truncate">
                                                📍 {{ $wifi->location }}
                                            </p>
                                            <p class="text-xs text-yellow-600 dark:text-yellow-300">
                                                🏢 {{ $wifi->provider?->name ?? 'No Provider' }}
                                            </p>
                                            <p class="text-xs text-yellow-600 dark:text-yellow-300">
                                                📅 {{ $wifi->service_expiry_date?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                @if (count($expiringIn7Days) > 3)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 bg-yellow-300 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-300 italic">... dan
                                            {{ count($expiringIn7Days) - 3 }} lainnya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada WiFi expired dalam 7 hari</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring in 30 Days -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-5 transition-all duration-200 hover:border-blue-400 dark:hover:border-blue-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 text-sm">📅</span>
                        </div>
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">30 Hari Lagi</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-800/50 dark:text-blue-100 border border-blue-200 dark:border-blue-700">
                        {{ count($expiringIn30Days) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringIn30Days) > 0)
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-100 dark:border-blue-800/50">
                            <div class="space-y-2">
                                @foreach ($expiringIn30Days->take(3) as $wifi)
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-blue-700 dark:text-blue-200 truncate">
                                                {{ $wifi->name }}
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300 truncate">
                                                📍 {{ $wifi->location }}
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300">
                                                🏢 {{ $wifi->provider?->name ?? 'No Provider' }}
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300">
                                                📅 {{ $wifi->service_expiry_date?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                @if (count($expiringIn30Days) > 3)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 bg-blue-300 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-blue-600 dark:text-blue-300 italic">... dan
                                            {{ count($expiringIn30Days) - 3 }} lainnya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada WiFi expired dalam 30 hari</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
                <a href="{{ route('filament.secret.resources.wifi-networks.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 action-button"
                    target="_blank">
                    <div class="w-5 h-5 mr-3 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                            </path>
                        </svg>
                    </div>
                    Kelola WiFi Networks
                </a>

                <div class="ml-2 test-wifi-notification-wrapper">
                    {{ $this->testWifiNotificationAction }}
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
    <style>
        .wifi-expiry-widget {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 50%, #e0f2fe 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .dark .wifi-expiry-widget {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #374151;
        }

        .card-hover {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }

        .dark .card-hover {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }

        .dark .card-hover:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .wifi-expiry-widget {
                padding: 1rem;
            }
        }

        /* Button hover effects */
        .action-button {
            position: relative;
            overflow: hidden;
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .action-button:hover::before {
            left: 100%;
        }

        /* Test notification button styling */
        .test-wifi-notification-wrapper .fi-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3) !important;
            transition: all 0.2s ease-in-out !important;
        }

        .test-wifi-notification-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 15px -3px rgba(245, 158, 11, 0.4) !important;
        }

        .test-wifi-notification-wrapper .fi-btn .fi-btn-label {
            color: white !important;
            font-weight: 600 !important;
        }

        .test-wifi-notification-wrapper .fi-btn .fi-icon {
            color: white !important;
        }

        /* Additional styling for better contrast */
        .test-wifi-notification-wrapper .fi-btn {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</x-filament-widgets::widget>