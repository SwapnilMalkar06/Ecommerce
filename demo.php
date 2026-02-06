<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyRoute AI Booking Assistant</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }
        /* Custom scrollbar for the chat window */
        #chat-window::-webkit-scrollbar {
            width: 6px;
        }
        #chat-window::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 3px;
        }
        #chat-window::-webkit-scrollbar-track {
            background-color: #f1f5f9; /* slate-100 */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div id="app" class="w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col h-[80vh] min-h-[500px]">
        
        <!-- Header -->
        <div class="p-4 bg-indigo-600 text-white shadow-md flex items-center rounded-t-xl">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            <h1 class="text-lg font-bold">SkyRoute Booking Assistant</h1>
        </div>

        <!-- Chat Window -->
        <div id="chat-window" class="flex-grow p-4 space-y-4 overflow-y-auto">
            <!-- Initial Bot Message -->
            <div class="flex justify-start">
                <div class="bg-gray-100 p-3 rounded-lg max-w-xs shadow-md">
                    <p class="text-sm font-semibold text-indigo-700">Assistant</p>
                    <p class="text-gray-800 mt-1">Hello! I'm your SkyRoute AI Assistant. How can I help you with your ticket booking or travel plans today?</p>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex space-x-3">
                <input type="text" id="user-input" placeholder="Ask about flights, prices, or bookings..."
                       class="flex-grow p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150"
                       onkeydown="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()" id="send-button"
                        class="bg-indigo-600 text-white p-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-150 shadow-lg disabled:bg-indigo-300">
                    Send
                </button>
            </div>
        </div>

    </div>

    <script>
        // --- Essential Configuration ---
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
        const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {};
        
        // IMPORTANT: If running locally, you must replace the empty string below
        // with your actual Google AI Studio API key for the app to function.
        const apiKey = ""; 

        // --- Gemini API Configuration ---
        const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;
        
        // Define the AI's persona and context for the ticket booking system
        const systemInstruction = {
            parts: [{ 
                text: "Act as a friendly, concise, and helpful AI Chatbot for the 'SkyRoute Airlines Ticket Booking System'. Your primary function is to assist users with flight-related inquiries (availability, prices, booking status) and answer general travel questions. Your tone should be professional and encouraging."
            }]
        };

        const chatWindow = document.getElementById('chat-window');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');

        let chatHistory = [];

        /**
         * Adds a message to the chat window.
         * @param {string} text - The message content.
         * @param {string} sender - 'user' or 'bot'.
         * @param {string} [sourcesHtml=''] - Optional HTML for grounding sources.
         */
        function addMessage(text, sender, sourcesHtml = '') {
            const messageContainer = document.createElement('div');
            messageContainer.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

            const messageBubble = document.createElement('div');
            messageBubble.className = `p-3 rounded-lg max-w-xs shadow-md ${
                sender === 'user' ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-800'
            }`;

            if (sender === 'bot') {
                const header = document.createElement('p');
                header.className = 'text-sm font-semibold mb-1 ' + (sender === 'user' ? 'text-indigo-200' : 'text-indigo-700');
                header.textContent = sender === 'user' ? 'You' : 'Assistant';
                messageBubble.appendChild(header);
            }

            const messageText = document.createElement('p');
            messageText.className = 'mt-1';
            // Use innerHTML for safety when including external source links
            messageText.textContent = text; 
            messageBubble.appendChild(messageText);

            if (sourcesHtml) {
                const sourcesDiv = document.createElement('div');
                sourcesDiv.className = 'mt-2 text-xs opacity-80';
                sourcesDiv.innerHTML = sourcesHtml;
                messageBubble.appendChild(sourcesDiv);
            }

            messageContainer.appendChild(messageBubble);
            chatWindow.appendChild(messageContainer);

            // Scroll to the latest message
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        /**
         * Simple retry mechanism with exponential backoff for fetch calls.
         * @param {function} fn - The function to retry (must return a Promise).
         * @param {number} retries - Number of times to retry.
         * @param {number} delay - Initial delay in milliseconds.
         */
        async function fetchWithRetry(fn, retries = 5, delay = 1000) {
            try {
                return await fn();
            } catch (error) {
                if (retries > 0) {
                    await new Promise(resolve => setTimeout(resolve, delay));
                    return fetchWithRetry(fn, retries - 1, delay * 2);
                } else {
                    throw error;
                }
            }
        }

        /**
         * Sends the user message to the Gemini API and handles the response.
         */
        async function sendMessage() {
            const userText = userInput.value.trim();
            if (!userText) return;

            // 1. Display user message and clear input
            addMessage(userText, 'user');
            userInput.value = '';
            
            // Disable input/button and show loading state
            sendButton.disabled = true;
            sendButton.textContent = 'Thinking...';
            
            // 2. Add user message to history
            chatHistory.push({ role: "user", parts: [{ text: userText }] });

            // 3. Construct the API payload
            const payload = {
                contents: chatHistory,
                tools: [{ "google_search": {} }], 
                systemInstruction: systemInstruction,
            };

            let botResponseText = "Sorry, I encountered an error while contacting the assistant. Please try again.";
            let sourcesHtml = '';

            try {
                const response = await fetchWithRetry(() => fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                }));
                
                if (!response.ok) {
                    const errorBody = await response.text();
                    // --- Diagnostic Logging ---
                    console.error("--- API Error Diagnosis ---");
                    console.error(`Gemini API Failed with Status: ${response.status}`);
                    console.error("Response body (Check this for error details, often JSON):", errorBody);
                    console.error("The most likely cause is an invalid or missing API key.");
                    console.error("---------------------------");
                    throw new Error(`API Request Failed. Status: ${response.status}`);
                }

                const result = await response.json();
                const candidate = result.candidates?.[0];

                if (candidate && candidate.content?.parts?.[0]?.text) {
                    botResponseText = candidate.content.parts[0].text;
                    
                    // Extract grounding sources
                    const groundingMetadata = candidate.groundingMetadata;
                    if (groundingMetadata && groundingMetadata.groundingAttributions) {
                        const sources = groundingMetadata.groundingAttributions
                            .map(attribution => ({
                                uri: attribution.web?.uri,
                                title: attribution.web?.title,
                            }))
                            .filter(source => source.uri && source.title);

                        if (sources.length > 0) {
                            sourcesHtml = '<p class="font-normal mt-1 pt-1 border-t border-gray-300">Sources:</p>';
                            sources.forEach((source, index) => {
                                sourcesHtml += `<a href="${source.uri}" target="_blank" class="block text-indigo-600 hover:underline">${index + 1}. ${source.title}</a>`;
                            });
                        }
                    }
                }

            } catch (error) {
                console.error("Fetch or API call failed:", error);
                // The generic error message is already set in botResponseText
            } finally {
                // 4. Add bot response to history and display
                chatHistory.push({ role: "model", parts: [{ text: botResponseText }] });
                addMessage(botResponseText, 'bot', sourcesHtml);

                // Re-enable input and button
                sendButton.disabled = false;
                sendButton.textContent = 'Send';
                userInput.focus();
            }
        }
        
        // Initial focus on the input field when the app loads
        document.addEventListener('DOMContentLoaded', () => {
            userInput.focus();
        });
    </script>
</body>
</html>