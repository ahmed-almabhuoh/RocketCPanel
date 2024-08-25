<?php

namespace App\Jobs\Auth\User;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class UserCreatedJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Http::withHeaders(getRocketShMAPIKeys())->get(getRocketShMAPILink() . 'build/user/password/' . Crypt::encrypt($this->user->id));
        Http::withHeaders(getRocketShMAPIKeys())->get(getRocketShMAPILink() . 'build/user/balance/' . Crypt::encrypt($this->user->id));
        Http::withHeaders(getRocketShMAPIKeys())->get(getRocketShMAPILink() . 'build/user/settings/' . Crypt::encrypt($this->user->id));
    }
}
