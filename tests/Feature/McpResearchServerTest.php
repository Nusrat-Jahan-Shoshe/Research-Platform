<?php

namespace Tests\Feature;

use App\Mcp\Servers\ResearchServer;
use App\Mcp\Tools\ProjectContextTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class McpResearchServerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mcp_server_requires_bearer_authorization(): void
    {
        config()->set('services.mcp.access_token', 'test-mcp-token');

        $this->postJson('/mcp/research', [])->assertUnauthorized();
    }

    public function test_research_mcp_server_registers_project_context_tool(): void
    {
        $server = new ReflectionClass(ResearchServer::class);
        $tools = $server->getDefaultProperties()['tools'] ?? [];

        $this->assertContains(ProjectContextTool::class, $tools);
    }
}
