<?php

namespace App\Providers;

use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Events\CertificationExpired;
use App\Events\JobApplicationSubmitted;
use App\Events\JobPosted;
use App\Events\UserSuspended;
use App\Events\WorkerBooked;
use App\Listeners\NotifyEmployerOfApplication;
use App\Listeners\NotifyMatchedWorkers;
use App\Listeners\NotifyPartiesOfCancellation;
use App\Listeners\NotifyWorkerOfBooking;
use App\Listeners\NotifyUserOfSuspension;
use App\Listeners\NotifyWorkerOfConfirmation;
use App\Listeners\SendComplianceAlert;
use App\Listeners\TriggerAIMatching;
use App\Listeners\UpdateApplicationOnBookingConfirmed;
use App\AI\Contracts\AIProviderInterface;
use App\AI\Providers\OpenAIProvider;
use OpenAI\Factory as OpenAIFactory;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\CertificationBodyRepositoryInterface;
use App\Repositories\Contracts\CertificationRepositoryInterface;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Repositories\Contracts\JobRepositoryInterface;
use App\Repositories\Contracts\TradeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\CertificationBodyRepository;
use App\Repositories\Eloquent\CertificationRepository;
use App\Repositories\Eloquent\JobApplicationRepository;
use App\Repositories\Eloquent\JobRepository;
use App\Repositories\Eloquent\TradeRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WorkerRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AIProviderInterface::class, function () {
            $client = (new OpenAIFactory())
                ->withApiKey(env('OPENAI_API_KEY', ''))
                ->make();
            return new OpenAIProvider($client);
        });

        $this->app->bind(JobRepositoryInterface::class, JobRepository::class);
        $this->app->bind(WorkerRepositoryInterface::class, WorkerRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(CertificationRepositoryInterface::class, CertificationRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(JobApplicationRepositoryInterface::class, JobApplicationRepository::class);
        $this->app->bind(TradeRepositoryInterface::class, TradeRepository::class);
        $this->app->bind(CertificationBodyRepositoryInterface::class, CertificationBodyRepository::class);
    }

    public function boot(): void
    {
        Event::listen(JobPosted::class, TriggerAIMatching::class);
        Event::listen(JobPosted::class, NotifyMatchedWorkers::class);

        Event::listen(JobApplicationSubmitted::class, NotifyEmployerOfApplication::class);

        Event::listen(WorkerBooked::class, NotifyWorkerOfBooking::class);
        Event::listen(BookingConfirmed::class, NotifyWorkerOfConfirmation::class);
        Event::listen(BookingConfirmed::class, UpdateApplicationOnBookingConfirmed::class);
        Event::listen(BookingCancelled::class, NotifyPartiesOfCancellation::class);

        Event::listen(UserSuspended::class, NotifyUserOfSuspension::class);

        Event::listen(CertificationExpired::class, SendComplianceAlert::class);
    }
}
