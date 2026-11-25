# Data model

## Entities

- `User`: account, ORCID/profile fields, teacher flag, trial/subscription identity.
- `ResearchProject`: owner, title/discipline/description/status/progress, methodology fields, paper layout, active editor presence.
- `project_members`: many-to-many user/project membership with role.
- `DocumentVersion`: project/author, JSON content, version number.
- `Citation`: project/added-by, bibliographic fields, generated in-text tag.
- `ProjectMedia`: project/uploader, file or URL, figure number and metadata.
- `ProjectTask`: project, assignee/creator, status, priority, due date.
- `ProjectFeedback`: project, author, optional document version, body/status/offsets.
- `Paper`: project/owner, title/abstract/status and versions.
- Subscription tables and Laravel cache/queue/session tables support Cashier and operational state.

## Relationships

Users own projects and participate through `project_members`. Projects own versions, citations, media, tasks, feedback, and papers. Feedback may point to a version. Tasks and feedback are user-authored. Foreign keys cascade project-owned records and null optional user references where specified.

## Transaction/state notes

Most controller writes are individual Eloquent writes without explicit transactions. Version creation broadcasts `DocumentVersionCreated`. Presence is mutable project state with a five-minute freshness check. Queue/cache/session behavior is environment-configured and defaults to database-backed stores.
