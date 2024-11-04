"use client";
import { useEffect, useRef, useState } from "react";

export default function ChatPage() {
  const [userId, setUserId] = useState<string>("");
  const [targetUserId, setTargetUserId] = useState<string>("");
  const [messages, setMessages] = useState<{ userId: string; message: string }[]>([]);
  const [input, setInput] = useState("");
  const [peerConnection, setPeerConnection] = useState<RTCPeerConnection | null>(null);
  const ws = useRef<WebSocket | null>(null);
  const localVideoRef = useRef<HTMLVideoElement>(null);
  const remoteVideoRef = useRef<HTMLVideoElement>(null);

  useEffect(() => {
    // Genera un ID de usuario aleatorio único al abrir la página
    const generatedUserId = `user_${Math.floor(Math.random() * 10000)}`;
    setUserId(generatedUserId);

    // Conectar al servidor WebSocket
    ws.current = new WebSocket("ws://localhost:8080");

    ws.current.onopen = () => {
      // Enviar el ID de usuario para registro en el servidor
      ws.current?.send(JSON.stringify({ type: "login", userId: generatedUserId }));
    };

    ws.current.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === "message") {
        setMessages((prev) => [...prev, { userId: data.userId, message: data.message }]);
      } else if (data.type === "offer" || data.type === "answer" || data.type === "candidate") {
        handleRTCMessage(data);
      }
    };

    // Cerrar la conexión WebSocket al salir
    return () => {
      ws.current?.close();
    };
  }, []);

  // Manejo de mensaje de entrada
  const sendMessage = () => {
    if (ws.current && input) {
      ws.current.send(JSON.stringify({ type: "message", message: input }));
      setMessages((prev) => [...prev, { userId, message: input }]);
      setInput("");
    }
  };

  const handleRTCMessage = async (data: any) => {
    if (data.type === "offer") {
      const pc = await startRTCConnection();
      await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
      const answer = await pc.createAnswer();
      await pc.setLocalDescription(answer);
      ws.current?.send(JSON.stringify({ type: "answer", answer, target: data.userId }));
    } else if (data.type === "answer" && peerConnection) {
      await peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
    } else if (data.type === "candidate" && peerConnection) {
      await peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
    }
  };

  const startRTCConnection = async () => {
    const localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    if (localVideoRef.current) localVideoRef.current.srcObject = localStream;

    const pc = new RTCPeerConnection();

    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

    pc.ontrack = (event) => {
      if (remoteVideoRef.current) remoteVideoRef.current.srcObject = event.streams[0];
    };

    pc.onicecandidate = (event) => {
      if (event.candidate && ws.current) {
        ws.current.send(JSON.stringify({ type: "candidate", candidate: event.candidate, target: targetUserId }));
      }
    };

    setPeerConnection(pc);
    return pc;
  };

  const startVideoChat = async () => {
    if (!targetUserId) {
      alert("Por favor, ingresa el ID del usuario con el que deseas comunicarte");
      return;
    }
    const pc = await startRTCConnection();
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    ws.current?.send(JSON.stringify({ type: "offer", offer, target: targetUserId }));
  };

  return (
    <div className="min-h-screen flex flex-col items-center bg-gray-100 p-4">
      <h1 className="text-3xl font-bold mb-6 text-blue-600">Chat con WebSocket y WebRTC</h1>
      <p>Tu ID de Usuario: <strong>{userId}</strong></p>
      
      <div className="flex gap-4 mb-6">
        <video ref={localVideoRef} autoPlay muted className="w-60 h-40 border rounded-lg shadow-lg" />
        <video ref={remoteVideoRef} autoPlay className="w-60 h-40 border rounded-lg shadow-lg" />
      </div>

      <input
        type="text"
        value={targetUserId}
        onChange={(e) => setTargetUserId(e.target.value)}
        placeholder="Ingresa ID de usuario para videollamada"
        className="mb-4 p-2 border rounded-lg w-80"
      />
      <button
        onClick={startVideoChat}
        className="mb-6 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition-colors"
      >
        Iniciar Videollamada
      </button>

      <div className="w-full max-w-md bg-white p-4 rounded-lg shadow-lg">
        <h2 className="text-2xl font-semibold mb-4">Mensajes:</h2>
        <div className="h-60 overflow-y-auto mb-4 border p-2 rounded-lg bg-gray-50">
          <ul className="space-y-2">
            {messages.map((msg, index) => (
              <li key={index} className="text-gray-700 text-sm">
                <strong>{msg.userId}:</strong> {msg.message}
              </li>
            ))}
          </ul>
        </div>

        <div className="flex gap-2">
          <input
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Escribe un mensaje"
            className="flex-grow p-2 border rounded-lg"
          />
          <button
            onClick={sendMessage}
            className="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 transition-colors"
          >
            Enviar
          </button>
        </div>
      </div>
    </div>
  );
}
