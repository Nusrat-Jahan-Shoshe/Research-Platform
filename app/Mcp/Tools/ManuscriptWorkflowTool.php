<?php

namespace App\Mcp\Tools;

use App\Models\ResearchProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Returns evidence and human-decision guardrails for an authorized manuscript-development stage.')]
class ManuscriptWorkflowTool extends Tool
{
    private const array STAGES = [
        'draft_initialization',
        'venue_selection',
        'authorship',
        'research_ethics',
        'submission_preparation',
        'peer_review',
        'revision',
    ];

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'min:1'],
            'stage' => ['required', 'string', 'in:'.implode(',', self::STAGES)],
        ]);

        $allowedProjectIds = array_values(array_filter(array_map(
            static fn (string $projectId): int => (int) trim($projectId),
            explode(',', (string) config('services.mcp.allowed_project_ids', '')),
        )));

        abort_unless(
            $allowedProjectIds !== [] && in_array((int) $validated['project_id'], $allowedProjectIds, true),
            403,
            'This MCP token is not authorized for the requested research project.'
        );

        $project = ResearchProject::query()
            ->with([
                'members:id,name',
                'papers:id,research_project_id,title,abstract,status',
                'citations:id,research_project_id,key,title,authors,year,doi',
                'feedback' => fn ($query) => $query->where('status', 'open')->latest()->limit(10)->with('author:id,name'),
            ])
            ->findOrFail($validated['project_id']);
        $latestVersion = $project->documentVersions()->with('author:id,name')->latest('version_number')->first();
        $stages = $this->stageGuidance();

        return Response::json([
            'workflow' => $stages[$validated['stage']],
            'project_evidence' => [
                'project' => [
                    'title' => $project->title,
                    'discipline' => $project->discipline,
                    'description' => $project->description,
                    'research_question' => $project->research_question,
                    'methodology' => $project->methodology,
                    'expected_outcome' => $project->expected_outcome,
                    'ethics_status' => $project->ethics_status,
                ],
                'collaborators' => $project->members->map(fn ($member): array => [
                    'name' => $member->name,
                    'project_role' => $member->pivot->role,
                ])->values()->all(),
                'latest_draft' => $latestVersion ? [
                    'version' => $latestVersion->version_number,
                    'author' => $latestVersion->author?->name,
                    'excerpt' => mb_substr((string) ($latestVersion->content['text'] ?? strip_tags((string) ($latestVersion->content['html'] ?? ''))), 0, 5000),
                ] : null,
                'open_feedback' => $project->feedback->map(fn ($feedback): array => [
                    'author' => $feedback->author?->name,
                    'comment' => $feedback->body,
                ])->values()->all(),
                'papers' => $project->papers->map(fn ($paper): array => [
                    'title' => $paper->title,
                    'abstract' => $paper->abstract,
                    'status' => $paper->status,
                ])->values()->all(),
                'citations' => $project->citations->take(20)->map(fn ($citation): array => [
                    'key' => $citation->key,
                    'title' => $citation->title,
                    'authors' => $citation->authors,
                    'year' => $citation->year,
                    'doi' => $citation->doi,
                ])->values()->all(),
                'venue_record' => null,
                'authorship_agreement' => null,
            ],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The authorized research project ID.')->required(),
            'stage' => $schema->string()->description('One of: '.implode(', ', self::STAGES).'.')->required(),
        ];
    }

    /** @return array<string, array{title: string, objective: string, task_brief: string, human_decisions: list<string>}> */
    private function stageGuidance(): array
    {
        return [
            'draft_initialization' => [
                'title' => 'Draft initialization and editing',
                'objective' => 'Shape a defensible manuscript outline and improve supplied draft text using project evidence.',
                'task_brief' => 'Propose a section plan and edit only supplied text. Mark unsupported claims and missing evidence as questions or placeholders; never invent findings, citations, or data.',
                'human_decisions' => ['Authors approve substantive edits and claims.', 'Supervisors provide guidance without replacing author responsibility.'],
            ],
            'venue_selection' => [
                'title' => 'Target venue selection',
                'objective' => 'Compare possible venues against the project contribution, audience, discipline, and methods.',
                'task_brief' => 'Suggest comparison criteria and a shortlist only when supported by supplied information. Do not invent venue scope, fees, deadlines, acceptance rates, or current rules; flag each item for checking on the official venue site.',
                'human_decisions' => ['Authors choose and verify the target venue and current instructions.'],
            ],
            'authorship' => [
                'title' => 'Authorship and contribution discussion',
                'objective' => 'Make contribution expectations and author responsibilities explicit for the team.',
                'task_brief' => 'Create a contribution matrix for discussion, identifying unknown contributions. Do not infer authorship or author order from project roles, and do not claim consensus without explicit agreement from every author.',
                'human_decisions' => ['All contributors agree to authorship, order, contributions, and final manuscript approval.'],
            ],
            'research_ethics' => [
                'title' => 'Research ethics readiness',
                'objective' => 'Identify ethics, consent, privacy, and data-governance evidence that may be needed.',
                'task_brief' => 'Report the stored ethics status exactly and list unanswered questions about approvals, consent, privacy, and data handling. Do not treat this review as ethics approval or permission to begin research.',
                'human_decisions' => ['The responsible ethics committee and research team determine requirements and approval.'],
            ],
            'submission_preparation' => [
                'title' => 'Submission preparation',
                'objective' => 'Prepare a verifiable checklist for manuscript files, metadata, declarations, and venue requirements.',
                'task_brief' => 'Build a checklist from supplied venue instructions and project evidence. Mark absent requirements as unknown and items needing official-source verification; never submit externally or claim submission is complete.',
                'human_decisions' => ['Authors approve the final files, declarations, venue, and submission.'],
            ],
            'peer_review' => [
                'title' => 'Peer-review response',
                'objective' => 'Organize reviewer comments into evidence-based responses and manuscript actions.',
                'task_brief' => 'Create a point-by-point response matrix with the requested change, project evidence, proposed response, and unresolved work. Do not fabricate analyses or expose reviewer material beyond this authorized project context.',
                'human_decisions' => ['Authors approve responses and any changes to claims or analyses.'],
            ],
            'revision' => [
                'title' => 'Revision and resubmission',
                'objective' => 'Track revisions against feedback and verify the revised manuscript before resubmission.',
                'task_brief' => 'Map each open feedback item to an action, responsible collaborator, evidence, and draft location. Flag disagreements and unsupported edits; do not silently change conclusions or mark resubmission complete.',
                'human_decisions' => ['Authors and supervisors agree on revisions; authors control resubmission.'],
            ],
        ];
    }
}