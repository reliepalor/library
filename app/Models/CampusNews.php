<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CampusNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'featured_image',
        'gallery_images',
        'publish_date',
        'event_date',
        'event_time',
        'location',
        'category',
        'status',
        'is_featured',
        'views_count',
        'author_name',
        'author_id',
        'tags',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'event_date' => 'date',
        'event_time' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'gallery_images' => 'array',
        'tags' => 'array',
    ];

    protected $attributes = [
        'status' => 'draft',
        'category' => 'announcement',
        'is_featured' => false,
        'views_count' => 0,
    ];

    /**
     * Get the author of the news
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for published news
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured news
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for recent news
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('publish_date', 'desc')->limit($limit);
    }

    /**
     * Get excerpt from content if not provided
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 150);
    }

    /**
     * Get formatted publish date
     */
    public function getFormattedPublishDateAttribute()
    {
        return $this->publish_date->format('M d, Y');
    }

    /**
     * Get formatted event date and time
     */
    public function getFormattedEventDateTimeAttribute()
    {
        if (!$this->event_date) {
            return null;
        }

        $date = $this->event_date->format('M d, Y');
        $time = $this->event_time ? $this->event_time->format('g:i A') : null;

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get category badge color
     */
    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'academic' => 'blue',
            'events' => 'green',
            'sports' => 'orange',
            'research' => 'purple',
            'achievement' => 'yellow',
            'announcement' => 'gray',
            default => 'gray',
        };
    }
}
