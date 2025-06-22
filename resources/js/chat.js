document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-message');
    const updateButton = document.getElementById('update-chart');

    // Set up WebSocket connection
    const ws = new WebSocket(`ws://localhost:${window.reverbPort}`);

    ws.onopen = () => {
        console.log('Connected to WebSocket');
        ws.send(JSON.stringify({
            type: 'subscribe',
            channel: 'chat',
            key: window.reverbKey
        }));
    };

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.type === 'chat-message') {
            appendMessage(data.user, data.message);
        }
    };

    function appendMessage(user, message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'mb-2';
        messageElement.innerHTML = `
            <span class="font-bold">${user}:</span>
            <span>${message}</span>
        `;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            });
            messageInput.value = '';
        }
    }

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Handle chart updates
    updateButton.addEventListener('click', () => {
        const sales2024 = Array.from(document.querySelectorAll('.sales-2024')).map(input => parseInt(input.value) || 0);
        const sales2023 = Array.from(document.querySelectorAll('.sales-2023')).map(input => parseInt(input.value) || 0);

        fetch('/api/chart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                data: [
                    { label: 'Sales 2024', values: sales2024 },
                    { label: 'Sales 2023', values: sales2023 }
                ]
            })
        });
    });
});
