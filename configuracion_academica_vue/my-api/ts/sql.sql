CREATE TABLE ACTIVIDAD (
    ID_ACTIVIDAD NUMBER PRIMARY KEY,
    NOMBRE_ACTIVIDAD VARCHAR2(100),
    ID_JERARQUIA NUMBER REFERENCES JERARQUIA(ID_JERARQUIA), -- 1 = Universidad, 2 = Facultad, 3 = Especialidad
    ID_FACULTAD NUMBER REFERENCES FACULTAD(ID_FACULTAD), -- Este campo será NULL si la actividad es para todas las facultades
    ID_ESPECIALIDAD NUMBER REFERENCES ESPECIALIDAD(ID_ESPECIALIDAD), -- NULL si es para todas las facultades o a nivel universidad

    -- Nuevas columnas solicitadas
    PERIODO VARCHAR2(5), -- Ejemplo: '20241' o '20242'
    COD_ACTI VARCHAR2(10), -- Ejemplo: 'TI', 'EF'

    ESTADO CHAR(1) CHECK (ESTADO IN ('A', 'I')), -- 'A' para activo, 'I' para inactivo
    ID_MEDIO NUMBER REFERENCES MEDIO(ID_MEDIO), -- Referencia a la tabla medio
    ID_RESPONSABLE NUMBER REFERENCES RESPONSABLE(ID_RESPONSABLE), -- Referencia a la tabla responsable
    ID_PROCESA NUMBER REFERENCES PROCESA(ID_PROCESA), -- Referencia a la tabla procesa
    OBSERVACION VARCHAR2(1000), -- Texto libre para observaciones
    
    -- Columnas para FullCalendar
    titulo VARCHAR2(255) NOT NULL,
    descripcion VARCHAR2(1000),
    fecha_inicio TIMESTAMP NOT NULL,
    fecha_fin TIMESTAMP,  -- Puede ser NULL si es un evento de un solo día o indefinido
    tipo_actividad VARCHAR2(50) CHECK (tipo_actividad IN ('Un Día', 'Rango de Fechas', 'Hasta Fecha')),
    todo_el_dia CHAR(1) CHECK (todo_el_dia IN ('Y', 'N')),
    color VARCHAR2(20),

    -- Restricción para asegurar que la fecha de fin sea igual o posterior a la fecha de inicio
    CONSTRAINT ck_fecha CHECK (fecha_fin IS NULL OR fecha_fin >= fecha_inicio)
);
