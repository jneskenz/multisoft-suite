<div>
    {{-- Contenedor de notificaciones toast --}}
    <div 
        x-data="{ 
            notifications: [],
            add(notification) {
                const id = Date.now();
                this.notifications.push({ ...notification, id });
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }"
        x-on:notify.window="add($event.detail)"
        class="toast-container position-fixed top-0 end-0 p-3"
        style="z-index: 1100;"
    >
        <template x-for="notification in notifications" :key="notification.id">
            <div 
                x-show="true"
                x-transition:enter="animate__animated animate__fadeInRight"
                x-transition:leave="animate__animated animate__fadeOutRight"
                class="toast show mb-2"
                :class="{
                    'bg-success': notification.type === 'success',
                    'bg-danger': notification.type === 'error',
                    'bg-warning': notification.type === 'warning',
                    'bg-info': notification.type === 'info'
                }"
                role="alert"
            >
                <div class="toast-header">
                    <i 
                        class="ti me-2"
                        :class="{
                            'tabler-check text-success': notification.type === 'success',
                            'tabler-x text-danger': notification.type === 'error',
                            'tabler-alert-triangle text-warning': notification.type === 'warning',
                            'tabler-info-circle text-info': notification.type === 'info'
                        }"
                    ></i>
                    <strong 
                        class="me-auto"
                        x-text="notification.type === 'success' ? 'Éxito' : 
                                notification.type === 'error' ? 'Error' : 
                                notification.type === 'warning' ? 'Advertencia' : 'Info'"
                    ></strong>
                    <button 
                        type="button" 
                        class="btn-close" 
                        @click="remove(notification.id)"
                    ></button>
                </div>
                <div class="toast-body text-white" x-text="notification.message"></div>
            </div>
        </template>
    </div>
</div>
