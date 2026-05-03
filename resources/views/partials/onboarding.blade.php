@if (auth()->user()->onboarding()->inProgress())
    <flux:spacer />

    <div class="erp-search">
        <flux:dropdown>
            {{-- Trigger Button --}}
            <flux:button variant="ghost" size="sm" icon:trailing="chevron-down" class="flex items-center gap-2">
                <span class="hidden md:block">Complete Setup</span>

                <flux:badge color="purple" size="sm">
                    {{ Auth::user()->onboarding()->percentageCompleted() }}%
                </flux:badge>
            </flux:button>

            {{-- Dropdown Content --}}
            <flux:menu class="w-72 p-2">

                {{-- Header --}}
                <div class="px-3 py-2 text-xs text-gray-500">
                    Complete these steps to finish setup
                </div>

                {{-- Progress Bar --}}
                <div class="px-3 pb-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-purple-500 h-2 rounded-full transition-all duration-300"
                            style="width: {{ Auth::user()->onboarding()->percentageCompleted() }}%">
                        </div>
                    </div>
                </div>

                {{-- Steps --}}
                @foreach (auth()->user()->onboarding()->steps as $step)
                    @php $isComplete = $step->complete(); @endphp

                    <flux:menu.item class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50">

                        {{-- Icon --}}
                        <div>
                            @if($isComplete)
                                <flux:icon name="check-circle" class="text-green-500 w-5 h-5"/>
                            @else
                                <flux:icon name="clock" class="text-gray-400 w-5 h-5"/>
                            @endif
                        </div>

                        {{-- Step Text --}}
                        <div class="flex flex-col">
                                        <span class="text-sm {{ $isComplete ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                            {{ $loop->iteration }}. {{ $step->title }}
                                        </span>

                            @if(!$isComplete)
                                <flux:link href="{{$step->link}}" size="sm">{{ $step->cta }}</flux:link>
                            @endif
                        </div>

                    </flux:menu.item>
                @endforeach

            </flux:menu>
        </flux:dropdown>
    </div>

    <flux:spacer />
@endif
