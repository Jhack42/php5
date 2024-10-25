from manim import *

class TextoAnimacion(Scene):
    def construct(self):
        texto = Text("¡Absolutamente! 3Blue1Brown utiliza una librería en Python llamada Manim para crear sus animaciones tan visualmente atractivas y explicativas.")
        self.play(Write(texto))  # Aparece el texto
        self.wait(2)             # Espera para que el texto se mantenga en pantalla
        self.play(FadeOut(texto))  # Desaparece el texto
