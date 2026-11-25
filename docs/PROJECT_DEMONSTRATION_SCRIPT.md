# Co-Auth Project Demonstration Script

## Demonstration objective

Co-Auth is a collaborative academic research platform for students, co-authors, and supervisors. It brings research planning, academic writing, citations, feedback, version history, media, real-time collaboration, and context-aware AI into one Laravel application.

The demonstration must begin by showing that the active project has a three-member team:

1. **Test User** — Student / primary author
2. **Mira Sen** — Co-author / research partner
3. **Dr. Alex Kim** — Supervisor / academic advisor

This three-person structure is already seeded for the project's demo workspaces and is the main collaboration scenario for the presentation.

## Short presentation script

### 1. Opening: the problem

“Academic research is usually divided between a document editor, a reference manager, email feedback, task lists, and separate AI chat tools. That fragmentation creates duplicate files, lost feedback, weak traceability, and AI answers that do not understand the actual research project.

Co-Auth solves this problem by providing one shared research workspace. The platform connects the research question, methodology, manuscript, evidence, citations, feedback, and AI assistance in a single project context.”

### 2. Team checkpoint

“Before opening the workspace, we verify the collaboration model. This project contains exactly three members: the student author, the co-author, and the supervisor.

The student owns the manuscript, the co-author contributes evidence and sections, and the supervisor reviews methodology and feedback. Each member is connected to the same project record and receives a role-specific view of the research process.”

For the local demonstration, use the seeded accounts from the project README:

- `test@example.com` — Test User
- `mira@example.com` — Mira Sen
- `alex@example.com` — Dr. Alex Kim

The local demo password is `password` for all three seeded accounts.

### 3. Platform overview

“The main screen is the research dashboard. It provides an overview of projects and their progress. From here, a researcher can open a project workspace, review tasks, check supervisor feedback, and continue the manuscript.”

Open the project **Adaptive Learning in Distributed Teams** and explain that the same three-member team is attached to it.

### 4. Research setup and methodology

“The project workspace begins with the research setup. The team can define the discipline, research question, methodology, expected outcome, and ethics status.

Co-Auth makes the methodology visible instead of treating it as an afterthought. The platform supports qualitative, quantitative, mixed-methods, design science, literature review, and action research workflows. When a methodology is selected, the interface presents its theoretical foundation, appropriate evidence, and research-rigor criteria.”

Demonstrate the methodology panel and the research lifecycle:

**Research question → Method and theory → Evidence and sources → Review and reflection → Contribution**

### 5. Manuscript workspace

“The manuscript editor is designed as an academic paper workspace rather than a generic text box. The team can write sections, format headings, insert lists and figures, and see paper-style output.

The workspace also provides live word count, character count, reading-time estimates, paper margins, column layouts, typography controls, themes, and a print-friendly presentation mode.”

Show the available layout options:

- Studio workspace for full collaboration
- Focus or Zen layout for distraction-free writing
- Reviewer split for manuscript and supervisor feedback
- Single-column and two-column paper formats

### 6. Collaboration, feedback, and versions

“The platform keeps collaboration inside the project. Tasks turn research decisions into visible next steps. The feedback desk keeps supervisor comments, author responses, and resolved decisions together. Each saved manuscript version creates an immutable version trail, so the team can understand how the research changed over time.

Presence and real-time events are supported through Laravel Echo and Laravel Reverb, allowing collaborators to see active editing and workspace updates.”

Show the three members, a task, a feedback item, and the version history in the project sidebar.

### 7. Citations, papers, and evidence

“Co-Auth treats evidence as part of the writing workflow. Researchers can maintain a citation library, insert in-text citation tags, look up DOI information, import BibTeX, and export references as BibTeX or RIS. Figures and other media can be uploaded or linked, numbered automatically, captioned, and inserted into the manuscript.

The same workspace can manage research papers and connect them to the project’s methodology and evidence trail.”

Demonstrate the seeded citations, the figure gallery, and one citation or figure insertion.

### 8. AI functionality

“The AI assistant is project-aware. It does not receive only an isolated prompt. Laravel loads the current project context, including the title, discipline, methodology, progress, open tasks, feedback, citations, papers, and collaborators, and sends that context to the configured AI gateway.

The assistant can summarize the current research state, recommend the next actions, evaluate supplied test data, and generate a methodology and section-governance matrix. The interface also supports model selection and research controls such as zero-hallucination guidance, methodology auditing, IMRaD compliance, peer-review critique, and LaTeX or statistical notation.”

Run the AI summary with this demonstration instruction:

> Summarize the current project state and recommend the next three research actions. Ground the response only in the project context and identify any unresolved supervisor feedback.

If no external AI key is configured, explain that the AI panel remains available but the live provider call is unavailable. Do not present a simulated AI response as a live result.

### 9. MCP integration

“Co-Auth also exposes a protected Model Context Protocol endpoint at `/mcp/research`. Its project context tool makes structured research information available to authorized external AI agents such as Claude, Cursor, or Gemini.

This is the bridge between the application’s database and external AI tools. Instead of asking an external agent to guess the state of a project, the agent can receive the project’s research question, methodology, collaborators, tasks, feedback, citations, papers, and media context through a controlled interface.”

### 10. Backend and architecture explanation

“The backend is built with PHP 8.4 and Laravel 13. Laravel provides authentication, routing, middleware, validation, controllers, Eloquent models, database migrations, API responses, events, and service integration.

The relational data layer is designed for MySQL and is accessed through Laravel Eloquent. The current local demo environment is running on SQLite for convenience; a MySQL deployment is configured through the normal Laravel database environment variables.

The browser interface uses Blade templates, modern JavaScript, Vite, and Tailwind CSS. JavaScript handles workspace interactions, live metrics, AI requests, presence updates, citations, media, templates, and export controls. Laravel Reverb and Laravel Echo support real-time browser updates.

For deployment, Nginx serves the public Laravel entry point, forwards PHP requests to PHP-FPM 8.4, and proxies WebSocket upgrade traffic for Reverb. The supplied Nginx configuration listens on port 8080 and uses `/home/site/wwwroot/public` as the web root for the Azure hosting layout.”

### 11. Closing

“Co-Auth is more than an online editor. It is a research operating environment. It connects a three-member academic team to a structured research lifecycle, traceable evidence, supervised review, version history, real-time collaboration, and AI that is grounded in the project’s own context.

The result is a clearer, more accountable path from research question to defensible academic contribution.”

## Key technical notes

### Core technology stack

- **Backend:** PHP 8.4 and Laravel 13.34
- **Database layer:** Laravel Eloquent with migrations; MySQL-ready and SQLite-supported for the local demo
- **Frontend:** Blade views, modern JavaScript, Vite, Tailwind CSS, Laravel Echo, and Pusher-compatible browser events
- **Real-time layer:** Laravel Reverb WebSockets
- **AI layer:** `AiGateway` with configurable OpenRouter, Gemini, Groq, or local Ollama-compatible endpoints
- **AI context bridge:** Laravel MCP endpoint at `/mcp/research`
- **Web server:** Nginx with PHP-FPM 8.4 and WebSocket proxy support
- **Quality:** PHPUnit feature tests and Laravel Pint formatting

### Main platform modules

1. Authentication and ORCID-linked researcher profiles
2. Research project dashboard
3. Three-member project collaboration and role management
4. Research setup, methodology, ethics, and expected outcomes
5. Academic manuscript editor and paper layouts
6. Tasks, milestones, supervisor feedback, and review workflow
7. Immutable document versions and presence updates
8. Citation library, DOI lookup, BibTeX import, and BibTeX/RIS export
9. Papers, figures, media gallery, automatic figure numbering, and captions
10. Markdown, LaTeX, Word, print, and PDF-oriented export flows
11. AI summary, evaluation, and methodology-matrix features
12. Protected MCP project-context access for external AI agents

### Demonstration rules

- Start with the **three-member team checkpoint**.
- Use the seeded project and show real stored records before explaining advanced features.
- Demonstrate one complete path: project setup → manuscript → citation/figure → feedback/task → AI assistance → export.
- Explain the role of each team member during the workflow.
- Clearly distinguish a live AI provider response from an unavailable or fallback response.
- Do not claim that a feature is live unless it is visible in the running application or supported by the project implementation.
- Do not expose API keys, service credentials, or production environment values during the presentation.
- Use the Nginx file to explain deployment routing and PHP-FPM/WebSocket handling, not as a user-facing feature.

## Suggested five-minute timing

| Time | Demonstration section | Main message |
| --- | --- | --- |
| 0:00–0:30 | Opening and problem | Research work is fragmented across separate tools. |
| 0:30–1:00 | Three-member checkpoint | Student, co-author, and supervisor share one project. |
| 1:00–2:00 | Dashboard and workspace | The platform organizes the research lifecycle. |
| 2:00–3:00 | Manuscript, citations, media | Writing and evidence remain connected. |
| 3:00–4:00 | Tasks, feedback, versions, presence | Collaboration is traceable and reviewable. |
| 4:00–4:40 | AI and MCP | AI receives project context and follows research rules. |
| 4:40–5:00 | Architecture and closing | Laravel, database, JavaScript, real-time services, and Nginx form the platform foundation. |

