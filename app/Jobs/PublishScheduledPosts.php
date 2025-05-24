<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\PlatformPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledPosts implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Query posts that are scheduled and due for publishing
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '<=', now())
            ->with('platforms') // Load related platforms
            ->get();

        $publisher = app(PlatformPublisher::class); // Resolve the service

        foreach ($posts as $post) {
            // Use the PlatformPublisher service to simulate publishing
            foreach ($post->platforms as $platform) {
                $publisher->publish($post, $platform);
            }

            // Update the post status to 'published'
            $post->update(['status' => 'published']);
        }
    }
}
