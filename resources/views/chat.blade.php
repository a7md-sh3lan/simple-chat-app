<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <!-- Include Laravel Echo and Socket.IO -->
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
    <div id="app">
        <div class="chat-window">
            <div class="messages" v-scroll-bottom>
                <ul>
                    <li v-for="message in messages">
                        <strong>@{{ message.sender.name }}:</strong> @{{ message.content }}
                    </li>
                </ul>
            </div>
            <div class="input-box">
                <input type="text" v-model="newMessage" @keyup.enter="sendMessage">
                <button @click="sendMessage">Send</button>
            </div>
        </div>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: {
                messages: [],
                newMessage: '',
            },
            created() {
                this.fetchMessages();
                Echo.private('chat.' + userId)
                    .listen('MessageSent', (e) => {
                        this.messages.push(e.message);
                    });
            },
            methods: {
                fetchMessages() {
                    // Fetch messages from the server
                },
                sendMessage() {
                    // Send message to the server
                },
            },
        });
    </script>
</body>
</html>
