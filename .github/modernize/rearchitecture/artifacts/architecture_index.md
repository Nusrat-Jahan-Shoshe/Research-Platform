# Architecture Index

This index is not the full contract. Do not implement from this file alone; follow the artifact paths below.

## Global artifacts

- `unit_graph.yaml`: entrypoints, dependencies, exported signatures, dynamic entrypoints, and shared references.
- `wire_contracts.yaml`: API and external integration request/response/error contracts; filter by `unit`.
- `shared_modules.yaml`: cross-unit coupling and allowed shared fields; filter by `used_by_units`.
- `cross_unit_state.yaml`: session, presence, browser, queue, and configuration state; filter by writer/reader unit.
- `project-structure.md`, `tech-stack.md`, `data-model.md`: global context for structure, runtime, and persisted entities.

## Implementation Guide

Every unit below must read its `behavior.yaml`, `bindings.yaml`, and `unit_decomposition.yaml`, then filter global artifacts by unit name. Completion evidence must report artifacts read, preserved branches/side effects/contracts, unresolved or deferred items, and test/build/runtime results. `unit_decomposition.yaml` contains candidates only and must not be treated as a committed split.

- `auth`: `units/auth/*`; preserve auth redirects, validation, session, and logout behavior.
- `dashboard-and-pages`: `units/dashboard-and-pages/*`; preserve page guards, project membership, loading and navigation behavior.
- `project-api`: `units/project-api/*`; preserve project/member/version/presence contracts and architecture audit side effects.
- `collaboration-api`: `units/collaboration-api/*`; preserve membership checks, task/feedback validation and status transitions.
- `citation-io`: `units/citation-io/*`; preserve BibTeX/RIS/DOI wire shapes and import error behavior.
- `manuscript-export`: `units/manuscript-export/*`; preserve latest-version selection, downloadable MIME types, and escaping semantics.
- `media-and-papers-api`: `units/media-and-papers-api/*`; preserve upload/URL validation, storage paths, and ownership checks.
- `ai-api`: `units/ai-api/*`; preserve model selection, timeouts, provider fallback, access control, and API error contracts.
- `mcp-context`: `units/mcp-context/*`; preserve bearer authentication and project authorization before context disclosure.
- `ops-and-console`: `units/ops-and-console/*`; preserve CLI exit behavior, health output, and operational configuration.
