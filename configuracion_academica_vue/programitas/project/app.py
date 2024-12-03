from flask import Flask, render_template
from flask_socketio import SocketIO, emit
import subprocess

app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app)

# Rutas a diferentes intérpretes
GIT_BASH_PATH = r"C:\Program Files\Git\bin\bash.exe"
CMD_PATH = "cmd"
POWERSHELL_PATH = "powershell"

# Directorio base
WORKING_DIRECTORY = r"C:\xampp_php5\htdocs\php5\configuracion_academica_vue"

@app.route('/')
def index():
    return render_template('index.html')

@socketio.on('execute_command')
def execute_command(data):
    command = data.get('command', '').strip()
    shell = data.get('shell', '').strip().lower()

    if not command:
        emit('command_result', {'output': 'Error: No se ingresó un comando.\n'})
        return

    try:
        # Seleccionar el intérprete adecuado
        if shell == "bash":
            executable = [GIT_BASH_PATH, "-c", f"cd {WORKING_DIRECTORY} && {command}"]
        elif shell == "cmd":
            executable = [CMD_PATH, "/c", f"cd {WORKING_DIRECTORY} && {command}"]
        elif shell == "powershell":
            executable = [POWERSHELL_PATH, "-Command", f"cd {WORKING_DIRECTORY}; {command}"]
        else:
            emit('command_result', {'output': f'Error: Intérprete "{shell}" no reconocido.\n'})
            return

        # Ejecutar el comando
        result = subprocess.run(
            executable,
            capture_output=True,
            text=True,
            shell=False  # Seguridad
        )

        # Procesar la salida
        output = result.stdout.strip() if result.stdout else result.stderr.strip()
        emit('command_result', {'output': output})
    except Exception as e:
        emit('command_result', {'output': f'Error al ejecutar el comando: {str(e)}\n'})

if __name__ == '__main__':
    socketio.run(app, debug=True)
