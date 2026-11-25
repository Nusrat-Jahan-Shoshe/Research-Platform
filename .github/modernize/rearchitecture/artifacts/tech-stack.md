# Technology stack

- PHP 8.4; Laravel 13.34.0; PHPUnit 12.5.37; Laravel Pint 1.32.1.
- Laravel Boost 2.10.1, Cashier 16.8.0, Reverb 1.12.0, Tinker 3.0.2.
- Node/Vite frontend with Tailwind CSS 4, Laravel Vite plugin 3.1, Laravel Echo 2.5, Pusher JS 8.6.
- Persistence defaults to SQLite locally and can use MySQL in Compose/Azure. Sessions, cache, and queues default to database drivers in `.env.example`.
- External integrations: OpenRouter-compatible AI, Gemini-compatible AI, Groq, Ollama, optional Supabase REST, Crossref DOI lookup, Stripe Cashier.
- CI uses GitHub Actions, PHP 8.4, Node 22, Composer, npm, PHPUnit, and Vite.
- Deployment uses Azure App Service startup scripts and Nginx, with queue and Reverb processes launched from startup scripts.
