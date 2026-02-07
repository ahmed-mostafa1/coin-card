@extends('layouts.app')

@section('title', 'VIP Discount Debug')

@section('content')
    <div class="max-w-4xl mx-auto">
        <x-card :hover="false">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">VIP Discount Debug Information</h1>

            @php
                $currentUser = auth()->user();
                $userVipStatus = null;
                $vipTier = null;
                $vipDiscount = 0;
                
                if ($currentUser) {
                    $currentUser->load('vipStatus.vipTier');
                    $userVipStatus = $currentUser->vipStatus;
                    if ($userVipStatus && $userVipStatus->vipTier) {
                        $vipTier = $userVipStatus->vipTier;
                        $vipDiscount = $vipTier->discount_percentage ?? 0;
                    }
                }
            @endphp

            <div class="space-y-6">
                <!-- User Info -->
                <div class="rounded-lg border border-slate-200 p-4">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">1. Current User</h2>
                    @auth
                        <div class="space-y-2 text-sm">
                            <p><span class="font-semibold">Name:</span> {{ $currentUser->name }}</p>
                            <p><span class="font-semibold">Email:</span> {{ $currentUser->email }}</p>
                            <p><span class="font-semibold">ID:</span> {{ $currentUser->id }}</p>
                        </div>
                    @else
                        <p class="text-rose-600">❌ Not logged in</p>
                    @endauth
                </div>

                <!-- VIP Status -->
                <div class="rounded-lg border border-slate-200 p-4">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">2. VIP Status</h2>
                    @if ($userVipStatus)
                        <div class="space-y-2 text-sm">
                            <p class="text-emerald-600">✅ User has VIP status</p>
                            <p><span class="font-semibold">VIP Tier ID:</span> {{ $userVipStatus->vip_tier_id }}</p>
                            <p><span class="font-semibold">Lifetime Spent:</span> ${{ number_format($userVipStatus->lifetime_spent, 2) }}</p>
                            <p><span class="font-semibold">Calculated At:</span> {{ $userVipStatus->calculated_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</p>
                        </div>
                    @else
                        <p class="text-amber-600">⚠️ User has no VIP status assigned</p>
                        <p class="text-xs text-slate-500 mt-2">VIP status is assigned based on lifetime deposits/spending</p>
                    @endif
                </div>

                <!-- VIP Tier -->
                <div class="rounded-lg border border-slate-200 p-4">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">3. VIP Tier Details</h2>
                    @if ($vipTier)
                        <div class="space-y-2 text-sm">
                            <p class="text-emerald-600">✅ VIP Tier loaded successfully</p>
                            <p><span class="font-semibold">Tier Name (EN):</span> {{ $vipTier->title_en }}</p>
                            <p><span class="font-semibold">Tier Name (AR):</span> {{ $vipTier->title_ar }}</p>
                            <p><span class="font-semibold">Rank:</span> {{ $vipTier->rank }}</p>
                            <p><span class="font-semibold text-emerald-700">Discount Percentage:</span> <span class="text-lg font-bold text-emerald-700">{{ $vipTier->discount_percentage }}%</span></p>
                            <p><span class="font-semibold">Required Deposits:</span> ${{ number_format($vipTier->deposits_required, 2) }}</p>
                        </div>
                    @else
                        <p class="text-rose-600">❌ No VIP tier found</p>
                        <p class="text-xs text-slate-500 mt-2">User needs VIP status to have a tier</p>
                    @endif
                </div>

                <!-- Discount Calculation -->
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <h2 class="text-lg font-semibold text-emerald-900 mb-3">4. Discount Calculation</h2>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-semibold">VIP Discount:</span> <span class="text-2xl font-bold text-emerald-700">{{ $vipDiscount }}%</span></p>
                        
                        @if ($vipDiscount > 0)
                            <div class="mt-4 p-3 bg-white rounded border border-emerald-200">
                                <p class="font-semibold mb-2">Example Calculation:</p>
                                @php
                                    $testPrice = 100;
                                    $discountedPrice = $testPrice * (1 - $vipDiscount / 100);
                                    $saved = $testPrice - $discountedPrice;
                                @endphp
                                <p>Original Price: <span class="line-through">${{ number_format($testPrice, 2) }}</span></p>
                                <p>Discount: {{ $vipDiscount }}%</p>
                                <p class="text-emerald-700 font-bold">Final Price: ${{ number_format($discountedPrice, 2) }}</p>
                                <p class="text-emerald-600">You Save: ${{ number_format($saved, 2) }}</p>
                            </div>
                        @else
                            <p class="text-amber-600">⚠️ No discount will be applied</p>
                        @endif
                    </div>
                </div>

                <!-- All VIP Tiers -->
                <div class="rounded-lg border border-slate-200 p-4">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">5. All Available VIP Tiers</h2>
                    @php
                        $allTiers = \App\Models\VipTier::orderBy('rank')->get();
                    @endphp
                    @if ($allTiers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Rank</th>
                                        <th class="px-3 py-2 text-left">Name</th>
                                        <th class="px-3 py-2 text-left">Discount</th>
                                        <th class="px-3 py-2 text-left">Required</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($allTiers as $tier)
                                        <tr class="{{ $vipTier && $vipTier->id == $tier->id ? 'bg-emerald-50' : '' }}">
                                            <td class="px-3 py-2">{{ $tier->rank }}</td>
                                            <td class="px-3 py-2">{{ $tier->title_en }}</td>
                                            <td class="px-3 py-2 font-semibold text-emerald-700">{{ $tier->discount_percentage }}%</td>
                                            <td class="px-3 py-2">${{ number_format($tier->deposits_required, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-rose-600">❌ No VIP tiers configured in database</p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h2 class="text-lg font-semibold text-blue-900 mb-3">Actions</h2>
                    <div class="space-y-2">
                        @if (!$userVipStatus && $currentUser)
                            <form method="POST" action="{{ route('admin.test.assign-vip') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                                    Assign Test VIP Status (Rank 2 - 4% discount)
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('services.index') }}" class="inline-block px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-700">
                            View Services
                        </a>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
@endsection
