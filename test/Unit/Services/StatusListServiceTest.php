<?php

namespace Test\Unit\Services;

use \Mockery;
use Test\TestCase;
use Core\Di\DiContainer as Di;
use App\Models\User;
use App\Models\Status;
use App\Repositories\StatusRepository;
use App\Services\StatusListService;
use Presentation\Models\Components\StatusList;
use Presentation\Models\Components\StatusListItem;

/**
 * @coversDefaultClass \App\Services\StatusListService
 */
class StatusListServiceTest extends TestCase {

    /**
     * @test
     * @covers ::createPersonalStatusListViewModel
     */
    public function testCreatePersonalStatusList() {
        $status = Mockery::mock(Status::class);
        $status->shouldReceive('body')->andReturn('testBody');
        $user = Mockery::mock(User::class);
        $user->shouldReceive('personalStatuses')->andReturn([$status, $status, $status]);

        $statusList = (new StatusListService())->createPersonalStatusListViewModel($user);

        $this->assertTrue($statusList->hasStatuses());
        $statusListItems = $statusList->statusListItems();
        $this->assertCount(3, $statusListItems);
        $this->assertSame('testBody', $statusListItems[0]->getBody());
    }

    /**
     * @test
     * @covers ::createUsersStatusListViewModel
     */
    public function testCreateUsersStatusList() {
        $status = Mockery::mock(Status::class);
        $status->shouldReceive('body')->andReturn('testBody');
        $user = Mockery::mock(User::class);
        $user->shouldReceive('statuses')->andReturn([$status, $status, $status]);

        $statusList = (new StatusListService())->createUsersStatusListViewModel($user);

        $this->assertTrue($statusList->hasStatuses());
        $statusListItems = $statusList->statusListItems();
        $this->assertCount(3, $statusListItems);
        $this->assertSame('testBody', $statusListItems[0]->getBody());
    }

    /**
     * @test
     * @covers ::createStatusViewModel
     */
    public function testCreateStatus() {
        $status = Mockery::mock(Status::class);
        $status->shouldReceive('body')->andReturn('testBody');

        $statusListItem = (new StatusListService())->createStatusViewModel($status);

        $this->assertSame('testBody', $statusListItem->getBody());
    }
}
