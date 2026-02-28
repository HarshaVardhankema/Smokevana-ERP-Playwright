// WebSocket Configuration for Laravel WebSockets
// This file contains the updated Echo configuration to use local WebSocket server

// Update the Echo configuration in bootstrap.js to use Laravel WebSockets
window.Echo = new Echo({
    authEndpoint: base_path + '/broadcasting/auth',
    broadcaster: 'pusher',
    key: APP.PUSHER_APP_KEY,
    cluster: APP.PUSHER_APP_CLUSTER,
    // Use local WebSocket server instead of Pusher
    host: window.location.hostname,
    port: 6001,
    forceTLS: false, // Set to false for local development
    encrypted: false, // Set to false for local development
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});

// Example of how to listen to channels
// window.Echo.channel('test-channel')
//     .listen('TestEvent', (e) => {
//         console.log('Received event:', e);
//     });

// Example of how to listen to private channels
// window.Echo.private('private-channel')
//     .listen('PrivateEvent', (e) => {
//         console.log('Received private event:', e);
//     });

// Example of how to listen to presence channels
// window.Echo.join('presence-channel')
//     .here((users) => {
//         console.log('Users currently in the channel:', users);
//     })
//     .joining((user) => {
//         console.log('User joined:', user);
//     })
//     .leaving((user) => {
//         console.log('User left:', user);
//     }); 