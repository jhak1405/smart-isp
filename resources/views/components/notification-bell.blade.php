<!-- Notification Bell Dropdown Component (Sin Alpine.js para evitar conflictos) -->
<div class="relative" id="notification-bell-container" style="display: inline-block;">
    <!-- Bell Icon Button -->
    <button id="notification-bell-btn" class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
            title="Notificaciones" style="background: none; border: none; cursor: pointer;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <!-- Notification Badge Counter -->
        <span id="notification-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
              style="display: none; min-width: 20px; min-height: 20px;">
            0
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div id="notification-dropdown"
         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
         style="display: none; top: 100%; min-width: 320px;">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
            <p class="text-xs text-gray-500 mt-1">
                <span id="unread-count">0</span> sin leer
            </p>
        </div>

        <!-- Notifications List -->
        <div id="notifications-list" class="max-h-96 overflow-y-auto">
            <div class="px-4 py-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                    </path>
                </svg>
                <p class="text-sm text-gray-600">No hay notificaciones</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg flex gap-2">
            <button id="mark-all-read-btn"
                    class="flex-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 py-2 px-3 rounded transition"
                    style="background: none; border: none; cursor: pointer;">
                Marcar todo como leído
            </button>
            <button id="close-dropdown-btn"
                    class="flex-1 text-xs font-medium text-gray-600 hover:text-gray-700 hover:bg-gray-200 py-2 px-3 rounded transition"
                    style="background: none; border: none; cursor: pointer;">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // Esperar a que el DOM esté listo
    const init = () => {
        const bell = document.getElementById('notification-bell-btn');
        const dropdown = document.getElementById('notification-dropdown');
        const notificationsList = document.getElementById('notifications-list');
        const badge = document.getElementById('notification-badge');
        const unreadCount = document.getElementById('unread-count');
        const markAllReadBtn = document.getElementById('mark-all-read-btn');
        const closeBtn = document.getElementById('close-dropdown-btn');

        if (!bell) {
            setTimeout(init, 100);
            return;
        }

        let notifications = @json($notifications ?? []);
        let isOpen = false;

        // Renderizar notificaciones
        function renderNotifications() {
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="px-4 py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-sm text-gray-600">No hay notificaciones</p>
                    </div>
                `;
                return;
            }

            notificationsList.innerHTML = notifications.map(n => `
                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${n.data.title || 'Notificación'}</p>
                            <p class="text-sm text-gray-600 mt-1">${n.data.body || ''}</p>
                            <p class="text-xs text-gray-400 mt-2">${formatTime(n.created_at)}</p>
                        </div>
                        ${!n.read_at ? '<div class="w-2 h-2 bg-blue-500 rounded-full ml-2 mt-1.5" style="flex-shrink: 0;"></div>' : ''}
                    </div>
                </div>
            `).join('');

            updateUnreadCount();
        }

        function updateUnreadCount() {
            const count = notifications.filter(n => !n.read_at).length;
            unreadCount.textContent = count;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'Hace unos segundos';
            if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
            if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
            return `Hace ${Math.floor(diff / 86400)} días`;
        }

        // Toggle dropdown
        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;
            dropdown.style.display = isOpen ? 'block' : 'none';
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!document.getElementById('notification-bell-container').contains(e.target)) {
                isOpen = false;
                dropdown.style.display = 'none';
            }
        });

        // Close button
        closeBtn.addEventListener('click', function() {
            isOpen = false;
            dropdown.style.display = 'none';
        });

        // Mark all as read
        markAllReadBtn.addEventListener('click', function() {
            fetch('{{ route("tecnico.notifications.read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                notifications.forEach(n => n.read_at = new Date().toISOString());
                renderNotifications();
            }).catch(err => console.error('Error:', err));
        });

        // Setup Polling (Same as Admin logic)
        const fetchNewNotifications = () => {
            fetch('{{ route("tecnico.notifications.get") }}')
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        // Actualizar la lista local con los datos frescos
                        notifications = data;
                        renderNotifications();
                    }
                })
                .catch(err => console.error('Error polling notifications:', err));
        };

        // Poll every 30 seconds
        setInterval(fetchNewNotifications, 30000);

        // Initial render
        renderNotifications();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
