# Project structure

This is a full-stack Laravel application: PHP 8.4 backend, Blade pages, Vite frontend assets, relational persistence, optional Supabase sync, Laravel MCP, and Laravel Reverb.

## Layers

- HTTP entrypoints: `routes/web.php`, `routes/api.php`, `routes/ai.php`.
- Controllers: authentication, project lifecycle, collaboration, citations, exports, AI, media, papers, and integration health.
- Services: `AiGateway`, `IntegrationHealth`, and `SupabaseClient` centralize external calls.
- Models and migrations: Eloquent entities under `app/Models` and schema history under `database/migrations`.
- UI: Blade templates under `resources/views`, workspace behavior in `resources/js/workspace.js`, shared styling in `resources/css/app.css`.
- Operations: GitHub Actions, shell startup scripts, Azure Nginx configuration, and Docker Compose.

## Functional areas

Authentication; project workspace; research setup and formatting; manuscript versions; citations and import/export; tasks and feedback; figures and papers; AI assistance; MCP project context; integration health; architecture audit.

## Complexity observations

The main coupling centers are `workspace.js`, `AiGateway`, `ResearchProjectController`, and the startup scripts. Import/export and AI provider probing are synchronous request paths. Azure startup starts background processes per web instance, which creates scale-out duplication risk.
