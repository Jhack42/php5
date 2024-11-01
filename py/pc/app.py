import wmi
import time

# Inicializa WMI en el espacio de nombres predeterminado
w = wmi.WMI(namespace="root/CIMV2")

def obtener_temperaturas():
    temperaturas = {}
    for sensor in w.Win32_TemperatureProbe():
        temperaturas[sensor.Name] = sensor.CurrentTemperature
    return temperaturas

# Monitoreo en tiempo real
while True:
    temperaturas = obtener_temperaturas()
    if temperaturas:
        for componente, temp in temperaturas.items():
            # Conversión de temperatura a Celsius (si es necesario)
            celsius_temp = (temp - 2732) / 10.0
            print(f"{componente}: {celsius_temp}°C")
    else:
        print("No se encontraron datos de temperatura.")
    time.sleep(5)  # Actualiza cada 5 segundos
