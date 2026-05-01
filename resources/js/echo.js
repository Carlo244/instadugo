import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

class NoopEcho {
    private() {
        return this;
    }

    channel() {
        return this;
    }

    join() {
        return this;
    }

    leave() {
        return this;
    }

    leaveChannel() {
        return this;
    }

    listen() {
        return this;
    }

    stopListening() {
        return this;
    }

    whisper() {
        return this;
    }

    notification() {
        return this;
    }
}

const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;

window.Echo = reverbAppKey
    ? new Echo({
          broadcaster: 'reverb',
          key: reverbAppKey,
          wsHost: import.meta.env.VITE_REVERB_HOST,
          wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
          wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
          forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
          enabledTransports: ['ws', 'wss'],
      })
    : new NoopEcho();