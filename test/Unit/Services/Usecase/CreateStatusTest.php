<?php

namespace Test\Unit\Services\Usecase;

use \Mockery;
use Test\TestCase;
use Core\Di\DiContainer as Di;
use Core\Storage\File;
use App\Models\User;
use App\Models\Status;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Repositories\ImageRepository;
use App\Services\Usecase\CreateStatus;
use Presentation\Models\Status\Components\StatusPostForm;

/**
 * @coversDefaultClass \App\Services\Usecase\CreateStatus
 */
class CreateStatusTest extends TestCase {

    /**
     * @test
     * @dataProvider postDataProvider
     * @covers ::createFromPost
     * @covers ::createStatus
     */
    public function testCreateFromPost(string $body, ?File $image, bool $expectedResult) {
        $statusPostFormViewModel = Mockery::mock(StatusPostForm::class);
        $statusPostFormViewModel->shouldReceive('retrieveBody')->andReturn($body);
        $statusPostFormViewModel->shouldReceive('retrieveImage')->andReturn($image);
        $statusPostFormViewModel->shouldReceive('preserveBody')->with($body);
        $user = Mockery::mock(User::class);
        $authRepository = Mockery::mock(AuthRepository::class);
        $authRepository->shouldReceive('user')->andReturn($user);
        $status = Mockery::mock(Status::class);
        $statusRepository = Mockery::mock(StatusRepository::class);
        $statusRepository->shouldReceive('insert')->with($user, $body)->andReturn($status);
        $imageRepository = Mockery::mock(ImageRepository::class);
        $imageRepository->shouldReceive('insert')->with($status, $image);

        Di::set(StatusPostForm::class, $statusPostFormViewModel);
        Di::set(AuthRepository::class, $authRepository);
        Di::set(StatusRepository::class, $statusRepository);
        Di::set(ImageRepository::class, $imageRepository);

        $this->assertSame($expectedResult, (new CreateStatus())->createFromPost());
    }
    public function postDataProvider() {
        $image = Mockery::mock(File::class);
        $longBody = 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' .
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' .
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' .
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        return [
            ['test', null, true],
            ['', null, false],
            [$longBody, null, false],
            ['test', $image, true],
            ['', $image, false],
            [$longBody, $image, false],
        ];
    }
}
