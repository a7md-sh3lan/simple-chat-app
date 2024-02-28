const express = require('express')
const app = express();
const bodyParser = require("body-parser");
const http = require('http');
const server = http.Server(app);
const socketIO = require('socket.io');
const io = socketIO(server, {
    allowEIO3: true,
  cors: {
    origin: true,
    credentials: true,
    methods: ["GET", "POST"]
  }
});

app.use(express.json({limit: '100mb'}));
app.use(express.urlencoded({limit: '100mb',extended:true,parameterLimit:5000}));

const router = express.Router();

const port = process.env.PORT || 3001;

let body = null;

router.post('/message', (req,res)=>
{
     body = req.body;
     res.sendStatus(200);
     console.log(body);
     io.emit('message',body);
});

io.on('connection', (socket) => {
    console.log('connection');
});

router.get('/test', () =>
{
    console.log('SH3LAN..test node server..');
})


server.listen(port, () => {
    console.log(`started on port: ${port}`);    
});

app.use("/", router);