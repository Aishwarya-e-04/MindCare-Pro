<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <div class="glass shadow-lg border-0 overflow-hidden fade-in rounded-5">
                    <div class="p-4 bg-primary text-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fa-solid fa-robot fa-xl text-white"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">MindCare Ally</h5>
                                <div class="small opacity-75"><i class="fa-solid fa-circle text-success me-1"
                                        style="font-size: 8px;"></i>Always Listening</div>
                            </div>
                        </div>
                        <a href="dashboard.php"
                            class="btn btn-sm btn-white bg-white text-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-house me-2"></i>Exit
                        </a>
                    </div>

                    <div id="chat-window-container" class="p-5 bg-white bg-opacity-50"
                        style="height: 550px; overflow-y: auto;">
                        <div id="chat-window" class="d-flex flex-column gap-4">
                            <div class="message ai-message shadow-sm p-4 rounded-4"
                                style="border-bottom-left-radius: 0 !important;">
                                <div class="small fw-bold mb-2 text-primary text-uppercase"
                                    style="letter-spacing: 1px;">MindCare AI</div>
                                Hello! I'm your dedicated emotional support assistant. Our conversation is private and
                                anonymous. How can I help you navigate your feelings today?
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-light bg-opacity-80 border-top">
                        <form id="chat-form">
                            <div class="input-group input-group-lg bg-white rounded-pill p-1 shadow-sm border">
                                <input type="text" id="user-input" class="form-control border-0 bg-transparent ps-4"
                                    placeholder="Share what's on your mind..." style="font-size: 1rem;" required
                                    autofocus>
                                <button type="submit"
                                    class="btn btn-primary rounded-pill px-5 shadow-sm transition-all hover-lift">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Send
                                </button>
                            </div>
                            <div class="mt-3 text-center">
                                <p class="small text-muted mb-0"><i class="fa-solid fa-shield-halved me-1"></i> Data
                                    encrypted. Secure end-to-end conversation.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #chat-window-container::-webkit-scrollbar {
            width: 6px;
        }

        #chat-window-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .message {
            max-width: 85%;
            line-height: 1.6;
            animation: messageFadeIn 0.3s ease-out;
        }

        .user-message {
            background: linear-gradient(135deg, var(--primary-color), #4F46E5);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0 !important;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.15);
        }

        .ai-message {
            background: white;
            color: var(--text-color);
            align-self: flex-start;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        @keyframes messageFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        const chatContainer = document.getElementById('chat-window-container');
        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');

        function appendMessage(text, role) {
            const msgWrapper = document.createElement('div');
            msgWrapper.classList.add('message', role === 'user' ? 'user-message' : 'ai-message', 'shadow-sm', 'p-4', 'rounded-4');

            if (role === 'ai') {
                msgWrapper.style.borderBottomLeftRadius = '0';
                msgWrapper.innerHTML = `<div class="small fw-bold mb-2 text-primary text-uppercase" style="letter-spacing: 1px;">MindCare AI</div>${text}`;
            } else {
                msgWrapper.style.borderBottomRightRadius = '0';
                msgWrapper.textContent = text;
            }

            chatWindow.appendChild(msgWrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = userInput.value;
            if (!message.trim()) return;

            appendMessage(message, 'user');
            userInput.value = '';

            // Typing indicator
            const thinkingDiv = document.createElement('div');
            thinkingDiv.classList.add('message', 'ai-message', 'shadow-sm', 'p-4', 'rounded-4', 'text-muted', 'small');
            thinkingDiv.style.borderBottomLeftRadius = '0';
            thinkingDiv.innerHTML = '<i class="fa-solid fa-ellipsis fa-bounce me-2"></i>Analyzing emotional context...';
            chatWindow.appendChild(thinkingDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            try {
                const response = await fetch('process_chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `message=${encodeURIComponent(message)}`
                });
                const data = await response.json();

                chatWindow.removeChild(thinkingDiv);
                appendMessage(data.reply, 'ai');
            } catch (error) {
                chatWindow.removeChild(thinkingDiv);
                appendMessage("I apologize, but I encountered a momentary disconnection. Could you please share that again?", 'ai');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>