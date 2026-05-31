# Plan de trabajo - Laravel Domo

Este documento resume lo que existe hoy, lo que falta, y que se puede mejorar. Incluye el como y el por que de cada propuesta.

## Estado actual (lo que hay)
- Paquete Laravel (library) con compatibilidad 11.x/12.x y PHP 8.2+, PSR-4 en `src/`.
- Comandos: `domo:serve` y `domo:tui`.
- Configuracion central en `config/domo.php` (dashboard, TUI, MCP, AI drivers, database).
- Proveedor `DomoServiceProvider` registra rutas, comandos y bindings.
- Dashboard web con rutas y controlador: index, schema, models, analyze (GET/POST).
- Vistas Blade basicas para dashboard, schema y models.
- Servicios base: Schema Analyzer, MigrationGenerator/Previewer, MCP server, AI drivers (OpenAI/Anthropic) y TUI (DomoTuiApp/Screens/Widgets).
- Documentacion inicial: README, QUICKSTART, STRUCTURE, UI_ARCHITECTURE, TUI_SERVE_IMPLEMENTATION.
- Tests base en `test/Unit` y `test/Feature` (estructura existente).

## Pendientes/faltantes
- `DashboardServer::start()` no inicia servidor real (TODO).
- `DomoServeCommand` solo muestra instrucciones, no arranca el server.
- Vista `resources/views/dashboard/analyze.blade.php` no existe, pero la ruta si.
- AI drivers (`OpenAIDriver`, `AnthropicDriver`) estan en stub y lanzan excepcion.
- `DomoMcpServer::start/stop` no implementados.
- `MigrationPreviewer` sin logica real.
- Schema analyzer usa consultas tipo MySQL (DESCRIBE/SHOW INDEX) y no cubre bien otros drivers.
- `TableCollector` solo soporta MySQL para tablas, columnas e indices.
- TUI con pantallas base, pero sin flujo completo para migraciones, export/import y AI.

## Mejoras y nuevas oportunidades (que, como, por que)
- Separar logica por driver (MySQL/Postgres/SQLite/SQL Server).
  - Como: clases por driver + factory, o usar `Schema` y `Doctrine` cuando aplique.
  - Por que: evitar errores y habilitar soporte real multi-DB.
- Completar dashboard de AI (vista + endpoint real).
  - Como: crear `analyze.blade.php`, conectar controlador con AI driver y validar inputs.
  - Por que: las rutas ya existen y el UI lo promete.
- Implementar servidor integrado para `domo:serve`.
  - Como: wrapper sobre `php artisan serve` o `symfony/process`.
  - Por que: evita friccion y cumple la promesa del comando.
- Implementar MCP real.
  - Como: servidor HTTP/JSON-RPC basico con rutas y handlers.
  - Por que: desbloquea integracion con agentes externos.
- Preview real de migraciones.
  - Como: usar `Schema::getConnection()->getDoctrineSchemaManager()` o parsear migraciones.
  - Por que: reduce riesgo antes de aplicar cambios.
- Caching y performance.
  - Como: cache por tabla/consulta y expiracion configurada.
  - Por que: bases con muchas tablas pueden ser lentas.
- Seguridad y validacion.
  - Como: validar inputs en endpoints, limitar tablas excluidas desde config.
  - Por que: evita acceso accidental a tablas sensibles.
- Mejoras UX (web/TUI).
  - Como: busqueda, filtros, paginacion, estados de carga, feedbacks.
  - Por que: mejora la usabilidad real con DBs medianas/grandes.

## Plan sugerido por fases
### Fase 1 - Basico funcional
- Implementar `DashboardServer::start()` y actualizar `DomoServeCommand`.
- Crear vista `dashboard/analyze.blade.php` y wiring del controlador.
- Corregir `SchemaAnalyzer` para usar queries segun driver.

### Fase 2 - AI + MCP
- Implementar `AiDriverInterface` con SDK real (OpenAI/Anthropic).
- Implementar `DomoMcpServer::start/stop` y routing.
- Agregar pruebas unitarias para AI/MCP y validaciones.

### Fase 3 - Migraciones y export
- Completar `MigrationPreviewer` y UI.
- Integrar `MigrationGenerator` con UI y TUI.
- Agregar export/import (SQL y migraciones).

### Fase 4 - Calidad y DX
- Caching, logs, metadatos de performance.
- Documentacion tecnica mas precisa (API, ejemplos, compatibilidad por driver).
- CI con mas checks (phpstan nivel alto, coverage minimo, lint estricto).

## Notas de ejecucion
- Priorizar lo que ya esta en el UI/Docs para alinear expectativas.
- Mantener compatibilidad 11-13 y strict types en todo nuevo codigo.
- No introducir secretos ni claves en el repo.
