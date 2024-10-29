import tensorflow as tf
import numpy as np
import datetime

# Datos de entrada (grados Celsius) y salida (grados Fahrenheit)
celsius = np.array([-40, -10, 0, 8, 15, 22, 38], dtype=float)
fahrenheit = np.array([-40, 14, 32, 46, 59, 72, 100], dtype=float)

# Definimos un modelo simple de red neuronal
model = tf.keras.Sequential([
    tf.keras.layers.Dense(units=4, input_shape=[1], activation='relu'),
    tf.keras.layers.Dense(units=4, activation='relu'),
    tf.keras.layers.Dense(units=1)
])

# Compilamos el modelo
model.compile(
    optimizer=tf.keras.optimizers.Adam(0.1),
    loss='mean_squared_error'
)

# Configuramos TensorBoard
log_dir = "logs/fit/" + datetime.datetime.now().strftime("%Y%m%d-%H%M%S")
tensorboard_callback = tf.keras.callbacks.TensorBoard(log_dir=log_dir, histogram_freq=1)

# Entrenamos el modelo con los datos de entrada y salida
history = model.fit(
    celsius, fahrenheit, epochs=500, verbose=0, 
    callbacks=[tensorboard_callback]
)

# Hacemos una predicción
print("Predicción de 100 grados Celsius en Fahrenheit:")
print(model.predict([100.0]))

# Lanzamos TensorBoard
%load_ext tensorboard
%tensorboard --logdir logs/fit
