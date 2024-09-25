const oracledb = require('oracledb');

// Obtener todos los datos de la vista
async function getVistaFacultadesEventos(req, res) {
  let connection;
  try {
    connection = await oracledb.getConnection();
    const result = await connection.execute(`SELECT * FROM PHP5.VISTA_FACULTADES_EVENTOS`);
    
    const data = result.rows.map(row => ({
      facultad_id: row[0],
      nombre_facultad: row[1],
      imagen_url: `http://localhost:3000/api/img/${row[2]}`, // URL completa de la imagen
      evento_id: row[3],
      periodo: row[4],
      cod_acti: row[5],
      descripcion: row[6],
      fecha_inicio: row[7],
      fecha_fin: row[8],
      estado: row[9],
      links: [
        {
          rel: 'self',
          href: `/api/vista-facultades-eventos/${row[0]}`,
          method: 'GET'
        },
        {
          rel: 'update',
          href: `/api/vista-facultades-eventos/${row[0]}`,
          method: 'PUT'
        },
        {
          rel: 'delete',
          href: `/api/vista-facultades-eventos/${row[0]}`,
          method: 'DELETE'
        }
      ]
    }));

    res.json(data);
  } catch (err) {
    res.status(500).send('Error en la consulta: ' + err.message);
  } finally {
    if (connection) {
      try {
        await connection.close();
      } catch (err) {
        console.error('Error al cerrar la conexión', err);
      }
    }
  }
}


// Crear un nuevo evento en la base de datos
async function createEvento(req, res) {
  let connection;
  try {
    connection = await oracledb.getConnection();
    const { facultad_id, periodo, cod_acti, descripcion, fecha_inicio, fecha_fin, estado } = req.body;

    const result = await connection.execute(
      `INSERT INTO PHP5.EVENTOS (FACULTAD_ID, PERIODO, COD_ACTI, DESCRIPCION, FECHA_INICIO, FECHA_FIN, ESTADO) 
      VALUES (:facultad_id, :periodo, :cod_acti, :descripcion, :fecha_inicio, :fecha_fin, :estado)`, 
      { facultad_id, periodo, cod_acti, descripcion, fecha_inicio, fecha_fin, estado },
      { autoCommit: true }
    );

    res.status(201).json({
      message: 'Evento creado exitosamente',
      links: [
        { rel: 'self', href: `/api/vista-facultades-eventos`, method: 'POST' },
        { rel: 'get', href: `/api/vista-facultades-eventos/${result.evento_id}`, method: 'GET' }
      ]
    });
  } catch (err) {
    res.status(500).send('Error al crear el evento: ' + err.message);
  } finally {
    if (connection) {
      try {
        await connection.close();
      } catch (err) {
        console.error('Error al cerrar la conexión', err);
      }
    }
  }
}

// Actualizar un evento
async function updateEvento(req, res) {
  let connection;
  try {
    const { evento_id } = req.params;
    const { facultad_id, periodo, cod_acti, descripcion, fecha_inicio, fecha_fin, estado } = req.body;

    connection = await oracledb.getConnection();
    await connection.execute(
      `UPDATE PHP5.EVENTOS SET FACULTAD_ID=:facultad_id, PERIODO=:periodo, COD_ACTI=:cod_acti, 
      DESCRIPCION=:descripcion, FECHA_INICIO=:fecha_inicio, FECHA_FIN=:fecha_fin, ESTADO=:estado 
      WHERE EVENTO_ID=:evento_id`,
      { facultad_id, periodo, cod_acti, descripcion, fecha_inicio, fecha_fin, estado, evento_id },
      { autoCommit: true }
    );

    res.json({
      message: 'Evento actualizado exitosamente',
      links: [
        { rel: 'self', href: `/api/vista-facultades-eventos/${evento_id}`, method: 'GET' },
        { rel: 'update', href: `/api/vista-facultades-eventos/${evento_id}`, method: 'PUT' }
      ]
    });
  } catch (err) {
    res.status(500).send('Error al actualizar el evento: ' + err.message);
  } finally {
    if (connection) {
      try {
        await connection.close();
      } catch (err) {
        console.error('Error al cerrar la conexión', err);
      }
    }
  }
}

// Eliminar un evento
async function deleteEvento(req, res) {
  let connection;
  try {
    const { evento_id } = req.params;

    connection = await oracledb.getConnection();
    await connection.execute(
      `DELETE FROM PHP5.EVENTOS WHERE EVENTO_ID=:evento_id`,
      { evento_id },
      { autoCommit: true }
    );

    res.json({
      message: 'Evento eliminado exitosamente',
      links: [
        { rel: 'create', href: `/api/vista-facultades-eventos`, method: 'POST' }
      ]
    });
  } catch (err) {
    res.status(500).send('Error al eliminar el evento: ' + err.message);
  } finally {
    if (connection) {
      try {
        await connection.close();
      } catch (err) {
        console.error('Error al cerrar la conexión', err);
      }
    }
  }
}

module.exports = { getVistaFacultadesEventos, createEvento, updateEvento, deleteEvento };
