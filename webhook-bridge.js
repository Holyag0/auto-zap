const http = require('http');
const https = require('https');

const CHATWOOT_URL = 'http://localhost:3000';
const CHATWOOT_ACCOUNT_ID = 1;
const CHATWOOT_INBOX_ID = 1;
const CHATWOOT_HMAC_TOKEN = 'p63Ye4S31jPzAa1xuDFzp2je';

const server = http.createServer((req, res) => {
  if (req.method === 'POST' && req.url === '/webhook') {
    let body = '';
    req.on('data', chunk => { body += chunk.toString(); });
    req.on('end', () => {
      try {
        const data = JSON.parse(body);
        console.log('Recebido do Evolution API:', JSON.stringify(data, null, 2));
        
        // Processar mensagem do Evolution API e enviar para Chatwoot
        if (data.event === 'messages.upsert' && data.data) {
          const message = data.data;
          sendToChatwoot(message);
        }
        
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ success: true }));
      } catch (e) {
        console.error('Erro:', e);
        res.writeHead(500, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: e.message }));
      }
    });
  } else {
    res.writeHead(404);
    res.end();
  }
});

function sendToChatwoot(message) {
  // Implementar envio para Chatwoot via API REST
  console.log('Enviando para Chatwoot:', message);
}

server.listen(3001, () => {
  console.log('Webhook bridge rodando em http://localhost:3001/webhook');
});
