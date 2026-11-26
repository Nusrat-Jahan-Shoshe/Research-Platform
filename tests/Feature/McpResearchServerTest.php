<?php

namespace Tests\Feature;

use App\Mcp\Servers\ResearchServer;
use App\Mcp\Tools\ManuscriptWorkflowTool;
use App\Mcp\Tools\ProjectContextTool;
use App\Models\ResearchProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Request;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class McpResearchServerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mcp_server_requires_bearer_authorization(): void
    {
        config()->set('services.mcp.access_token', 'test-mcp-token');

        $this->postJson('/mcp/research', [])->assertUnauthorized();
    }

    public function test_research_mcp_server_registers_project_tools(): void
    {
        $server = new ReflectionClass(ResearchServer::class);
        $tools = $server->getDefaultProperties()['tools'] ?? [];

        $this->assertContains(ProjectContextTool::class, $tools);
        $this->assertContains(ManuscriptWorkflowTool::class, $tools);
    }

    public function test_manuscript_workflow_tool_returns_project_evidence_and_stage_guardrails(): void
    {
        $user = User::factory()->create();
        $project = ResearchProject::create([
            'owner_id' => $user->id,
            'title' => 'Ethical Learning Systems',
            'discipline' => 'Education',
            'ethics_status' => 'submitted',
        ]);
        $project->members()->attach($user->id, ['role' => 'student']);
        $project->documentVersions()->create([
            'author_id' => $user->id,
            'content' => ['text' => 'A draft grounded in collected evidence.'],
            'version_number' => 1,
        ]);
        config()->set('services.mcp.allowed_project_ids', (string) $project->id);

        $tool = new ManuscriptWorkflowTool;
        $response = $tool->handle(new Request([
            'project_id' => $project->id,
            'stage' => 'research_ethics',
        ]));
        $payload = json_decode((string) $response->content(), true);

        $this->assertSame('Research ethics readiness', $payload['workflow']['title']);
        $this->assertStringContainsString('Do not treat this review as ethics approval', $payload['workflow']['task_brief']);
        $this->assertSame('submitted', $payload['project_evidence']['project']['ethics_status']);
        $this->assertSame('A draft grounded in collected evidence.', $payload['project_evidence']['latest_draft']['excerpt']);
        $this->assertNull($payload['project_evidence']['venue_record']);
    }

    public function test_manuscript_workflow_tool_rejects_projects_outside_its_allowlist(): void
    {
        $user = User::factory()->create();
        $project = ResearchProject::create(['owner_id' => $user->id, 'title' => 'Restricted Project']);
        config()->set('services.mcp.allowed_project_ids', '999999');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new ManuscriptWorkflowTool)->handle(new Request([
            'project_id' => $project->id,
            'stage' => 'authorship',
        ]));
    }
}
