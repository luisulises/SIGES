# Pendientes – Epic 3 (Seguimiento y evidencias)

Este archivo registra decisiones y pendientes detectados en Epic 3.

## Resuelto (implementado)

- Se renombró la sección UI de **“Colaboración”** a **“Seguimiento del ticket”**.
- Para **cliente interno**, la visibilidad del comentario queda fija en **Público** (sin selector).
- Se simplificó el flujo de archivos: **los adjuntos se suben solo dentro de un comentario** (no hay adjunto “suelto” del ticket en la UI).
- Se habilitó **descarga** de adjuntos (endpoint dedicado) y se mantiene sin vista previa.

## Pendiente (por decidir / fuera de MVP)

- Vista previa de adjuntos (PDF/imágenes) vs descarga directa.
- Definir cómo manejar URLs firmadas cuando se migre a Wasabi (S3-compatible).
- Hardening adicional: antivirus/retención/cotas por ticket (según política).
