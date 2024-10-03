// Importamos el decorador Component de Angular para definir un componente
import { Component } from '@angular/core';

// Importamos RouterOutlet, que permite que este componente utilice rutas y muestre otros componentes según la ruta activa
import { RouterOutlet } from '@angular/router';

// Decorador que marca esta clase como un componente Angular
@Component({
  // El selector define cómo se insertará este componente en el HTML. Aquí, <app-root> se utiliza para insertar el componente
  selector: 'app-root',

  // 'standalone: true' indica que este es un componente independiente, lo que significa que no necesita estar registrado en un módulo.
  standalone: true,

  // 'imports' permite importar módulos o funcionalidades adicionales. Aquí importamos RouterOutlet para manejar las rutas.
  imports: [RouterOutlet],

  // 'templateUrl' indica la ubicación del archivo HTML asociado a este componente
  templateUrl: './app.component.html',

  // 'styleUrl' indica la ubicación del archivo CSS que define los estilos de este componente
  styleUrl: './app.component.css'
})
export class AppComponent {
  // Aquí definimos una propiedad 'title' que puede ser utilizada en el HTML de este componente
  title = 'proyecto_angular';
}
