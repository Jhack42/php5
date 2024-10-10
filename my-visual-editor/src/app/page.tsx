'use client';  // Esto indica que el componente es un Client Component

import { useState, useEffect } from 'react';
import Image from 'next/image';

// Definimos el tipo de los componentes en el canvas
type Component = {
  type: 'text' | 'image';
  content?: string;
};

type Project = {
  id: number;
  nombre: string;
};

export default function Editor() {
  // Estado para almacenar componentes y proyectos
  const [canvasContent, setCanvasContent] = useState<Component[]>([]);
  const [projects, setProjects] = useState<Project[]>([]);
  const [currentProject, setCurrentProject] = useState<number | null>(null);
  const [projectName, setProjectName] = useState('');

  // Función que se ejecuta cuando se suelta un componente en el canvas
  const onDrop = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    const componentType = e.dataTransfer.getData('component');
    setCanvasContent([...canvasContent, { type: componentType as 'text' | 'image', content: '' }]);
  };

  // Función que se ejecuta cuando un componente está siendo arrastrado sobre el canvas
  const onDragOver = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
  };

  // Función para cargar los proyectos desde la API
  const loadProjects = async () => {
    const response = await fetch('/api/projects');
    const data = await response.json();
    setProjects(data);
  };

  // Función para crear un nuevo proyecto
  const createProject = async () => {
    const response = await fetch('/api/projects', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre: projectName }),
    });
    const newProject = await response.json();
    setProjects([...projects, newProject]);
    setProjectName('');
  };

  // Función para guardar el contenido del proyecto actual
  const saveProject = async () => {
    if (currentProject !== null) {
      const response = await fetch(`/api/projects/${currentProject}/components`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ components: canvasContent }),
      });
      const data = await response.json();
      console.log('Componentes guardados', data);
    }
  };

  // Cargar los proyectos al montar el componente
  useEffect(() => {
    loadProjects();
  }, []);

  return (
    <div style={{ display: 'flex' }}>
      {/* Menú lateral con proyectos y componentes */}
      <aside style={{ width: '200px', padding: '10px', borderRight: '1px solid #ccc' }}>
        {/* Crear un nuevo proyecto */}
        <input
          type="text"
          value={projectName}
          placeholder="Nombre del proyecto"
          onChange={(e) => setProjectName(e.target.value)}
        />
        <button onClick={createProject}>Crear Proyecto</button>

        <hr />

        {/* Lista de proyectos */}
        <div>
          <h4>Proyectos</h4>
          {projects.map((project) => (
            <div key={project.id}>
              <button onClick={() => setCurrentProject(project.id)}>{project.nombre}</button>
            </div>
          ))}
        </div>

        <hr />

        {/* Componentes para arrastrar */}
        <div
          draggable
          onDragStart={(e) => e.dataTransfer.setData('component', 'text')}
        >
          Texto
        </div>
        <div
          draggable
          onDragStart={(e) => e.dataTransfer.setData('component', 'image')}
        >
          Imagen
        </div>
        <button onClick={saveProject}>Guardar Proyecto</button>
      </aside>

      {/* Área de trabajo (canvas) */}
      <main
        onDrop={onDrop}
        onDragOver={onDragOver}
        style={{ flex: 1, minHeight: '500px', border: '1px solid #ccc', padding: '10px' }}
      >
        {canvasContent.map((component, index) => {
          if (component.type === 'text') {
            return <p key={index} contentEditable="true">Escribe tu texto aquí</p>;
          } else if (component.type === 'image') {
            return (
              <Image
                key={index}
                src="https://via.placeholder.com/150"
                alt="Imagen"
                width={150}
                height={150}
              />
            );
          }
          return null;
        })}
      </main>
    </div>
  );
}
