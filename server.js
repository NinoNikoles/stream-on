const express = require('express');
const http = require('http');
const WebSocket = require('ws');

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

// Erstelle ein Objekt, um die WebSocket-Clients für jede remotesessionID zu verfolgen
const clients = {};

wss.on('connection', (ws, req) => {
    const urlParams = new URLSearchParams(req.url.slice(req.url.indexOf('?')));
    const remotesessionID = urlParams.get('remotesessionID');

    // Füge den WebSocket-Client zur Liste der Clients für diese remotesessionID hinzu
    if (!clients[remotesessionID]) {
        clients[remotesessionID] = [];
    }
    clients[remotesessionID].push(ws);

    ws.on('message', (message) => {
        console.log('Received message:', message);
        // Hier empfängst du Nachrichten von einem Benutzer und sendest sie an alle anderen Benutzer
        clients[remotesessionID].forEach((client) => {
            if (client !== ws && client.readyState === WebSocket.OPEN) {
                client.send(message.toString());
            }
        });
    });

    ws.on('close', () => {
        // Entferne den WebSocket-Client aus der Liste, wenn die Verbindung geschlossen wird
        const index = clients[remotesessionID].indexOf(ws);
        if (index !== -1) {
            clients[remotesessionID].splice(index, 1);
        }
    });
});

server.listen(3000, () => {
    console.log('Server is listening on port 3000');
});
