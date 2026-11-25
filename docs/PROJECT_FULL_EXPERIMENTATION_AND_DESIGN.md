# Co-Auth Full Experimentation and Design Plan

## 1. Purpose

Co-Auth is a collaborative academic research studio. The product brings the research question, methodology, evidence, manuscript, citations, feedback, tasks, version history, real-time collaboration, and context-aware AI into one project workspace.

This document is the working plan for taking the current Laravel application from a verified baseline through product design, focused experimentation, implementation, and final demonstration. It complements the architecture evidence in `.github/modernize/rearchitecture/artifacts/` and the presentation script in `PROJECT_DEMONSTRATION_SCRIPT.md`.

The plan preserves two existing visual requirements:

- Theme controls keep their current position and behavior.
- Workspace content starts one centimetre below the top edge of the main content area.

## 2. Product promise

> A three-person research team can move from question to defensible contribution without losing the evidence, review decisions, or context behind the work.

The application should make five relationships visible:

1. Research question → methodology.
2. Methodology → evidence and citations.
3. Evidence → manuscript claims, figures, and papers.
4. Review → tasks, feedback, and version history.
5. Project context → grounded AI guidance and the next research step.

## 3. Team model

The demonstration and the default product story use exactly three project members:

| Member | Role | Primary responsibility |
| --- | --- | --- |
| Test User | Student / primary author | Owns the manuscript and research execution. |
| Mira Sen | Co-author / research partner | Contributes evidence, citations, and draft sections. |
| Dr. Alex Kim | Supervisor / academic advisor | Reviews methodology, feedback, limitations, and research quality. |

The seeded accounts are `test@example.com`, `mira@example.com`, and `alex@example.com`. The demonstration may use the shared local password documented in the project README, but credentials must never be shown in a public recording or committed to production configuration.

## 4. Current system baseline

### Core technology

- PHP 8.4 and Laravel 13.34.
- Eloquent persistence with SQLite for the local demo and MySQL-ready configuration.
- Blade-rendered pages with JavaScript, Vite, and Tailwind CSS.
- Laravel Echo and Reverb for presence and real-time events.
- `AiGateway` for configurable OpenRouter, Gemini, Groq, or Ollama-compatible providers.
- Laravel MCP project context at `/mcp/research`.
- Nginx + PHP-FPM 8.4, with WebSocket proxying for Reverb.
- PHPUnit feature/unit coverage and Laravel Pint formatting.

### Main runtime areas

1. Authentication and researcher identity.
2. Dashboard and project workspace.
3. Research setup, methodology, ethics, and lifecycle.
4. Manuscript editing, paper layouts, themes, and formatting.
5. Tasks, milestones, supervisor feedback, and review.
6. Immutable document versions and active-editor presence.
7. Citation library, DOI lookup, BibTeX import, and BibTeX/RIS export.
8. Papers, figures, media, numbering, captions, and insertion.
9. AI summary, evaluation, methodology matrix, and research chat.
10. MCP project context and integration health.

### Important coupling points

- `resources/js/workspace.js` coordinates most workspace interactions.
- `resources/css/app.css` contains the visual system, responsive rules, themes, paper layouts, and AI surfaces.
- `ResearchProjectController` composes the project context and workspace payload.
- `AiGateway` controls provider selection, model selection, and external AI calls.
- Startup scripts launch queue and Reverb processes; this must be checked carefully for multi-instance deployments.

## 5. End-to-end user journey

```text
Sign in
  → verify three-member team
  → open research project
  → complete methodology setup
  → write or load manuscript structure
  → attach citation, paper, or figure evidence
  → assign task / add supervisor feedback
  → save a version
  → ask AI using the paper context
  → review recommended next step
  → export manuscript or references
```

Each transition must have a visible confirmation, a useful empty state, and a recoverable error state. The user should never have to infer whether a change was saved, whether an AI response is live, or which project context informed the recommendation.

## 6. AI design contract

### Context supplied to AI

AI requests must be grounded in the current project record and must use only context available to the authorized project member. The context envelope should include:

- Project title, discipline, description, progress, methodology, research question, expected outcome, and ethics status.
- The three collaborators and their roles.
- Open tasks and their status, priority, assignee, and due date.
- Open feedback, author, status, version link, and document offsets where available.
- Papers, citations, media, figures, and the current manuscript version.

### AI response behavior

The assistant must:

1. State when the provider is unavailable instead of fabricating a result.
2. Separate facts from recommendations.
3. Identify the project evidence behind a recommendation.
4. Surface unresolved supervisor feedback before suggesting new work.
5. Prefer one concrete next action over a generic list.
6. Keep the recommendation scoped to the selected research methodology.
7. Never disclose another project’s data through either the normal API or MCP.

### Recommended context prompt

> Review the current paper context. Summarize the strongest unresolved research issue, identify the evidence already available for it, and recommend the next action for the three-person team. Separate stored project facts from your recommendation. If the project does not contain enough evidence, say exactly what is missing.

### AI experiment set

| Experiment | Hypothesis | Method | Success evidence |
| --- | --- | --- | --- |
| Context grounding | Project-aware prompts produce more actionable recommendations than an isolated prompt. | Compare the same instruction with and without project context. | Recommendation names a stored task, feedback item, citation, or missing evidence. |
| Methodology fit | Method-specific constraints reduce generic AI advice. | Run the prompt for each supported methodology. | Output references the selected methodology’s rigor criteria. |
| Feedback prioritization | Open supervisor feedback is a better next-step signal than project progress alone. | Give AI projects with different feedback states. | First recommendation addresses the highest-impact unresolved feedback. |
| Hallucination resistance | Explicit zero-hallucination instructions reduce unsupported claims. | Use a paper with intentionally incomplete evidence. | AI labels missing evidence and does not invent authors, results, or citations. |
| Team coordination | Role-aware recommendations reduce duplicate work. | Run the same prompt with student, co-author, and supervisor roles visible. | Recommendation names an appropriate owner and avoids assigning supervisor-only work to the author. |

## 7. Frontend design contract

### Alignment rules

- The workspace main content begins exactly `1cm` below the top of its content box.
- The title, description, action controls, progress indicator, lifecycle rail, editor, and sidebar share the same content grid.
- The heading aligns from the top edge; the progress indicator must not vertically center against a multi-line title.
- AI action buttons wrap as a coherent group and do not push the paper or theme controls.
- The theme switcher and layout switcher retain their existing topbar position.
- Desktop uses a two-column editor/sidebar grid; tablet and mobile collapse in a predictable order: editor, feedback, team, versions, AI.
- All section headings use a consistent left edge, spacing scale, and line-height.
- Long paper titles wrap without overlapping status badges or the progress indicator.

### Theme safety rules

- Theme changes must remain scoped to the workspace root class.
- Do not move the theme switcher while changing workspace spacing.
- Do not change dark-mode colors as part of a spacing fix.
- Preserve the print stylesheet and paper-layout controls.
- Verify Studio, Focus, Review, White, Sepia, and Dark states after any layout change.

### Visual experiment set

| Experiment | Hypothesis | Method | Success evidence |
| --- | --- | --- | --- |
| Top spacing | One centimetre of breathing room improves hierarchy without hiding content. | Compare the workspace at desktop, tablet, and mobile widths. | Title and first action row are visible without overlap or unexpected horizontal scroll. |
| Action grouping | Grouping AI actions above the lifecycle improves discoverability. | Run the demo with and without grouped controls. | A first-time user can find the assistant and next-step action without instruction. |
| Paper context | A readable paper surface improves sustained writing. | Compare Studio and Focus layouts with the same manuscript. | Users can identify section, evidence, and feedback state at a glance. |
| Responsive collapse | Ordered single-column layout reduces mobile cognitive load. | Test 520px, 850px, and desktop breakpoints. | No control is clipped; keyboard focus follows visual order. |

## 8. Implementation phases

### Phase 0 — Baseline and guardrails

- Confirm the current Git state before each change.
- Keep the three-member seed scenario intact.
- Preserve the theme switcher position and the `1cm` workspace top padding.
- Run the current PHPUnit suite and Vite build as the baseline.
- Record any provider, database, or browser-runtime limitation instead of masking it.

### Phase 1 — Product surface and visual system

- Normalize the workspace grid and shared section alignment.
- Check title wrapping, action wrapping, lifecycle rail, editor canvas, sidebar, and responsive breakpoints.
- Keep theme/layout controls visually stable while improving surrounding spacing.
- Add only reusable CSS rules; avoid one-off inline positioning.
- Verify keyboard focus, contrast, print layout, and narrow viewport behavior.

### Phase 2 — Research context and evidence flow

- Make the relationship between methodology, evidence, manuscript, and feedback explicit in the workspace.
- Ensure citations, papers, figures, tasks, feedback, and versions are visible from the project context.
- Make empty and loading states explain the next user action.
- Verify membership authorization for every project-scoped action.

### Phase 3 — AI research copilot

- Keep the existing model and provider controls.
- Add context-aware prompts for summary, evidence gaps, methodology audit, and next recommended action.
- Display the active provider/model and live/unavailable status.
- Keep AI output in the project conversation history without exposing secrets.
- Test normal AI API responses, validation failures, provider failures, and unauthorized access.

### Phase 4 — Collaboration and review

- Demonstrate the student, co-author, and supervisor roles in one project.
- Connect feedback to a manuscript version and task.
- Show version creation and active-editor presence.
- Verify that the workspace communicates saved state and update state clearly.

### Phase 5 — Export and deployment readiness

- Verify LaTeX, Word-compatible, print/PDF, BibTeX, and RIS paths.
- Confirm escaping of user-authored manuscript and citation content.
- Check Nginx PHP-FPM routing and Reverb WebSocket proxy behavior.
- Document the queue/Reverb per-instance behavior before any scale-out deployment.

## 9. Verification matrix

| Area | Command or evidence | Pass condition |
| --- | --- | --- |
| Backend behavior | `php artisan test --compact` | Full suite passes with no unexpected warnings. |
| PHP style | `vendor/bin/pint --dirty --format agent` after PHP edits | Formatter completes cleanly. |
| Frontend build | `npm.cmd run build` | Vite build completes and manifest is generated. |
| Route surface | `php artisan route:list` | Expected web, API, AI, and MCP entrypoints exist. |
| Three-member seed | Database seed/demo workspace inspection | Exactly the three documented demo members are present. |
| Project authorization | Feature tests for member/non-member requests | Member access succeeds; unrelated user receives the documented denial. |
| AI provider state | AI status endpoint and summary request | Live provider state is visible; unavailable state is explicit. |
| MCP security | MCP feature tests | Invalid bearer and unauthorized project requests disclose no context. |
| Alignment | Browser viewport checks at desktop/tablet/mobile | Shared left edges, 1cm top offset, stable theme controls, no clipping. |
| Export | Citation and manuscript export tests | MIME type, content disposition, escaping, and latest-version behavior pass. |

## 10. Demonstration runbook

1. Start by showing the three-member team checkpoint.
2. Open the seeded research project and name the research question.
3. Show methodology and the lifecycle from question to contribution.
4. Open the manuscript and point out the aligned title, action row, lifecycle rail, editor, and sidebar.
5. Insert or inspect a citation and figure so evidence is visibly connected to writing.
6. Add or review a task and supervisor feedback item.
7. Save a version and show the version trail.
8. Ask AI: “Review the current paper context and recommend the next research action, grounded only in stored project evidence.”
9. Explain the provider/model state and distinguish live output from an unavailable provider.
10. Show export options and close with Laravel, MySQL-ready persistence, JavaScript frontend behavior, AI, Reverb, MCP, and Nginx.

## 11. Definition of done

The project is ready for the final demonstration when:

- The workspace tells one coherent research story from question to next action.
- The three-member collaboration scenario is visible and reproducible.
- The title, action controls, paper content, lifecycle, and sidebar are aligned at supported breakpoints.
- Theme and layout controls remain in their intended position.
- AI responses are grounded in authorized project context and clearly identify unavailable providers.
- Citations, papers, media, tasks, feedback, versions, and exports are demonstrably connected.
- Backend tests and frontend build pass after the final change set.
- Browser/runtime evidence confirms the visual result rather than relying only on source inspection.
- Any deployment limitation, external credential requirement, or unverified provider behavior is explicitly disclosed.

