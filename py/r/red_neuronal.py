import pandas as pd
import tkinter as tk
from tkinter import messagebox
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
from sklearn.neural_network import MLPRegressor

# Cargar y preparar datos
file_path = r"C:\xampp_php5\htdocs\php5\py\r\StudentsPerformance.csv"
datos = pd.read_csv(file_path)
X = datos[['género', 'raza/etnicidad', 'nivel de educación de los padres', 'almuerzo', 'curso de preparación para el examen']]
y = datos[['calificación de matemáticas', 'calificación de lectura', 'calificación de escritura']]
X = pd.get_dummies(X)
scaler_y = MinMaxScaler()
y_scaled = scaler_y.fit_transform(y)

# Crear y entrenar el modelo
model = MLPRegressor(hidden_layer_sizes=(2, 3), max_iter=1000, random_state=0)
model.fit(X, y_scaled)


# Crear la ventana principal
root = tk.Tk()
root.title("Predicción de Calificaciones")
root.geometry("400x300")

# Variables para los inputs
genero_var = tk.StringVar()
raza_var = tk.StringVar()
educacion_var = tk.StringVar()
almuerzo_var = tk.StringVar()
preparacion_var = tk.StringVar()

# Función para predecir
def predecir():
    # Crear DataFrame con los valores ingresados
    input_data = pd.DataFrame([[genero_var.get(), raza_var.get(), educacion_var.get(), almuerzo_var.get(), preparacion_var.get()]],
                              columns=['género', 'raza/etnicidad', 'nivel de educación de los padres', 'almuerzo', 'curso de preparación para el examen'])
    input_data = pd.get_dummies(input_data).reindex(columns=X.columns, fill_value=0)

    # Realizar la predicción
    pred_scaled = model.predict(input_data)
    pred_unscaled = scaler_y.inverse_transform(pred_scaled)
    
    # Mostrar resultados en un cuadro de diálogo
    messagebox.showinfo("Predicción", 
                        f"Calificación de Matemáticas: {pred_unscaled[0][0]:.2f}\n"
                        f"Calificación de Lectura: {pred_unscaled[0][1]:.2f}\n"
                        f"Calificación de Escritura: {pred_unscaled[0][2]:.2f}")

# Crear los campos y etiquetas
tk.Label(root, text="Género (male/female):").pack()
tk.Entry(root, textvariable=genero_var).pack()

tk.Label(root, text="Raza/etnicidad (group A, B, C, D, E):").pack()
tk.Entry(root, textvariable=raza_var).pack()

tk.Label(root, text="Nivel de educación de los padres:").pack()
tk.Entry(root, textvariable=educacion_var).pack()

tk.Label(root, text="Almuerzo (standard/free/reduced):").pack()
tk.Entry(root, textvariable=almuerzo_var).pack()

tk.Label(root, text="Curso de preparación para el examen (completed/none):").pack()
tk.Entry(root, textvariable=preparacion_var).pack()

# Botón para realizar la predicción
tk.Button(root, text="Predecir", command=predecir).pack()

# Iniciar el loop de la interfaz
root.mainloop()
