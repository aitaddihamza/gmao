import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY ?? 'local',
    wsHost: window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 8080,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
});

window.Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log('Notification reçue:', notification);
        new Audio('/notification.mp3').play();
    });
