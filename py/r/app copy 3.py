from flask import Flask, render_template, request
from flask_socketio import SocketIO, emit
import tensorflow as tf
import numpy as np

app = Flask(__name__)
socketio = SocketIO(app)

# Función para crear el modelo dinámicamente
def create_model(layers):
    model = tf.keras.Sequential()
    model.add(tf.keras.layers.Input(shape=(2,)))
    for i, units in enumerate(layers):
        model.add(tf.keras.layers.Dense(units=units, activation='relu', name=f'hidden_layer_{i+1}'))
    model.add(tf.keras.layers.Dense(units=1, name='output_layer'))
    model.compile(optimizer='adam', loss='mean_squared_error')
    return model

# Callback personalizado para enviar pesos y pérdida
class LiveWeightsAndLossCallback(tf.keras.callbacks.Callback):
    def __init__(self):
        super().__init__()
        self.epoch_losses = []

    def on_epoch_end(self, epoch, logs=None):
        weights_data = []
        for layer in self.model.layers:
            if isinstance(layer, tf.keras.layers.Dense):
                weights, biases = layer.get_weights()
                weights_data.append(weights.tolist())  # Convertir pesos a lista para enviarlos como JSON
        
        loss = logs.get('loss')
        self.epoch_losses.append(loss)
        
        # Emitir datos de pesos y pérdida
        socketio.emit('training_update', {
            'epoch': epoch,
            'weights': weights_data,
            'loss': loss
        })

@app.route('/')
def index():
    return render_template('index.html')

@socketio.on('start_training')
def handle_training(config):
    # Leer la configuración desde el frontend
    layers = config.get('layers', [10, 10, 10])  # Capas predeterminadas
    epochs = config.get('epochs', 50)

    # Crear el modelo dinámicamente
    global model
    model = create_model(layers)

    # Generar datos de entrenamiento
    a_values = np.random.uniform(1, 10, 500)
    b_values = np.random.uniform(1, 10, 500)
    c_values = np.sqrt(a_values**2 + b_values**2)
    X = np.column_stack((a_values, b_values))
    y = c_values

    # Entrenar el modelo con el callback
    model.fit(X, y, epochs=epochs, callbacks=[LiveWeightsAndLossCallback()], verbose=0)

if __name__ == '__main__':
    socketio.run(app, debug=True)
