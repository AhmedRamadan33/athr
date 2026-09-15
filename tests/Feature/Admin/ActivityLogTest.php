<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_permission_cannot_view_activity_log(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.activity-log.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_and_filter_activity_log_by_causer(): void
    {
        $viewer = Admin::factory()->create();
        $viewer->givePermissionTo(Permission::firstOrCreate(['name' => 'activity-log.view', 'guard_name' => 'admin']));

        $actor = Admin::factory()->create();
        $this->actingAs($actor, 'admin');
        Category::factory()->create();

        $this->actingAs($viewer, 'admin');
        $otherActor = Admin::factory()->create();
        $this->actingAs($otherActor, 'admin');
        Category::factory()->create();

        $response = $this->actingAs($viewer, 'admin')->get(route('admin.activity-log.index', ['causer_id' => $actor->id]));

        $response->assertOk();
        $activities = $response->viewData('activities');
        $this->assertGreaterThan(0, $activities->total());
        foreach ($activities as $activity) {
            $this->assertSame($actor->id, $activity->causer_id);
        }
    }
}
