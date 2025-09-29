@if(session()->has('gamification_notifications'))
    @php
        $notifications = session('gamification_notifications', []);
        session()->forget('gamification_notifications');
    @endphp
    
    <!-- Debug: Show notification count -->
    <script>console.log('Gamification notifications found:', @json($notifications));</script>
    
    @foreach($notifications as $notification)
        <div id="notification-{{ $loop->index }}" class="fixed top-20 right-6 z-50 transform translate-x-full transition-transform duration-500 ease-in-out">
            <div class="bg-white border-l-4 {{ $notification['type'] === 'level_up' ? 'border-yellow-400 bg-gradient-to-r from-yellow-50 to-orange-50' : 'border-purple-400 bg-gradient-to-r from-purple-50 to-pink-50' }} rounded-lg shadow-xl p-4 max-w-sm min-w-[300px]">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($notification['type'] === 'level_up')
                            <div class="text-3xl animate-bounce">🎉</div>
                        @else
                            <div class="text-3xl animate-pulse">{{ $notification['icon'] ?? '🏆' }}</div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-bold {{ $notification['type'] === 'level_up' ? 'text-yellow-800' : 'text-purple-800' }}">
                            {{ $notification['title'] }}
                        </h3>
                        <p class="mt-1 text-base font-medium text-gray-800">
                            {{ $notification['message'] }}
                        </p>
                        @if(isset($notification['description']))
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $notification['description'] }}
                            </p>
                        @endif
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="hideNotification({{ $loop->index }})" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show notifications with delay
            @foreach($notifications as $notification)
                setTimeout(() => {
                    const notification{{ $loop->index }} = document.getElementById('notification-{{ $loop->index }}');
                    if (notification{{ $loop->index }}) {
                        notification{{ $loop->index }}.classList.remove('translate-x-full');
                        notification{{ $loop->index }}.classList.add('translate-x-0');
                        
                        // Auto-hide after 6 seconds
                        setTimeout(() => {
                            hideNotification({{ $loop->index }});
                        }, 6000);
                    }
                }, {{ $loop->index * 500 }});
            @endforeach
        });

        function hideNotification(index) {
            const notification = document.getElementById('notification-' + index);
            if (notification) {
                notification.classList.remove('translate-x-0');
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }
    </script>
@else
    <script>console.log('No gamification notifications in session');</script>
@endif
