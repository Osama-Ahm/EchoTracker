<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Forums table
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#2d5a27');
            $table->string('icon')->default('bi-chat-dots');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Forum topics table
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['forum_id', 'is_pinned', 'last_activity_at']);
        });

        // Forum replies table
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();

            $table->index(['topic_id', 'created_at']);
        });

        // User points table
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // 'incident_report', 'forum_post', 'event_attend', etc.
            $table->integer('points');
            $table->string('description');
            $table->morphs('pointable'); // Related model (incident, topic, event, etc.)
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Badges table
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('icon');
            $table->string('color', 7)->default('#2d5a27');
            $table->string('criteria'); // JSON criteria for earning
            $table->integer('points_required')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User badges table
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']);
        });

        // Community events table
        Schema::create('community_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('type'); // 'cleanup', 'awareness', 'workshop', etc.
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('max_participants')->nullable();
            $table->text('requirements')->nullable();
            $table->text('what_to_bring')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index(['status', 'start_date']);
        });

        // Event RSVPs table
        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('community_events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['attending', 'maybe', 'not_attending'])->default('attending');
            $table->text('notes')->nullable();
            $table->boolean('attended')->default(false);
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        // Volunteer opportunities table
        Schema::create('volunteer_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category'); // 'cleanup', 'education', 'monitoring', etc.
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->integer('volunteers_needed');
            $table->text('skills_required')->nullable();
            $table->text('benefits')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('status')->default('active'); // active, filled, completed, cancelled
            $table->timestamps();

            $table->index(['status', 'start_date']);
        });

        // Volunteer applications table
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('volunteer_opportunities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message')->nullable();
            $table->text('skills')->nullable();
            $table->string('availability');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique(['opportunity_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
        Schema::dropIfExists('volunteer_opportunities');
        Schema::dropIfExists('event_rsvps');
        Schema::dropIfExists('community_events');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('user_points');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forums');
    }
};
