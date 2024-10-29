from flask import Flask, render_template
from flask_socketio import SocketIO, emit
import tensorflow as tf
import numpy as np

app = Flask(__name__)
socketio = SocketIO(app)

# Definimos el modelo de red neuronal
model = tf.keras.Sequential([
    tf.keras.layers.Input(shape=(2,)),
    tf.keras.layers.Dense(units=4, activation='relu', name='hidden_layer1'),
    tf.keras.layers.Dense(units=4, activation='relu', name='hidden_layer2'),
    tf.keras.layers.Dense(units=4, activation='relu', name='hidden_layer3'),
    tf.keras.layers.Dense(units=1, name='output_layer')
])

model.compile(optimizer='adam', loss='mean_squared_error')

# Callback personalizado para enviar pesos a través de WebSocket
class LiveWeightsCallback(tf.keras.callbacks.Callback):
    def on_epoch_end(self, epoch, logs=None):
        if epoch % 500 == 0:  # Enviar actualización cada 10 épocas
            weights_data = []
            for layer in model.layers:
                if isinstance(layer, tf.keras.layers.Dense):
                    weights, biases = layer.get_weights()
                    weights_data.append(weights.tolist())
            socketio.emit('weights_update', {'epoch': epoch, 'weights': weights_data})


@app.route('/')
def index():
    return render_template('index.html')

# Configurar los datos y entrenar el modelo con el callback
def train_model():
    # Generar datos para el entrenamiento
    a_values = np.random.uniform(1, 10, 500)
    b_values = np.random.uniform(1, 10, 500)
    c_values = np.sqrt(a_values**2 + b_values**2)
    X = np.column_stack((a_values, b_values))
    y = c_values
    
    # Entrenar el modelo y emitir los pesos en cada época
    model.fit(X, y, epochs=50, callbacks=[LiveWeightsCallback()], verbose=0)

if __name__ == '__main__':
    socketio.start_background_task(train_model)  # Iniciar el entrenamiento en segundo plano
    socketio.run(app, debug=True)
