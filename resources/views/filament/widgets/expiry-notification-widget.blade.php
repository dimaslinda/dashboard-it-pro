<x-filament-widgets::widget>
    <x-filament::section class="expiry-widget">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center pulse-animation">
                    <span class="text-red-600 dark:text-red-400 text-lg">🚨</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Domain & Hosting Expiry Alerts</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor domain dan hosting yang akan expired</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-4">
            <!-- Expiring Today -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-red-200 dark:border-red-700 rounded-xl p-5 transition-all duration-200 hover:border-red-400 dark:hover:border-red-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-red-600 dark:text-red-400 text-sm">⚠️</span>
                        </div>
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Expired Hari Ini</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800/50 dark:text-red-100 border border-red-200 dark:border-red-700">
                        {{ count($expiringToday['domains']) + count($expiringToday['hosting']) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringToday['domains']) > 0)
                        <div
                            class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 border border-red-100 dark:border-red-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-red-600 dark:text-red-400">🌐</span>
                                <p class="text-xs font-semibold text-red-700 dark:text-red-300">Domain
                                    ({{ count($expiringToday['domains']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringToday['domains'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-red-700 dark:text-red-200 truncate">{{ $website->name }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringToday['hosting']) > 0)
                        <div
                            class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 border border-red-100 dark:border-red-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-red-600 dark:text-red-400">🏠</span>
                                <p class="text-xs font-semibold text-red-700 dark:text-red-300">Hosting
                                    ({{ count($expiringToday['hosting']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringToday['hosting'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-red-700 dark:text-red-200 truncate">{{ $website->name }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringToday['domains']) == 0 && count($expiringToday['hosting']) == 0)
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada yang expired hari ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring in 7 Days -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-orange-200 dark:border-orange-700 rounded-xl p-5 transition-all duration-200 hover:border-orange-400 dark:hover:border-orange-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-orange-600 dark:text-orange-400 text-sm">⏰</span>
                        </div>
                        <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200">3 Hari Lagi</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-800/50 dark:text-orange-100 border border-orange-200 dark:border-orange-700">
                        {{ count($expiringIn3Days['domains']) + count($expiringIn3Days['hosting']) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringIn3Days['domains']) > 0)
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border border-orange-100 dark:border-orange-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-orange-600 dark:text-orange-400">🌐</span>
                                <p class="text-xs font-semibold text-orange-700 dark:text-orange-300">Domain
                                    ({{ count($expiringIn3Days['domains']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn3Days['domains'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-orange-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-orange-700 dark:text-orange-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn3Days['hosting']) > 0)
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border border-orange-100 dark:border-orange-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-orange-600 dark:text-orange-400">🏠</span>
                                <p class="text-xs font-semibold text-orange-700 dark:text-orange-300">Hosting
                                    ({{ count($expiringIn3Days['hosting']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn3Days['hosting'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-orange-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-orange-700 dark:text-orange-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn3Days['domains']) == 0 && count($expiringIn3Days['hosting']) == 0)
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada yang expired dalam 3 hari</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring in 3 Days -->
            <div
                class="bg-white dark:bg-gray-800 border-2 border-yellow-200 dark:border-yellow-700 rounded-xl p-5 transition-all duration-200 hover:border-yellow-400 dark:hover:border-yellow-600 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-yellow-600 dark:text-yellow-400 text-sm">📅</span>
                        </div>
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">7 Hari Lagi</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-800/50 dark:text-yellow-100 border border-yellow-200 dark:border-yellow-700">
                        {{ count($expiringIn7Days['domains']) + count($expiringIn7Days['hosting']) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringIn7Days['domains']) > 0)
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-100 dark:border-yellow-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-yellow-600 dark:text-yellow-400">🌐</span>
                                <p class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">Domain
                                    ({{ count($expiringIn7Days['domains']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn7Days['domains'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn7Days['hosting']) > 0)
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-100 dark:border-yellow-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-yellow-600 dark:text-yellow-400">🏠</span>
                                <p class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">Hosting
                                    ({{ count($expiringIn7Days['hosting']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn7Days['hosting'] as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn7Days['domains']) == 0 && count($expiringIn7Days['hosting']) == 0)
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada yang expired dalam 7 hari</p>
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
                            <span class="text-blue-600 dark:text-blue-400 text-sm">📊</span>
                        </div>
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">30 Hari Lagi</h3>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-800/50 dark:text-blue-100 border border-blue-200 dark:border-blue-700">
                        {{ count($expiringIn30Days['domains']) + count($expiringIn30Days['hosting']) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @if (count($expiringIn30Days['domains']) > 0)
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-100 dark:border-blue-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-blue-600 dark:text-blue-400">🌐</span>
                                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">Domain
                                    ({{ count($expiringIn30Days['domains']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn30Days['domains']->take(3) as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-blue-700 dark:text-blue-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                                @if (count($expiringIn30Days['domains']) > 3)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 bg-blue-300 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-blue-600 dark:text-blue-300 italic">... dan
                                            {{ count($expiringIn30Days['domains']) - 3 }} lainnya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn30Days['hosting']) > 0)
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-100 dark:border-blue-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-blue-600 dark:text-blue-400">🏠</span>
                                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">Hosting
                                    ({{ count($expiringIn30Days['hosting']) }})</p>
                            </div>
                            <div class="space-y-1">
                                @foreach ($expiringIn30Days['hosting']->take(3) as $website)
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-blue-700 dark:text-blue-200 truncate">
                                            {{ $website->name }}</p>
                                    </div>
                                @endforeach
                                @if (count($expiringIn30Days['hosting']) > 3)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 bg-blue-300 rounded-full flex-shrink-0"></div>
                                        <p class="text-xs text-blue-600 dark:text-blue-300 italic">... dan
                                            {{ count($expiringIn30Days['hosting']) - 3 }} lainnya</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (count($expiringIn30Days['domains']) == 0 && count($expiringIn30Days['hosting']) == 0)
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada yang expired dalam 30 hari
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
                <a href="{{ route('filament.secret.resources.websites.index') }}"
                    class="action-button inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 dark:text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 border border-orange-400 text-black"
                    target="_blank">
                    <div class="w-5 h-5 mr-3 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    Kelola Website
                </a>

                <div class="ml-2 test-notification-wrapper">
                    {{ $this->testNotificationAction }}
                </div>

                <div class="ml-2 mark-domain-paid-wrapper">
                    {{ $this->markDomainAsPaidAction }}
                </div>

                <div class="ml-2 mark-hosting-paid-wrapper">
                    {{ $this->markHostingAsPaidAction }}
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
    <style>
        .expiry-widget {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .dark .expiry-widget {
            background: #18181b;
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
            .expiry-widget {
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

        /* Test notification button styling for light mode */
        .test-notification-wrapper .fi-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
            border: 1px solid #d97706 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .test-notification-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-1px) !important;
        }

        .dark .test-notification-wrapper .fi-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border: 1px solid #f59e0b !important;
        }

        .dark .test-notification-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        }

        /* Ensure text visibility in test notification button */
        .test-notification-wrapper .fi-btn .fi-btn-label {
            color: white !important;
            font-weight: 600 !important;
        }

        .test-notification-wrapper .fi-btn .fi-icon {
            color: white !important;
        }

        /* Additional styling for better contrast */
        .test-notification-wrapper .fi-btn {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }

        /* Mark domain as paid button styling */
        .mark-domain-paid-wrapper .fi-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important;
            transition: all 0.2s ease-in-out !important;
        }

        .mark-domain-paid-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.4) !important;
        }

        .mark-domain-paid-wrapper .fi-btn .fi-btn-label {
            color: white !important;
            font-weight: 600 !important;
        }

        .mark-domain-paid-wrapper .fi-btn .fi-icon {
            color: white !important;
        }

        /* Mark hosting as paid button styling */
        .mark-hosting-paid-wrapper .fi-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3) !important;
            transition: all 0.2s ease-in-out !important;
        }

        .mark-hosting-paid-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 15px -3px rgba(59, 130, 246, 0.4) !important;
        }

        .mark-hosting-paid-wrapper .fi-btn .fi-btn-label {
            color: white !important;
            font-weight: 600 !important;
        }

        .mark-hosting-paid-wrapper .fi-btn .fi-icon {
            color: white !important;
        }
    </style>
</x-filament-widgets::widget>
