from flask import Flask, render_template, request
from flask_socketio import SocketIO, emit
import pandas as pd
import tensorflow as tf
from sklearn.preprocessing import StandardScaler
from io import StringIO
from test_model import ModelTester  # Nuevo módulo para pruebas

app = Flask(__name__)
socketio = SocketIO(app)

data = None
tester = None  # Instancia para probar el modelo

def detect_and_clean_numeric_columns(df):
    """Detect columns with numbers in formatted strings (e.g., $ 360.000.000) and clean them."""
    cleaned_columns = []
    for column in df.columns:
        if df[column].dtype == 'object':
            if df[column].str.contains(r'[0-9]', regex=True).any():
                df[column] = pd.to_numeric(df[column].replace({r'[^\d.]': ''}, regex=True), errors='coerce')
                cleaned_columns.append(column)
    return cleaned_columns

@socketio.on('upload_data')
def handle_upload(data_string):
    global data
    try:
        data = pd.read_csv(StringIO(data_string))
        cleaned_columns = detect_and_clean_numeric_columns(data)
        preview_html = data.head().to_html()
        emit('data_uploaded', {
            'message': 'Datos cargados correctamente',
            'preview': preview_html,
            'columns': data.columns.tolist(),
            'cleaned_columns': cleaned_columns
        })
    except Exception as e:
        emit('data_error', {'message': f'Error al cargar datos: {str(e)}'})

@socketio.on('clean_data')
def handle_clean():
    global data
    if data is None:
        emit('data_error', {'message': 'No se han cargado datos. Por favor, sube un archivo.'})
        return
    try:
        data.fillna(0, inplace=True)
        emit('data_cleaned', {'message': 'Datos limpiados correctamente'})
    except Exception as e:
        emit('data_error', {'message': f'Error al limpiar datos: {str(e)}'})

@socketio.on('convert_numeric')
def handle_convert_numeric():
    global data
    if data is None:
        emit('data_error', {'message': 'No se han cargado datos. Por favor, sube un archivo.'})
        return
    try:
        data = pd.get_dummies(data, drop_first=True)
        scaler = StandardScaler()
        numeric_cols = data.select_dtypes(include=['float64', 'int64']).columns
        data[numeric_cols] = scaler.fit_transform(data[numeric_cols])
        emit('data_converted', {'message': 'Datos convertidos a numéricos correctamente', 'preview': data.head().to_html()})
    except Exception as e:
        emit('data_error', {'message': f'Error al convertir datos: {str(e)}'})

@socketio.on('start_training')
def handle_training(config):
    global data, tester
    if data is None:
        emit('data_error', {'message': 'No se han cargado datos. Por favor, sube un archivo.'})
        return

    try:
        target_column = config.get('target_column')
        if target_column not in data.columns:
            emit('data_error', {'message': 'Columna objetivo no válida.'})
            return

        X = data.drop(target_column, axis=1)
        y = data[target_column]
        X = X.astype('float32')
        y = y.astype('float32')

        layers = config['layers']
        activation = config['activation']
        model = tf.keras.Sequential()
        model.add(tf.keras.layers.Input(shape=(X.shape[1],)))
        for units in layers:
            model.add(tf.keras.layers.Dense(units, activation=activation))
        model.add(tf.keras.layers.Dense(1))

        model.compile(optimizer='adam', loss=config['loss'])

        feature_names = X.columns.tolist()
        class TrainingCallback(tf.keras.callbacks.Callback):
            def on_epoch_end(self, epoch, logs=None):
                weights_data = []
                for layer in model.layers:
                    if isinstance(layer, tf.keras.layers.Dense):
                        weights, _ = layer.get_weights()
                        weights_data.append(weights.tolist())
                socketio.emit('training_update', {'epoch': epoch, 'loss': logs['loss'], 'weights': weights_data, 'feature_names': feature_names})

        model.fit(X, y, epochs=config['epochs'], callbacks=[TrainingCallback()], verbose=0)
        
        # Guardar el modelo en ambos formatos
        model.save('trained_model.h5')  # Guardar en formato .h5
        model.save('trained_model.keras', save_format='keras')  # Guardar en formato .keras
        
        tester = ModelTester('trained_model.h5', X.columns.tolist())  # Configura para pruebas
        emit('training_complete', {'message': 'Entrenamiento completado exitosamente y modelo guardado en .h5 y .keras'})
    except Exception as e:
        emit('data_error', {'message': f'Error durante el entrenamiento: {str(e)}'})

@socketio.on('test_model')
def handle_test_model(data):
    global tester
    if not tester:
        emit('test_error', {'message': 'El modelo no está cargado o entrenado.'})
        return
    try:
        prediction = tester.predict(data)
        emit('test_result', {'prediction': prediction.tolist()})
    except Exception as e:
        emit('test_error', {'message': f'Error al probar modelo: {str(e)}'})

@app.route('/')
def index():
    return render_template('index.html')

if __name__ == '__main__':
    socketio.run(app, debug=True)