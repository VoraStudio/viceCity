# 006 — Franja de acceso al blog antes del footer

**Stack**: css
**Estado**: aceptado
**Fecha**: 2026-10-06

## Contexto

Antes del footer, dentro de `#recursos`, quedaba una copia vacía de la franja lavanda de confianza, con el `id="confianca-titol"` repetido. Se necesita un acceso al blog.

## Decisión

- La copia se reutiliza como franja de acceso: título «Descobreix el nostre blog», flecha SVG y un CTA «Visitar el blog».
- El título usa la escala de los `<h2>` de sección: `text-3xl md:text-title-2`.
- La flecha es decorativa (`aria-hidden`) y hereda el color con `currentColor`. En móvil apunta hacia abajo (`rotate-90`) y desde `lg` hacia la derecha (`lg:rotate-0`), hacia el botón.
- El CTA es el botón primario de `tokens.md`: `h-11 rounded-full bg-purple-700 font-body font-medium text-white`, con el foco del resto de botones.
- El `id` del título pasa a `blog-titol`: un `id` repetido es HTML inválido y rompe `aria-labelledby`.

## Consecuencias

- El `href` es `#` porque la URL del blog no existe todavía. Hay que sustituirlo cuando se conozca.
- La franja queda dentro de `<section id="recursos">`, cuyo `aria-label` describe solo las tres tarjetas. Conviene decidir si debe ser su propia sección.
- La flecha mide `size-6` frente a un título de 40 px; puede quedar pequeña.
- La superficie `bg-lav-100/40` lleva texto, caso ya abierto en la decisión 004.
